import { useState, useEffect } from 'react';
import { getContactDetails, saveContactDetails, ContactDetails } from '../../lib/firestore';

const DEFAULT_CONTACT: ContactDetails = {
  email: 'hello@devbotics.in',
  whatsapp: '+919876543210',
  responseTime: 'Under 2 Hours · Mon–Sat, 9AM–7PM IST',
  officeIndia: {
    city: 'Jaipur, Rajasthan',
    address: 'Plot No. XX, Malviya Nagar, Jaipur - 302017, India',
    phone: '+91 98765 43210',
    email: 'jaipur@devbotics.in',
    mapLink: 'https://maps.google.com',
  },
  officeUSA: {
    city: 'Delaware, United States',
    address: '8 The Green, Suite A, Dover, Delaware - 19901, USA',
    phone: '+1 (302) 555-0198',
    email: 'usa@devbotics.com',
    mapLink: 'https://maps.google.com',
  },
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

function Field({ label, value, onChange, placeholder }: { label: string; value: string; onChange: (v: string) => void; placeholder?: string }) {
  return (
    <div>
      <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginBottom: 6, textTransform: 'uppercase', letterSpacing: '0.06em' }}>{label}</label>
      <input style={inputStyle} value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder}
        onFocus={e => e.target.style.borderColor = '#1F8844'} onBlur={e => e.target.style.borderColor = '#E5E7EB'} />
    </div>
  );
}

function OfficeCard({ title, flag, office, onChange }: {
  title: string; flag: string;
  office: ContactDetails['officeIndia'];
  onChange: (updated: ContactDetails['officeIndia']) => void;
}) {
  return (
    <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24 }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 20, paddingBottom: 14, borderBottom: '1px solid #F3F4F6' }}>
        <span style={{ fontSize: 20 }}>{flag}</span>
        <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, color: '#111827' }}>{title}</span>
      </div>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
        <Field label="City / Region" value={office.city} onChange={v => onChange({ ...office, city: v })} placeholder="e.g. Jaipur, Rajasthan" />
        <Field label="Full Address" value={office.address} onChange={v => onChange({ ...office, address: v })} placeholder="Street, City, ZIP" />
        <Field label="Phone" value={office.phone} onChange={v => onChange({ ...office, phone: v })} placeholder="+91 98765 43210" />
        <Field label="Email" value={office.email} onChange={v => onChange({ ...office, email: v })} placeholder="office@devbotics.in" />
        <Field label="Google Maps Link" value={office.mapLink} onChange={v => onChange({ ...office, mapLink: v })} placeholder="https://maps.google.com/..." />
      </div>
    </div>
  );
}

export default function ContactManager() {
  const [data, setData] = useState<ContactDetails>(DEFAULT_CONTACT);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 3000);
  };

  useEffect(() => {
    getContactDetails().then(d => { if (d) setData(d); }).finally(() => setLoading(false));
  }, []);

  const handleSave = async () => {
    setSaving(true);
    try {
      await saveContactDetails(data);
      showToast('Contact details saved!');
    } catch { showToast('Failed to save. Try again.', 'error'); }
    setSaving(false);
  };

  if (loading) return <div style={{ textAlign: 'center', padding: '60px', color: '#9CA3AF', fontFamily: 'Lexend, sans-serif' }}>Loading contact details...</div>;

  return (
    <div>
      {toast && <Toast msg={toast.msg} type={toast.type} />}

      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
        <div>
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24, color: '#111827' }}>Contact Details</h1>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', marginTop: 4 }}>
            Update contact information shown on the Contact page, Footer, and throughout the website
          </p>
        </div>
        <button onClick={handleSave} disabled={saving} style={{
          background: '#1F8844', color: '#FFFFFF', border: 'none', borderRadius: 8,
          padding: '10px 22px', cursor: saving ? 'not-allowed' : 'pointer',
          fontFamily: 'Lexend, sans-serif', fontSize: 14, fontWeight: 500, opacity: saving ? 0.7 : 1,
        }}>
          {saving ? 'Saving...' : '✓ Save Changes'}
        </button>
      </div>

      <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
        {/* Primary contact */}
        <div style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24 }}>
          <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, color: '#111827', marginBottom: 20, paddingBottom: 12, borderBottom: '1px solid #F3F4F6' }}>
            Primary Contact
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16 }}>
            <Field label="Primary Email" value={data.email} onChange={v => setData({ ...data, email: v })} placeholder="hello@devbotics.in" />
            <Field label="WhatsApp Number (with country code)" value={data.whatsapp} onChange={v => setData({ ...data, whatsapp: v })} placeholder="+919876543210" />
            <Field label="Response Time Text" value={data.responseTime} onChange={v => setData({ ...data, responseTime: v })} placeholder="Under 2 Hours · Mon–Sat, 9AM–7PM IST" />
          </div>
        </div>

        {/* Office cards */}
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
          <OfficeCard title="India Office (HQ)" flag="🇮🇳"
            office={data.officeIndia}
            onChange={v => setData({ ...data, officeIndia: v })} />
          <OfficeCard title="USA Office (Sales)" flag="🇺🇸"
            office={data.officeUSA}
            onChange={v => setData({ ...data, officeUSA: v })} />
        </div>

        {/* Preview card */}
        <div style={{ background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 10, padding: 24 }}>
          <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.1em', marginBottom: 16 }}>Live Preview</div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16 }}>
            {[
              { label: '📧 Email', val: data.email },
              { label: '📞 WhatsApp', val: data.whatsapp },
              { label: '⏱ Response', val: data.responseTime },
            ].map(item => (
              <div key={item.label} style={{ background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 8, padding: '16px' }}>
                <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#9CA3AF', marginBottom: 6 }}>{item.label}</div>
                <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 14, color: '#111827' }}>{item.val || '—'}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
