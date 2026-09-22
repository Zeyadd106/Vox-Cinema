import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api, apiError } from '../services/api';
import { useLang } from '../context/LangContext';

export default function ResetPassword() {
  const { t } = useLang();
  const { token } = useParams();
  const [valid, setValid] = useState<boolean | null>(null);
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [error, setError] = useState('');
  const [done, setDone] = useState(false);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    api.get(`/auth/reset/${token}`)
      .then(() => setValid(true))
      .catch(() => setValid(false));
  }, [token]);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (password !== confirm) {
      setError(t.auth.mismatch);
      return;
    }
    setError('');
    setBusy(true);
    try {
      await api.post('/auth/reset', { token, password });
      setDone(true);
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const input =
    'w-full rounded-lg border border-slate-300 bg-white px-4 pb-2.5 pt-6 text-[15px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-vox-pink focus:ring-2 focus:ring-vox-pink/25';

  return (
    <div className="bg-[#0b0b0e] text-white">
      <div className="mx-auto flex min-h-[calc(100vh-220px)] max-w-[460px] flex-col justify-center px-6 py-12">
        <h1 className="text-[26px] font-bold">{t.auth.resetTitle}</h1>
        {valid === null && <p className="mt-4 text-sm text-white/55">{t.auth.verifying}</p>}
        {valid === false && (
          <div className="mt-4 rounded-xl border border-red-500/30 bg-red-500/10 p-6 text-center">
            <p className="font-semibold text-red-300">{t.auth.invalidLink}</p>
            <Link to="/forgot-password" className="mt-4 inline-block font-medium text-vox-blue hover:underline">{t.auth.requestNew}</Link>
          </div>
        )}
        {valid && !done && (
          <form onSubmit={submit} className="mt-6 space-y-4">
            {error && <p className="rounded-lg bg-red-500/10 px-4 py-2.5 text-sm font-medium text-red-400 ring-1 ring-red-500/30">{error}</p>}
            <div className="relative">
              <input id="np-pass" type="password" required minLength={8} value={password} onChange={(e) => setPassword(e.target.value)} autoComplete="new-password" placeholder=" " className={input} />
              <label htmlFor="np-pass" className="pointer-events-none absolute -top-2.5 start-3 bg-white px-1.5 text-xs font-semibold text-slate-500">{t.auth.newPass}</label>
            </div>
            <div className="relative">
              <input id="np-confirm" type="password" required value={confirm} onChange={(e) => setConfirm(e.target.value)} autoComplete="new-password" placeholder=" " className={input} />
              <label htmlFor="np-confirm" className="pointer-events-none absolute -top-2.5 start-3 bg-white px-1.5 text-xs font-semibold text-slate-500">{t.auth.confirmPass}</label>
            </div>
            <button disabled={busy} className="w-full rounded-lg bg-gradient-to-b from-vox-pink to-vox-pink-dark py-3.5 text-[15px] font-semibold transition hover:-translate-y-px disabled:opacity-50">
              {busy ? t.auth.updating : t.auth.updatePass}
            </button>
          </form>
        )}
        {valid && done && (
          <div className="mt-4 rounded-xl border border-green-500/30 bg-green-500/10 p-6 text-center">
            <p className="font-semibold text-green-300">{t.auth.passUpdated}</p>
            <Link to="/login" className="mt-4 inline-block rounded-lg bg-gradient-to-b from-vox-pink to-vox-pink-dark px-8 py-2.5 text-sm font-semibold">{t.auth.loginBtn}</Link>
          </div>
        )}
      </div>
    </div>
  );
}
