import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { Cinema } from '../types';
import { api } from '../services/api';

interface CinemaCtx {
  cinemas: Cinema[];
  selectedId: number | 'all';
  setSelectedId: (id: number | 'all') => void;
  selectedName: string;
}

const Ctx = createContext<CinemaCtx | null>(null);

export function CinemaProvider({ children }: { children: ReactNode }) {
  const [cinemas, setCinemas] = useState<Cinema[]>([]);
  const [selectedId, setSelectedIdState] = useState<number | 'all'>(() => {
    const raw = localStorage.getItem('vox_cinema');
    if (!raw || raw === 'all') return 'all';
    const n = Number(raw);
    return Number.isInteger(n) ? n : 'all';
  });

  useEffect(() => {
    api.get('/cinemas').then(({ data }) => setCinemas(data.cinemas)).catch(() => undefined);
  }, []);

  const setSelectedId = (id: number | 'all') => {
    setSelectedIdState(id);
    localStorage.setItem('vox_cinema', String(id));
  };

  const selectedName = selectedId === 'all' ? 'All Cinemas' : cinemas.find((c) => c.id === selectedId)?.name ?? 'All Cinemas';

  return <Ctx.Provider value={{ cinemas, selectedId, setSelectedId, selectedName }}>{children}</Ctx.Provider>;
}

export function useCinemas(): CinemaCtx {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error('useCinemas must be used within CinemaProvider');
  return ctx;
}
