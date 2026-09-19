import { Navigate, useLocation } from 'react-router-dom';
import type { ReactElement } from 'react';
import { useAuth } from '../context/AuthContext';

export function ProtectedRoute({ children, adminOnly }: { children: ReactElement; adminOnly?: boolean }) {
  const { user, loading } = useAuth();
  const location = useLocation();
  if (loading) return <div className="p-16 text-center text-[#999]">Loading...</div>;
  if (!user) return <Navigate to="/login" state={{ from: location.pathname }} replace />;
  if (adminOnly && !user.is_admin) return <Navigate to="/" replace />;
  return children;
}
