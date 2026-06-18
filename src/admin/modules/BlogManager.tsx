import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { saveBlog, getBlogBySlug, getBlogCategories, BlogPost, BlogCategory } from '../../lib/firestore';
import { getBlogs, deleteBlog } from '../../lib/firestore';
import { Link } from 'react-router-dom';
import RichEditor from './RichEditor';
import { Timestamp } from 'firebase/firestore';

// ─── Blog List ────────────────────────────────────────────────────────────────
export function BlogManager() {
  const [blogs, setBlogs] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState<'all' | 'published' | 'draft'>('all');
  const navigate = useNavigate();

  const load = async () => {
    setLoading(true);
    const all = await getBlogs();
    setBlogs(all);
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const handleDelete = async (id: string, title: string) => {
    if (!confirm(`Delete "${title}"? This cannot be undone.`)) return;
    await deleteBlog(id);
    load();
  };

  const filtered = filter === 'all' ? blogs : blogs.filter(b => b.status === filter);

  const formatDate = (d: any) => {
    try {
      if (d?.toDate) return d.toDate().toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
      return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch { return '—'; }
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
        <div>
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24, color: '#111827' }}>Blog Posts</h1>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', marginTop: 4 }}>
            {blogs.length} total · {blogs.filter(b => b.status === 'published').length} published
          </p>
        </div>
        <Link to="/admin/blogs/new" style={{
          display: 'flex', alignItems: 'center', gap: 8,
          background: '#1F8844', color: '#FFFFFF', border: 'none', textDecoration: 'none',
          borderRadius: 8, padding: '10px 18px', fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500,
        }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          New Post
        </Link>
      </div>

      {/* Filter tabs */}
      <div style={{ display: 'flex', gap: 4, marginBottom: 20, background: '#F3F4F6', borderRadius: 8, padding: 4, width: 'fit-content' }}>
        {(['all', 'published', 'draft'] as const).map(f => (
          <button key={f} onClick={() => setFilter(f)} style={{
            padding: '7px 16px', borderRadius: 6, border: 'none', cursor: 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 13, fontWeight: filter === f ? 500 : 400,
            background: filter === f ? '#FFFFFF' : 'transparent',
            color: filter === f ? '#111827' : '#6B7280',
            boxShadow: filter === f ? '0 1px 4px rgba(0,0,0,0.08)' : 'none',
            textTransform: 'capitalize', transition: 'all 0.15s',
          }}>{f}</button>
        ))}
      </div>

      {loading ? (
        <div style={{ textAlign: 'center', padding: '48px 0', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif' }}>Loading blog posts...</div>
      ) : filtered.length === 0 ? (
        <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: '60px 0', textAlign: 'center' }}>
          <div style={{ color: '#D1D5DB', marginBottom: 12 }}>
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" style={{ margin: '0 auto', display: 'block' }}><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
          </div>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#9CA3AF', marginBottom: 16 }}>No posts found</p>
          <Link to="/admin/blogs/new" style={{ color: '#1F8844', fontSize: 14, textDecoration: 'none' }}>Create your first post →</Link>
        </div>
      ) : (
        <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, overflow: 'hidden' }}>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ background: '#F9FAFB', borderBottom: '1px solid #E5E7EB' }}>
                {['Title', 'Category', 'Status', 'Date', 'Actions'].map(h => (
                  <th key={h} style={{ padding: '12px 16px', textAlign: 'left', fontFamily: 'Lexend, sans-serif', fontSize: 12, fontWeight: 500, color: '#6B7280', letterSpacing: '0.05em', textTransform: 'uppercase' }}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {filtered.map((blog, i) => (
                <tr key={blog.id} style={{ borderBottom: i < filtered.length - 1 ? '1px solid #F3F4F6' : 'none' }}>
                  <td style={{ padding: '14px 16px' }}>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 500, fontSize: 14, color: '#111827', marginBottom: 2 }}>{blog.title}</div>
                    <div style={{ fontFamily: 'Lexend, monospace', fontSize: 11, color: '#9CA3AF' }}>/{blog.slug}</div>
                  </td>
                  <td style={{ padding: '14px 16px' }}>
                    <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', background: '#F3F4F6', padding: '3px 8px', borderRadius: 4 }}>{blog.category || '—'}</span>
                  </td>
                  <td style={{ padding: '14px 16px' }}>
                    <span style={{
                      padding: '4px 10px', borderRadius: 100, fontSize: 12, fontFamily: 'Lexend, sans-serif',
                      background: blog.status === 'published' ? 'rgba(31,136,68,0.1)' : 'rgba(245,158,11,0.1)',
                      color: blog.status === 'published' ? '#1F8844' : '#D97706',
                    }}>{blog.status}</span>
                  </td>
                  <td style={{ padding: '14px 16px', fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#9CA3AF' }}>{formatDate(blog.createdAt)}</td>
                  <td style={{ padding: '14px 16px' }}>
                    <div style={{ display: 'flex', gap: 8 }}>
                      <Link to={`/admin/blogs/${blog.id}/edit`} style={{
                        display: 'flex', alignItems: 'center', gap: 6, color: '#374151',
                        textDecoration: 'none', border: '1px solid #E5E7EB', borderRadius: 6,
                        padding: '6px 12px', fontFamily: 'Lexend, sans-serif', fontSize: 13,
                        transition: 'all 0.15s',
                      }}
                        onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; }}
                        onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.color = '#374151'; }}
                      >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                      </Link>
                      <button onClick={() => handleDelete(blog.id!, blog.title)} style={{
                        display: 'flex', alignItems: 'center', background: 'transparent',
                        border: '1px solid #E5E7EB', borderRadius: 6, padding: '6px 10px',
                        color: '#9CA3AF', cursor: 'pointer', transition: 'all 0.15s',
                      }}
                        onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#EF4444'; (e.currentTarget as HTMLElement).style.color = '#EF4444'; }}
                        onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.color = '#9CA3AF'; }}
                      >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

// ─── Blog Editor ──────────────────────────────────────────────────────────────
export function BlogEditor() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isNew = id === 'new' || !id;

  const [categories, setCategories] = useState<BlogCategory[]>([]);
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);
  const [form, setForm] = useState<BlogPost>({
    title: '', slug: '', excerpt: '', body: '', category: '',
    tags: [], coverImage: '', author: 'DevBotics Team',
    status: 'draft', createdAt: new Date(), updatedAt: new Date(), readTime: '',
  });

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 3000);
  };

  useEffect(() => {
    getBlogCategories().then(cats => setCategories(cats));
    if (!isNew && id) {
      // Load existing — need to get by ID not slug for edit
      getBlogs().then(all => {
        const found = all.find(b => b.id === id);
        if (found) setForm(found);
      });
    }
  }, [id]);

  const autoSlug = (title: string) =>
    title.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();

  const handleTitleChange = (title: string) => {
    setForm(f => ({ ...f, title, slug: isNew ? autoSlug(title) : f.slug }));
  };

  const handleSave = async (status: 'draft' | 'published') => {
    if (!form.title || !form.slug) { showToast('Title and slug are required', 'error'); return; }
    setSaving(true);
    try {
      const savedId = await saveBlog({ ...form, status });
      showToast(status === 'published' ? 'Post published!' : 'Draft saved!');
      if (isNew) setTimeout(() => navigate(`/admin/blogs/${savedId}/edit`), 1500);
    } catch {
      showToast('Failed to save. Try again.', 'error');
    }
    setSaving(false);
  };

  const inputStyle: React.CSSProperties = {
    width: '100%', background: '#FFFFFF', border: '1px solid #E5E7EB',
    borderRadius: 8, padding: '10px 14px', fontFamily: 'Lexend, sans-serif',
    fontSize: 14, color: '#111827', outline: 'none', boxSizing: 'border-box',
    transition: 'border-color 0.2s',
  };

  return (
    <div>
      {toast && (
        <div style={{
          position: 'fixed', bottom: 24, right: 24, zIndex: 9999,
          background: toast.type === 'success' ? '#1F8844' : '#EF4444',
          color: '#FFFFFF', padding: '12px 20px', borderRadius: 8,
          fontFamily: 'Lexend, sans-serif', fontSize: 14,
          boxShadow: '0 8px 24px rgba(0,0,0,0.15)',
        }}>{toast.msg}</div>
      )}

      {/* Header */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
          <button onClick={() => navigate('/admin/blogs')} style={{ background: 'transparent', border: 'none', color: '#6B7280', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 6, fontFamily: 'Lexend, sans-serif', fontSize: 14 }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M19 12H5"/><polyline points="12,19 5,12 12,5"/></svg>
            Back
          </button>
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 22, color: '#111827' }}>
            {isNew ? 'New Blog Post' : 'Edit Blog Post'}
          </h1>
        </div>
        <div style={{ display: 'flex', gap: 10 }}>
          <button onClick={() => handleSave('draft')} disabled={saving} style={{
            background: 'transparent', border: '1px solid #E5E7EB', color: '#374151',
            borderRadius: 8, padding: '9px 18px', cursor: 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, transition: 'all 0.2s',
          }}>
            {saving ? 'Saving...' : 'Save Draft'}
          </button>
          <button onClick={() => handleSave('published')} disabled={saving} style={{
            background: '#1F8844', color: '#FFFFFF', border: 'none',
            borderRadius: 8, padding: '9px 20px', cursor: saving ? 'not-allowed' : 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500, transition: 'all 0.2s',
          }}>
            {saving ? 'Publishing...' : 'Publish →'}
          </button>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 300px', gap: 24 }}>
        {/* Main editor */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {/* Title */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Post Title *</label>
            <input style={{ ...inputStyle, fontSize: 22, fontFamily: 'Montserrat, sans-serif', fontWeight: 700, border: 'none', padding: '0', borderRadius: 0 }}
              value={form.title} onChange={e => handleTitleChange(e.target.value)}
              placeholder="Enter post title..." />
          </div>

          {/* Slug */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>URL Slug *</label>
            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
              <span style={{ fontFamily: 'Lexend, monospace', fontSize: 13, color: '#9CA3AF' }}>/blog/</span>
              <input style={{ ...inputStyle, flex: 1, fontFamily: 'Lexend, monospace' }}
                value={form.slug} onChange={e => setForm(f => ({ ...f, slug: e.target.value }))}
                placeholder="your-post-slug"
                onFocus={e => e.target.style.borderColor = '#1F8844'}
                onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
            </div>
          </div>

          {/* Excerpt */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Excerpt (shown in blog listing)</label>
            <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 80 }}
              value={form.excerpt} onChange={e => setForm(f => ({ ...f, excerpt: e.target.value }))}
              placeholder="Short description of this post..."
              onFocus={e => e.target.style.borderColor = '#1F8844'}
              onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
          </div>

          {/* Rich Text Body */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, overflow: 'hidden' }}>
            <div style={{ padding: '14px 20px', borderBottom: '1px solid #E5E7EB', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', textTransform: 'uppercase', letterSpacing: '0.06em' }}>Post Body *</div>
            <RichEditor content={form.body} onChange={body => setForm(f => ({ ...f, body }))} />
          </div>
        </div>

        {/* Sidebar */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {/* Status */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 12, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Status</label>
            <div style={{ display: 'flex', gap: 8 }}>
              {(['draft', 'published'] as const).map(s => (
                <button key={s} onClick={() => setForm(f => ({ ...f, status: s }))} style={{
                  flex: 1, padding: '8px 0', borderRadius: 6, border: '1px solid',
                  borderColor: form.status === s ? (s === 'published' ? '#1F8844' : '#F59E0B') : '#E5E7EB',
                  background: form.status === s ? (s === 'published' ? 'rgba(31,136,68,0.1)' : 'rgba(245,158,11,0.1)') : 'transparent',
                  color: form.status === s ? (s === 'published' ? '#1F8844' : '#D97706') : '#6B7280',
                  fontFamily: 'Lexend, sans-serif', fontSize: 13, cursor: 'pointer', textTransform: 'capitalize',
                }}>{s}</button>
              ))}
            </div>
          </div>

          {/* Category */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Category</label>
            <select style={{ ...inputStyle }} value={form.category} onChange={e => setForm(f => ({ ...f, category: e.target.value }))}
              onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'}>
              <option value="">Select category...</option>
              {categories.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
            </select>
          </div>

          {/* Cover Image */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Cover Image URL</label>
            <input style={inputStyle} value={form.coverImage} onChange={e => setForm(f => ({ ...f, coverImage: e.target.value }))}
              placeholder="https://... or /images/..."
              onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
            {form.coverImage && (
              <img src={form.coverImage} alt="Cover preview" style={{ width: '100%', height: 140, objectFit: 'cover', borderRadius: 6, marginTop: 10, border: '1px solid #E5E7EB' }} />
            )}
          </div>

          {/* Meta */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Author</label>
            <input style={{ ...inputStyle, marginBottom: 12 }} value={form.author} onChange={e => setForm(f => ({ ...f, author: e.target.value }))}
              onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Read Time</label>
            <input style={inputStyle} value={form.readTime ?? ''} onChange={e => setForm(f => ({ ...f, readTime: e.target.value }))} placeholder="e.g. 8 min"
              onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
          </div>

          {/* Tags */}
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
            <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Tags (comma separated)</label>
            <input style={inputStyle} value={form.tags.join(', ')}
              onChange={e => setForm(f => ({ ...f, tags: e.target.value.split(',').map(t => t.trim()).filter(Boolean) }))}
              placeholder="React, Firebase, AI"
              onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
          </div>
        </div>
      </div>
    </div>
  );
}
