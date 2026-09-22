import { useEffect, useState } from 'react';
import { api, apiError } from '../services/api';
import { Movie } from '../types';
import MovieCard from '../components/MovieCard';
import { useLang } from '../context/LangContext';

export default function ComingSoon() {
  const { t } = useLang();
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
    <div className="bg-white text-slate-900">
    <div className="mx-auto max-w-7xl px-[6%] py-12">
      <h1 className="mb-2 text-center text-3xl font-bold uppercase tracking-widest text-vox-blue">{t.comingSoon}</h1>
      <p className="mb-8 text-center text-slate-500">{t.soon.sub}</p>
      {msg && <p className="mb-6 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-center text-green-800">{msg}</p>}
      {error && <p className="text-center text-red-600">{error}</p>}
      <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {movies.map((m) => <MovieCard key={m.id} movie={m} action="notify" onNotify={notify} />)}
      </div>
      {movies.length === 0 && !error && <p className="text-center text-slate-500">{t.soon.empty}</p>}
    </div>
    </div>
  );
}
