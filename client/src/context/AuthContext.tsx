import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { User, RegisterData } from '../types';
import { api } from '../services/api';

interface AuthCtx {
  user: User | null;
  token: string | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<User>;
  register: (data: RegisterData) => Promise<{ user: User; token: string }>;
  logout: () => void;
  refresh: () => Promise<void>;
}

const Ctx = createContext<AuthCtx | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(() => {
    try {
      const raw = localStorage.getItem('vox_user');
      return raw ? (JSON.parse(raw) as User) : null;
    } catch {
      return null;
    }
  });
  const [token, setToken] = useState<string | null>(() => localStorage.getItem('vox_token'));
  const [loading, setLoading] = useState(true);

  const refresh = async () => {
    if (!localStorage.getItem('vox_token')) {
      setLoading(false);
      return;
    }
    try {
      const { data } = await api.get('/auth/me');
      setUser(data.user);
      localStorage.setItem('vox_user', JSON.stringify(data.user));
    } catch {
      setUser(null);
      setToken(null);
      localStorage.removeItem('vox_token');
      localStorage.removeItem('vox_user');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    refresh();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const login = async (email: string, password: string) => {
    const { data } = await api.post('/auth/login', { email, password });
    setUser(data.user);
    setToken(data.token);
    localStorage.setItem('vox_token', data.token);
    localStorage.setItem('vox_user', JSON.stringify(data.user));
    return data.user as User;
  };

  const register = async (data: RegisterData) => {
    const res = await api.post('/auth/register', data);
    // Auto-login after signup like the major cinema apps
    setUser(res.data.user);
    setToken(res.data.token);
    localStorage.setItem('vox_token', res.data.token);
    localStorage.setItem('vox_user', JSON.stringify(res.data.user));
    return res.data as { user: User; token: string };
  };

  const logout = () => {
    setUser(null);
    setToken(null);
    localStorage.removeItem('vox_token');
    localStorage.removeItem('vox_user');
  };

  return <Ctx.Provider value={{ user, token, loading, login, register, logout, refresh }}>{children}</Ctx.Provider>;
}

export function useAuth(): AuthCtx {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
