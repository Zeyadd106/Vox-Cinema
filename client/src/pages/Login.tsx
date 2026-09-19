import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { apiError } from '../services/api';

export default function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      const user = await login(email, password);
      const from = (location.state as { from?: string })?.from;
      navigate(from ?? (user.is_admin ? '/admin' : '/'), { replace: true });
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="mx-auto max-w-md px-6 py-16">
      <h1 className="mb-8 text-center text-3xl font-bold text-vox">Login</h1>
      <form onSubmit={submit} className="space-y-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        {error && <p className="rounded bg-red-950 px-3 py-2 text-sm text-red-300">{error}</p>}
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Email</label>
          <input type="email" required value={email} onChange={(e) => setEmail(e.target.value)} className="w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox" />
        </div>
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Password</label>
          <input type="password" required value={password} onChange={(e) => setPassword(e.target.value)} className="w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox" />
        </div>
        <button disabled={busy} className="w-full rounded-md bg-vox py-3 font-semibold transition hover:bg-vox-dark disabled:opacity-50">
          {busy ? 'Logging in...' : 'Login'}
        </button>
        <p className="text-center text-sm text-[#999]">No account? <Link to="/register" className="text-vox-light hover:underline">Register</Link></p>
        <p className="text-center text-xs text-[#666]">Admin: admin@voxcinemas.com / password</p>
      </form>
    </div>
  );
}
