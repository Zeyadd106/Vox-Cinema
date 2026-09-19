import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { api, apiError } from '../../services/api';

const GENRES = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Adventure', 'Romance', 'Animation', 'Documentary', 'Thriller'];
const RATINGS = ['G', 'PG', 'PG-13', 'R', 'NC-17', 'PG12', '12+', '16+', '18+', '18TC'];

export default function AdminMovieForm() {
  const { id } = useParams();
  const isEdit = Boolean(id && id !== 'new');
  const navigate = useNavigate();
  const [form, setForm] = useState({ title: '', description: '', duration: '', genre: 'Action', rating: 'PG', trailer_url: '', status: 'current', release_date: '' });
  const [poster, setPoster] = useState<File | null>(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    if (isEdit) {
      api.get(`/movies/${id}`).then(({ data }) => {
        const m = data.movie;
        setForm({ title: m.title, description: m.description, duration: m.duration, genre: m.genre, rating: m.rating, trailer_url: m.trailer_url ?? '', status: m.status, release_date: m.release_date });
      }).catch((e) => setError(apiError(e)));
    }
  }, [id, isEdit]);

  const set = (k: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [k]: e.target.value }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    setError('');
    try {
      const fd = new FormData();
      Object.entries(form).forEach(([k, v]) => fd.append(k, v));
      if (poster) fd.append('poster', poster);
      if (isEdit) await api.put(`/movies/${id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      else await api.post('/movies', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      navigate('/admin/movies');
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox [color-scheme:dark]';

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">{isEdit ? 'Edit Movie' : 'Add Movie'}</h1>
      <form onSubmit={submit} className="grid max-w-3xl gap-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        {error && <p className="rounded bg-red-950 px-3 py-2 text-sm text-red-300">{error}</p>}
        <div><label className="mb-1 block text-sm text-[#aaa]">Title</label><input required value={form.title} onChange={set('title')} className={input} /></div>
        <div><label className="mb-1 block text-sm text-[#aaa]">Description</label><textarea required rows={3} value={form.description} onChange={set('description')} className={input} /></div>
        <div className="grid grid-cols-2 gap-4">
          <div><label className="mb-1 block text-sm text-[#aaa]">Duration (e.g. 2h 15m)</label><input required value={form.duration} onChange={set('duration')} className={input} /></div>
          <div><label className="mb-1 block text-sm text-[#aaa]">Trailer URL</label><input value={form.trailer_url} onChange={set('trailer_url')} placeholder="https://..." className={input} /></div>
        </div>
        <div className="grid grid-cols-2 gap-4">
          <div><label className="mb-1 block text-sm text-[#aaa]">Genre</label>
            <select value={form.genre} onChange={set('genre')} className={input}>{GENRES.map((g) => <option key={g}>{g}</option>)}</select></div>
          <div><label className="mb-1 block text-sm text-[#aaa]">Rating</label>
            <select value={form.rating} onChange={set('rating')} className={input}>{RATINGS.map((r) => <option key={r}>{r}</option>)}</select></div>
        </div>
        <div className="grid grid-cols-2 gap-4">
          <div><label className="mb-1 block text-sm text-[#aaa]">Status</label>
            <select value={form.status} onChange={set('status')} className={input}>
              <option value="current">Current</option>
              <option value="coming_soon">Coming Soon</option>
            </select></div>
          <div><label className="mb-1 block text-sm text-[#aaa]">Release Date</label><input type="date" required value={form.release_date} onChange={set('release_date')} className={input} /></div>
        </div>
        <div><label className="mb-1 block text-sm text-[#aaa]">Poster {!isEdit && '(required, max 2MB)'}</label>
          <input type="file" accept="image/*" onChange={(e) => setPoster(e.target.files?.[0] ?? null)} className="text-sm text-[#aaa]" /></div>
        <button disabled={busy} className="rounded-md bg-vox py-3 font-semibold hover:bg-vox-dark disabled:opacity-50">
          {busy ? 'Saving...' : isEdit ? 'Update Movie' : 'Create Movie'}
        </button>
      </form>
    </div>
  );
}
