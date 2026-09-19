import { useEffect, useRef, useState } from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCinemas } from '../context/CinemaContext';
import { useLang } from '../context/LangContext';

function EgyptFlag() {
  return (
    <span className="inline-flex h-4 w-6 flex-col overflow-hidden rounded-[2px] ring-1 ring-black/10" title="Egypt">
      <span className="h-1/3 bg-[#ce1126]" />
      <span className="h-1/3 bg-white" />
      <span className="h-1/3 bg-black" />
    </span>
  );
}

interface DropItem {
  label: string;
  to: string;
}

function NavDrop({ label, to, items, mobile }: { label: string; to: string; items: DropItem[]; mobile?: boolean }) {
  const [open, setOpen] = useState(false);
  if (mobile) {
    return (
      <div className="border-b border-slate-100">
        <div className="flex items-center">
          <Link to={to} className="flex-1 px-5 py-3 text-sm font-semibold uppercase tracking-wide text-slate-900">{label}</Link>
          <button onClick={() => setOpen((o) => !o)} className="px-5 py-3 text-slate-500" aria-label="toggle submenu">{open ? '▴' : '▾'}</button>
        </div>
        {open && items.map((it) => (
          <Link key={it.label} to={it.to} className="block bg-slate-50 px-8 py-2.5 text-sm text-slate-700">{it.label}</Link>
        ))}
      </div>
    );
  }
  return (
    <div className="group relative" onMouseLeave={() => setOpen(false)}>
      <div className="flex items-center">
        <NavLink
          to={to}
          className={({ isActive }) => `flex items-center gap-1 px-4 py-4 text-[13px] font-semibold uppercase tracking-wider transition ${isActive ? 'text-vox-pink' : 'text-vox-ink hover:text-vox-pink'}`}
        >
          {label} <span className="text-[9px] text-slate-400">▼</span>
        </NavLink>
      </div>
      <div className="invisible absolute left-0 top-full z-50 min-w-[220px] translate-y-1 opacity-0 transition-all duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
        <div className="overflow-hidden rounded-b-lg border-t-2 border-vox-pink bg-white shadow-xl">
          {items.map((it) => (
            <Link key={it.label} to={it.to} className="block px-5 py-3 text-[13px] font-medium uppercase tracking-wide text-slate-700 transition hover:bg-slate-50 hover:text-vox-pink">
              {it.label}
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}

export default function Layout({ children }: { children: React.ReactNode }) {
  const { user, logout } = useAuth();
  const { cinemas, selectedId, setSelectedId, selectedName } = useCinemas();
  const { t, toggle } = useLang();
  const navigate = useNavigate();
  const [searchOpen, setSearchOpen] = useState(false);
  const [query, setQuery] = useState('');
  const [mobileOpen, setMobileOpen] = useState(false);
  const [userOpen, setUserOpen] = useState(false);
  const searchRef = useRef<HTMLInputElement>(null);
  const userRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (searchOpen) searchRef.current?.focus();
  }, [searchOpen]);

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') { setSearchOpen(false); setMobileOpen(false); setUserOpen(false); }
    };
    const onClick = (e: MouseEvent) => {
      if (userRef.current && !userRef.current.contains(e.target as Node)) setUserOpen(false);
    };
    document.addEventListener('keydown', onKey);
    document.addEventListener('mousedown', onClick);
    return () => { document.removeEventListener('keydown', onKey); document.removeEventListener('mousedown', onClick); };
  }, []);

  const submitSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setSearchOpen(false);
    setMobileOpen(false);
    navigate(`/movies?q=${encodeURIComponent(query.trim())}`);
    setQuery('');
  };

  const goFindTimes = () => {
    setMobileOpen(false);
    navigate('/');
    setTimeout(() => document.getElementById('find-times')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
  };

  const formats = ['IMAX', 'MAX', 'GOLD', '4DX', 'KIDS'];

  return (
    <div className="min-h-screen bg-black text-white">
      <header role="banner" className="sticky top-0 z-50 shadow-md">
        {/* ── Utility bar ─────────────────────────────── */}
        <div className="bg-vox-navy text-white">
          <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6">
            <Link to="/" className="flex items-center gap-2">
              <img src="/logo.png" alt="VOX Cinemas" className="h-9 w-9" onError={(e) => ((e.target as HTMLImageElement).style.display = 'none')} />
              <span className="text-xl font-bold uppercase tracking-[2px] text-vox-pink">Vox Cinemas</span>
            </Link>
            <nav className="flex items-center gap-4 text-[13px] font-medium">
              <button onClick={() => setSearchOpen((o) => !o)} title={t.search} aria-label={t.search} className="rounded-full p-1.5 transition hover:bg-white/10 hover:text-vox-pink">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round"><circle cx="11" cy="11" r="7" /><line x1="16.5" y1="16.5" x2="21" y2="21" /></svg>
              </button>
              {user ? (
                <div className="relative" ref={userRef}>
                  <button onClick={() => setUserOpen((o) => !o)} className="flex items-center gap-1.5 rounded-full border border-white/25 px-3 py-1.5 transition hover:border-vox-pink hover:text-vox-pink">
                    <span className="flex h-6 w-6 items-center justify-center rounded-full bg-vox-pink text-[11px] font-bold text-white">
                      {user.name.charAt(0).toUpperCase()}
                    </span>
                    <span className="hidden max-w-[100px] truncate sm:inline">{user.name.split(' ')[0]}</span>
                    <span className="text-[9px] text-white/60">▼</span>
                  </button>
                  {userOpen && (
                    <div className="absolute end-0 top-full z-50 mt-2 w-48 overflow-hidden rounded-lg bg-white py-1 text-slate-800 shadow-xl ring-1 ring-black/5">
                      <Link to="/dashboard" onClick={() => setUserOpen(false)} className="block px-4 py-2.5 transition hover:bg-slate-50 hover:text-vox-pink">{t.dashboard}</Link>
                      <Link to="/bookings" onClick={() => setUserOpen(false)} className="block px-4 py-2.5 transition hover:bg-slate-50 hover:text-vox-pink">{t.myBookings}</Link>
                      {user.is_admin && <Link to="/admin" onClick={() => setUserOpen(false)} className="block px-4 py-2.5 transition hover:bg-slate-50 hover:text-vox-pink">{t.admin}</Link>}
                      <button
                        onClick={() => { setUserOpen(false); logout(); navigate('/'); }}
                        className="block w-full px-4 py-2.5 text-start transition hover:bg-slate-50 hover:text-vox-pink"
                      >
                        {t.logout}
                      </button>
                    </div>
                  )}
                </div>
              ) : (
                <>
                  <Link to="/login" className="transition hover:text-vox-pink">{t.login}</Link>
                  <Link to="/register" className="hidden rounded-full bg-vox-pink px-4 py-1.5 font-semibold text-white transition hover:bg-vox-pink-dark sm:inline">{t.signUp}</Link>
                </>
              )}
              <span className="hidden items-center gap-1.5 md:flex" title={t.egypt}>
                <EgyptFlag />
                <span>{t.egypt}</span>
              </span>
              <button onClick={toggle} className="font-semibold text-vox-pink transition hover:text-white" title="Change language">
                {t.arabic}
              </button>
              <button className="p-1.5 lg:hidden" onClick={() => setMobileOpen((o) => !o)} aria-label="Navigation">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round"><line x1="4" y1="7" x2="20" y2="7" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="17" x2="20" y2="17" /></svg>
              </button>
            </nav>
          </div>
          {/* Search overlay */}
          {searchOpen && (
            <div className="border-t border-white/10">
              <form onSubmit={submitSearch} className="mx-auto flex max-w-7xl items-center gap-2 px-4 py-2.5 sm:px-6">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" strokeWidth="2.4" strokeLinecap="round"><circle cx="11" cy="11" r="7" /><line x1="16.5" y1="16.5" x2="21" y2="21" /></svg>
                <input
                  ref={searchRef}
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder={t.searchPlaceholder}
                  className="w-full bg-transparent text-[15px] text-white outline-none placeholder:text-white/40"
                />
                <button type="button" onClick={() => setSearchOpen(false)} className="text-sm font-medium text-white/50 hover:text-white">✕</button>
              </form>
            </div>
          )}
        </div>

        {/* ── Primary nav (desktop) ───────────────────── */}
        <nav className="hidden border-t border-slate-100 bg-white lg:block" aria-label="Primary">
          <div className="mx-auto flex max-w-7xl items-center px-4 sm:px-6">
            <NavLink to="/" end className={({ isActive }) => `px-4 py-4 text-[13px] font-semibold uppercase tracking-wider transition ${isActive ? 'text-vox-pink' : 'text-vox-ink hover:text-vox-pink'}`}>
              {t.home}
            </NavLink>
            <NavDrop label={t.movies} to="/movies" items={[
              { label: t.whatsOn, to: '/movies' },
              { label: t.comingSoon, to: '/coming-soon' },
            ]} />
            <NavDrop label={t.foodDrinks} to="/food-and-drinks" items={[{ label: t.preOrder, to: '/food-and-drinks' }]} />
            <NavDrop label={t.waysToWatch} to="/ways-to-watch" items={[
              { label: t.sensory, to: '/ways-to-watch/sensory-friendly' },
              ...formats.map((f) => ({ label: f, to: `/ways-to-watch/${f.toLowerCase()}` })),
            ]} />
            <NavDrop label={t.offers} to="/offers" items={[{ label: t.ladiesOffer, to: '/offers' }]} />
          </div>
        </nav>

        {/* ── Mobile flyout ───────────────────────────── */}
        {mobileOpen && (
          <nav className="max-h-[70vh] overflow-y-auto border-t border-slate-100 bg-white lg:hidden" aria-label="Mobile">
            <Link to="/" onClick={() => setMobileOpen(false)} className="block border-b border-slate-100 px-5 py-3 text-sm font-semibold uppercase tracking-wide text-slate-900">{t.home}</Link>
            <NavDrop mobile label={t.movies} to="/movies" items={[
              { label: t.whatsOn, to: '/movies' },
              { label: t.comingSoon, to: '/coming-soon' },
            ]} />
            <NavDrop mobile label={t.foodDrinks} to="/food-and-drinks" items={[{ label: t.preOrder, to: '/food-and-drinks' }]} />
            <NavDrop mobile label={t.waysToWatch} to="/ways-to-watch" items={[
              { label: t.sensory, to: '/ways-to-watch/sensory-friendly' },
              ...formats.map((f) => ({ label: f, to: `/ways-to-watch/${f.toLowerCase()}` })),
            ]} />
            <NavDrop mobile label={t.offers} to="/offers" items={[{ label: t.ladiesOffer, to: '/offers' }]} />
            {!user && (
              <div className="flex gap-3 p-5">
                <Link to="/login" onClick={() => setMobileOpen(false)} className="flex-1 rounded-full border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-800">{t.login}</Link>
                <Link to="/register" onClick={() => setMobileOpen(false)} className="flex-1 rounded-full bg-vox-pink px-4 py-2.5 text-center text-sm font-semibold text-white">{t.signUp}</Link>
              </div>
            )}
            <button onClick={goFindTimes} className="m-5 mt-0 w-[calc(100%-2.5rem)] rounded-full bg-vox-pink px-4 py-2.5 text-sm font-bold uppercase tracking-wide text-white">{t.findTimes}</button>
          </nav>
        )}

        {/* ── Cinema strip ────────────────────────────── */}
        <div className="bg-vox-strip text-white">
          <div className="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-2.5 sm:px-6">
            <span className="text-[13px] font-medium text-white/85">📍 {selectedName}</span>
            <select
              value={selectedId}
              onChange={(e) => setSelectedId(e.target.value === 'all' ? 'all' : Number(e.target.value))}
              aria-label={t.selectCinema}
              className="cursor-pointer rounded-md border border-white/25 bg-white/10 px-3 py-1.5 text-[13px] text-white outline-none focus:border-vox-pink [&>option]:text-slate-900"
            >
              <option value="all">{t.allCinemas}</option>
              {cinemas.map((c) => <option key={c.id} value={c.id}>{c.name} — {c.city}</option>)}
            </select>
            <button onClick={goFindTimes} className="ms-auto rounded-full bg-vox-pink px-6 py-2 text-[13px] font-bold uppercase tracking-wide text-white transition hover:bg-vox-pink-dark">
              {t.findTimes}
            </button>
          </div>
        </div>
      </header>

      <main>{children}</main>

      <footer className="border-t border-[#333] bg-[#1a1a1a] px-[6%] py-8 text-sm text-[#ccc]">
        <div className="mx-auto grid max-w-6xl gap-8 md:grid-cols-3">
          <div>
            <h3 className="mb-3 font-semibold uppercase tracking-wider text-vox">About Vox Cinemas</h3>
            <p>Bringing you the latest blockbusters and timeless classics. Experience the best in entertainment.</p>
          </div>
          <div>
            <h3 className="mb-3 font-semibold uppercase tracking-wider text-vox">Quick Links</h3>
            <ul className="space-y-2">
              <li><Link className="hover:text-vox-light" to="/">Home</Link></li>
              <li><Link className="hover:text-vox-light" to="/coming-soon">Coming Soon</Link></li>
              <li><Link className="hover:text-vox-light" to="/movies">Movies</Link></li>
            </ul>
          </div>
          <div>
            <h3 className="mb-3 font-semibold uppercase tracking-wider text-vox">Contact Us</h3>
            <p>info@voxcinemas.com</p>
            <p>+123 456 7890</p>
          </div>
        </div>
        <p className="mt-8 text-center text-xs text-[#888]">&copy; 2026 Vox Cinemas. All rights reserved.</p>
      </footer>
    </div>
  );
}
