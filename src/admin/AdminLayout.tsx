import { useState } from 'react';
import { NavLink, Outlet, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const navItems = [
  {
    href: '/admin/dashboard', label: 'Dashboard',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>,
  },
  {
    href: '/admin/navigation', label: 'Navigation',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>,
  },
  {
    href: '/admin/blogs', label: 'Blog Posts',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
  },
  {
    href: '/admin/categories', label: 'Categories',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>,
  },
  {
    href: '/admin/footer', label: 'Footer',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="17" x2="21" y2="17"/></svg>,
  },
  {
    href: '/admin/contact', label: 'Contact Info',
    icon: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>,
  },
];

export default function AdminLayout() {
  const { logout, currentUser } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [collapsed, setCollapsed] = useState(false);

  const handleLogout = async () => {
    await logout();
    navigate('/admin/login');
  };

  return (
    <div style={{ display: 'flex', minHeight: '100vh', background: '#F3F4F6', fontFamily: 'Lexend, sans-serif' }}>
      {/* Sidebar */}
      <aside style={{
        width: collapsed ? 72 : 240, flexShrink: 0,
        background: '#0A0C0F', display: 'flex', flexDirection: 'column',
        transition: 'width 0.25s ease', overflow: 'hidden',
        borderRight: '1px solid #1F2937', position: 'fixed', top: 0, left: 0, bottom: 0, zIndex: 100,
      }}>
        {/* Logo */}
        <div style={{
          padding: collapsed ? '20px 0' : '20px 20px',
          borderBottom: '1px solid #1F2937', display: 'flex', alignItems: 'center',
          justifyContent: collapsed ? 'center' : 'space-between',
        }}>
          {!collapsed && (
            <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 18, color: '#FFFFFF', letterSpacing: '-0.03em', whiteSpace: 'nowrap' }}>
              DevBotics<span style={{ color: '#1F8844' }}>.</span>
            </span>
          )}
          <button onClick={() => setCollapsed(!collapsed)} style={{
            background: 'transparent', border: 'none', color: '#4B5563',
            cursor: 'pointer', padding: 4, display: 'flex', alignItems: 'center', flexShrink: 0,
          }}>
            {collapsed
              ? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 18l6-6-6-6"/></svg>
              : <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M15 18l-6-6 6-6"/></svg>
            }
          </button>
        </div>

        {/* Nav items */}
        <nav style={{ flex: 1, padding: '12px 0', overflowY: 'auto', overflowX: 'hidden' }}>
          {navItems.map(item => {
            const isActive = location.pathname === item.href || location.pathname.startsWith(item.href + '/');
            return (
              <NavLink key={item.href} to={item.href} style={{
                display: 'flex', alignItems: 'center', gap: 12,
                padding: collapsed ? '12px 0' : '12px 20px',
                justifyContent: collapsed ? 'center' : 'flex-start',
                color: isActive ? '#1F8844' : '#6B7280',
                background: isActive ? 'rgba(31,136,68,0.08)' : 'transparent',
                textDecoration: 'none', fontSize: 14, fontWeight: isActive ? 500 : 400,
                transition: 'all 0.15s ease', whiteSpace: 'nowrap',
                borderLeft: isActive ? '2px solid #1F8844' : '2px solid transparent',
              }}
                onMouseEnter={e => { if (!isActive) { (e.currentTarget as HTMLElement).style.color = '#9CA3AF'; (e.currentTarget as HTMLElement).style.background = 'rgba(255,255,255,0.03)'; } }}
                onMouseLeave={e => { if (!isActive) { (e.currentTarget as HTMLElement).style.color = '#6B7280'; (e.currentTarget as HTMLElement).style.background = 'transparent'; } }}
              >
                <span style={{ flexShrink: 0 }}>{item.icon}</span>
                {!collapsed && <span>{item.label}</span>}
              </NavLink>
            );
          })}
        </nav>

        {/* User + Logout */}
        <div style={{ borderTop: '1px solid #1F2937', padding: collapsed ? '16px 0' : '16px 20px' }}>
          {!collapsed && (
            <div style={{ marginBottom: 12 }}>
              <div style={{ fontSize: 11, color: '#4B5563', marginBottom: 2, textTransform: 'uppercase', letterSpacing: '0.08em' }}>Logged in as</div>
              <div style={{ fontSize: 13, color: '#9CA3AF', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{currentUser?.email}</div>
            </div>
          )}
          <button onClick={handleLogout} style={{
            width: collapsed ? 40 : '100%', height: collapsed ? 40 : 'auto',
            display: 'flex', alignItems: 'center', justifyContent: collapsed ? 'center' : 'flex-start',
            gap: 8, background: 'rgba(239,68,68,0.08)', border: '1px solid rgba(239,68,68,0.15)',
            borderRadius: 6, color: '#EF4444', cursor: 'pointer', padding: collapsed ? '0' : '8px 12px',
            fontFamily: 'Lexend, sans-serif', fontSize: 13, transition: 'all 0.2s',
            margin: collapsed ? '0 auto' : '0',
          }}
            onMouseEnter={e => (e.currentTarget.style.background = 'rgba(239,68,68,0.15)')}
            onMouseLeave={e => (e.currentTarget.style.background = 'rgba(239,68,68,0.08)')}
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            {!collapsed && 'Logout'}
          </button>
        </div>
      </aside>

      {/* Main content */}
      <div style={{ flex: 1, marginLeft: collapsed ? 72 : 240, transition: 'margin-left 0.25s ease', display: 'flex', flexDirection: 'column', minHeight: '100vh' }}>
        {/* Top bar */}
        <header style={{
          background: '#FFFFFF', borderBottom: '1px solid #E5E7EB',
          padding: '0 32px', height: 60, display: 'flex', alignItems: 'center',
          justifyContent: 'space-between', position: 'sticky', top: 0, zIndex: 50,
        }}>
          <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 15, color: '#111827' }}>
            {navItems.find(n => location.pathname.startsWith(n.href))?.label ?? 'Admin'}
          </div>
          <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
            <a href="/" target="_blank" rel="noopener noreferrer" style={{
              display: 'flex', alignItems: 'center', gap: 6,
              color: '#4B5563', fontSize: 13, textDecoration: 'none',
              padding: '6px 12px', border: '1px solid #E5E7EB', borderRadius: 6,
              transition: 'all 0.2s',
            }}
              onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; }}
              onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.color = '#4B5563'; }}
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              View Site
            </a>
            <div style={{
              width: 36, height: 36, borderRadius: '50%',
              background: '#1F8844', display: 'flex', alignItems: 'center',
              justifyContent: 'center', color: '#FFFFFF', fontFamily: 'Montserrat, sans-serif',
              fontWeight: 700, fontSize: 13,
            }}>
              {currentUser?.email?.[0]?.toUpperCase() ?? 'A'}
            </div>
          </div>
        </header>

        {/* Page content */}
        <main style={{ flex: 1, padding: 32 }}>
          <Outlet />
        </main>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Lexend:wght@300;400;500;600&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1F2937; border-radius: 4px; }
      `}</style>
    </div>
  );
}
