import { Router } from 'express';
import { db } from '../config/db.js';
import { auth, admin, AuthRequest } from '../middleware/auth.js';

const router = Router();
export const FORMATS = ['Standard', 'IMAX', 'MAX', 'GOLD', '4DX', 'KIDS'];

function cinemaImage(p: string | null): string | null {
  if (!p) return null;
  if (/^https?:\/\//.test(p)) return p;
  return `/uploads/cinemas/${p}`;
}

/** Public: list active cinemas */
router.get('/', (_req, res) => {
  const rows = db.prepare(
    `SELECT c.*, (SELECT COUNT(*) FROM halls h WHERE h.cinema_id = c.id) AS hall_count,
            (SELECT COUNT(*) FROM showtimes s JOIN halls h ON h.id = s.hall_id WHERE h.cinema_id = c.id AND s.date >= date('now')) AS upcoming_count
     FROM cinemas c WHERE c.is_active = 1 ORDER BY c.name`
  ).all() as Record<string, unknown>[];
  res.json({ cinemas: rows.map((c) => ({ ...c, image_url: cinemaImage(c.image_path as string) })) });
});

/** Public: halls (optionally filtered by cinema) with seat counts */
router.get('/halls', (req, res) => {
  const { cinema_id } = req.query as { cinema_id?: string };
  const rows = (
    cinema_id
      ? db.prepare('SELECT h.*, c.name AS cinema_name, (SELECT COUNT(*) FROM seats s WHERE s.hall_id = h.id) AS seat_count FROM halls h JOIN cinemas c ON c.id = h.cinema_id WHERE h.cinema_id = ? ORDER BY h.name').all(cinema_id)
      : db.prepare('SELECT h.*, c.name AS cinema_name, (SELECT COUNT(*) FROM seats s WHERE s.hall_id = h.id) AS seat_count FROM halls h JOIN cinemas c ON c.id = h.cinema_id ORDER BY c.name, h.name').all()
  ) as Record<string, unknown>[];
  res.json({ halls: rows });
});

/** Public: cinema detail with halls + now-playing movies + upcoming showtimes */
router.get('/:id', (req, res) => {
  const cinema = db.prepare('SELECT * FROM cinemas WHERE id = ? AND is_active = 1').get(req.params.id) as Record<string, unknown> | null;
  if (!cinema) return res.status(404).json({ message: 'Cinema not found' });
  const halls = db.prepare(
    'SELECT h.*, (SELECT COUNT(*) FROM seats s WHERE s.hall_id = h.id) AS seat_count FROM halls h WHERE h.cinema_id = ? ORDER BY h.name'
  ).all(req.params.id);
  const showtimes = db.prepare(
    `SELECT s.*, m.title AS movie_title, m.poster_path, h.name AS hall_name, h.format
     FROM showtimes s JOIN movies m ON m.id = s.movie_id JOIN halls h ON h.id = s.hall_id
     WHERE h.cinema_id = ? AND s.date >= date('now') ORDER BY s.date, s.time`
  ).all(req.params.id) as Record<string, unknown>[];
  const movies = db.prepare(
    `SELECT DISTINCT m.* FROM movies m JOIN showtimes s ON s.movie_id = m.id JOIN halls h ON h.id = s.hall_id
     WHERE h.cinema_id = ? AND s.date >= date('now') ORDER BY m.title`
  ).all(req.params.id) as Record<string, unknown>[];
  const poster = (p: unknown) => (!p ? null : /^https?:\/\//.test(String(p)) ? p : `/uploads/posters/${p}`);
  res.json({
    cinema: { ...cinema, image_url: cinemaImage(cinema.image_path as string) },
    halls,
    movies: movies.map((m) => ({ ...m, poster_url: poster(m.poster_path) })),
    showtimes: showtimes.map((s) => ({ ...s })),
  });
});

router.post('/', auth, admin, (req: AuthRequest, res) => {
  const { name, city, address } = req.body as Record<string, unknown>;
  const errors: Record<string, string[]> = {};
  if (!String(name ?? '').trim()) errors.name = ['Name is required'];
  if (!String(city ?? '').trim()) errors.city = ['City is required'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  const r = db.prepare('INSERT INTO cinemas (name, city, address) VALUES (?, ?, ?)').run(
    String(name).trim(), String(city).trim(), String(address ?? '').trim()
  );
  res.status(201).json({ cinema: db.prepare('SELECT * FROM cinemas WHERE id = ?').get(Number(r.lastInsertRowid)), message: 'Cinema created' });
});

router.put('/:id', auth, admin, (req: AuthRequest, res) => {
  const cinema = db.prepare('SELECT id FROM cinemas WHERE id = ?').get(req.params.id);
  if (!cinema) return res.status(404).json({ message: 'Cinema not found' });
  const { name, city, address, is_active } = req.body as Record<string, unknown>;
  if (!String(name ?? '').trim()) return res.status(422).json({ errors: { name: ['Name is required'] } });
  db.prepare("UPDATE cinemas SET name=?, city=?, address=?, is_active=?, updated_at=datetime('now') WHERE id=?").run(
    String(name).trim(), String(city ?? '').trim(), String(address ?? '').trim(), is_active === false || is_active === 0 ? 0 : 1, req.params.id
  );
  res.json({ cinema: db.prepare('SELECT * FROM cinemas WHERE id = ?').get(req.params.id), message: 'Cinema updated' });
});

router.delete('/:id', auth, admin, (req: AuthRequest, res) => {
  const cinema = db.prepare('SELECT id FROM cinemas WHERE id = ?').get(req.params.id);
  if (!cinema) return res.status(404).json({ message: 'Cinema not found' });
  const shows = (db.prepare('SELECT COUNT(*) c FROM showtimes s JOIN halls h ON h.id = s.hall_id WHERE h.cinema_id = ?').get(req.params.id) as { c: number }).c;
  if (Number(shows) > 0) return res.status(409).json({ message: 'Cannot delete a cinema with scheduled showtimes' });
  db.prepare('DELETE FROM halls WHERE cinema_id = ?').run(req.params.id);
  db.prepare('DELETE FROM cinemas WHERE id = ?').run(req.params.id);
  res.json({ message: 'Cinema deleted' });
});

/** Admin: create a hall and auto-generate its seat layout */
router.post('/halls', auth, admin, (req: AuthRequest, res) => {
  const { cinema_id, name, format, row_labels, seats_per_row } = req.body as Record<string, unknown>;
  const errors: Record<string, string[]> = {};
  if (!cinema_id || !db.prepare('SELECT id FROM cinemas WHERE id = ?').get(Number(cinema_id))) errors.cinema_id = ['Valid cinema is required'];
  if (!String(name ?? '').trim()) errors.name = ['Hall name is required'];
  if (!FORMATS.includes(String(format ?? 'Standard'))) errors.format = [`Format must be one of: ${FORMATS.join(', ')}`];
  const rows = String(row_labels ?? 'A,B,C,D,E,F,G').split(',').map((r) => r.trim().toUpperCase()).filter(Boolean);
  const perRow = Number(seats_per_row ?? 10);
  if (!rows.length || rows.length > 26) errors.row_labels = ['Provide 1-26 row labels, e.g. A,B,C,D,E,F,G'];
  if (!Number.isInteger(perRow) || perRow < 1 || perRow > 30) errors.seats_per_row = ['Seats per row must be 1-30'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });
  const h = db.prepare('INSERT INTO halls (cinema_id, name, format) VALUES (?, ?, ?)').run(Number(cinema_id), String(name).trim(), String(format ?? 'Standard'));
  const hid = Number(h.lastInsertRowid);
  const ins = db.prepare('INSERT INTO seats (row, number, hall_id) VALUES (?, ?, ?)');
  for (const row of rows) for (let n = 1; n <= perRow; n++) ins.run(row, n, hid);
  res.status(201).json({ hall: db.prepare('SELECT * FROM halls WHERE id = ?').get(hid), message: `Hall created with ${rows.length * perRow} seats` });
});

router.put('/halls/:id', auth, admin, (req: AuthRequest, res) => {
  const hall = db.prepare('SELECT id FROM halls WHERE id = ?').get(req.params.id);
  if (!hall) return res.status(404).json({ message: 'Hall not found' });
  const { name, format } = req.body as Record<string, unknown>;
  if (!String(name ?? '').trim()) return res.status(422).json({ errors: { name: ['Hall name is required'] } });
  if (!FORMATS.includes(String(format ?? 'Standard'))) return res.status(422).json({ errors: { format: ['Invalid format'] } });
  db.prepare("UPDATE halls SET name=?, format=?, updated_at=datetime('now') WHERE id=?").run(String(name).trim(), String(format), req.params.id);
  res.json({ hall: db.prepare('SELECT * FROM halls WHERE id = ?').get(req.params.id), message: 'Hall updated' });
});

router.delete('/halls/:id', auth, admin, (req: AuthRequest, res) => {
  const hall = db.prepare('SELECT id FROM halls WHERE id = ?').get(req.params.id);
  if (!hall) return res.status(404).json({ message: 'Hall not found' });
  const shows = (db.prepare('SELECT COUNT(*) c FROM showtimes WHERE hall_id = ?').get(req.params.id) as { c: number }).c;
  if (Number(shows) > 0) return res.status(409).json({ message: 'Cannot delete a hall with scheduled showtimes' });
  db.prepare('DELETE FROM seats WHERE hall_id = ?').run(req.params.id);
  db.prepare('DELETE FROM halls WHERE id = ?').run(req.params.id);
  res.json({ message: 'Hall deleted' });
});

export default router;
