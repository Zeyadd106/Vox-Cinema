import { useEffect, useState } from 'react';
import { api, apiError } from '../../services/api';

export default function AdminSettings() {
  const [form, setForm] = useState({ site_name: '', contact_email: '', phone_number: '', address: '', booking_fee: '0', tax_rate: '5' });
  const [msg, setMsg] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/admin/settings').then(({ data }) => setForm((f) => ({ ...f, ...data.settings }))).catch((e) => setError(apiError(e)));
  }, []);

  const set = (k: string) => (e: React.ChangeEvent<HTMLInputElement>) => setForm((f) => ({ ...f, [k]: e.target.value }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const { data } = await api.put('/admin/settings', { ...form, booking_fee: Number(form.booking_fee), tax_rate: Number(form.tax_rate) });
      setMsg(data.message);
    } catch (err) {
      setMsg(apiError(err));
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2.5 outline-none focus:border-vox';

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">Settings</h1>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#222] px-4 py-2 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}
      <form onSubmit={submit} className="grid max-w-2xl gap-4 rounded-lg border border-[#333] bg-[#1a1a1a] p-8">
        <div><label className="mb-1 block text-sm text-[#aaa]">Site Name</label><input value={form.site_name} onChange={set('site_name')} className={input} /></div>
        <div><label className="mb-1 block text-sm text-[#aaa]">Contact Email</label><input value={form.contact_email} onChange={set('contact_email')} className={input} /></div>
        <div><label className="mb-1 block text-sm text-[#aaa]">Phone</label><input value={form.phone_number} onChange={set('phone_number')} className={input} /></div>
        <div><label className="mb-1 block text-sm text-[#aaa]">Address</label><input value={form.address} onChange={set('address')} className={input} /></div>
        <div className="grid grid-cols-2 gap-4">
          <div><label className="mb-1 block text-sm text-[#aaa]">Booking Fee</label><input type="number" min={0} step="0.01" value={form.booking_fee} onChange={set('booking_fee')} className={input} /></div>
          <div><label className="mb-1 block text-sm text-[#aaa]">Tax Rate (%)</label><input type="number" min={0} max={100} step="0.01" value={form.tax_rate} onChange={set('tax_rate')} className={input} /></div>
        </div>
        <button className="rounded-md bg-vox py-3 font-semibold hover:bg-vox-dark">Save Settings</button>
      </form>
    </div>
  );
}
