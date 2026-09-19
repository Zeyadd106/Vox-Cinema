import { Router } from 'express';
import { db, purgeExpiredHolds } from '../config/db.js';
import { auth, admin, AuthRequest } from '../middleware/auth.js';

const router = Router();

const ENRICHED = `SELECT s.*, m.title AS movie_title, h.name AS hall_name, h.format, h.cinema_id, c.name AS cinema_name
  FROM showtimes s JOIN movies m ON m.id = s.movie_id
  LEFT JOIN halls h ON h.id = s.hall_id LEFT JOIN cinemas c ON c.id = h.cinema_id`;

router.get('/', (req, res) => {
  const { movie_id, cinema_id, date } = req.query as { movie_id?: string; cinema_id?: string; date?: string };
  const where: string[] = [];
  const vals: (string | number)[] = [];
  if (movie_id) { where.push('s.movie_id = ?'); vals.push(Number(movie_id)); }
  if (cinema_id) { where.push('h.cinema_id = ?'); vals.push(Number(cinema_id)); }
  if (date) { where.push('s.date = ?'); vals.push(date); }
  const sql = ENRICHED + (where.length ? ` WHERE ${where.join(' AND ')}` : '') + ' ORDER BY s.date, s.time';
  res.json({ showtimes: db.prepare(sql).all(...vals) });
});

router.get('/movie/:movieId', (req, res) => {
  const today = new Date().toISOString().slice(0, 10);
  const { cinema_id } = req.query as { cinema_id?: string };
  let sql = ENRICHED + ' WHERE s.movie_id = ? AND s.date >= ?';
  const vals: (string | number)[] = [Number(req.params.movieId), today];
  if (cinema_id) { sql += ' AND h.cinema_id = ?'; vals.push(Number(cinema_id)); }
  res.json({ showtimes: db.prepare(sql + ' ORDER BY s.date, s.time').all(...vals) });
});

router.get('/:id', (req, res) => {
  const row = db.prepare(ENRICHED + ' WHERE s.id = ?').get(req.params.id);
  if (!row) return res.status(404).json({ message: 'Showtime not found' });
  res.json({ showtime: row });
});

router.get('/:id/seats', auth, (req: AuthRequest, res) => {
  purgeExpiredHolds();
  const show = db.prepare('SELECT * FROM showtimes WHERE id = ?').get(req.params.id) as { id: number; hall_id: number } | null;
  if (!show) return res.status(404).json({ message: 'Showtime not found' });
  const all = db.prepare('SELECT * FROM seats WHERE hall_id = ? ORDER BY row, number').all(show.hall_id) as { id: number; row: string; number: number }[];
  const booked = db.prepare(
    'SELECT bs.seat_id FROM booking_seats bs JOIN bookings b ON b.id = bs.booking_id WHERE b.showtime_id = ? AND b.status != ?'
  ).all(req.params.id, 'Cancelled') as { seat_id: number }[];
  const bookedSet = new Set(booked.map((b) => b.seat_id));
  const holds = db.prepare("SELECT seat_id, user_id FROM seat_holds WHERE showtime_id = ? AND expires_at > datetime('now')").all(req.params.id) as { seat_id: number; user_id: number }[];
  const heldBy = new Map(holds.map((h) => [h.seat_id, h.user_id]));
  res.json({
    hall_id: show.hall_id,
    seats: all.map((s) => ({
      ...s,
      seat_number: `${s.row}${s.number}`,
      is_available: !bookedSet.has(s.id) && !heldBy.has(s.id),
      is_held: heldBy.has(s.id),
      held_by_me: heldBy.get(s.id) === req.user!.id,
    })),
  });
});

function validateHall(hall_id: unknown): string | null {
  if (hall_id === undefined || hall_id === null || hall_id === '') return 'Hall is required';
  if (!db.prepare('SELECT id FROM halls WHERE id = ?').get(Number(hall_id))) return 'Valid hall is required';
  return null;
}

router.post('/', auth, admin, (req: AuthRequest, res) => {
  const { movie_id, hall_id, date, time } = req.body as { movie_id?: number; hall_id?: number; date?: string; time?: string };
  const errors: Record<string, string[]> = {};
  if (!movie_id || !db.prepare('SELECT id FROM movies WHERE id = ?').get(movie_id)) errors.movie_id = ['Valid movie is required'];
  const hallErr = validateHall(hall_id);
  if (hallErr) errors.hall_id = [hallErr];
  if (!date) errors.date = ['Date is required'];
  else if (date < new Date().toISOString().slice(0, 10)) errors.date = ['Date must be today or in the future'];
  if (!time) errors.time = ['Time is required'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  const r = db.prepare('INSERT INTO showtimes (movie_id, hall_id, date, time) VALUES (?, ?, ?, ?)').run(Number(movie_id), Number(hall_id), String(date), String(time));
  const row = db.prepare(ENRICHED + ' WHERE s.id = ?').get(Number(r.lastInsertRowid));
  res.status(201).json({ showtime: row, message: 'Showtime created successfully' });
});

router.put('/:id', auth, admin, (req: AuthRequest, res) => {
  const show = db.prepare('SELECT id FROM showtimes WHERE id = ?').get(req.params.id);
  if (!show) return res.status(404).json({ message: 'Showtime not found' });
  const { movie_id, hall_id, date, time } = req.body as { movie_id?: number; hall_id?: number; date?: string; time?: string };
  const errors: Record<string, string[]> = {};
  if (movie_id !== undefined && !db.prepare('SELECT id FROM movies WHERE id = ?').get(movie_id)) errors.movie_id = ['Valid movie is required'];
  if (hall_id !== undefined) {
    const hallErr = validateHall(hall_id);
    if (hallErr) errors.hall_id = [hallErr];
  }
  if (date !== undefined && date < new Date().toISOString().slice(0, 10)) errors.date = ['Date must be today or in the future'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  const fields: string[] = [];
  const vals: (string | number | null)[] = [];
  if (movie_id !== undefined) { fields.push('movie_id = ?'); vals.push(Number(movie_id)); }
  if (hall_id !== undefined) { fields.push('hall_id = ?'); vals.push(Number(hall_id)); }
  if (date !== undefined) { fields.push('date = ?'); vals.push(String(date)); }
  if (time !== undefined) { fields.push('time = ?'); vals.push(String(time)); }
  if (fields.length) db.prepare(`UPDATE showtimes SET ${fields.join(', ')}, updated_at=datetime('now') WHERE id = ?`).run(...vals, req.params.id);
  const row = db.prepare(ENRICHED + ' WHERE s.id = ?').get(req.params.id);
  res.json({ showtime: row, message: 'Showtime updated successfully' });
});

router.delete('/:id', auth, admin, (req: AuthRequest, res) => {
  const show = db.prepare('SELECT id FROM showtimes WHERE id = ?').get(req.params.id);
  if (!show) return res.status(404).json({ message: 'Showtime not found' });
  db.prepare('DELETE FROM showtimes WHERE id = ?').run(req.params.id);
  res.json({ message: 'Showtime deleted successfully' });
});

export default router;
