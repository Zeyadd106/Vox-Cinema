import { useEffect, useState } from 'react';
import { api, apiError } from '../services/api';
import { Movie } from '../types';
import MovieCard from '../components/MovieCard';

export default function ComingSoon() {
  const [movies, setMovies] = useState<Movie[]>([]);
  const [msg, setMsg] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/movies', { params: { status: 'coming_soon' } })
      .then(({ data }) => setMovies(data.movies))
      .catch((e) => setError(apiError(e)));
  }, []);

  const notify = async (m: Movie) => {
    try {
      const { data } = await api.post(`/movies/${m.id}/notify`);
      setMsg(`${m.title}: ${data.message}`);
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  return (
    <div className="mx-auto max-w-7xl px-[6%] py-12">
      <h1 className="mb-2 text-center text-3xl font-bold uppercase tracking-widest text-vox">Coming Soon</h1>
      <p className="mb-8 text-center text-[#aaa]">Be the first to know when tickets go on sale.</p>
      {msg && <p className="mb-6 rounded-md border border-green-700 bg-green-950 px-4 py-3 text-center text-green-300">{msg}</p>}
      {error && <p className="text-center text-red-400">{error}</p>}
      <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {movies.map((m) => <MovieCard key={m.id} movie={m} action="notify" onNotify={notify} />)}
      </div>
      {movies.length === 0 && !error && <p className="text-center text-[#888]">No coming-soon titles right now.</p>}
    </div>
  );
}
