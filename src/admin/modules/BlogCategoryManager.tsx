import { useState, useEffect } from 'react';
import { getBlogCategories, saveBlogCategory, deleteBlogCategory, BlogCategory } from '../../lib/firestore';

export default function BlogCategoryManager() {
  const [cats, setCats] = useState<BlogCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [newName, setNewName] = useState('');
  const [editingId, setEditingId] = useState<string | null>(null);
  const [editingName, setEditingName] = useState('');
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 3000);
  };

  const load = async () => {
    setLoading(true);
    const data = await getBlogCategories();
    setCats(data);
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const handleAdd = async () => {
    if (!newName.trim()) return;
    setSaving(true);
    try {
      await saveBlogCategory({ name: newName.trim(), order: cats.length });
      setNewName('');
      await load();
      showToast('Category added!');
    } catch { showToast('Failed to add.', 'error'); }
    setSaving(false);
  };

  const handleUpdate = async (cat: BlogCategory) => {
    if (!editingName.trim()) return;
    setSaving(true);
    try {
      await saveBlogCategory({ ...cat, name: editingName.trim() });
      setEditingId(null);
      await load();
      showToast('Updated!');
    } catch { showToast('Failed to update.', 'error'); }
    setSaving(false);
  };

  const handleDelete = async (cat: BlogCategory) => {
    if (!confirm(`Delete category "${cat.name}"?`)) return;
    try {
      await deleteBlogCategory(cat.id!);
      await load();
      showToast('Deleted!');
    } catch { showToast('Failed to delete.', 'error'); }
  };

  const inputStyle: React.CSSProperties = {
    width: '100%', background: '#F9FAFB', border: '1px solid #E5E7EB',
    borderRadius: 8, padding: '10px 14px', fontFamily: 'Lexend, sans-serif',
    fontSize: 14, color: '#111827', outline: 'none', boxSizing: 'border-box',
    transition: 'border-color 0.2s',
  };

  return (
    <div style={{ maxWidth: 640 }}>
      {toast && (
        <div style={{
          position: 'fixed', bottom: 24, right: 24, zIndex: 9999,
          background: toast.type === 'success' ? '#1F8844' : '#EF4444',
          color: '#FFFFFF', padding: '12px 20px', borderRadius: 8,
          fontFamily: 'Lexend, sans-serif', fontSize: 14,
          boxShadow: '0 8px 24px rgba(0,0,0,0.15)',
        }}>{toast.msg}</div>
      )}

      <div style={{ marginBottom: 24 }}>
        <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24, color: '#111827' }}>Blog Categories</h1>
        <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', marginTop: 4 }}>
          Manage blog post categories used in the blog listing and editor
        </p>
      </div>

      {/* Add new */}
      <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20, marginBottom: 20 }}>
        <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 15, color: '#111827', marginBottom: 16 }}>Add New Category</div>
        <div style={{ display: 'flex', gap: 10 }}>
          <input style={{ ...inputStyle, flex: 1 }} value={newName} onChange={e => setNewName(e.target.value)}
            placeholder="e.g. Tech & Development" onKeyDown={e => e.key === 'Enter' && handleAdd()}
            onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
          <button onClick={handleAdd} disabled={saving || !newName.trim()} style={{
            background: '#1F8844', color: '#FFFFFF', border: 'none', borderRadius: 8,
            padding: '0 20px', cursor: newName.trim() ? 'pointer' : 'not-allowed',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, opacity: newName.trim() ? 1 : 0.5,
          }}>
            {saving ? 'Adding...' : '+ Add'}
          </button>
        </div>
      </div>

      {/* List */}
      <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, overflow: 'hidden' }}>
        {loading ? (
          <div style={{ padding: '32px', textAlign: 'center', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif' }}>Loading...</div>
        ) : cats.length === 0 ? (
          <div style={{ padding: '40px', textAlign: 'center', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif' }}>
            No categories yet. Add your first one above.
          </div>
        ) : (
          cats.map((cat, i) => (
            <div key={cat.id} style={{
              display: 'flex', alignItems: 'center', gap: 12, padding: '14px 20px',
              borderBottom: i < cats.length - 1 ? '1px solid #F3F4F6' : 'none',
            }}>
              <div style={{ width: 32, height: 32, background: '#F3F4F6', borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#1F8844', flexShrink: 0 }}>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
              </div>

              {editingId === cat.id ? (
                <>
                  <input style={{ ...inputStyle, flex: 1 }} value={editingName} onChange={e => setEditingName(e.target.value)} autoFocus
                    onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                  <button onClick={() => handleUpdate(cat)} disabled={saving} style={{ background: '#1F8844', color: '#FFF', border: 'none', borderRadius: 6, padding: '7px 14px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 13 }}>
                    {saving ? '...' : 'Save'}
                  </button>
                  <button onClick={() => setEditingId(null)} style={{ background: 'transparent', color: '#6B7280', border: '1px solid #E5E7EB', borderRadius: 6, padding: '7px 12px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 13 }}>
                    Cancel
                  </button>
                </>
              ) : (
                <>
                  <span style={{ flex: 1, fontFamily: 'Lexend, sans-serif', fontSize: 15, color: '#111827' }}>{cat.name}</span>
                  <button onClick={() => { setEditingId(cat.id!); setEditingName(cat.name); }}
                    style={{ background: 'transparent', border: '1px solid #E5E7EB', borderRadius: 6, padding: '6px 12px', cursor: 'pointer', color: '#6B7280', fontFamily: 'Lexend, sans-serif', fontSize: 13 }}>
                    Edit
                  </button>
                  <button onClick={() => handleDelete(cat)}
                    style={{ background: 'transparent', border: 'none', color: '#D1D5DB', cursor: 'pointer', padding: 6, transition: 'color 0.15s' }}
                    onMouseEnter={e => (e.currentTarget as HTMLElement).style.color = '#EF4444'}
                    onMouseLeave={e => (e.currentTarget as HTMLElement).style.color = '#D1D5DB'}
                  >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  </button>
                </>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
}
