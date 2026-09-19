import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { apiError } from '../services/api';

export default function Register() {
  const { register } = useAuth();
  const navigate = useNavigate();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (password !== confirm) {
      setError('Passwords do not match');
      return;
    }
    setError('');
    setBusy(true);
    try {
      await register(name, email, password);
      navigate('/login');
    } catch (err) {
      setError(apiError(err));
    } finally {
      setBusy(false);
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox';

  return (
    <div className="mx-auto max-w-md px-6 py-16">
      <h1 className="mb-8 text-center text-3xl font-bold text-vox">Register</h1>
      <form onSubmit={submit} className="space-y-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        {error && <p className="rounded bg-red-950 px-3 py-2 text-sm text-red-300">{error}</p>}
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Name (min 3 chars)</label>
          <input required minLength={3} value={name} onChange={(e) => setName(e.target.value)} className={input} />
        </div>
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Email</label>
          <input type="email" required value={email} onChange={(e) => setEmail(e.target.value)} className={input} />
        </div>
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Password (min 8 chars)</label>
          <input type="password" required minLength={8} value={password} onChange={(e) => setPassword(e.target.value)} className={input} />
        </div>
        <div>
          <label className="mb-1 block text-sm text-[#aaa]">Confirm Password</label>
          <input type="password" required value={confirm} onChange={(e) => setConfirm(e.target.value)} className={input} />
        </div>
        <button disabled={busy} className="w-full rounded-md bg-vox py-3 font-semibold transition hover:bg-vox-dark disabled:opacity-50">
          {busy ? 'Registering...' : 'Register'}
        </button>
        <p className="text-center text-sm text-[#999]">Have an account? <Link to="/login" className="text-vox-light hover:underline">Login</Link></p>
      </form>
    </div>
  );
}
