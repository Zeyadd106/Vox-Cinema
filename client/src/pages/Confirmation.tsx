import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { QRCodeSVG } from 'qrcode.react';
import { api, apiError } from '../services/api';
import { Ticket } from '../types';
import { useLang } from '../context/LangContext';

export default function Confirmation() {
  const { t } = useLang();
  const { bookingId } = useParams();
  const [ticket, setTicket] = useState<Ticket | null>(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get(`/bookings/${bookingId}/ticket`)
      .then(({ data }) => setTicket(data.ticket))
      .catch((e) => setError(apiError(e)));
  }, [bookingId]);

  if (error) return <p className="p-16 text-center text-red-400">{error}</p>;
  if (!ticket) return <p className="p-16 text-center text-[#888]">{t.common.loading}</p>;

  return (
    <div className="mx-auto max-w-xl px-6 py-12 text-center">
      <div className="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-600 text-3xl">✓</div>
      <h1 className="mb-2 text-3xl font-bold">{t.ticket.success}</h1>
      <p className="mb-8 text-[#aaa]">{t.ticket.confirmedSub}</p>
      <div className="rounded-lg border border-[#333] bg-[#1a1a1a] p-8 text-start">
        <h2 className="mb-4 text-center text-xl font-bold text-vox">{t.ticket.eticket}</h2>
        <TicketRow label={t.ticket.ref} value={ticket.booking_reference} />
        <TicketRow label={t.ticket.movieL} value={ticket.movie_title} />
        {ticket.cinema_name && <TicketRow label={t.ticket.cinemaL} value={`${ticket.cinema_name}${ticket.hall_name ? ` • ${ticket.hall_name}` : ''}${ticket.format && ticket.format !== 'Standard' ? ` (${ticket.format})` : ''}`} />}
        <TicketRow label={t.ticket.dateTime} value={`${ticket.show_date} at ${ticket.show_time?.slice(0, 5)}`} />
        <TicketRow label={t.ticket.seatsL} value={ticket.seats.join(', ')} />
        <TicketRow label={t.ticket.totalPaid} value={`$${Number(ticket.total_price).toFixed(2)}`} />
        <div className="mx-auto mt-6 w-fit rounded-lg bg-white p-4">
          <QRCodeSVG value={ticket.qr_data} size={180} />
        </div>
        <p className="mt-3 text-center font-mono text-xs tracking-widest text-[#888]">{ticket.booking_reference}</p>
        {ticket.checked_in_at && (
          <p className="mt-2 text-center text-sm font-semibold text-green-400">
            {t.ticket.checkedInAt} {new Date(ticket.checked_in_at).toLocaleString()}
          </p>
        )}
      </div>
      <div className="mt-6 flex justify-center gap-3">
        <button onClick={() => window.print()} className="rounded-md border border-[#555] px-6 py-2.5 hover:border-vox">{t.ticket.print}</button>
        <Link to="/bookings" className="rounded-md bg-vox px-6 py-2.5 font-semibold hover:bg-vox-dark">{t.ticket.myBookings}</Link>
      </div>
    </div>
  );
}

function TicketRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between gap-4 border-b border-dashed border-[#444] py-2 text-sm">
      <span className="text-[#999]">{label}</span>
      <span className="font-semibold">{value}</span>
    </div>
  );
}
