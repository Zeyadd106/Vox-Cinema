import { useEffect, useState } from 'react';
import { api, apiError } from '../services/api';
import { Movie } from '../types';
import MovieCard from '../components/MovieCard';
import FindTimes from '../components/FindTimes';

export default function Home() {
  const [movies, setMovies] = useState<Movie[]>([]);
  const [query, setQuery] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/movies', { params: { status: 'current' } })
      .then(({ data }) => setMovies(data.movies))
      .catch((e) => setError(apiError(e)));
  }, []);

  const filtered = movies.filter((m) => m.title.toLowerCase().includes(query.toLowerCase()));

  return (
    <div>
      <section className="flex min-h-[85vh] items-center justify-center bg-gradient-to-b from-[#1a0508] via-black to-black px-6 text-center">
        <div>
          <p className="mb-4 text-xl font-semibold uppercase tracking-[4px] text-vox-light">Experience the Magic of Cinema</p>
          <h1 className="text-5xl font-bold uppercase leading-tight tracking-wide md:text-6xl">Your Ultimate<br />Movie Destination</h1>
        </div>
      </section>
      <section id="find-times" className="scroll-mt-48 px-[6%] pt-10">
        <FindTimes />
      </section>
      <section className="px-[6%] py-12">
        <h2 className="mb-8 text-center text-3xl font-bold uppercase tracking-widest text-vox">Now Showing</h2>
        <div className="mx-auto mb-10 max-w-md">
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Search for movies..."
            className="w-full rounded-lg border border-[#333] bg-[#1a1a1a] px-4 py-3 text-white placeholder-[#888] outline-none focus:border-vox-light"
          />
        </div>
        {error && <p className="mb-6 text-center text-red-400">{error}</p>}
        {filtered.length === 0 && !error && <p className="text-center text-[#888]">No movies found.</p>}
        <div className="mx-auto grid max-w-7xl gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {filtered.map((m) => <MovieCard key={m.id} movie={m} action="book" />)}
        </div>
      </section>
    </div>
  );
}
