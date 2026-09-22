import { useEffect, useState } from 'react';
import { useNavigate, useParams, useSearchParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { Movie, PriceQuote, Seat, Showtime, TICKET_PRICE } from '../types';
import SeatMap from '../components/SeatMap';
import { useLang } from '../context/LangContext';
import { fmtDay } from '../i18n';

interface Hold {
  token: string;
  expiresAt: number;
  seatIds: number[];
  quote: PriceQuote;
}

function useCountdown(target: number | null): string {
  const [now, setNow] = useState(Date.now());
  useEffect(() => {
    if (!target) return;
    const t = setInterval(() => setNow(Date.now()), 1000);
    return () => clearInterval(t);
  }, [target]);
  if (!target) return '';
  const ms = Math.max(0, target - now);
  const m = Math.floor(ms / 60000);
  const s = Math.floor((ms % 60000) / 1000);
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}

export default function BookMovie() {
  const { t, lang } = useLang();
  const { movieId } = useParams();
  const [params] = useSearchParams();
  const navigate = useNavigate();
  const [movie, setMovie] = useState<Movie | null>(null);
  const [showtimes, setShowtimes] = useState<Showtime[]>([]);
  const [activeDate, setActiveDate] = useState('');
  const [showtimeId, setShowtimeId] = useState<number | null>(Number(params.get('showtime')) || null);
  const [seats, setSeats] = useState<Seat[]>([]);
  const [showInfo, setShowInfo] = useState<Showtime | null>(null);
  const [selected, setSelected] = useState<number[]>([]);
  const [hold, setHold] = useState<Hold | null>(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const countdown = useCountdown(hold?.expiresAt ?? null);

  useEffect(() => {
    api.get(`/movies/${movieId}`)
      .then(({ data }) => {
        setMovie(data.movie);
        setShowtimes(data.showtimes);
        const dates = [...new Set((data.showtimes as Showtime[]).map((s) => s.date))] as string[];
        if (dates.length && !activeDate) setActiveDate(dates[0]);
      })
      .catch((e) => setError(apiError(e)));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [movieId]);

  const loadSeats = (id: number) => {
    api.get(`/showtimes/${id}/seats`)
      .then(({ data }) => { setSeats(data.seats); })
      .catch((e) => setError(apiError(e)));
    api.get(`/showtimes/${id}`).then(({ data }) => setShowInfo(data.showtime)).catch(() => undefined);
  };

  useEffect(() => {
    if (!showtimeId) {
      setSeats([]);
      setSelected([]);
      return;
    }
    setHold(null);
    setSelected([]);
    loadSeats(showtimeId);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [showtimeId]);

  // Hold expiry watchdog
  useEffect(() => {
    if (hold && Date.now() >= hold.expiresAt) {
      setHold(null);
      setSelected([]);
      setError(t.book.holdExpired);
      if (showtimeId) loadSeats(showtimeId);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [countdown]);

  const dates = [...new Set(showtimes.map((s) => s.date))].sort();

  const toggle = (id: number) => {
    if (hold) return; // locked while held
    setSelected((prev) => (prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]));
  };

  const createHold = async () => {
    if (!showtimeId || selected.length === 0) return;
    setBusy(true);
    setError('');
    try {
      const { data } = await api.post('/holds', { showtime_id: showtimeId, seat_ids: selected });
      setHold({
        token: data.hold_token,
        expiresAt: new Date(data.expires_at).getTime(),
        seatIds: data.seat_ids,
        quote: data.quote,
      });
      loadSeats(showtimeId);
    } catch (e) {
      setError(apiError(e));
      if (showtimeId) loadSeats(showtimeId);
    } finally {
      setBusy(false);
    }
  };

  const releaseHold = async () => {
    if (hold) {
      try { await api.delete(`/holds/${hold.token}`); } catch { /* ignore */ }
      setHold(null);
      setSelected([]);
      if (showtimeId) loadSeats(showtimeId);
    }
  };

  const changeShowtime = (id: number) => {
    if (hold) releaseHold();
    setShowtimeId(id);
  };

  const proceed = async () => {
    if (!showtimeId || !hold) return;
    setBusy(true);
    setError('');
    try {
      const { data } = await api.post('/bookings', { showtime_id: showtimeId, seat_ids: hold.seatIds, hold_token: hold.token });
      navigate(`/pay/${data.booking.id}`);
    } catch (e) {
      const msg = apiError(e);
      setError(msg);
      if (/expired|held/i.test(msg)) {
        setHold(null);
        setSelected([]);
        if (showtimeId) loadSeats(showtimeId);
      }
    } finally {
      setBusy(false);
    }
  };

  const heldSeatNames = seats.filter((s) => hold?.seatIds.includes(s.id)).map((s) => s.seat_number).join(', ');
  const selectedNames = seats.filter((s) => selected.includes(s.id)).map((s) => s.seat_number).join(', ');

  return (
    <div className="mx-auto max-w-6xl px-[6%] py-12">
      <h1 className="mb-1 text-3xl font-bold">{t.book.title}{movie ? ` — ${movie.title}` : ''}</h1>
      <p className="mb-8 text-sm text-[#999]">
        ${TICKET_PRICE} {t.book.perSeat}
        {showInfo && ` • ${showInfo.cinema_name} • ${showInfo.hall_name}${showInfo.format !== 'Standard' ? ` (${showInfo.format})` : ''}`}
      </p>
      {error && <p className="mb-4 rounded bg-red-950 px-4 py-3 text-sm text-red-300">{error}</p>}

      <div className="grid gap-8 lg:grid-cols-[1fr_320px]">
        <div>
          <h2 className="mb-3 font-semibold text-vox">1. {t.book.step1}</h2>
          <div className="mb-3 flex flex-wrap gap-2">
            {dates.map((d) => (
              <button key={d} onClick={() => setActiveDate(d)}
                className={`rounded-md border px-4 py-2 text-sm font-semibold ${d === activeDate ? 'border-vox bg-vox' : 'border-[#444] bg-[#1a1a1a] hover:border-vox'}`}>
                {fmtDay(d, lang)}
              </button>
            ))}
          </div>
          <div className="mb-8 flex flex-wrap gap-2">
            {showtimes.filter((s) => s.date === activeDate).map((s) => (
              <button key={s.id} onClick={() => changeShowtime(s.id)}
                className={`rounded-md border px-5 py-2.5 font-semibold ${s.id === showtimeId ? 'border-vox bg-vox' : 'border-[#444] bg-[#1a1a1a] hover:border-vox'}`}>
                {s.time.slice(0, 5)}
              </button>
            ))}
          </div>
          {showtimeId ? (
            <>
              <div className="mb-3 flex items-center justify-between">
                <h2 className="font-semibold text-vox">2. {t.book.step2}</h2>
                <button onClick={() => loadSeats(showtimeId)} className="text-xs text-[#888] hover:text-white">{t.book.refresh}</button>
              </div>
              <SeatMap seats={seats} selected={hold ? hold.seatIds : selected} onToggle={toggle} />
              {!hold ? (
                <button
                  disabled={selected.length === 0 || busy}
                  onClick={createHold}
                  className="mt-4 w-full rounded-md bg-vox py-3 font-semibold transition hover:bg-vox-dark disabled:cursor-not-allowed disabled:opacity-40"
                >
                  {busy ? t.book.booking : `${t.book.holdSeats} (${selected.length})${selectedNames ? ` (${selectedNames})` : ''}`}
                </button>
              ) : (
                <div className="mt-4 rounded-lg border border-amber-600 bg-amber-950 px-4 py-3 text-sm text-amber-200">
                  <b>{heldSeatNames}</b> {t.book.heldFor} <b className="tabular-nums">{countdown}</b>.
                  <button onClick={releaseHold} className="ms-3 underline hover:text-white">{t.book.release}</button>
                </div>
              )}
            </>
          ) : (
            <p className="text-[#888]">{t.book.selectPrompt}</p>
          )}
        </div>
        <aside className="h-fit rounded-lg border border-[#333] bg-[#1a1a1a] p-6 lg:sticky lg:top-24">
          <h2 className="mb-4 font-semibold text-vox">{t.book.summary}</h2>
          <p className="text-sm text-[#aaa]">{t.book.movieL}</p>
          <p className="mb-3 font-semibold">{movie?.title ?? '—'}</p>
          <p className="text-sm text-[#aaa]">{t.book.seatsL}</p>
          <p className="mb-3 font-semibold">{hold ? heldSeatNames : selectedNames || '—'}</p>
          {hold ? (
            <div className="mb-4 space-y-1.5 border-t border-[#333] pt-4 text-sm">
              <div className="flex justify-between text-[#ccc]"><span>{t.book.subtotal}</span><span>${hold.quote.subtotal.toFixed(2)}</span></div>
              <div className="flex justify-between text-[#ccc]"><span>{t.book.fee}</span><span>${hold.quote.booking_fee.toFixed(2)}</span></div>
              <div className="flex justify-between text-[#ccc]"><span>{t.book.tax} ({hold.quote.tax_rate}%)</span><span>${hold.quote.tax_amount.toFixed(2)}</span></div>
              <div className="flex justify-between border-t border-[#333] pt-2 text-lg font-bold"><span>{t.book.totalDue}</span><span>${hold.quote.total.toFixed(2)}</span></div>
            </div>
          ) : (
            <div className="mb-4 flex justify-between border-t border-[#333] pt-4 text-lg font-bold">
              <span>{t.book.totalDue}</span>
              <span>{selected.length === 0 ? '—' : `${t.book.from} $${(selected.length * TICKET_PRICE).toFixed(2)}`}</span>
            </div>
          )}
          <button
            disabled={!hold || busy}
            onClick={proceed}
            className="w-full rounded-md bg-vox py-3 font-semibold transition hover:bg-vox-dark disabled:cursor-not-allowed disabled:opacity-40"
          >
            {busy ? t.book.booking : t.book.proceed}
          </button>
          {!hold && <p className="mt-2 text-center text-xs text-[#666]">{t.book.holdFirst}</p>}
        </aside>
      </div>
    </div>
  );
}
