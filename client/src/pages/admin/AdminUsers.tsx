import { useEffect, useState } from 'react';
import { api, apiError } from '../../services/api';
import { User } from '../../types';
import { useLang } from '../../context/LangContext';

interface AdminUser extends User {
  bookings_count: number;
  created_at: string;
}

export default function AdminUsers() {
  const { t, lang } = useLang();
  const [users, setUsers] = useState<AdminUser[]>([]);
  const [msg, setMsg] = useState('');
  const [error, setError] = useState('');

  const load = () => api.get('/admin/users').then(({ data }) => setUsers(data.users)).catch((e) => setError(apiError(e)));
  useEffect(() => { load(); }, []);

  const toggle = async (id: number) => {
    try {
      const { data } = await api.patch(`/admin/users/${id}/toggle-admin`);
      setMsg(data.message);
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  const remove = async (id: number) => {
    if (!confirm(t.admin.deleteUserConfirm)) return;
    try {
      await api.delete(`/admin/users/${id}`);
      setMsg(t.admin.userDeleted);
      load();
    } catch (e) {
      setMsg(apiError(e));
    }
  };

  const yesNo = (v: boolean) => v ? (lang === 'ar' ? 'نعم' : 'Yes') : (lang === 'ar' ? 'لا' : 'No');

  return (
    <div>
      <h1 className="mb-6 text-2xl font-bold">{t.admin.users}</h1>
      {msg && <p className="mb-4 rounded border border-[#444] bg-[#222] px-4 py-2 text-sm">{msg}</p>}
      {error && <p className="text-red-400">{error}</p>}
      <div className="overflow-x-auto rounded-lg border border-[#333]">
        <table className="w-full text-start text-sm">
          <thead className="bg-[#222] text-[#aaa]">
            <tr><th className="px-4 py-2.5">{t.admin.name}</th><th className="px-4 py-2.5">{t.admin.email}</th><th className="px-4 py-2.5">{t.admin.adminCol}</th><th className="px-4 py-2.5">{t.admin.bookingsCol}</th><th className="px-4 py-2.5">{t.common.actions}</th></tr>
          </thead>
          <tbody>
            {users.map((u) => (
              <tr key={u.id} className="border-t border-[#2a2a2a]">
                <td className="px-4 py-2.5 font-semibold">{u.name}</td>
                <td className="px-4 py-2.5">{u.email}</td>
                <td className="px-4 py-2.5">{yesNo(Boolean(u.is_admin))}</td>
                <td className="px-4 py-2.5">{u.bookings_count}</td>
                <td className="flex gap-3 px-4 py-2.5">
                  <button onClick={() => toggle(u.id)} className="text-vox-light hover:underline">{u.is_admin ? t.admin.revokeAdmin : t.admin.makeAdmin}</button>
                  <button onClick={() => remove(u.id)} className="text-red-400 hover:underline">{t.common.delete}</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

