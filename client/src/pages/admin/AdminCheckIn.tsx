import { useState } from 'react';
import { api, apiError } from '../../services/api';

interface CheckInResult {
  message: string;
  booking: {
    booking_reference: string;
    movie_title: string;
    show_date: string;
    show_time: string;
    user_name: string;
    seats: string[];
    checked_in_at?: string;
  };
}

export default function AdminCheckIn() {
  const [ref, setRef] = useState('');
  const [token, setToken] = useState('');
  const [result, setResult] = useState<CheckInResult | null>(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    setError('');
    setResult(null);
    let body: Record<string, string> = { booking_reference: ref.trim(), check_in_token: token.trim() };
    // Allow pasting the raw scanned QR JSON
    const t = ref.trim();
    if (t.startsWith('{')) {
      body = { qr_data: t };
    }
    try {
      const { data } = await api.post('/admin/check-in', body);
      setResult(data);
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox font-mono';

  return (
    <div>
      <h1 className="mb-2 text-2xl font-bold">Ticket Check-in</h1>
      <p className="mb-6 text-sm text-[#999]">Scan the guest's QR code (paste its content) or enter the reference + code manually.</p>
      <form onSubmit={submit} className="grid max-w-xl gap-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        {error && <p className="rounded bg-red-950 px-3 py-2 text-sm text-red-300">{error}</p>}
        {result && (
          <div className="rounded border border-green-700 bg-green-950 px-4 py-3 text-sm text-green-200">
            <p className="font-semibold">{result.message}</p>
            <div className="mt-2 space-y-1 text-green-100/90">
              <p><b>{result.booking.movie_title}</b> — {result.booking.show_date} at {result.booking.show_time?.slice(0, 5)}</p>
              <p>Guest: {result.booking.user_name} • Seats: {result.booking.seats.join(', ')}</p>
              <p>Ref: <span className="font-mono">{result.booking.booking_reference}</span></p>
            </div>
          </div>
        )}
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Booking reference (or paste scanned QR JSON)</label>
          <input required value={ref} onChange={(e) => setRef(e.target.value)} placeholder="VOXXXXXXXXX or {&quot;ref&quot;:...}" className={input} />
        </div>
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Check-in code (from QR; skip if pasted above)</label>
          <input value={token} onChange={(e) => setToken(e.target.value)} placeholder="24-char code" className={input} />
        </div>
        <button disabled={busy} className="rounded-md bg-vox py-3 font-semibold hover:bg-vox-dark disabled:opacity-50">
          {busy ? 'Verifying...' : 'Check In'}
        </button>
      </form>
    </div>
  );
}
