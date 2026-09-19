import { Link } from 'react-router-dom';
import { Movie } from '../types';

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
  return (
    <article className="flex flex-col overflow-hidden rounded-lg border border-[#333] bg-[#1a1a1a] transition duration-300 hover:-translate-y-2 hover:border-vox hover:shadow-[0_20px_40px_rgba(227,24,55,0.25)]">
      <Link to={`/movies/${movie.id}`} className="relative block aspect-[2/3] overflow-hidden">
        <Poster movie={movie} className="absolute inset-0 h-full w-full object-cover" />
        <span className="absolute left-2 top-2 rounded bg-black/70 px-2 py-0.5 text-xs font-semibold text-amber-400">{movie.rating}</span>
      </Link>
      <div className="flex flex-1 flex-col p-4 text-center">
        <h3 className="mb-1 text-lg font-semibold">
          <Link to={`/movies/${movie.id}`} className="transition hover:text-vox-light">{movie.title}</Link>
        </h3>
        <p className="mb-1 text-xs text-[#999]">{movie.genre} • {movie.duration}</p>
        <div className="mt-auto pt-2">
          {action === 'book' && (
            <Link to={`/book/${movie.id}`} className="inline-block rounded-md border-2 border-vox bg-vox px-5 py-2 font-semibold transition hover:bg-transparent hover:text-vox-light">
              Book Now
            </Link>
          )}
          {action === 'notify' && (
            <button onClick={() => onNotify?.(movie)} className="inline-block rounded-md border-2 border-vox bg-vox px-5 py-2 font-semibold transition hover:bg-transparent hover:text-vox-light">
              Notify Me
            </button>
          )}
        </div>
      </div>
    </article>
  );
}
