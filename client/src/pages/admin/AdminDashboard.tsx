import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../../services/api';
import { AdminStats } from '../../types';

export default function AdminDashboard() {
  const [stats, setStats] = useState<AdminStats | null>(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/admin/stats').then(({ data }) => setStats(data.stats)).catch((e) => setError(apiError(e)));
  }, []);

  if (error) return <p className="text-red-400">{error}</p>;
  if (!stats) return <p className="text-[#888]">Loading...</p>;

  const cards = [
    { label: 'Total Users', value: stats.total_users },
    { label: 'Total Bookings', value: stats.total_bookings },
    { label: 'Total Movies', value: stats.total_movies },
    { label: 'Coming Soon', value: stats.coming_soon_count },
    { label: 'Revenue', value: `$${Number(stats.revenue).toFixed(2)}` },
  ];

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">Dashboard</h1>
      <div className="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        {cards.map((c) => (
          <div key={c.label} className="rounded-lg border border-[#333] bg-[#1a1a1a] p-5">
            <p className="text-xs uppercase tracking-wider text-[#999]">{c.label}</p>
            <p className="mt-1 text-2xl font-bold text-vox-light">{c.value}</p>
          </div>
        ))}
      </div>
      <h2 className="mb-3 text-lg font-semibold">Recent Bookings</h2>
      <div className="overflow-x-auto rounded-lg border border-[#333]">
        <table className="w-full text-left text-sm">
          <thead className="bg-[#222] text-[#aaa]">
            <tr><th className="px-4 py-2.5">Ref</th><th className="px-4 py-2.5">User</th><th className="px-4 py-2.5">Movie</th><th className="px-4 py-2.5">Total</th><th className="px-4 py-2.5">Status</th></tr>
          </thead>
          <tbody>
            {stats.recent_bookings.map((b) => (
              <tr key={b.id} className="border-t border-[#2a2a2a]">
                <td className="px-4 py-2.5"><Link to={`/admin/bookings/${b.id}`} className="text-vox-light hover:underline">{b.booking_reference}</Link></td>
                <td className="px-4 py-2.5">{b.user_name}</td>
                <td className="px-4 py-2.5">{b.movie_title}</td>
                <td className="px-4 py-2.5">${Number(b.total_price).toFixed(2)}</td>
                <td className="px-4 py-2.5">{b.payment_status}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
