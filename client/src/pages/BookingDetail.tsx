import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Booking } from '../types';

export default function BookingDetail() {
  const { id } = useParams();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get(`/bookings/${id}`)
      .then(({ data }) => setBooking(data.booking))
      .catch((e) => setError(apiError(e)));
  }, [id]);

  if (error) return <p className="p-16 text-center text-red-400">{error}</p>;
  if (!booking) return <p className="p-16 text-center text-[#888]">Loading...</p>;

  return (
    <div className="mx-auto max-w-3xl px-6 py-12">
      <h1 className="mb-8 text-3xl font-bold text-vox">Booking {booking.booking_reference}</h1>
      <div className="space-y-3 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        <Row label="Movie" value={booking.movie_title ?? '—'} />
        {booking.cinema_name && <Row label="Cinema" value={`${booking.cinema_name}${booking.hall_name ? ` • ${booking.hall_name}` : ''}${booking.format && booking.format !== 'Standard' ? ` (${booking.format})` : ''}`} />}
        <Row label="Date / Time" value={`${booking.show_date} at ${booking.show_time?.slice(0, 5)}`} />
        <Row label="Seats" value={booking.seats?.map((s) => s.seat_number).join(', ') ?? '—'} />
        <Row label="Subtotal" value={`$${Number(booking.subtotal ?? 0).toFixed(2)}`} />
        <Row label="Booking fee" value={`$${Number(booking.booking_fee ?? 0).toFixed(2)}`} />
        <Row label="Tax" value={`$${Number(booking.tax_amount ?? 0).toFixed(2)}`} />
        <Row label="Total" value={`$${Number(booking.total_price).toFixed(2)}`} />
        <Row label="Status" value={`${booking.status} / ${booking.payment_status}`} />
        {booking.checked_in_at && <Row label="Checked in" value={new Date(booking.checked_in_at).toLocaleString()} />}
        {booking.payment && <Row label="Transaction" value={`${booking.payment.transaction_id} (${booking.payment.payment_method})`} />}
      </div>
      <div className="mt-6 flex gap-3">
        {booking.payment_status === 'paid' ? (
          <Link to={`/confirmation/${booking.id}`} className="rounded-md bg-vox px-6 py-2.5 font-semibold hover:bg-vox-dark">View Ticket</Link>
        ) : (
          <Link to={`/pay/${booking.id}`} className="rounded-md bg-vox px-6 py-2.5 font-semibold hover:bg-vox-dark">Pay Now</Link>
        )}
        <Link to="/bookings" className="rounded-md border border-[#555] px-6 py-2.5 hover:border-vox">Back</Link>
      </div>
    </div>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between gap-4 border-b border-[#2a2a2a] pb-2 text-sm">
      <span className="text-[#999]">{label}</span>
      <span className="text-right font-semibold">{value}</span>
    </div>
  );
}
