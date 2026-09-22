import { useState } from 'react';
import { Link } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { useLang } from '../context/LangContext';

export default function ForgotPassword() {
  const { t } = useLang();
  const [email, setEmail] = useState('');
  const [done, setDone] = useState(false);
  const [busy, setBusy] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBusy(true);
    try {
      await api.post('/auth/forgot', { email: email.trim() });
      setDone(true);
    } catch (err) {
      setDone(true);
      void apiError(err);
    } finally {
      setBusy(false);
    }
  };

  const input =
    'w-full rounded-lg border border-slate-300 bg-white px-4 pb-2.5 pt-6 text-[15px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-vox-pink focus:ring-2 focus:ring-vox-pink/25';

  return (
    <div className="bg-[#0b0b0e] text-white">
      <div className="mx-auto flex min-h-[calc(100vh-220px)] max-w-[460px] flex-col justify-center px-6 py-12">
        <h1 className="text-[26px] font-bold">{t.auth.forgotTitle}</h1>
        <p className="mb-7 mt-1.5 text-sm text-white/60">{t.auth.forgotSub}</p>
        {done ? (
          <div className="rounded-xl border border-white/15 bg-white/5 p-6 text-center">
            <p className="text-lg font-semibold">{t.auth.checkInbox}</p>
            <p className="mt-2 text-sm text-white/60">{t.auth.checkInboxSub}</p>
            <Link to="/login" className="mt-5 inline-block rounded-lg bg-gradient-to-b from-vox-pink to-vox-pink-dark px-8 py-2.5 text-sm font-semibold transition hover:-translate-y-px">{t.auth.backToLogin}</Link>
          </div>
        ) : (
          <form onSubmit={submit} className="space-y-4">
            <div className="relative">
              <input id="fp-email" type="email" required value={email} onChange={(e) => setEmail(e.target.value)} autoComplete="email" placeholder=" " className={input} />
              <label htmlFor="fp-email" className="pointer-events-none absolute -top-2.5 start-3 bg-white px-1.5 text-xs font-semibold text-slate-500">{t.auth.emailLabel}</label>
            </div>
            <button disabled={busy} className="w-full rounded-lg bg-gradient-to-b from-vox-pink to-vox-pink-dark py-3.5 text-[15px] font-semibold transition hover:-translate-y-px disabled:opacity-50">
              {busy ? t.auth.sending : t.auth.sendLink}
            </button>
            <p className="text-center text-sm text-white/55"><Link to="/login" className="font-medium text-vox-blue hover:underline">{t.auth.backToLogin}</Link></p>
          </form>
        )}
      </div>
    </div>
  );
}
