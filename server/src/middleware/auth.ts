import { Request, Response, NextFunction } from 'express';
import { verifyToken } from '../utils/auth.js';

export interface AuthRequest extends Request {
  user?: { id: number; email: string; name: string; is_admin: boolean };
}

export function auth(req: AuthRequest, res: Response, next: NextFunction) {
  const header = req.headers.authorization;
  if (!header?.startsWith('Bearer ')) {
    return res.status(401).json({ message: 'Unauthenticated' });
  }
  try {
    req.user = verifyToken(header.slice(7));
    next();
  } catch {
    return res.status(401).json({ message: 'Invalid or expired token' });
  }
}

export function admin(req: AuthRequest, res: Response, next: NextFunction) {
  if (!req.user?.is_admin) {
    return res.status(403).json({ message: 'Admin access required' });
  }
  next();
}
