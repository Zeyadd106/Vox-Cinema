import { Link } from 'react-router-dom';
import { Movie } from '../types';
import { useLang } from '../context/LangContext';

export function Poster({ movie, className }: { movie: Pick<Movie, 'poster_url' | 'title'>; className?: string }) {
  if (movie.poster_url) {
    return <img src={movie.poster_url} alt={movie.title} className={className} loading="lazy" />;
  }
  const initial = movie.title.charAt(0).toUpperCase();
  return (
    <div className={`flex items-center justify-center bg-gradient-to-br from-[#3a0a14] via-[#1a1a1a] to-black ${className ?? ''}`}>
      <span className="text-7xl font-bold text-vox/70">{initial}</span>
    </div>
  );
}

export default function MovieCard({ movie, action, onNotify }: { movie: Movie; action?: 'book' | 'notify' | 'none'; onNotify?: (m: Movie) => void }) {
  const { t } = useLang();
  return (
    <article className="group flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-2 hover:border-vox-pink hover:shadow-[0_20px_40px_rgba(212,15,125,0.18)]">
      <div className="relative aspect-[2/3] overflow-hidden">
        <Link to={`/movies/${movie.id}`} className="absolute inset-0" aria-label={movie.title}>
          <Poster movie={movie} className="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
        </Link>
        <span className="absolute start-2 top-2 z-10 rounded bg-black/70 px-2 py-0.5 text-xs font-semibold text-amber-400">{movie.rating}</span>
        {/* Hover overlay like voxcinemas.com poster wall */}
        <div className="absolute inset-0 z-10 flex flex-col justify-end bg-gradient-to-t from-black via-black/80 to-transparent p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-within:opacity-100">
          <h3 className="text-lg font-bold">{movie.title}</h3>
          <p className="mt-0.5 text-xs text-[#bbb]">{movie.genre} • {movie.duration}</p>
          {movie.description && (
            <p className="mt-2 line-clamp-4 text-[13px] leading-relaxed text-[#ddd]">{movie.description.split('\n\n')[0]}</p>
          )}
          <div className="mt-3 flex gap-2">
            <Link to={`/movies/${movie.id}`} className="flex-1 rounded-full border-2 border-white/80 px-3 py-1.5 text-center text-sm font-semibold transition hover:bg-white hover:text-black">
              {t.card.view}
            </Link>
            {action === 'book' && (
              <Link to={`/book/${movie.id}`} className="flex-1 rounded-full bg-vox-pink px-3 py-1.5 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-vox-pink-dark">
                {t.card.book}
              </Link>
            )}
            {action === 'notify' && (
              <button onClick={() => onNotify?.(movie)} className="flex-1 rounded-full bg-vox-pink px-3 py-1.5 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-vox-pink-dark">
                {t.card.notifyMe}
              </button>
            )}
          </div>
        </div>
      </div>
      <div className="flex items-center justify-between gap-2 p-3">
        <h3 className="truncate text-[15px] font-semibold text-slate-900">
          <Link to={`/movies/${movie.id}`} className="transition hover:text-vox-pink">{movie.title}</Link>
        </h3>
        {action === 'book' ? (
          <Link to={`/book/${movie.id}`} className="shrink-0 rounded-full bg-vox-pink px-4 py-1 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-vox-pink-dark">
            {t.card.book}
          </Link>
        ) : action === 'notify' ? (
          <button onClick={() => onNotify?.(movie)} className="shrink-0 rounded-full bg-vox-pink px-4 py-1 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-vox-pink-dark">
            {t.card.notify}
          </button>
        ) : (
          <Link to={`/movies/${movie.id}`} className="shrink-0 text-xs font-semibold text-vox-pink hover:text-vox-pink-dark">{t.card.view}</Link>
        )}
      </div>
    </article>
  );
}
