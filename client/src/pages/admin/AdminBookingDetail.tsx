import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { api, apiError } from '../../services/api';
import { Booking } from '../../types';
import { useLang } from '../../context/LangContext';

export default function AdminBookingDetail() {
  const { t } = useLang();
  const { id } = useParams();
  const navigate = useNavigate();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [error, setError] = useState('');
  const [msg, setMsg] = useState('');

  useEffect(() => {
    api.get(`/bookings/${id}`).then(({ data }) => setBooking(data.booking)).catch((e) => setError(apiError(e)));
  }, [id]);

  const destroy = async () => {
    if (!confirm(t.admin.deleteBookingConfirm)) return;
    try {
      await api.delete(`/bookings/${id}`);
      navigate('/admin/bookings');
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  if (error) return <p className="text-red-400">{error}</p>;
  if (!booking) return <p className="text-[#888]">{t.common.loading}</p>;

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">{t.admin.bookingTitle} {booking.booking_reference}</h1>
      {msg && <p className="mb-4 text-sm text-red-300">{msg}</p>}
      <div className="max-w-2xl space-y-2 rounded-lg border border-[#333] bg-[#1a1a1a] p-8 text-sm">
        <p><span className="text-[#999]">{t.admin.userL}:</span> <b>{booking.user_name} ({booking.user_email})</b></p>
        <p><span className="text-[#999]">{t.admin.movieL}:</span> <b>{booking.movie_title}</b></p>
        <p><span className="text-[#999]">{t.admin.showL}:</span> <b>{booking.show_date} at {booking.show_time?.slice(0, 5)}</b></p>
        <p><span className="text-[#999]">{t.admin.seatsL}:</span> <b>{booking.seats?.map((s) => s.seat_number).join(', ')}</b></p>
        <p><span className="text-[#999]">{t.admin.totalL}:</span> <b>${Number(booking.total_price).toFixed(2)}</b></p>
        <p><span className="text-[#999]">{t.admin.statusL}:</span> <b>{booking.status} / {booking.payment_status}</b></p>
      </div>
      <div className="mt-6 flex gap-3">
        <Link to="/admin/bookings" className="rounded-md border border-[#555] px-6 py-2.5 hover:border-vox">{t.common.back}</Link>
        <button onClick={destroy} className="rounded-md border border-red-800 px-6 py-2.5 text-red-400 hover:bg-red-950">{t.common.delete}</button>
      </div>
    </div>
  );
}
