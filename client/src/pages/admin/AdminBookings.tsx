import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../../services/api';
import { Booking } from '../../types';

export default function AdminBookings() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/bookings', { params: { all: 'true' } })
      .then(({ data }) => setBookings(data.bookings))
      .catch((e) => setError(apiError(e)));
  }, []);

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">All Bookings</h1>
      {error && <p className="text-red-400">{error}</p>}
      <div className="overflow-x-auto rounded-lg border border-[#333]">
        <table className="w-full text-left text-sm">
          <thead className="bg-[#222] text-[#aaa]">
            <tr><th className="px-4 py-2.5">Ref</th><th className="px-4 py-2.5">User</th><th className="px-4 py-2.5">Movie</th><th className="px-4 py-2.5">Cinema</th><th className="px-4 py-2.5">Date</th><th className="px-4 py-2.5">Total</th><th className="px-4 py-2.5">Payment</th></tr>
          </thead>
          <tbody>
            {bookings.map((b) => (
              <tr key={b.id} className="border-t border-[#2a2a2a]">
                <td className="px-4 py-2.5"><Link to={`/admin/bookings/${b.id}`} className="text-vox-light hover:underline">{b.booking_reference}</Link></td>
                <td className="px-4 py-2.5">{b.user_name}</td>
                <td className="px-4 py-2.5">{b.movie_title}</td>
                <td className="px-4 py-2.5">{b.cinema_name ?? '—'}</td>
                <td className="px-4 py-2.5">{b.show_date}</td>
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
