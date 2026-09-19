import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Movie, Showtime } from '../types';
import { useCinemas } from '../context/CinemaContext';

function today(): string {
  return new Date().toISOString().slice(0, 10);
}

export default function FindTimes() {
  const { cinemas, selectedId } = useCinemas();
  const [movies, setMovies] = useState<Movie[]>([]);
  const [cinema, setCinema] = useState<string>(String(selectedId));
  const [movie, setMovie] = useState<string>('any');
  const [date, setDate] = useState<string>(today());
  const [results, setResults] = useState<Showtime[] | null>(null);
  const [movieMap, setMovieMap] = useState<Record<number, Movie>>({});
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  useEffect(() => { setCinema(String(selectedId)); }, [selectedId]);

  useEffect(() => {
    api.get('/movies', { params: { status: 'current' } })
      .then(({ data }) => {
        setMovies(data.movies);
        const map: Record<number, Movie> = {};
        for (const m of data.movies as Movie[]) map[m.id] = m;
        setMovieMap(map);
      })
      .catch(() => undefined);
  }, []);

  const search = async (e?: React.FormEvent) => {
    e?.preventDefault();
    setBusy(true);
    setError('');
    try {
      const params: Record<string, string> = { date };
      if (cinema !== 'all') params.cinema_id = cinema;
      if (movie !== 'any') params.movie_id = movie;
      const { data } = await api.get('/showtimes', { params });
      setResults(data.showtimes);
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const grouped = new Map<number, Showtime[]>();
  for (const s of results ?? []) {
    const arr = grouped.get(s.movie_id) ?? [];
    arr.push(s);
    grouped.set(s.movie_id, arr);
  }

  const select = 'rounded-md border border-[#444] bg-black px-3 py-2.5 text-sm outline-none focus:border-vox [color-scheme:dark]';

  return (
    <div className="mx-auto max-w-6xl rounded-xl border border-[#333] bg-[#141414] p-6">
      <h2 className="mb-4 text-center text-xl font-bold uppercase tracking-widest text-vox">Find Times and Book</h2>
      <form onSubmit={search} className="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto]">
        <label className="text-sm">
          <span className="mb-1 block text-xs text-[#999]">Cinema</span>
          <select value={cinema} onChange={(e) => setCinema(e.target.value)} className={`${select} w-full`}>
            <option value="all">All Cinemas</option>
            {cinemas.map((c) => <option key={c.id} value={c.id}>{c.name} — {c.city}</option>)}
          </select>
        </label>
        <label className="text-sm">
          <span className="mb-1 block text-xs text-[#999]">Movie</span>
          <select value={movie} onChange={(e) => setMovie(e.target.value)} className={`${select} w-full`}>
            <option value="any">Any Movie</option>
            {movies.map((m) => <option key={m.id} value={m.id}>{m.title}</option>)}
          </select>
        </label>
        <label className="text-sm">
          <span className="mb-1 block text-xs text-[#999]">Date</span>
          <input type="date" value={date} min={today()} onChange={(e) => setDate(e.target.value)} className={`${select} w-full`} />
        </label>
        <button disabled={busy} className="self-end rounded-md bg-vox px-8 py-2.5 font-semibold transition hover:bg-vox-dark disabled:opacity-50">
          {busy ? '...' : 'Search'}
        </button>
      </form>
      {error && <p className="mt-3 text-center text-sm text-red-400">{error}</p>}
      {results !== null && (
        <div className="mt-6 space-y-5">
          {results.length === 0 && <p className="text-center text-sm text-[#888]">No showtimes match. Try another cinema, movie or date.</p>}
          {[...grouped.entries()].map(([mid, times]) => (
            <div key={mid} className="rounded-lg border border-[#2a2a2a] bg-black/40 p-4">
              <Link to={`/movies/${mid}`} className="font-semibold hover:text-vox-light">
                {times[0].movie_title ?? movieMap[mid]?.title ?? `Movie #${mid}`}
              </Link>
              <div className="mt-2 flex flex-wrap gap-2">
                {times.map((s) => (
                  <Link
                    key={s.id}
                    to={`/book/${mid}?showtime=${s.id}`}
                    title={`${s.cinema_name} • ${s.hall_name} (${s.format})`}
                    className="rounded-md border border-[#444] bg-[#1a1a1a] px-4 py-1.5 text-sm font-semibold transition hover:border-vox hover:bg-vox"
                  >
                    {s.time.slice(0, 5)}
                    <span className="ml-1.5 text-[11px] font-normal text-[#aaa]">{s.format !== 'Standard' ? `${s.format} • ` : ''}{s.cinema_name}</span>
                  </Link>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
