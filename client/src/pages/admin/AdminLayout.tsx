import { Link, NavLink, Outlet } from 'react-router-dom';
import { useLang } from '../../context/LangContext';

export default function AdminLayout() {
  const { t } = useLang();
  const link = ({ isActive }: { isActive: boolean }) =>
    `block rounded-md px-4 py-2.5 text-sm font-medium transition ${isActive ? 'bg-vox text-white' : 'text-[#ccc] hover:bg-[#2a2a2a] hover:text-white'}`;
  return (
    <div className="mx-auto grid max-w-7xl gap-8 px-[6%] py-12 lg:grid-cols-[240px_1fr]">
      <aside className="h-fit rounded-lg border border-[#333] bg-[#1a1a1a] p-4 lg:sticky lg:top-24">
        <h2 className="mb-4 px-2 text-lg font-bold text-vox">{t.admin.panel}</h2>
        <nav className="space-y-1">
          <NavLink to="/admin" end className={link}>{t.admin.dashboard}</NavLink>
          <NavLink to="/admin/movies" className={link}>{t.admin.movies}</NavLink>
          <NavLink to="/admin/cinemas" className={link}>{t.admin.cinemas}</NavLink>
          <NavLink to="/admin/showtimes" className={link}>{t.admin.showtimes}</NavLink>
          <NavLink to="/admin/bookings" className={link}>{t.admin.bookings}</NavLink>
          <NavLink to="/admin/check-in" className={link}>{t.admin.checkin}</NavLink>
          <NavLink to="/admin/users" className={link}>{t.admin.users}</NavLink>
          <NavLink to="/admin/settings" className={link}>{t.admin.settings}</NavLink>
        </nav>
        <Link to="/" className="mt-4 block px-2 text-sm text-[#888] hover:text-white">{t.admin.backToSite}</Link>
      </aside>
      <div className="min-w-0"><Outlet /></div>
    </div>
  );
}
