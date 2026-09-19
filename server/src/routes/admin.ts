import { Router } from 'express';
import { db } from '../config/db.js';
import { auth, admin, AuthRequest } from '../middleware/auth.js';
import { publicUser } from '../utils/auth.js';

const router = Router();
router.use(auth, admin);

router.get('/stats', (_req, res) => {
  const one = (sql: string, ...p: unknown[]) => (db.prepare(sql).get(...(p as (string | number | null)[])) as Record<string, unknown> | undefined);
  const total_users = Number(one('SELECT COUNT(*) c FROM users')?.c ?? 0);
  const total_bookings = Number(one('SELECT COUNT(*) c FROM bookings')?.c ?? 0);
  const total_movies = Number(one('SELECT COUNT(*) c FROM movies')?.c ?? 0);
  const coming_soon_count = Number(one("SELECT COUNT(*) c FROM movies WHERE status='coming_soon'")?.c ?? 0);
  const revenue = Number(one("SELECT COALESCE(SUM(total_price),0) s FROM bookings WHERE payment_status='paid'")?.s ?? 0);
  const recent_bookings = db.prepare(
    `SELECT b.*, m.title AS movie_title, s.date AS show_date, s.time AS show_time, u.name AS user_name, u.email AS user_email
     FROM bookings b JOIN showtimes s ON s.id=b.showtime_id JOIN movies m ON m.id=s.movie_id JOIN users u ON u.id=b.user_id
     ORDER BY b.id DESC LIMIT 5`
  ).all();
  res.json({ stats: { total_users, total_bookings, total_movies, coming_soon_count, revenue, recent_bookings } });
});

router.get('/users', (_req, res) => {
  const rows = db.prepare('SELECT u.*, (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id) AS bookings_count FROM users u ORDER BY u.id DESC').all() as Record<string, unknown>[];
  res.json({ users: rows.map((u) => ({ ...publicUser(u), bookings_count: u.bookings_count, created_at: u.created_at })) });
});

router.patch('/users/:id/toggle-admin', (req: AuthRequest, res) => {
  const user = db.prepare('SELECT * FROM users WHERE id = ?').get(req.params.id) as Record<string, unknown> | null;
  if (!user) return res.status(404).json({ message: 'User not found' });
  if (Number(user.id) === req.user!.id) return res.status(422).json({ message: 'You cannot modify your own admin status.' });
  db.prepare('UPDATE users SET is_admin = ?, updated_at=datetime(\'now\') WHERE id = ?').run(Number(user.is_admin) ? 0 : 1, req.params.id);
  res.json({ message: Number(user.is_admin) ? 'Admin privileges revoked.' : 'Admin privileges granted.' });
});

router.put('/users/:id', (req, res) => {
  const user = db.prepare('SELECT * FROM users WHERE id = ?').get(req.params.id) as Record<string, unknown> | null;
  if (!user) return res.status(404).json({ message: 'User not found' });
  const { name, email } = req.body as { name?: string; email?: string };
  const errors: Record<string, string[]> = {};
  if (!name || name.trim().length < 2) errors.name = ['Name is required'];
  if (!email || !/^\S+@\S+\.\S+$/.test(email)) errors.email = ['Valid email is required'];
  const dupe = db.prepare('SELECT id FROM users WHERE email = ? AND id != ?').get(String(email).toLowerCase(), req.params.id);
  if (dupe) errors.email = ['Email is already taken'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  db.prepare('UPDATE users SET name=?, email=?, updated_at=datetime(\'now\') WHERE id=?').run(name!.trim(), email!.toLowerCase(), req.params.id);
  res.json({ message: 'User updated successfully.' });
});

router.delete('/users/:id', (req: AuthRequest, res) => {
  const user = db.prepare('SELECT id FROM users WHERE id = ?').get(req.params.id);
  if (!user) return res.status(404).json({ message: 'User not found' });
  if (Number(req.params.id) === req.user!.id) return res.status(422).json({ message: 'You cannot delete your own account.' });
  db.prepare('DELETE FROM users WHERE id = ?').run(req.params.id);
  res.json({ message: 'User deleted successfully.' });
});

router.get('/settings', (_req, res) => {
  const rows = db.prepare('SELECT key, value FROM settings').all() as { key: string; value: string }[];
  res.json({ settings: Object.fromEntries(rows.map((r) => [r.key, r.value])) });
});

router.put('/settings', (req, res) => {
  const { site_name, contact_email, phone_number, address, booking_fee, tax_rate } = req.body as Record<string, unknown>;
  const errors: Record<string, string[]> = {};
  if (!String(site_name ?? '').trim()) errors.site_name = ['Site name is required'];
  if (!/^\S+@\S+\.\S+$/.test(String(contact_email ?? ''))) errors.contact_email = ['Valid contact email is required'];
  if (!String(phone_number ?? '').trim()) errors.phone_number = ['Phone number is required'];
  if (!String(address ?? '').trim()) errors.address = ['Address is required'];
  if (booking_fee === undefined || isNaN(Number(booking_fee)) || Number(booking_fee) < 0) errors.booking_fee = ['Booking fee must be >= 0'];
  if (tax_rate === undefined || isNaN(Number(tax_rate)) || Number(tax_rate) < 0 || Number(tax_rate) > 100) errors.tax_rate = ['Tax rate must be 0-100'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  const up = db.prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
  up.run('site_name', String(site_name));
  up.run('contact_email', String(contact_email));
  up.run('phone_number', String(phone_number));
  up.run('address', String(address));
  up.run('booking_fee', String(booking_fee));
  up.run('tax_rate', String(tax_rate));
  res.json({ message: 'Settings updated successfully.' });
});

router.get('/dashboard', (_req, res) => {
  const today = new Date().toISOString().slice(0, 10);
  const upcoming = db.prepare(
    `SELECT b.*, m.title AS movie_title, s.date AS show_date, s.time AS show_time
     FROM bookings b JOIN showtimes s ON s.id=b.showtime_id JOIN movies m ON m.id=s.movie_id
     WHERE s.date >= ? ORDER BY b.id DESC LIMIT 5`
  ).all(today);
  const history = db.prepare(
    `SELECT b.*, m.title AS movie_title, s.date AS show_date, s.time AS show_time
     FROM bookings b JOIN showtimes s ON s.id=b.showtime_id JOIN movies m ON m.id=s.movie_id
     WHERE s.date < ? ORDER BY b.id DESC LIMIT 10`
  ).all(today);
  res.json({ upcoming, history });
});

/**
 * Usher check-in: validate a ticket's reference + token and mark it used.
 * Accepts either { booking_reference, check_in_token } or { qr_data } (scanned QR JSON).
 */
router.post('/check-in', (req, res) => {
  let ref = String(req.body?.booking_reference ?? '').trim().toUpperCase();
  let token = String(req.body?.check_in_token ?? '').trim();
  if (req.body?.qr_data) {
    try {
      const parsed = JSON.parse(String(req.body.qr_data));
      ref = String(parsed.ref ?? '').toUpperCase();
      token = String(parsed.token ?? '');
    } catch {
      return res.status(422).json({ message: 'Unrecognized QR code' });
    }
  }
  if (!ref || !token) return res.status(422).json({ errors: { booking_reference: ['Reference and token are required'] } });

  const booking = db.prepare(
    `SELECT b.*, m.title AS movie_title, s.date AS show_date, s.time AS show_time, u.name AS user_name
     FROM bookings b JOIN showtimes s ON s.id = b.showtime_id JOIN movies m ON m.id = s.movie_id JOIN users u ON u.id = b.user_id
     WHERE b.booking_reference = ?`
  ).get(ref) as Record<string, unknown> | null;
  if (!booking || booking.check_in_token !== token) {
    return res.status(404).json({ message: 'Ticket not found. Check the reference and code.' });
  }
  if (booking.payment_status !== 'paid') {
    return res.status(422).json({ message: 'Ticket has not been paid for.' });
  }
  if (booking.checked_in_at) {
    return res.status(409).json({ message: `Ticket already checked in at ${booking.checked_in_at}.`, booking });
  }
  db.prepare("UPDATE bookings SET checked_in_at = datetime('now'), updated_at = datetime('now') WHERE id = ?").run(booking.id as number);
  const seats = db.prepare(
    'SELECT s.row, s.number FROM seats s JOIN booking_seats bs ON bs.seat_id = s.id WHERE bs.booking_id = ? ORDER BY s.row, s.number'
  ).all(booking.id as number) as { row: string; number: number }[];
  res.json({
    message: 'Check-in successful. Enjoy the movie!',
    booking: { ...booking, checked_in_at: new Date().toISOString(), seats: seats.map((s) => `${s.row}${s.number}`) },
  });
});

export default router;
