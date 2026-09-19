import { Router } from 'express';
import crypto from 'node:crypto';
import { db, purgeExpiredHolds, holdTtlMinutes } from '../config/db.js';
import { auth, AuthRequest } from '../middleware/auth.js';
import { priceQuote } from '../utils/pricing.js';

const router = Router();

function bookedSeatIds(showtimeId: number | string): Set<number> {
  const rows = db.prepare(
    'SELECT bs.seat_id FROM booking_seats bs JOIN bookings b ON b.id = bs.booking_id WHERE b.showtime_id = ? AND b.status != ?'
  ).all(showtimeId, 'Cancelled') as { seat_id: number }[];
  return new Set(rows.map((r) => r.seat_id));
}

/** Create (or refresh) a hold on seats. Returns hold token + expiry. */
router.post('/', auth, (req: AuthRequest, res) => {
  purgeExpiredHolds();
  const { showtime_id, seat_ids } = req.body as { showtime_id?: number; seat_ids?: number[] };
  const show = (showtime_id ? db.prepare('SELECT id, hall_id FROM showtimes WHERE id = ?').get(Number(showtime_id)) : null) as { id: number; hall_id: number } | null;
  if (!show) {
    return res.status(422).json({ errors: { showtime_id: ['Valid showtime is required'] } });
  }
  const ids = [...new Set((Array.isArray(seat_ids) ? seat_ids : []).map(Number).filter(Boolean))];
  if (!ids.length || ids.length > 10) {
    return res.status(422).json({ errors: { seat_ids: ['Select between 1 and 10 seats'] } });
  }
  const seatRows = db.prepare('SELECT id, hall_id FROM seats WHERE id IN (' + ids.map(() => '?').join(',') + ')').all(...ids) as { id: number; hall_id: number }[];
  if (seatRows.length !== ids.length || seatRows.some((s) => Number(s.hall_id) !== Number(show.hall_id))) {
    return res.status(422).json({ errors: { seat_ids: ['One or more seats are invalid for this showtime'] } });
  }

  const taken = bookedSeatIds(Number(showtime_id));
  if (ids.some((id) => taken.has(id))) {
    return res.status(409).json({ message: 'Some seats are already booked. Please choose different seats.' });
  }

  // Remove this user's stale holds for other showtimes/seats so a user holds one selection at a time
  db.prepare('DELETE FROM seat_holds WHERE user_id = ?').run(req.user!.id);

  const token = crypto.randomBytes(18).toString('hex');
  const expiresAt = new Date(Date.now() + holdTtlMinutes() * 60_000).toISOString().slice(0, 19).replace('T', ' ');
  const ins = db.prepare('INSERT INTO seat_holds (showtime_id, seat_id, user_id, hold_token, expires_at) VALUES (?, ?, ?, ?, ?)');
  try {
    for (const seatId of ids) ins.run(Number(showtime_id), seatId, req.user!.id, token, expiresAt);
  } catch {
    db.prepare('DELETE FROM seat_holds WHERE hold_token = ?').run(token);
    return res.status(409).json({ message: 'Some seats were just held by someone else. Please try again.' });
  }

  res.status(201).json({
    hold_token: token,
    showtime_id: Number(showtime_id),
    seat_ids: ids,
    expires_at: new Date(Date.now() + holdTtlMinutes() * 60_000).toISOString(),
    ttl_minutes: holdTtlMinutes(),
    quote: priceQuote(ids.length),
    message: `Seats held for ${holdTtlMinutes()} minutes. Complete payment before the hold expires.`,
  });
});

router.get('/:token', auth, (req: AuthRequest, res) => {
  purgeExpiredHolds();
  const rows = db.prepare('SELECT * FROM seat_holds WHERE hold_token = ? AND user_id = ?').all(req.params.token, req.user!.id) as Record<string, unknown>[];
  if (!rows.length) return res.status(404).json({ message: 'Hold not found or expired' });
  res.json({
    hold_token: req.params.token,
    showtime_id: rows[0].showtime_id,
    seat_ids: rows.map((r) => r.seat_id),
    expires_at: new Date(String(rows[0].expires_at).replace(' ', 'T') + 'Z').toISOString(),
  });
});

router.delete('/:token', auth, (req: AuthRequest, res) => {
  db.prepare('DELETE FROM seat_holds WHERE hold_token = ? AND user_id = ?').run(req.params.token, req.user!.id);
  res.json({ message: 'Hold released' });
});

export default router;
