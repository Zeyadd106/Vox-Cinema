import { useEffect, useState } from 'react';
import { api, apiError } from '../../services/api';
import { Hall, Movie, Showtime } from '../../types';
import { useLang } from '../../context/LangContext';

export default function AdminShowtimes() {
  const { t } = useLang();
  const [showtimes, setShowtimes] = useState<Showtime[]>([]);
  const [movies, setMovies] = useState<Movie[]>([]);
  const [halls, setHalls] = useState<Hall[]>([]);
  const [form, setForm] = useState({ movie_id: '', hall_id: '', date: '', time: '' });
  const [msg, setMsg] = useState('');
  const [error, setError] = useState('');

  const load = () => {
    api.get('/showtimes').then(({ data }) => setShowtimes(data.showtimes)).catch((e) => setError(apiError(e)));
    api.get('/movies').then(({ data }) => setMovies(data.movies)).catch(() => undefined);
    api.get('/cinemas/halls').then(({ data }) => setHalls(data.halls)).catch(() => undefined);
  };
  useEffect(load, []);

  const create = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/showtimes', { movie_id: Number(form.movie_id), hall_id: Number(form.hall_id), date: form.date, time: form.time });
      setMsg(t.admin.showCreated);
      setForm({ movie_id: '', hall_id: '', date: '', time: '' });
      load();
    } catch (err) {
      setMsg(apiError(err));
    }
  };

  const remove = async (id: number) => {
    if (!confirm(t.admin.deleteShowConfirm)) return;
    try {
      await api.delete(`/showtimes/${id}`);
      setMsg(t.admin.showDeleted);
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  const input = 'rounded-md border border-[#444] bg-black px-3 py-2 outline-none focus:border-vox [color-scheme:dark]';

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">{t.admin.showtimes}</h1>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#222] px-4 py-2 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}
      <form onSubmit={create} className="mb-8 flex flex-wrap items-end gap-3 rounded-lg border border-[#333] bg-[#1a1a1a] p-5">
        <div><label className="mb-1 block text-xs text-[#aaa]">{t.admin.movie}</label>
          <select required value={form.movie_id} onChange={(e) => setForm({ ...form, movie_id: e.target.value })} className={input}>
            <option value="">{t.admin.movieForm.selectMovie}</option>
            {movies.map((m) => <option key={m.id} value={m.id}>{m.title}</option>)}
          </select></div>
        <div><label className="mb-1 block text-xs text-[#aaa]">{t.admin.hall}</label>
          <select required value={form.hall_id} onChange={(e) => setForm({ ...form, hall_id: e.target.value })} className={input}>
            <option value="">{t.admin.movieForm.selectHall}</option>
            {halls.map((h) => <option key={h.id} value={h.id}>{h.cinema_name} — {h.name} ({h.format})</option>)}
          </select></div>
        <div><label className="mb-1 block text-xs text-[#aaa]">{t.admin.date}</label>
          <input type="date" required value={form.date} onChange={(e) => setForm({ ...form, date: e.target.value })} className={input} /></div>
        <div><label className="mb-1 block text-xs text-[#aaa]">{t.admin.time}</label>
          <input type="time" required value={form.time} onChange={(e) => setForm({ ...form, time: e.target.value })} className={input} /></div>
        <button className="rounded-md bg-vox px-6 py-2 font-semibold hover:bg-vox-dark">{t.admin.add}</button>
      </form>
      <div className="overflow-x-auto rounded-lg border border-[#333]">
        <table className="w-full text-start text-sm">
          <thead className="bg-[#222] text-[#aaa]">
            <tr><th className="px-4 py-2.5">{t.admin.movie}</th><th className="px-4 py-2.5">{t.admin.cinema}</th><th className="px-4 py-2.5">{t.admin.hall}</th><th className="px-4 py-2.5">{t.admin.date}</th><th className="px-4 py-2.5">{t.admin.time}</th><th className="px-4 py-2.5">{t.common.actions}</th></tr>
          </thead>
          <tbody>
            {showtimes.map((s) => (
              <tr key={s.id} className="border-t border-[#2a2a2a]">
                <td className="px-4 py-2.5">{s.movie_title}</td>
                <td className="px-4 py-2.5">{s.cinema_name ?? '—'}</td>
                <td className="px-4 py-2.5">{s.hall_name ?? '—'} <span className="text-xs text-[#888]">({s.format})</span></td>
                <td className="px-4 py-2.5">{s.date}</td>
                <td className="px-4 py-2.5">{s.time.slice(0, 5)}</td>
                <td className="px-4 py-2.5"><button onClick={() => remove(s.id)} className="text-red-400 hover:underline">{t.common.delete}</button></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

