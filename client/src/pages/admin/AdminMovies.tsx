import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../../services/api';
import { Movie } from '../../types';
import { useLang } from '../../context/LangContext';

export default function AdminMovies() {
  const { t } = useLang();
  const [movies, setMovies] = useState<Movie[]>([]);
  const [error, setError] = useState('');
  const [msg, setMsg] = useState('');

  const load = () => api.get('/movies').then(({ data }) => setMovies(data.movies)).catch((e) => setError(apiError(e)));
  useEffect(() => { load(); }, []);

  const remove = async (id: number) => {
    if (!confirm(t.admin.deleteConfirmMovie)) return;
    try {
      await api.delete(`/movies/${id}`);
      setMsg(t.admin.deletedMovie);
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-2xl font-bold">{t.admin.movies}</h1>
        <Link to="/admin/movies/new" className="rounded-md bg-vox px-5 py-2 font-semibold hover:bg-vox-dark">{t.admin.addMovie}</Link>
      </div>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#222] px-4 py-2 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}
      <div className="overflow-x-auto rounded-lg border border-[#333]">
        <table className="w-full text-start text-sm">
          <thead className="bg-[#222] text-[#aaa]">
            <tr><th className="px-4 py-2.5">{t.admin.titleF}</th><th className="px-4 py-2.5">{t.admin.genre}</th><th className="px-4 py-2.5">{t.admin.rating}</th><th className="px-4 py-2.5">{t.admin.status}</th><th className="px-4 py-2.5">{t.admin.release}</th><th className="px-4 py-2.5">{t.common.actions}</th></tr>
          </thead>
          <tbody>
            {movies.map((m) => (
              <tr key={m.id} className="border-t border-[#2a2a2a]">
                <td className="px-4 py-2.5 font-semibold">{m.title}</td>
                <td className="px-4 py-2.5">{m.genre}</td>
                <td className="px-4 py-2.5">{m.rating}</td>
                <td className="px-4 py-2.5">{m.status}</td>
                <td className="px-4 py-2.5">{m.release_date}</td>
                <td className="flex gap-2 px-4 py-2.5">
                  <Link to={`/admin/movies/${m.id}/edit`} className="text-vox-light hover:underline">{t.common.edit}</Link>
                  <button onClick={() => remove(m.id)} className="text-red-400 hover:underline">{t.common.delete}</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

