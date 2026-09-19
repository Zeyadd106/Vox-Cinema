import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { api, apiError } from '../../services/api';
import { Booking } from '../../types';

export default function AdminBookingDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [error, setError] = useState('');
  const [msg, setMsg] = useState('');

  useEffect(() => {
    api.get(`/bookings/${id}`).then(({ data }) => setBooking(data.booking)).catch((e) => setError(apiError(e)));
  }, [id]);

  const destroy = async () => {
    if (!confirm('Delete this booking permanently?')) return;
    try {
      await api.delete(`/bookings/${id}`);
      navigate('/admin/bookings');
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  if (error) return <p className="text-red-400">{error}</p>;
  if (!booking) return <p className="text-[#888]">Loading...</p>;

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">Booking {booking.booking_reference}</h1>
      {msg && <p className="mb-4 text-sm text-red-300">{msg}</p>}
      <div className="max-w-2xl space-y-2 rounded-lg border border-[#333] bg-[#1a1a1a] p-8 text-sm">
        <p><span className="text-[#999]">User:</span> <b>{booking.user_name} ({booking.user_email})</b></p>
        <p><span className="text-[#999]">Movie:</span> <b>{booking.movie_title}</b></p>
        <p><span className="text-[#999]">Show:</span> <b>{booking.show_date} at {booking.show_time?.slice(0, 5)}</b></p>
        <p><span className="text-[#999]">Seats:</span> <b>{booking.seats?.map((s) => s.seat_number).join(', ')}</b></p>
        <p><span className="text-[#999]">Total:</span> <b>${Number(booking.total_price).toFixed(2)}</b></p>
        <p><span className="text-[#999]">Status:</span> <b>{booking.status} / {booking.payment_status}</b></p>
      </div>
      <div className="mt-6 flex gap-3">
        <Link to="/admin/bookings" className="rounded-md border border-[#555] px-6 py-2.5 hover:border-vox">Back</Link>
        <button onClick={destroy} className="rounded-md border border-red-800 px-6 py-2.5 text-red-400 hover:bg-red-950">Delete</button>
      </div>
    </div>
  );
}
