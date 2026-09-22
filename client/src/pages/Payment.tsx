import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Booking } from '../types';
import { useLang } from '../context/LangContext';

export default function Payment() {
  const { t } = useLang();
  const { bookingId } = useParams();
  const navigate = useNavigate();
  const [booking, setBooking] = useState<Booking | null>(null);
  const [method, setMethod] = useState<'credit_card' | 'paypal'>('credit_card');
  const [cardNumber, setCardNumber] = useState('');
  const [cardName, setCardName] = useState('');
  const [expiry, setExpiry] = useState('');
  const [cvv, setCvv] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    api.get(`/bookings/${bookingId}`)
      .then(({ data }) => setBooking(data.booking))
      .catch((e) => setError(apiError(e)));
  }, [bookingId]);

  const formatCard = (v: string) => v.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ');
  const formatExpiry = (v: string) => {
    const d = v.replace(/\D/g, '').slice(0, 4);
    return d.length > 2 ? `${d.slice(0, 2)}/${d.slice(2)}` : d;
  };

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    setError('');
    try {
      await api.post('/payments/process', {
        booking_id: Number(bookingId),
        payment_method: method,
        card_number: cardNumber,
        card_name: cardName,
        expiry_date: expiry,
        cvv,
      });
      navigate(`/confirmation/${bookingId}`);
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox';

  return (
    <div className="mx-auto max-w-xl px-6 py-12">
      <h1 className="mb-2 text-3xl font-bold text-vox">{t.payment.title}</h1>
      {booking && (
        <div className="mb-6 rounded-lg border border-[#333] bg-[#1a1a1a] p-5 text-sm">
          <p className="mb-3 font-semibold">{booking.movie_title} • {booking.seats?.map((s) => s.seat_number).join(', ')}</p>
          <div className="space-y-1 text-[#ccc]">
            <div className="flex justify-between"><span>{t.payment.subtotal}</span><span>${Number(booking.subtotal ?? 0).toFixed(2)}</span></div>
            <div className="flex justify-between"><span>{t.payment.fee}</span><span>${Number(booking.booking_fee ?? 0).toFixed(2)}</span></div>
            <div className="flex justify-between"><span>{t.payment.tax}</span><span>${Number(booking.tax_amount ?? 0).toFixed(2)}</span></div>
            <div className="flex justify-between border-t border-[#333] pt-2 text-base font-bold text-white"><span>{t.payment.totalDue}</span><span>${Number(booking.total_price).toFixed(2)}</span></div>
          </div>
        </div>
      )}
      <form onSubmit={submit} className="space-y-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        {error && <p className="rounded bg-red-950 px-3 py-2 text-sm text-red-300">{error}</p>}
        <div className="grid grid-cols-2 gap-2">
          <button type="button" onClick={() => setMethod('credit_card')} className={`rounded-md border py-2.5 font-semibold ${method === 'credit_card' ? 'border-vox bg-vox' : 'border-[#555] hover:border-vox'}`}>{t.payment.card}</button>
          <button type="button" onClick={() => setMethod('paypal')} className={`rounded-md border py-2.5 font-semibold ${method === 'paypal' ? 'border-vox bg-vox' : 'border-[#555] hover:border-vox'}`}>{t.payment.paypal}</button>
        </div>
        {method === 'credit_card' && (
          <>
            <div>
              <label className="mb-1 block text-sm text-[#aaa]">{t.payment.cardNum}</label>
              <input required value={cardNumber} onChange={(e) => setCardNumber(formatCard(e.target.value))} placeholder="4242 4242 4242 4242" inputMode="numeric" className={input} />
            </div>
            <div>
              <label className="mb-1 block text-sm text-[#aaa]">{t.payment.cardName}</label>
              <input required value={cardName} onChange={(e) => setCardName(e.target.value)} className={input} />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="mb-1 block text-sm text-[#aaa]">{t.payment.expiry}</label>
                <input required value={expiry} onChange={(e) => setExpiry(formatExpiry(e.target.value))} placeholder="12/28" className={input} />
              </div>
              <div>
                <label className="mb-1 block text-sm text-[#aaa]">{t.payment.cvv}</label>
                <input required value={cvv} onChange={(e) => setCvv(e.target.value.replace(/\D/g, '').slice(0, 4))} placeholder="123" inputMode="numeric" className={input} />
              </div>
            </div>
          </>
        )}
        {method === 'paypal' && <p className="rounded border border-[#444] bg-black px-4 py-3 text-sm text-[#aaa]">{t.payment.paypalNote}</p>}
        <button disabled={busy} className="w-full rounded-md bg-vox py-3 font-semibold transition hover:bg-vox-dark disabled:opacity-50">
          {busy ? t.payment.processing : `${t.payment.payBtn} ${booking ? `$${Number(booking.total_price).toFixed(2)}` : ''}`}
        </button>
      </form>
    </div>
  );
}
