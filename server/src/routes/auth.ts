import { Router } from 'express';
import bcrypt from 'bcryptjs';
import { db } from '../config/db.js';
import { signToken, publicUser } from '../utils/auth.js';
import { auth, AuthRequest } from '../middleware/auth.js';

const router = Router();

router.post('/register', (req, res) => {
  const { name, email, password } = req.body as { name?: string; email?: string; password?: string };
  const errors: Record<string, string[]> = {};
  if (!name || name.trim().length < 3) errors.name = ['Name must be at least 3 characters'];
  if (!email || !/^\S+@\S+\.\S+$/.test(email)) errors.email = ['A valid email is required'];
  if (!password || password.length < 8) errors.password = ['Password must be at least 8 characters'];
  if (Object.keys(errors).length) return res.status(422).json({ errors });

  const exists = db.prepare('SELECT id FROM users WHERE email = ?').get(email!.toLowerCase());
  if (exists) return res.status(422).json({ errors: { email: ['Email is already taken'] } });

  const hash = bcrypt.hashSync(password!, 10);
  const r = db.prepare('INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)').run(name!.trim(), email!.toLowerCase(), hash);
  const row = db.prepare('SELECT * FROM users WHERE id = ?').get(Number(r.lastInsertRowid)) as Record<string, unknown>;
  const user = publicUser(row);
  return res.status(201).json({ user, token: signToken(user as never), message: 'Registered successfully' });
});

router.post('/login', (req, res) => {
  const { email, password } = req.body as { email?: string; password?: string };
  if (!email || !password) return res.status(422).json({ errors: { email: ['Email and password are required'] } });
  const row = db.prepare('SELECT * FROM users WHERE email = ?').get(email.toLowerCase()) as Record<string, unknown> | null;
  if (!row || !bcrypt.compareSync(password, row.password as string)) {
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

export default router;
