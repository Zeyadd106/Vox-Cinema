import { Link, NavLink, Outlet } from 'react-router-dom';

export default function AdminLayout() {
  const link = ({ isActive }: { isActive: boolean }) =>
    `block rounded-md px-4 py-2.5 text-sm font-medium transition ${isActive ? 'bg-vox text-white' : 'text-[#ccc] hover:bg-[#2a2a2a] hover:text-white'}`;
  return (
    <div className="mx-auto grid max-w-7xl gap-8 px-[6%] py-12 lg:grid-cols-[240px_1fr]">
      <aside className="h-fit rounded-lg border border-[#333] bg-[#1a1a1a] p-4 lg:sticky lg:top-24">
        <h2 className="mb-4 px-2 text-lg font-bold text-vox">Admin Panel</h2>
        <nav className="space-y-1">
          <NavLink to="/admin" end className={link}>Dashboard</NavLink>
          <NavLink to="/admin/movies" className={link}>Movies</NavLink>
          <NavLink to="/admin/cinemas" className={link}>Cinemas & Halls</NavLink>
          <NavLink to="/admin/showtimes" className={link}>Showtimes</NavLink>
          <NavLink to="/admin/bookings" className={link}>Bookings</NavLink>
          <NavLink to="/admin/check-in" className={link}>Check-in</NavLink>
          <NavLink to="/admin/users" className={link}>Users</NavLink>
          <NavLink to="/admin/settings" className={link}>Settings</NavLink>
        </nav>
        <Link to="/" className="mt-4 block px-2 text-sm text-[#888] hover:text-white">← Back to site</Link>
      </aside>
      <div className="min-w-0"><Outlet /></div>
    </div>
  );
}
