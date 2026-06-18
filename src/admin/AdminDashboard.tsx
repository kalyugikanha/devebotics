import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getBlogs, getBlogCategories } from '../lib/firestore';

function StatCard({ label, value, icon, color }: { label: string; value: string | number; icon: React.ReactNode; color: string }) {
  return (
    <div style={{
      background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10,
      padding: '24px', display: 'flex', alignItems: 'center', gap: 16,
    }}>
      <div style={{
        width: 48, height: 48, borderRadius: 10, background: color + '15',
        display: 'flex', alignItems: 'center', justifyContent: 'center', color,
        flexShrink: 0,
      }}>{icon}</div>
      <div>
        <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 28, color: '#111827', lineHeight: 1 }}>{value}</div>
        <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#6B7280', marginTop: 4 }}>{label}</div>
      </div>
    </div>
  );
}

function QuickLink({ to, label, desc, icon }: { to: string; label: string; desc: string; icon: React.ReactNode }) {
  return (
    <Link to={to} style={{
      background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10,
      padding: '20px', textDecoration: 'none', display: 'flex', alignItems: 'center',
      gap: 14, transition: 'all 0.2s',
    }}
      onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.boxShadow = '0 4px 16px rgba(31,136,68,0.1)'; }}
      onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.boxShadow = 'none'; }}
    >
      <div style={{ color: '#1F8844', flexShrink: 0 }}>{icon}</div>
      <div>
        <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 14, color: '#111827' }}>{label}</div>
        <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#9CA3AF', marginTop: 2 }}>{desc}</div>
      </div>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" strokeWidth="2" style={{ marginLeft: 'auto', flexShrink: 0 }}><path d="M9 18l6-6-6-6"/></svg>
    </Link>
  );
}

export default function AdminDashboard() {
  const [stats, setStats] = useState({ total: 0, published: 0, draft: 0, categories: 0 });
  const [loading, setLoading] = useState(true);
  const [recentBlogs, setRecentBlogs] = useState<any[]>([]);

  useEffect(() => {
    const load = async () => {
      try {
        const [allBlogs, cats] = await Promise.all([getBlogs(), getBlogCategories()]);
        const published = allBlogs.filter((b: any) => b.status === 'published').length;
        setStats({ total: allBlogs.length, published, draft: allBlogs.length - published, categories: cats.length });
        setRecentBlogs(allBlogs.slice(0, 5));
      } catch {}
      setLoading(false);
    };
    load();
  }, []);

  return (
    <div>
      {/* Header */}
      <div style={{ marginBottom: 32 }}>
        <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 28, color: '#111827', marginBottom: 6 }}>
          Dashboard
        </h1>
        <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280' }}>
          Welcome back, Akash. Here's what's happening with your website.
        </p>
      </div>

      {/* Stats */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16, marginBottom: 32 }}>
        <StatCard label="Total Blog Posts" value={loading ? '—' : stats.total}
          color="#1F8844" icon={<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>} />
        <StatCard label="Published" value={loading ? '—' : stats.published}
          color="#3B82F6" icon={<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>} />
        <StatCard label="Drafts" value={loading ? '—' : stats.draft}
          color="#F59E0B" icon={<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>} />
        <StatCard label="Categories" value={loading ? '—' : stats.categories}
          color="#8B5CF6" icon={<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>} />
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 340px', gap: 24 }}>
        {/* Recent blogs */}
        <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: '24px' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
            <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, color: '#111827' }}>Recent Blog Posts</div>
            <Link to="/admin/blogs/new" style={{
              background: '#1F8844', color: '#FFFFFF', textDecoration: 'none',
              padding: '7px 14px', borderRadius: 6, fontFamily: 'Lexend, sans-serif',
              fontSize: 13, fontWeight: 500, display: 'flex', alignItems: 'center', gap: 6,
            }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              New Post
            </Link>
          </div>
          {loading ? (
            <div style={{ color: '#9CA3AF', fontSize: 14, textAlign: 'center', padding: '24px 0' }}>Loading...</div>
          ) : recentBlogs.length === 0 ? (
            <div style={{ textAlign: 'center', padding: '40px 0' }}>
              <div style={{ color: '#D1D5DB', marginBottom: 12 }}>
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" style={{ margin: '0 auto', display: 'block' }}><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
              </div>
              <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#9CA3AF' }}>No blog posts yet</div>
              <Link to="/admin/blogs/new" style={{ color: '#1F8844', fontSize: 13, marginTop: 8, display: 'inline-block', textDecoration: 'none' }}>Create your first post →</Link>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column' }}>
              {recentBlogs.map((blog, i) => (
                <div key={blog.id} style={{
                  display: 'flex', alignItems: 'center', gap: 12, padding: '12px 0',
                  borderBottom: i < recentBlogs.length - 1 ? '1px solid #F3F4F6' : 'none',
                }}>
                  <div style={{ flex: 1, minWidth: 0 }}>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 500, fontSize: 14, color: '#111827', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{blog.title}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#9CA3AF', marginTop: 2 }}>{blog.category}</div>
                  </div>
                  <span style={{
                    padding: '3px 10px', borderRadius: 100, fontSize: 11, fontFamily: 'Lexend, sans-serif',
                    background: blog.status === 'published' ? 'rgba(31,136,68,0.1)' : 'rgba(245,158,11,0.1)',
                    color: blog.status === 'published' ? '#1F8844' : '#D97706',
                  }}>{blog.status}</span>
                  <Link to={`/admin/blogs/${blog.id}/edit`} style={{ color: '#9CA3AF', textDecoration: 'none', padding: 4, transition: 'color 0.15s' }}
                    onMouseEnter={e => (e.currentTarget as HTMLElement).style.color = '#1F8844'}
                    onMouseLeave={e => (e.currentTarget as HTMLElement).style.color = '#9CA3AF'}
                  >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </Link>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Quick actions */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
          <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, color: '#111827', marginBottom: 4 }}>Quick Actions</div>
          <QuickLink to="/admin/navigation" label="Edit Navigation" desc="Manage menu items & sub-menus"
            icon={<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>} />
          <QuickLink to="/admin/blogs/new" label="New Blog Post" desc="Write & publish a new article"
            icon={<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>} />
          <QuickLink to="/admin/footer" label="Edit Footer" desc="Update footer links & contact"
            icon={<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="17" x2="21" y2="17"/></svg>} />
          <QuickLink to="/admin/contact" label="Contact Details" desc="Update phone, email, addresses"
            icon={<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>} />
          <QuickLink to="/admin/categories" label="Blog Categories" desc="Add, rename or delete categories"
            icon={<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>} />
        </div>
      </div>
    </div>
  );
}
