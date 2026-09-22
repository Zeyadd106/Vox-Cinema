import { Router } from 'express';
import bcrypt from 'bcryptjs';
import crypto from 'node:crypto';
import { db } from '../config/db.js';
import { signToken, publicUser } from '../utils/auth.js';
import { auth, AuthRequest } from '../middleware/auth.js';

const router = Router();

router.post('/register', (req, res) => {
  const { name, first_name, last_name, email, password, phone, birth_date, gender, preferred_cinema_id } = req.body as Record<string, unknown>;
  const fullName = String(first_name ?? name ?? '').trim() + (first_name ? ` ${String(last_name ?? '').trim()}` : '');
  const errors: Record<string, string[]> = {};
  if (fullName.trim().length < 3) errors.name = ['Please enter your full name (min 3 characters)'];
  if (!email || !/^\S+@\S+\.\S+$/.test(String(email))) errors.email = ['A valid email is required'];
  if (!password || String(password).length < 8) errors.password = ['Password must be at least 8 characters'];
  if (phone === undefined || phone === '' || !/^\+?\d{8,15}$/.test(String(phone).replace(/[\s-]/g, ''))) {
    errors.phone = ['Enter a valid mobile number'];
  }
  if (birth_date !== undefined && birth_date !== '') {
    const d = new Date(String(birth_date));
    if (isNaN(d.getTime()) || d > new Date()) errors.birth_date = ['Enter a valid date of birth'];
  }
  if (gender !== undefined && gender !== '' && !['male', 'female', 'other'].includes(String(gender))) {
    errors.gender = ['Select a valid option'];
  }
  if (preferred_cinema_id !== undefined && preferred_cinema_id !== '' && preferred_cinema_id !== null) {
    if (!db.prepare('SELECT id FROM cinemas WHERE id = ?').get(Number(preferred_cinema_id))) {
      errors.preferred_cinema_id = ['Selected cinema is invalid'];
    }
  }
  if (Object.keys(errors).length) return res.status(422).json({ errors });

  const emailNorm = String(email).toLowerCase();
  const exists = db.prepare('SELECT id FROM users WHERE email = ?').get(emailNorm);
  if (exists) return res.status(422).json({ errors: { email: ['Email is already taken'] } });

  const hash = bcrypt.hashSync(String(password), 10);
  const r = db.prepare(
    'INSERT INTO users (name, email, password, is_admin, phone, birth_date, gender, preferred_cinema_id) VALUES (?, ?, ?, 0, ?, ?, ?, ?)'
  ).run(
    fullName.trim(),
    emailNorm,
    hash,
    phone ? String(phone).trim() : null,
    birth_date ? String(birth_date) : null,
    gender ? String(gender) : '',
    preferred_cinema_id ? Number(preferred_cinema_id) : null
  );
  const row = db.prepare('SELECT * FROM users WHERE id = ?').get(Number(r.lastInsertRowid)) as Record<string, unknown>;
  const user = publicUser(row);
  return res.status(201).json({ user, token: signToken(user as never), message: 'Account created. Welcome to REX Rewards!' });
});

router.post('/login', (req, res) => {
  const { email, password } = req.body as { email?: string; password?: string };
  const identifier = String(email ?? '').trim();
  if (!identifier || !password) return res.status(422).json({ errors: { email: ['Email or phone number and password are required'] } });

  // Email or phone number (phones stored with optional +/spaces/dashes — compare normalized;
  // Egyptian mobiles typed as 01xxxxxxxxx match stored +201xxxxxxxxx and vice versa)
  const normPhone = (p: string) => {
    const d = p.replace(/[\s-]/g, '');
    const out = new Set([d, d.replace(/^\+/, '')]);
    if (/^\+20\d{10}$/.test(d)) out.add('0' + d.slice(3));
    if (/^01\d{9}$/.test(d)) out.add('+20' + d.slice(1));
    return [...out];
  };
  let row: Record<string, unknown> | null = null;
  if (/^\+?[\d\s-]{8,17}$/.test(identifier)) {
    const variants = normPhone(identifier);
    row = db.prepare(
      `SELECT * FROM users WHERE ${variants.map(() => "REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?").join(' OR ')}`
    ).get(...variants) as Record<string, unknown> | null;
    if (!row) {
      row = db.prepare('SELECT * FROM users WHERE email = ?').get(identifier.toLowerCase()) as Record<string, unknown> | null;
    }
  } else {
    row = db.prepare('SELECT * FROM users WHERE email = ?').get(identifier.toLowerCase()) as Record<string, unknown> | null;
  }
  if (!row || !bcrypt.compareSync(String(password), row.password as string)) {
    return res.status(401).json({ message: 'Invalid login credentials' });
  }
  const user = publicUser(row);
  return res.json({ user, token: signToken(user as never), message: 'Login successful' });
});

router.get('/me', auth, (req: AuthRequest, res) => {
  const row = db.prepare('SELECT * FROM users WHERE id = ?').get(req.user!.id) as Record<string, unknown> | null;
  if (!row) return res.status(401).json({ message: 'Unauthenticated' });
  return res.json({ user: publicUser(row) });
});

/** Request a password reset link (token emailed in production; logged in dev). */
router.post('/forgot', (req, res) => {
  const email = String(req.body?.email ?? '').trim().toLowerCase();
  const done = { message: 'If an account exists for this email, a reset link has been sent.' };
  if (!email || !/^\S+@\S+\.\S+$/.test(email)) return res.json(done);
  const row = db.prepare('SELECT id FROM users WHERE email = ?').get(email) as { id: number } | null;
  if (!row) return res.json(done);
  const token = crypto.randomBytes(32).toString('hex');
  db.prepare('DELETE FROM password_resets WHERE email = ?').run(email);
  db.prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, datetime('now', '+1 hour'))").run(email, token);
  const link = `${process.env.CLIENT_URL ?? 'http://localhost:5173'}/reset/${token}`;
  if (process.env.NODE_ENV !== 'production') {
    console.log(`[auth] password reset for ${email}: ${link}`);
  }
  // TODO: send `link` via email/SMS provider in production
  return res.json(done);
});

router.get('/reset/:token', (req, res) => {
  const row = db.prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > datetime('now')").get(req.params.token);
  if (!row) return res.status(404).json({ message: 'This reset link is invalid or has expired.' });
  return res.json({ valid: true });
});

router.post('/reset', (req, res) => {
  const { token, password } = req.body as { token?: string; password?: string };
  if (!token) return res.status(422).json({ errors: { token: ['Reset token is required'] } });
  if (!password || String(password).length < 8) {
    return res.status(422).json({ errors: { password: ['Password must be at least 8 characters'] } });
  }
  const row = db.prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > datetime('now')").get(String(token)) as { email: string } | null;
  if (!row) return res.status(422).json({ errors: { token: ['This reset link is invalid or has expired'] } });
  db.prepare('UPDATE users SET password = ?, updated_at = datetime(\'now\') WHERE email = ?').run(bcrypt.hashSync(String(password), 10), row.email);
  db.prepare('DELETE FROM password_resets WHERE email = ?').run(row.email);
  return res.json({ message: 'Password updated. You can now log in.' });
});

export default router;
