import { useState, useEffect } from 'react';
import { getNavItems, saveNavItem, deleteNavItem, NavItem } from '../../lib/firestore';

const DEFAULT_NAV: NavItem[] = [
  { label: 'Home', type: 'link', href: '/', order: 0 },
  { label: 'About Us', type: 'simple', order: 1, children: [{ label: 'About Us', href: '/about-us', order: 0 }, { label: 'Careers', href: '/careers', order: 1 }] },
  { label: 'Development Hub', type: 'mega', order: 2, children: [{ label: 'Custom Development', href: '/development/custom-development', order: 0 }, { label: 'Web Development', href: '/web-development', order: 1 }, { label: 'App Development', href: '/app-development', order: 2 }] },
  { label: 'AI Development', type: 'mega', order: 3, children: [{ label: 'AI Website Development', href: '/ai-services/ai-website-development', order: 0 }, { label: 'AI Software', href: '/ai-services/ai-software', order: 1 }, { label: 'WhatsApp Admin Development', href: '/ai-services/whatsapp-admin-development', order: 2 }] },
  { label: 'Blog', type: 'link', href: '/blog', order: 4 },
  { label: 'Contact', type: 'link', href: '/contact', order: 5 },
];

function Toast({ msg, type }: { msg: string; type: 'success' | 'error' }) {
  return (
    <div style={{
      position: 'fixed', bottom: 24, right: 24, zIndex: 9999,
      background: type === 'success' ? '#1F8844' : '#EF4444',
      color: '#FFFFFF', padding: '12px 20px', borderRadius: 8,
      fontFamily: 'Lexend, sans-serif', fontSize: 14,
      boxShadow: '0 8px 24px rgba(0,0,0,0.15)', animation: 'slideUp 0.3s ease',
    }}>
      {msg}
    </div>
  );
}

export default function NavigationManager() {
  const [items, setItems] = useState<NavItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [editingItem, setEditingItem] = useState<NavItem | null>(null);
  const [addingNew, setAddingNew] = useState(false);
  const [newItem, setNewItem] = useState<NavItem>({ label: '', type: 'link', href: '', order: 0 });
  const [newChildLabel, setNewChildLabel] = useState('');
  const [newChildHref, setNewChildHref] = useState('');

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 3000);
  };

  const load = async () => {
    setLoading(true);
    try {
      const nav = await getNavItems();
      setItems(nav.length > 0 ? nav : DEFAULT_NAV);
    } catch (err) {
      console.error("Load error:", err);
      setItems(DEFAULT_NAV);
    }
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const handleSaveItem = async (item: NavItem) => {
    setSaving(true);
    try {
      await saveNavItem(item);
      await load();
      setEditingItem(null);
      showToast('Navigation item saved!');
    } catch (err) {
      console.error("Save error:", err);
      showToast('Failed to save. Try again.', 'error');
    }
    setSaving(false);
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Delete this navigation item?')) return;
    try {
      await deleteNavItem(id);
      await load();
      showToast('Deleted successfully');
    } catch {
      showToast('Failed to delete.', 'error');
    }
  };

  const handleAddNew = async () => {
    if (!newItem.label) return;
    setSaving(true);
    try {
      await saveNavItem({ ...newItem, order: items.length });
      await load();
      setAddingNew(false);
      setNewItem({ label: '', type: 'link', href: '', order: 0 });
      showToast('Navigation item added!');
    } catch {
      showToast('Failed to add.', 'error');
    }
    setSaving(false);
  };

  const moveItem = async (index: number, dir: 'up' | 'down') => {
    const newItems = [...items];
    const swapIdx = dir === 'up' ? index - 1 : index + 1;
    if (swapIdx < 0 || swapIdx >= newItems.length) return;
    [newItems[index], newItems[swapIdx]] = [newItems[swapIdx], newItems[index]];
    for (let i = 0; i < newItems.length; i++) {
      await saveNavItem({ ...newItems[i], order: i });
    }
    await load();
  };

  const addChild = (item: NavItem) => {
    if (!newChildLabel || !newChildHref) return;
    const updated = {
      ...item,
      children: [...(item.children || []), { label: newChildLabel, href: newChildHref, order: (item.children || []).length }],
    };
    setEditingItem(updated);
    setNewChildLabel('');
    setNewChildHref('');
  };

  const removeChild = (item: NavItem, idx: number) => {
    const updated = { ...item, children: (item.children || []).filter((_, i) => i !== idx) };
    setEditingItem(updated);
  };

  const inputStyle: React.CSSProperties = {
    width: '100%', background: '#F9FAFB', border: '1px solid #E5E7EB',
    borderRadius: 6, padding: '9px 12px', fontFamily: 'Lexend, sans-serif',
    fontSize: 14, color: '#111827', outline: 'none', boxSizing: 'border-box',
    transition: 'border-color 0.2s',
  };

  const handleRestoreDefaults = async () => {
    setSaving(true);
    let added = 0;
    try {
      for (const def of DEFAULT_NAV) {
        if (!items.find(i => i.label === def.label)) {
          await saveNavItem({ ...def, order: items.length + added });
          added++;
        }
      }
      if (added > 0) {
        await load();
        showToast(`${added} default items restored!`);
      } else {
        showToast('All defaults are already present.');
      }
    } catch (err) {
      console.error(err);
      showToast('Failed to restore items.', 'error');
    }
    setSaving(false);
  };

  return (
    <div>
      {toast && <Toast msg={toast.msg} type={toast.type} />}

      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
        <div>
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24, color: '#111827' }}>Navigation Manager</h1>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', marginTop: 4 }}>Manage menu items, sub-menus, and mega menus</p>
        </div>
        <div style={{ display: 'flex', gap: 12 }}>
          <button onClick={handleRestoreDefaults} disabled={saving} style={{
            display: 'flex', alignItems: 'center', gap: 8,
            background: 'transparent', color: '#1F8844', border: '1px solid #1F8844',
            borderRadius: 8, padding: '10px 18px', cursor: 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500,
          }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Restore Missing
          </button>
          <button onClick={() => setAddingNew(true)} style={{
            display: 'flex', alignItems: 'center', gap: 8,
            background: '#1F8844', color: '#FFFFFF', border: 'none',
            borderRadius: 8, padding: '10px 18px', cursor: 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500,
          }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Menu Item
          </button>
        </div>
      </div>

      {/* Add new item form */}
      {addingNew && (
        <div style={{ background: '#FFFFFF', border: '1px solid #1F8844', borderRadius: 10, padding: 24, marginBottom: 20 }}>
          <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 16, color: '#111827', marginBottom: 16 }}>New Menu Item</div>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 12, marginBottom: 16 }}>
            <div>
              <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>Label *</label>
              <input style={inputStyle} value={newItem.label} onChange={e => setNewItem({ ...newItem, label: e.target.value })} placeholder="e.g. Services" onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
            </div>
            <div>
              <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>Type</label>
              <select style={{ ...inputStyle }} value={newItem.type} onChange={e => setNewItem({ ...newItem, type: e.target.value as any })}>
                <option value="link">Simple Link</option>
                <option value="simple">Dropdown</option>
                <option value="mega">Mega Menu</option>
              </select>
            </div>
            {newItem.type === 'link' && (
              <div>
                <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>URL</label>
                <input style={inputStyle} value={newItem.href} onChange={e => setNewItem({ ...newItem, href: e.target.value })} placeholder="/page-slug" onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
              </div>
            )}
          </div>
          <div style={{ display: 'flex', gap: 10 }}>
            <button onClick={handleAddNew} disabled={saving} style={{ background: '#1F8844', color: '#FFF', border: 'none', borderRadius: 6, padding: '9px 18px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 14 }}>
              {saving ? 'Saving...' : 'Save Item'}
            </button>
            <button onClick={() => setAddingNew(false)} style={{ background: 'transparent', color: '#6B7280', border: '1px solid #E5E7EB', borderRadius: 6, padding: '9px 18px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 14 }}>
              Cancel
            </button>
          </div>
        </div>
      )}

      {loading ? (
        <div style={{ textAlign: 'center', padding: '40px 0', color: '#9CA3AF' }}>Loading navigation...</div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
          {items.map((item, index) => (
            <div key={item.id ?? item.label} style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, overflow: 'hidden' }}>
              {/* Item row */}
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '14px 20px' }}>
                {/* Order controls */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                  <button onClick={() => moveItem(index, 'up')} disabled={index === 0} style={{ background: 'transparent', border: 'none', color: index === 0 ? '#D1D5DB' : '#6B7280', cursor: index === 0 ? 'not-allowed' : 'pointer', padding: 2 }}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 15l-6-6-6 6"/></svg>
                  </button>
                  <button onClick={() => moveItem(index, 'down')} disabled={index === items.length - 1} style={{ background: 'transparent', border: 'none', color: index === items.length - 1 ? '#D1D5DB' : '#6B7280', cursor: index === items.length - 1 ? 'not-allowed' : 'pointer', padding: 2 }}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M6 9l6 6 6-6"/></svg>
                  </button>
                </div>

                <div style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 500, fontSize: 15, color: '#111827', flex: 1 }}>{item.label}</div>
                <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B7280', background: '#F3F4F6', padding: '3px 8px', borderRadius: 4 }}>{item.type}</span>
                {item.href && <span style={{ fontFamily: 'Lexend, monospace', fontSize: 12, color: '#9CA3AF' }}>{item.href}</span>}
                {(item.children?.length ?? 0) > 0 && (
                  <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#1F8844' }}>{item.children!.length} sub-items</span>
                )}

                <button onClick={() => { setEditingItem(item); setExpandedId(item.id ?? item.label); }}
                  style={{ background: 'transparent', border: '1px solid #E5E7EB', borderRadius: 6, padding: '6px 12px', cursor: 'pointer', color: '#6B7280', fontFamily: 'Lexend, sans-serif', fontSize: 13 }}>
                  Edit
                </button>
                {item.id && (
                  <button onClick={() => handleDelete(item.id!)}
                    style={{ background: 'transparent', border: 'none', color: '#EF4444', cursor: 'pointer', padding: 6 }}>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  </button>
                )}
              </div>

              {/* Edit panel */}
              {editingItem && (editingItem.id === item.id || (!editingItem.id && editingItem.label === item.label)) && (
                <div style={{ borderTop: '1px solid #E5E7EB', padding: '20px 20px 20px', background: '#F9FAFB' }}>
                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                    <div>
                      <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>Label</label>
                      <input style={inputStyle} value={editingItem.label} onChange={e => setEditingItem({ ...editingItem, label: e.target.value })} onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                    </div>
                    {editingItem.type === 'link' && (
                      <div>
                        <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>URL</label>
                        <input style={inputStyle} value={editingItem.href ?? ''} onChange={e => setEditingItem({ ...editingItem, href: e.target.value })} onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                      </div>
                    )}
                  </div>

                  {/* Sub-items */}
                  {(editingItem.type === 'simple' || editingItem.type === 'mega') && (
                    <div style={{ marginBottom: 16 }}>
                      <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, fontWeight: 500, color: '#374151', marginBottom: 10 }}>Sub-menu Items</div>
                      {(editingItem.children || []).map((child, ci) => (
                        <div key={ci} style={{ display: 'flex', gap: 8, marginBottom: 8, alignItems: 'center' }}>
                          <input style={{ ...inputStyle, flex: 1 }} value={child.label}
                            onChange={e => { const c = [...(editingItem.children || [])]; c[ci] = { ...c[ci], label: e.target.value }; setEditingItem({ ...editingItem, children: c }); }}
                            onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                          <input style={{ ...inputStyle, flex: 1 }} value={child.href}
                            onChange={e => { const c = [...(editingItem.children || [])]; c[ci] = { ...c[ci], href: e.target.value }; setEditingItem({ ...editingItem, children: c }); }}
                            placeholder="/url" onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                          <button onClick={() => removeChild(editingItem, ci)} style={{ background: 'transparent', border: 'none', color: '#EF4444', cursor: 'pointer', padding: 4 }}>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                          </button>
                        </div>
                      ))}
                      <div style={{ display: 'flex', gap: 8, marginTop: 8 }}>
                        <input style={{ ...inputStyle, flex: 1 }} value={newChildLabel} onChange={e => setNewChildLabel(e.target.value)} placeholder="Label" onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                        <input style={{ ...inputStyle, flex: 1 }} value={newChildHref} onChange={e => setNewChildHref(e.target.value)} placeholder="/url" onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                        <button onClick={() => addChild(editingItem)} style={{ background: '#E7F7EE', color: '#1F8844', border: '1px solid #1F8844', borderRadius: 6, padding: '0 12px', cursor: 'pointer', flexShrink: 0 }}>+ Add</button>
                      </div>
                    </div>
                  )}

                  <div style={{ display: 'flex', gap: 10 }}>
                    <button onClick={() => handleSaveItem(editingItem)} disabled={saving} style={{ background: '#1F8844', color: '#FFF', border: 'none', borderRadius: 6, padding: '9px 20px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 14 }}>
                      {saving ? 'Saving...' : 'Save Changes'}
                    </button>
                    <button onClick={() => setEditingItem(null)} style={{ background: 'transparent', color: '#6B7280', border: '1px solid #E5E7EB', borderRadius: 6, padding: '9px 16px', cursor: 'pointer', fontFamily: 'Lexend, sans-serif', fontSize: 14 }}>
                      Cancel
                    </button>
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>
      )}
      <style>{`@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }`}</style>
    </div>
  );
}
