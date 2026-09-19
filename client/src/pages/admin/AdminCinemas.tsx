import { useEffect, useState } from 'react';
import { api, apiError } from '../../services/api';
import { Cinema, Hall } from '../../types';

const FORMATS = ['Standard', 'IMAX', 'MAX', 'GOLD', '4DX', 'KIDS'];

export default function AdminCinemas() {
  const [cinemas, setCinemas] = useState<Cinema[]>([]);
  const [halls, setHalls] = useState<Hall[]>([]);
  const [msg, setMsg] = useState('');
  const [error, setError] = useState('');
  const [form, setForm] = useState({ name: '', city: '', address: '' });
  const [editing, setEditing] = useState<number | null>(null);
  const [hallForm, setHallForm] = useState({ cinema_id: '', name: '', format: 'Standard', row_labels: 'A,B,C,D,E,F,G', seats_per_row: '10' });
  const [showHallForm, setShowHallForm] = useState<number | null>(null);

  const load = () => {
    api.get('/cinemas').then(({ data }) => setCinemas(data.cinemas)).catch((e) => setError(apiError(e)));
    api.get('/cinemas/halls').then(({ data }) => setHalls(data.halls)).catch(() => undefined);
  };
  useEffect(load, []);

  const submitCinema = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editing) {
        await api.put(`/cinemas/${editing}`, form);
        setMsg('Cinema updated.');
      } else {
        await api.post('/cinemas', form);
        setMsg('Cinema created.');
      }
      setForm({ name: '', city: '', address: '' });
      setEditing(null);
      load();
    } catch (err) {
      setMsg(apiError(err));
    }
  };

  const removeCinema = async (id: number) => {
    if (!confirm('Delete this cinema and its halls? (Blocked if showtimes exist.)')) return;
    try {
      await api.delete(`/cinemas/${id}`);
      setMsg('Cinema deleted.');
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  const submitHall = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const { data } = await api.post('/cinemas/halls', {
        cinema_id: Number(hallForm.cinema_id),
        name: hallForm.name,
        format: hallForm.format,
        row_labels: hallForm.row_labels,
        seats_per_row: Number(hallForm.seats_per_row),
      });
      setMsg(data.message);
      setHallForm({ cinema_id: '', name: '', format: 'Standard', row_labels: 'A,B,C,D,E,F,G', seats_per_row: '10' });
      setShowHallForm(null);
      load();
    } catch (err) {
      setMsg(apiError(err));
    }
  };

  const removeHall = async (id: number) => {
    if (!confirm('Delete this hall and its seats? (Blocked if showtimes exist.)')) return;
    try {
      await api.delete(`/cinemas/halls/${id}`);
      setMsg('Hall deleted.');
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  const input = 'w-full rounded-md border border-[#444] bg-black px-3 py-2 outline-none focus:border-vox text-sm';

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">Cinemas & Halls</h1>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#222] px-4 py-2 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}

      <form onSubmit={submitCinema} className="mb-8 flex flex-wrap items-end gap-3 rounded-lg border border-[#333] bg-[#1a1a1a] p-5">
        <div><label className="mb-1 block text-xs text-[#aaa]">Name</label>
          <input required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className={input} placeholder="Mall of Egypt" /></div>
        <div><label className="mb-1 block text-xs text-[#aaa]">City</label>
          <input required value={form.city} onChange={(e) => setForm({ ...form, city: e.target.value })} className={input} placeholder="Giza" /></div>
        <div className="min-w-[220px] flex-1"><label className="mb-1 block text-xs text-[#aaa]">Address</label>
          <input value={form.address} onChange={(e) => setForm({ ...form, address: e.target.value })} className={input} /></div>
        <button className="rounded-md bg-vox px-6 py-2 text-sm font-semibold hover:bg-vox-dark">{editing ? 'Update' : 'Add Cinema'}</button>
        {editing && <button type="button" onClick={() => { setEditing(null); setForm({ name: '', city: '', address: '' }); }} className="text-sm text-[#888] hover:text-white">Cancel</button>}
      </form>

      <div className="space-y-6">
        {cinemas.map((c) => (
          <div key={c.id} className="rounded-lg border border-[#333] bg-[#1a1a1a] p-5">
            <div className="flex flex-wrap items-center justify-between gap-3">
              <div>
                <h2 className="text-lg font-bold">{c.name} <span className="text-sm font-normal text-[#888]">— {c.city}</span></h2>
                <p className="text-xs text-[#888]">{c.address} • {c.hall_count} halls • {c.upcoming_count} upcoming showtimes</p>
              </div>
              <div className="flex gap-3 text-sm">
                <button onClick={() => { setEditing(c.id); setForm({ name: c.name, city: c.city, address: c.address }); }} className="text-vox-light hover:underline">Edit</button>
                <button onClick={() => removeCinema(c.id)} className="text-red-400 hover:underline">Delete</button>
                <button onClick={() => { setShowHallForm(showHallForm === c.id ? null : c.id); setHallForm((f) => ({ ...f, cinema_id: String(c.id) })); }} className="text-green-400 hover:underline">+ Hall</button>
              </div>
            </div>
            <div className="mt-3 overflow-x-auto">
              <table className="w-full text-left text-sm">
                <thead className="text-xs text-[#888]"><tr><th className="py-1.5 pr-4">Hall</th><th className="py-1.5 pr-4">Format</th><th className="py-1.5 pr-4">Seats</th><th className="py-1.5">Actions</th></tr></thead>
                <tbody>
                  {halls.filter((h) => h.cinema_id === c.id).map((h) => (
                    <tr key={h.id} className="border-t border-[#2a2a2a]">
                      <td className="py-1.5 pr-4">{h.name}</td>
                      <td className="py-1.5 pr-4"><span className="rounded bg-[#333] px-2 py-0.5 text-xs">{h.format}</span></td>
                      <td className="py-1.5 pr-4">{h.seat_count}</td>
                      <td className="py-1.5"><button onClick={() => removeHall(h.id)} className="text-red-400 hover:underline">Delete</button></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            {showHallForm === c.id && (
              <form onSubmit={submitHall} className="mt-3 flex flex-wrap items-end gap-3 rounded-md border border-[#333] bg-black/40 p-4">
                <div><label className="mb-1 block text-xs text-[#aaa]">Hall name</label>
                  <input required value={hallForm.name} onChange={(e) => setHallForm({ ...hallForm, name: e.target.value })} className={input} placeholder="Standard Hall 2" /></div>
                <div><label className="mb-1 block text-xs text-[#aaa]">Format</label>
                  <select value={hallForm.format} onChange={(e) => setHallForm({ ...hallForm, format: e.target.value })} className={input}>
                    {FORMATS.map((f) => <option key={f}>{f}</option>)}
                  </select></div>
                <div><label className="mb-1 block text-xs text-[#aaa]">Rows (A,B,C...)</label>
                  <input required value={hallForm.row_labels} onChange={(e) => setHallForm({ ...hallForm, row_labels: e.target.value })} className={input} /></div>
                <div><label className="mb-1 block text-xs text-[#aaa]">Seats/row</label>
                  <input type="number" min={1} max={30} required value={hallForm.seats_per_row} onChange={(e) => setHallForm({ ...hallForm, seats_per_row: e.target.value })} className={input} /></div>
                <button className="rounded-md bg-vox px-5 py-2 text-sm font-semibold hover:bg-vox-dark">Create Hall</button>
              </form>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
