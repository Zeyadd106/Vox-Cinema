import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Movie } from '../types';
import MovieCard from '../components/MovieCard';

export default function Movies() {
  const [params] = useSearchParams();
  const initialQ = params.get('q') ?? '';
  const [movies, setMovies] = useState<Movie[]>([]);
  const [query, setQuery] = useState(initialQ);
  const [error, setError] = useState('');
  useEffect(() => {
    api.get('/movies', { params: { status: 'current' } })
      .then(({ data }) => setMovies(data.movies))
      .catch((e) => setError(apiError(e)));
  }, []);
  useEffect(() => { setQuery(params.get('q') ?? ''); }, [params]);
  const filtered = movies.filter((m) => m.title.toLowerCase().includes(query.toLowerCase()));
  return (
    <div className="mx-auto max-w-7xl px-[6%] py-12">
      <h1 className="mb-2 text-center text-3xl font-bold uppercase tracking-widest text-vox">What's On</h1>
      {initialQ && <p className="mb-6 text-center text-sm text-[#888]">Results for “{initialQ}”</p>}
      <div className="mx-auto mb-8 max-w-md">
        <input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search movies..."
          className="w-full rounded-lg border border-[#333] bg-[#1a1a1a] px-4 py-3 text-white placeholder-[#888] outline-none focus:border-vox-light"
        />
      </div>
      {error && <p className="text-center text-red-400">{error}</p>}
      {filtered.length === 0 && !error && <p className="text-center text-[#888]">No movies found.</p>}
      <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {filtered.map((m) => <MovieCard key={m.id} movie={m} action="book" />)}
      </div>
    </div>
  );
}
