import { useEffect, useMemo, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Movie, Showtime, youtubeEmbed } from '../types';
import { Poster } from '../components/MovieCard';
import MovieCard from '../components/MovieCard';
import { useCinemas } from '../context/CinemaContext';

function splitStarring(description: string): { synopsis: string; starring: string | null } {
  const idx = description.indexOf('\n\nStarring:');
  if (idx === -1) return { synopsis: description, starring: null };
  return { synopsis: description.slice(0, idx).trim(), starring: description.slice(idx + '\n\nStarring:'.length).trim() };
}

function fmtDay(d: string): string {
  return new Date(d + 'T12:00:00').toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'short' });
}

export default function MovieDetail() {
  const { id } = useParams();
  const { cinemas, selectedId, setSelectedId } = useCinemas();
  const [movie, setMovie] = useState<Movie | null>(null);
  const [showtimes, setShowtimes] = useState<Showtime[]>([]);
  const [catalog, setCatalog] = useState<Movie[]>([]);
  const [activeDate, setActiveDate] = useState('');
  const [notifyMsg, setNotifyMsg] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    api.get(`/movies/${id}`)
      .then(({ data }) => {
        setMovie(data.movie);
        setShowtimes(data.showtimes);
        const dates = [...new Set((data.showtimes as Showtime[]).map((s) => s.date))] as string[];
        if (dates.length) setActiveDate(dates.sort()[0]);
      })
      .catch((e) => setError(apiError(e)));
    api.get('/movies', { params: { status: 'current' } })
      .then(({ data }) => setCatalog(data.movies))
      .catch(() => undefined);
  }, [id]);

  const visible = useMemo(
    () => (selectedId === 'all' ? showtimes : showtimes.filter((s) => s.cinema_id === selectedId)),
    [showtimes, selectedId]
  );
  const dates = useMemo(() => [...new Set(visible.map((s) => s.date))].sort().slice(0, 7), [visible]);
  const shownDate = dates.includes(activeDate) ? activeDate : (dates[0] ?? '');

  const byCinema = useMemo(() => {
    const map = new Map<string, Map<string, Showtime[]>>();
    for (const s of visible.filter((x) => x.date === shownDate)) {
      const c = s.cinema_name ?? 'Cinema';
      const f = `${s.format ?? 'Standard'}${s.hall_name ? ` — ${s.hall_name}` : ''}`;
      if (!map.has(c)) map.set(c, new Map());
      const fm = map.get(c)!;
      const arr = fm.get(f) ?? [];
      arr.push(s);
      fm.set(f, arr);
    }
    return map;
  }, [visible, shownDate]);

  const recommendations = useMemo(() => {
    if (!movie) return [];
    const others = catalog.filter((m) => m.id !== movie.id);
    const sameGenre = others.filter((m) => m.genre === movie.genre);
    const rest = others.filter((m) => m.genre !== movie.genre);
    return [...sameGenre, ...rest].slice(0, 4);
  }, [catalog, movie]);

  if (error) return <p className="p-16 text-center text-red-400">{error}</p>;
  if (!movie) return <p className="p-16 text-center text-[#888]">Loading...</p>;

  const { synopsis, starring } = splitStarring(movie.description);
  const embed = youtubeEmbed(movie.trailer_url);
  const language = movie.language ?? 'English';
  const pageUrl = window.location.href;
  const todayStr = new Date().toISOString().slice(0, 10);
  const tomorrowStr = new Date(Date.now() + 86400000).toISOString().slice(0, 10);
  const tabLabel = (d: string) => (d === todayStr ? 'Today' : d === tomorrowStr ? 'Tomorrow' : fmtDay(d));

  const notify = async () => {
    try {
      const { data } = await api.post(`/movies/${movie.id}/notify`);
      setNotifyMsg(data.message);
    } catch (e) {
      setNotifyMsg(apiError(e));
    }
  };

  return (
    <div className="mx-auto max-w-6xl px-[6%] py-10">
      {/* ── Title + classification + share ── */}
      <h1 className="text-4xl font-bold">{movie.title}</h1>
      <div className="mt-2 flex items-center gap-3">
        <span className="rounded bg-vox px-2.5 py-0.5 text-sm font-bold">{movie.rating}</span>
        <span className="text-sm text-[#888]">{movie.genre} • {movie.duration} • {language}</span>
        <span className="ms-auto flex items-center gap-2">
          <a
            href={`https://www.facebook.com/dialog/share?app_id=763253787190925&display=page&href=${encodeURIComponent(pageUrl)}`}
            target="_blank" rel="noreferrer" title={`Share "${movie.title}" on Facebook`}
            className="flex h-8 w-8 items-center justify-center rounded-full bg-[#1877f2] text-sm font-bold text-white transition hover:opacity-85"
          >f</a>
          <a
            href={`https://x.com/intent/post?url=${encodeURIComponent(pageUrl)}&text=${encodeURIComponent(movie.title)}`}
            target="_blank" rel="noreferrer" title={`Share "${movie.title}" on X`}
            className="flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-bold text-black transition hover:opacity-85"
          >𝕏</a>
        </span>
      </div>

      {/* ── Poster + trailer ── */}
      <div className="mt-6 grid gap-6 md:grid-cols-[300px_1fr]">
        <Poster movie={movie} className="aspect-[2/3] w-full rounded-lg border border-[#333] object-cover" />
        {embed ? (
          <div className="overflow-hidden rounded-lg border border-[#333] bg-black">
            <iframe src={embed} className="aspect-video h-full min-h-[280px] w-full" allowFullScreen title={`${movie.title} trailer`} />
          </div>
        ) : (
          <div className="flex flex-col justify-center rounded-lg border border-[#333] bg-gradient-to-br from-[#2a0a12] via-[#141414] to-black p-8">
            <p className="text-sm uppercase tracking-widest text-[#888]">{movie.genre}</p>
            <p className="mt-2 line-clamp-4 leading-relaxed text-[#ccc]">{synopsis}</p>
            <div className="mt-6 flex flex-wrap items-center gap-3">
              <span className="rounded bg-vox px-3 py-1 text-sm font-bold">{movie.rating}</span>
              <span className="text-sm text-[#aaa]">{movie.duration}{movie.duration !== 'TBA' ? ' runtime' : ''} • {language}</span>
              {movie.status === 'current' ? (
                <Link to={`/book/${movie.id}`} className="ms-auto rounded-md bg-vox px-8 py-2.5 font-semibold transition hover:bg-vox-dark">Book Tickets</Link>
              ) : (
                <button onClick={notify} className="ms-auto rounded-md bg-vox px-8 py-2.5 font-semibold transition hover:bg-vox-dark">Notify Me</button>
              )}
            </div>
            {notifyMsg && <p className="mt-3 text-sm text-green-400">{notifyMsg}</p>}
          </div>
        )}
      </div>

      {/* ── View showtimes + meta + synopsis ── */}
      {movie.status === 'current' ? (
        <p className="mt-8 text-center">
          <a href="#showtimes" className="inline-block rounded-full bg-vox px-10 py-3 font-bold uppercase tracking-wider transition hover:bg-vox-dark">View Showtimes</a>
        </p>
      ) : (
        <div className="mt-8 text-center">
          <p className="mb-3 text-[#aaa]">Releasing {new Date(movie.release_date + 'T12:00:00').toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' })}</p>
          {!embed && null}
          <button onClick={notify} className="inline-block rounded-full bg-vox px-10 py-3 font-bold uppercase tracking-wider transition hover:bg-vox-dark">Notify Me</button>
          {notifyMsg && <p className="mt-3 text-sm text-green-400">{notifyMsg}</p>}
        </div>
      )}

      <hr className="my-8 border-t border-dashed border-[#444]" />

      <div className="grid gap-8 md:grid-cols-[280px_1fr]">
        <aside className="space-y-2 rounded-lg border border-[#333] bg-[#141414] p-5 text-sm">
          <MetaRow label="Genre" value={movie.genre} />
          <MetaRow label="Running Time" value={movie.duration === 'TBA' ? 'To be announced' : movie.duration} />
          <MetaRow label="Release Date" value={new Date(movie.release_date + 'T12:00:00').toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' })} />
          {starring && <MetaRow label="Starring" value={starring} />}
          <MetaRow label="Language" value={language} />
          <MetaRow label="Subtitle(s)" value={language === 'English' ? 'Arabic' : '—'} />
        </aside>
        <article className="leading-relaxed text-[#ddd]">
          {synopsis.split('\n\n').map((p, i) => <p key={i} className="mb-4">{p}</p>)}
        </article>
      </div>

      {/* ── Showtimes ── */}
      {movie.status === 'current' && (
        <section id="showtimes" className="scroll-mt-48">
          <hr className="my-8 border-t border-dashed border-[#444]" />
          <h2 className="mb-1 text-2xl font-bold text-vox">{movie.title} - Showtimes</h2>
          <div className="mb-4 flex flex-wrap items-center gap-3">
            <p className="text-sm text-[#888]">Cinema:</p>
            <select
              value={selectedId}
              onChange={(e) => setSelectedId(e.target.value === 'all' ? 'all' : Number(e.target.value))}
              className="cursor-pointer rounded-md border border-[#444] bg-[#1a1a1a] px-3 py-1.5 text-sm outline-none focus:border-vox"
            >
              <option value="all">All Cinemas</option>
              {cinemas.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
            </select>
          </div>
          {dates.length === 0 && <p className="text-[#888]">No upcoming showtimes{selectedId === 'all' ? '.' : ' at this cinema.'}</p>}
          {dates.length > 0 && (
            <nav aria-label="Choose date">
              <ol className="flex gap-2 overflow-x-auto pb-1">
                {dates.map((d) => (
                  <li key={d}>
                    <button
                      onClick={() => setActiveDate(d)}
                      className={`whitespace-nowrap rounded-md border px-5 py-2.5 text-sm font-semibold transition ${d === shownDate ? 'border-vox bg-vox' : 'border-[#444] bg-[#1a1a1a] hover:border-vox'}`}
                    >
                      {tabLabel(d)}
                    </button>
                  </li>
                ))}
              </ol>
            </nav>
          )}
          <div className="mt-6 space-y-8">
            {[...byCinema.entries()].map(([cinema, formats]) => (
              <div key={cinema}>
                <h3 className="mb-3 text-lg font-bold text-vox-light">{cinema}</h3>
                <ol className="space-y-3">
                  {[...formats.entries()].map(([format, times]) => (
                    <li key={format} className="rounded-lg border border-[#2a2a2a] bg-[#141414] px-4 py-3">
                      <strong className="mb-2 block text-sm uppercase tracking-wider text-[#aaa]">{format}</strong>
                      <ol className="flex flex-wrap gap-2">
                        {times.map((s) => (
                          <li key={s.id}>
                            <Link
                              to={`/book/${movie.id}?showtime=${s.id}`}
                              className="inline-block rounded-md border border-[#444] bg-[#1a1a1a] px-5 py-2 font-semibold transition hover:border-vox hover:bg-vox"
                            >
                              {new Date(`2000-01-01T${s.time}`).toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true }).toLowerCase().replace(' ', '')}
                            </Link>
                          </li>
                        ))}
                      </ol>
                    </li>
                  ))}
                </ol>
              </div>
            ))}
          </div>
        </section>
      )}

      {/* ── Recommendations ── */}
      {recommendations.length > 0 && (
        <section className="mt-12">
          <hr className="my-8 border-t border-dashed border-[#444]" />
          <h2 className="mb-6 text-center text-2xl font-bold">Some other movies you might like</h2>
          <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {recommendations.map((m) => <MovieCard key={m.id} movie={m} action={m.status === 'current' ? 'book' : 'none'} />)}
          </div>
        </section>
      )}

      <p className="mt-12 flex flex-wrap justify-center gap-3">
        <Link to="/movies" className="rounded-full border-2 border-vox px-8 py-2.5 font-semibold transition hover:bg-vox">Now Showing</Link>
        <Link to="/coming-soon" className="rounded-full border-2 border-vox px-8 py-2.5 font-semibold transition hover:bg-vox">Coming Soon</Link>
      </p>
    </div>
  );
}

function MetaRow({ label, value }: { label: string; value: string }) {
  return (
    <p><strong className="text-white">{label}:</strong> <span className="text-[#bbb]">{value}</span></p>
  );
}
