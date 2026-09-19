import { Router } from 'express';
import { db } from '../config/db.js';
import { auth, AuthRequest } from '../middleware/auth.js';

const router = Router();

router.get('/', auth, (req: AuthRequest, res) => {
  const rows = db.prepare(
    `SELECT p.*, m.title AS movie_title FROM payments p
     JOIN bookings b ON b.id = p.booking_id
     JOIN showtimes s ON s.id = b.showtime_id
     JOIN movies m ON m.id = s.movie_id
     WHERE b.user_id = ? ORDER BY p.id DESC`
  ).all(req.user!.id);
  res.json({ payments: rows });
});

router.post('/process', auth, (req: AuthRequest, res) => {
  const { booking_id, payment_method, card_number, card_name, expiry_date, cvv } = req.body as Record<string, unknown>;
  const errors: Record<string, string[]> = {};
  const booking = (booking_id ? db.prepare('SELECT * FROM bookings WHERE id = ?').get(booking_id as number) : null) as Record<string, unknown> | null;
  if (!booking) errors.booking_id = ['Valid booking is required'];
  if (payment_method !== 'credit_card' && payment_method !== 'paypal') errors.payment_method = ['Payment method must be credit_card or paypal'];
  if (payment_method === 'credit_card') {
    const digits = String(card_number ?? '').replace(/\D/g, '');
    if (digits.length < 16) errors.card_number = ['Card number must have at least 16 digits'];
    if (String(card_name ?? '').trim().length < 3) errors.card_name = ['Name on card is required'];
    if (!/^\d{2}\/\d{2}$/.test(String(expiry_date ?? ''))) errors.expiry_date = ['Expiry must be MM/YY'];
    if (!/^\d{3,4}$/.test(String(cvv ?? ''))) errors.cvv = ['CVV must be 3-4 digits'];
  }
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  if (Number((booking as Record<string, unknown>).user_id) !== req.user!.id) {
    return res.status(403).json({ message: 'Unauthorized access to booking' });
  }
  if ((booking as Record<string, unknown>).payment_status === 'paid') {
    return res.status(422).json({ message: 'This booking has already been paid' });
  }
  if (db.prepare('SELECT id FROM payments WHERE booking_id = ?').get(booking_id as number)) {
    return res.status(422).json({ message: 'Payment already processed for this booking' });
  }

  const lastFour = payment_method === 'credit_card' ? String(card_number).replace(/\D/g, '').slice(-4) : null;
  const txn = `${payment_method === 'credit_card' ? 'CC' : 'PP'}_${Date.now()}${Math.floor(Math.random() * 1000)}`;
  const bid = Number(booking_id);
  const amount = Number((booking as Record<string, unknown>).total_price);
  const method = String(payment_method);
  const r = db.prepare(
    "INSERT INTO payments (booking_id, amount, payment_method, transaction_id, card_last_four, status) VALUES (?, ?, ?, ?, ?, 'completed')"
  ).run(bid, amount, method, txn, lastFour);
  db.prepare("UPDATE bookings SET payment_status='paid', payment_method=?, transaction_id=?, paid_at=datetime('now'), status='confirmed', updated_at=datetime('now') WHERE id=?").run(
    method, txn, bid
  );
  const payment = db.prepare('SELECT * FROM payments WHERE id = ?').get(Number(r.lastInsertRowid));
  res.json({ payment, message: 'Payment processed successfully!' });
});

export default router;
