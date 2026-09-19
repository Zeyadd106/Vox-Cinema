import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Booking } from '../types';

export default function Bookings() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [error, setError] = useState('');
  const [msg, setMsg] = useState('');

  const load = () => {
    api.get('/bookings')
      .then(({ data }) => setBookings(data.bookings))
      .catch((e) => setError(apiError(e)));
  };
  useEffect(load, []);

  const cancel = async (id: number) => {
    if (!confirm('Cancel this booking?')) return;
    try {
      const { data } = await api.delete(`/bookings/${id}/cancel`);
      setMsg(data.message);
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  return (
    <div className="mx-auto max-w-5xl px-[6%] py-12">
      <h1 className="mb-8 text-3xl font-bold text-vox">My Bookings</h1>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#1a1a1a] px-4 py-3 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}
      <div className="space-y-3">
        {bookings.map((b) => (
          <div key={b.id} className="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#333] bg-[#1a1a1a] px-4 py-3">
            <div>
              <Link to={`/bookings/${b.id}`} className="font-semibold hover:text-vox-light">{b.movie_title}</Link>
              <p className="text-xs text-[#999]">{b.cinema_name ? `${b.cinema_name} • ` : ''}{b.show_date} at {b.show_time?.slice(0, 5)} • {b.booking_reference} • ${Number(b.total_price).toFixed(2)} • {b.status} / {b.payment_status}{b.checked_in_at ? ' • ✓ checked in' : ''}</p>
            </div>
            <div className="flex gap-2">
              {b.payment_status !== 'paid' && b.status === 'pending' && (
                <Link to={`/pay/${b.id}`} className="rounded-md bg-vox px-4 py-1.5 text-sm font-semibold hover:bg-vox-dark">Pay</Link>
              )}
              <Link to={`/bookings/${b.id}`} className="rounded-md border border-[#555] px-4 py-1.5 text-sm hover:border-vox">View</Link>
              {b.status === 'pending' && (
                <button onClick={() => cancel(b.id)} className="rounded-md border border-red-800 px-4 py-1.5 text-sm text-red-400 hover:bg-red-950">Cancel</button>
              )}
            </div>
          </div>
        ))}
        {bookings.length === 0 && !error && <p className="text-[#888]">No bookings yet. <Link to="/" className="text-vox-light">Browse movies</Link>.</p>}
      </div>
    </div>
  );
}
