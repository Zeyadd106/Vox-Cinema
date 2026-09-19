import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Booking } from '../types';

function BookingRow({ b }: { b: Booking }) {
  return (
    <Link to={`/bookings/${b.id}`} className="flex items-center justify-between rounded-lg border border-[#333] bg-[#1a1a1a] px-4 py-3 transition hover:border-vox">
      <div>
        <p className="font-semibold">{b.movie_title}</p>
        <p className="text-xs text-[#999]">{b.show_date} at {b.show_time?.slice(0, 5)} • {b.booking_reference} • ${Number(b.total_price).toFixed(2)}</p>
      </div>
      <span className={`rounded-full px-3 py-1 text-xs font-semibold ${b.payment_status === 'paid' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300'}`}>
        {b.payment_status}
      </span>
    </Link>
  );
}

export default function Dashboard() {
  const [upcoming, setUpcoming] = useState<Booking[]>([]);
  const [history, setHistory] = useState<Booking[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/dashboard')
      .then(({ data }) => { setUpcoming(data.upcomingBookings); setHistory(data.bookingHistory); })
      .catch((e) => setError(apiError(e)));
  }, []);

  return (
    <div className="mx-auto max-w-5xl px-[6%] py-12">
      <h1 className="mb-8 text-3xl font-bold text-vox">My Dashboard</h1>
      {error && <p className="mb-4 text-red-400">{error}</p>}
      <h2 className="mb-3 text-xl font-semibold">Upcoming Bookings</h2>
      <div className="mb-10 space-y-3">
        {upcoming.length === 0 && <p className="text-[#888]">No upcoming bookings.</p>}
        {upcoming.map((b) => <BookingRow key={b.id} b={b} />)}
      </div>
      <h2 className="mb-3 text-xl font-semibold">Booking History</h2>
      <div className="space-y-3">
        {history.length === 0 && <p className="text-[#888]">No past bookings.</p>}
        {history.map((b) => <BookingRow key={b.id} b={b} />)}
      </div>
    </div>
  );
}
