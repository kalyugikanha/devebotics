import { useState, useEffect } from 'react';
import { getFooterData, saveFooterData, FooterData, FooterColumn } from '../../lib/firestore';

const DEFAULT_FOOTER: FooterData = {
  tagline: 'Building Tomorrow\'s Digital World, Today.',
  copyright: '© 2025 DevBotics. All Rights Reserved.',
  contact: { email: 'hello@devbotics.in', phone: '+91 98765 43210', address: 'Jaipur, Rajasthan, India' },
  socials: [
    { label: 'LinkedIn', href: '#' }, { label: 'Instagram', href: '#' },
    { label: 'Twitter', href: '#' }, { label: 'YouTube', href: '#' }, { label: 'WhatsApp', href: '#' },
  ],
  columns: [
    { heading: 'Development', links: [{ label: 'App Development', href: '#' }, { label: 'Web Development', href: '#' }, { label: 'Software Development', href: '#' }, { label: 'Custom Development', href: '#' }, { label: 'API Development', href: '#' }] },
    { heading: 'AI & Marketing', links: [{ label: 'AI Development', href: '#' }, { label: 'Chatbot Development', href: '#' }, { label: 'AI Automation', href: '#' }, { label: 'SEO Services', href: '#' }, { label: 'SMO Services', href: '#' }] },
    { heading: 'Hire Developers', links: [{ label: 'Hire React Developer', href: '#' }, { label: 'Hire Flutter Developer', href: '#' }, { label: 'Hire Node.js Developer', href: '#' }, { label: 'Hire Python Developer', href: '#' }] },
    { heading: 'Company', links: [{ label: 'About Us', href: '/about-us' }, { label: 'Portfolio', href: '#' }, { label: 'Blog', href: '/blog' }, { label: 'Careers', href: '/careers' }, { label: 'Contact Us', href: '/contact' }] },
  ],
};

function Toast({ msg, type }: { msg: string; type: 'success' | 'error' }) {
  return (
    <div style={{ position: 'fixed', bottom: 24, right: 24, zIndex: 9999, background: type === 'success' ? '#1F8844' : '#EF4444', color: '#FFFFFF', padding: '12px 20px', borderRadius: 8, fontFamily: 'Lexend, sans-serif', fontSize: 14, boxShadow: '0 8px 24px rgba(0,0,0,0.15)' }}>
      {msg}
    </div>
  );
}

const inputStyle: React.CSSProperties = {
  width: '100%', background: '#F9FAFB', border: '1px solid #E5E7EB',
  borderRadius: 8, padding: '10px 14px', fontFamily: 'Lexend, sans-serif',
  fontSize: 14, color: '#111827', outline: 'none', boxSizing: 'border-box', transition: 'border-color 0.2s',
};

const SectionTitle = ({ children }: { children: string }) => (
  <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, color: '#111827', marginBottom: 16, paddingBottom: 12, borderBottom: '2px solid #1F8844', display: 'inline-block' }}>
    {children}
  </div>
);

export default function FooterManager() {
  const [data, setData] = useState<FooterData>(DEFAULT_FOOTER);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);
  const [activeTab, setActiveTab] = useState<'general' | 'columns' | 'socials'>('general');

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 3000);
  };

  useEffect(() => {
    getFooterData().then(d => {
      if (d) setData(d);
    }).finally(() => setLoading(false));
  }, []);

  const handleSave = async () => {
    setSaving(true);
    try {
      await saveFooterData(data);
      showToast('Footer saved successfully!');
    } catch { showToast('Failed to save. Try again.', 'error'); }
    setSaving(false);
  };

  const updateColumn = (colIdx: number, field: keyof FooterColumn, value: any) => {
    const cols = [...data.columns];
    cols[colIdx] = { ...cols[colIdx], [field]: value };
    setData({ ...data, columns: cols });
  };

  const updateLink = (colIdx: number, linkIdx: number, field: 'label' | 'href', value: string) => {
    const cols = [...data.columns];
    const links = [...cols[colIdx].links];
    links[linkIdx] = { ...links[linkIdx], [field]: value };
    cols[colIdx] = { ...cols[colIdx], links };
    setData({ ...data, columns: cols });
  };

  const addLink = (colIdx: number) => {
    const cols = [...data.columns];
    cols[colIdx] = { ...cols[colIdx], links: [...cols[colIdx].links, { label: 'New Link', href: '#' }] };
    setData({ ...data, columns: cols });
  };

  const removeLink = (colIdx: number, linkIdx: number) => {
    const cols = [...data.columns];
    cols[colIdx] = { ...cols[colIdx], links: cols[colIdx].links.filter((_, i) => i !== linkIdx) };
    setData({ ...data, columns: cols });
  };

  const addColumn = () => {
    setData({ ...data, columns: [...data.columns, { heading: 'New Section', links: [] }] });
  };

  const removeColumn = (idx: number) => {
    if (!confirm('Remove this column?')) return;
    setData({ ...data, columns: data.columns.filter((_, i) => i !== idx) });
  };

  const tabs = [
    { key: 'general', label: 'General & Contact' },
    { key: 'columns', label: 'Link Columns' },
    { key: 'socials', label: 'Social Links' },
  ] as const;

  if (loading) return <div style={{ textAlign: 'center', padding: '60px', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif' }}>Loading footer data...</div>;

  return (
    <div>
      {toast && <Toast msg={toast.msg} type={toast.type} />}

      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
        <div>
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24, color: '#111827' }}>Footer Manager</h1>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', marginTop: 4 }}>Edit all footer sections, links, contact info and social media</p>
        </div>
        <button onClick={handleSave} disabled={saving} style={{
          background: '#1F8844', color: '#FFFFFF', border: 'none', borderRadius: 8,
          padding: '10px 22px', cursor: saving ? 'not-allowed' : 'pointer',
          fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500,
          display: 'flex', alignItems: 'center', gap: 8, opacity: saving ? 0.7 : 1,
        }}>
          {saving ? 'Saving...' : '✓ Save All Changes'}
        </button>
      </div>

      {/* Tabs */}
      <div style={{ display: 'flex', gap: 0, marginBottom: 24, borderBottom: '1px solid #E5E7EB' }}>
        {tabs.map(t => (
          <button key={t.key} onClick={() => setActiveTab(t.key)} style={{
            padding: '10px 20px', border: 'none', background: 'transparent', cursor: 'pointer',
            fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: activeTab === t.key ? 500 : 400,
            color: activeTab === t.key ? '#1F8844' : '#6B7280',
            borderBottom: activeTab === t.key ? '2px solid #1F8844' : '2px solid transparent',
            marginBottom: -1, transition: 'all 0.15s',
          }}>{t.label}</button>
        ))}
      </div>

      {/* GENERAL TAB */}
      {activeTab === 'general' && (
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24 }}>
            <SectionTitle>Brand</SectionTitle>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div>
                <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>Tagline</label>
                <input style={inputStyle} value={data.tagline} onChange={e => setData({ ...data, tagline: e.target.value })}
                  onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
              </div>
              <div>
                <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>Copyright Text</label>
                <input style={inputStyle} value={data.copyright} onChange={e => setData({ ...data, copyright: e.target.value })}
                  onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
              </div>
            </div>
          </div>

          <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24 }}>
            <SectionTitle>Contact Details</SectionTitle>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              {[
                { key: 'email', label: 'Email', placeholder: 'hello@devbotics.in' },
                { key: 'phone', label: 'Phone', placeholder: '+91 98765 43210' },
                { key: 'address', label: 'Address', placeholder: 'Jaipur, Rajasthan, India' },
              ].map(f => (
                <div key={f.key}>
                  <label style={{ display: 'block', fontSize: 12, color: '#6B7280', marginBottom: 6 }}>{f.label}</label>
                  <input style={inputStyle} value={(data.contact as any)[f.key]} placeholder={f.placeholder}
                    onChange={e => setData({ ...data, contact: { ...data.contact, [f.key]: e.target.value } })}
                    onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* COLUMNS TAB */}
      {activeTab === 'columns' && (
        <div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(2, 1fr)', gap: 20 }}>
            {data.columns.map((col, ci) => (
              <div key={ci} style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 20 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                  <input style={{ ...inputStyle, fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 15, border: 'none', padding: '0', background: 'transparent', flex: 1 }}
                    value={col.heading} onChange={e => updateColumn(ci, 'heading', e.target.value)} />
                  <button onClick={() => removeColumn(ci)} style={{ background: 'transparent', border: 'none', color: '#D1D5DB', cursor: 'pointer', padding: 4, transition: 'color 0.15s' }}
                    onMouseEnter={e => (e.currentTarget as HTMLElement).style.color = '#EF4444'}
                    onMouseLeave={e => (e.currentTarget as HTMLElement).style.color = '#D1D5DB'}>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  </button>
                </div>
                <div style={{ borderTop: '1px solid #F3F4F6', paddingTop: 14 }}>
                  {col.links.map((link, li) => (
                    <div key={li} style={{ display: 'flex', gap: 8, marginBottom: 8 }}>
                      <input style={{ ...inputStyle, flex: 1 }} value={link.label}
                        onChange={e => updateLink(ci, li, 'label', e.target.value)}
                        onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                      <input style={{ ...inputStyle, flex: 1 }} value={link.href} placeholder="/url"
                        onChange={e => updateLink(ci, li, 'href', e.target.value)}
                        onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                      <button onClick={() => removeLink(ci, li)} style={{ background: 'transparent', border: 'none', color: '#D1D5DB', cursor: 'pointer', padding: '0 4px', transition: 'color 0.15s', flexShrink: 0 }}
                        onMouseEnter={e => (e.currentTarget as HTMLElement).style.color = '#EF4444'}
                        onMouseLeave={e => (e.currentTarget as HTMLElement).style.color = '#D1D5DB'}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      </button>
                    </div>
                  ))}
                  <button onClick={() => addLink(ci)} style={{ background: 'transparent', border: '1px dashed #D1D5DB', borderRadius: 6, padding: '7px 14px', cursor: 'pointer', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif', fontSize: 13, marginTop: 4, width: '100%', transition: 'all 0.2s' }}
                    onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; }}
                    onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#D1D5DB'; (e.currentTarget as HTMLElement).style.color = '#9CA3AF'; }}>
                    + Add Link
                  </button>
                </div>
              </div>
            ))}
          </div>
          <button onClick={addColumn} style={{ marginTop: 16, background: 'transparent', border: '1px dashed #D1D5DB', borderRadius: 10, padding: '14px 24px', cursor: 'pointer', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif', fontSize: 14, width: '100%', transition: 'all 0.2s' }}
            onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; }}
            onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#D1D5DB'; (e.currentTarget as HTMLElement).style.color = '#9CA3AF'; }}>
            + Add New Column
          </button>
        </div>
      )}

      {/* SOCIALS TAB */}
      {activeTab === 'socials' && (
        <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24, maxWidth: 560 }}>
          <SectionTitle>Social Media Links</SectionTitle>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
            {data.socials.map((s, si) => (
              <div key={si} style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                <input style={{ ...inputStyle, width: 140, flex: 'none' }} value={s.label}
                  onChange={e => { const ss = [...data.socials]; ss[si] = { ...ss[si], label: e.target.value }; setData({ ...data, socials: ss }); }}
                  onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                <input style={{ ...inputStyle, flex: 1 }} value={s.href} placeholder="https://..."
                  onChange={e => { const ss = [...data.socials]; ss[si] = { ...ss[si], href: e.target.value }; setData({ ...data, socials: ss }); }}
                  onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
                <button onClick={() => setData({ ...data, socials: data.socials.filter((_, i) => i !== si) })}
                  style={{ background: 'transparent', border: 'none', color: '#D1D5DB', cursor: 'pointer', padding: 4, transition: 'color 0.15s', flexShrink: 0 }}
                  onMouseEnter={e => (e.currentTarget as HTMLElement).style.color = '#EF4444'}
                  onMouseLeave={e => (e.currentTarget as HTMLElement).style.color = '#D1D5DB'}>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </div>
            ))}
            <button onClick={() => setData({ ...data, socials: [...data.socials, { label: 'Platform', href: '#' }] })}
              style={{ background: 'transparent', border: '1px dashed #D1D5DB', borderRadius: 6, padding: '9px', cursor: 'pointer', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif', fontSize: 13, transition: 'all 0.2s' }}
              onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; }}
              onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#D1D5DB'; (e.currentTarget as HTMLElement).style.color = '#9CA3AF'; }}>
              + Add Social Link
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
