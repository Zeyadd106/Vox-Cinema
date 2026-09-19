import jwt from 'jsonwebtoken';

const SECRET = process.env.JWT_SECRET || 'vox-cinema-dev-secret-change-me';

export interface JwtUser {
  id: number;
  email: string;
  name: string;
  is_admin: boolean;
}

export function signToken(user: JwtUser): string {
  return jwt.sign(user, SECRET, { expiresIn: process.env.JWT_EXPIRES_IN || '7d' } as jwt.SignOptions);
}

export function verifyToken(token: string): JwtUser {
  return jwt.verify(token, SECRET) as JwtUser;
}

export function bookingRef(): string {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  return 'VOX' + Array.from({ length: 8 }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
}

export function publicUser(row: Record<string, unknown>) {
  return { id: row.id, name: row.name, email: row.email, is_admin: Boolean(row.is_admin), created_at: row.created_at };
}
