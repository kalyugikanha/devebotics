import { useState } from 'react';

const inputStyle: React.CSSProperties = {
  width: '100%', fontFamily: 'Lexend, sans-serif', fontSize: 14,
  background: '#0E1012', border: '1px solid #1C1E21', color: '#F0F0F0',
  padding: '12px 16px', borderRadius: 6, outline: 'none',
  transition: 'border-color 0.2s ease', boxSizing: 'border-box',
};

interface Props { preselectedApp?: string; }

const ContactForm = ({ preselectedApp = '' }: Props) => {
  const [submitted, setSubmitted] = useState(false);
  const [form, setForm] = useState({ name: '', email: '', phone: '', appType: preselectedApp, budget: '', desc: '' });
  const set = (k: string, v: string) => setForm(f => ({ ...f, [k]: v }));
  const focusBorder = (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    e.target.style.borderColor = '#E8FF47';
  };
  const blurBorder = (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    e.target.style.borderColor = '#1C1E21';
  };

  return (
    <section style={{ padding: '120px 0', background: '#0E1012' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
        <div className="fade-up" style={{ marginBottom: 64 }}>
          <h2 style={{ fontSize: 'clamp(32px, 4vw, 52px)', marginBottom: 16 }}>
            Let's Build Your App <span className="accent">Together.</span>
          </h2>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#6B6F76' }}>
            Tell us your idea. We'll get back within 24 hours.
          </p>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: '38% 1fr', gap: 80 }} className="contact-form-grid">
          {/* Left — info */}
          <div className="fade-up">
            <div style={{ display: 'flex', flexDirection: 'column', gap: 20, marginBottom: 40 }}>
              {[
                { icon: '📧', label: 'Email', val: 'hello@devbotics.in', href: 'mailto:hello@devbotics.in' },
                { icon: '📞', label: 'Call', val: '+91 98765 43210', href: 'tel:+919876543210' },
                { icon: '💬', label: 'WhatsApp', val: 'Chat Now →', href: 'https://wa.me/919876543210' },
              ].map(c => (
                <a key={c.label} href={c.href} style={{ display: 'flex', alignItems: 'center', gap: 16, textDecoration: 'none' }}>
                  <div style={{ width: 44, height: 44, background: '#111214', border: '1px solid #1C1E21', borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 18, flexShrink: 0 }}>{c.icon}</div>
                  <div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#3A3D42', letterSpacing: '0.1em', textTransform: 'uppercase', marginBottom: 2 }}>{c.label}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#F0F0F0' }}>{c.val}</div>
                  </div>
                </a>
              ))}
            </div>

            {/* Response badge */}
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: 10, background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 8, padding: '12px 18px', marginBottom: 32 }}>
              <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#E8FF47', flexShrink: 0, display: 'inline-block' }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#6B6F76' }}>Average response time: Under 2 hours</span>
            </div>

            {/* Trust badges */}
            <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
              {['🔒 NDA Protected', '⚡ Fast Delivery', '✅ Money-back'].map(b => (
                <span key={b} style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', background: '#111214', border: '1px solid #1C1E21', borderRadius: 6, padding: '7px 14px' }}>{b}</span>
              ))}
            </div>
          </div>

          {/* Right — form */}
          <div className="fade-up">
            {submitted ? (
              <div style={{ textAlign: 'center', padding: '80px 0' }}>
                <div style={{ fontSize: 48, marginBottom: 24 }}>✅</div>
                <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontSize: 24, color: '#F0F0F0', marginBottom: 12 }}>Message Sent!</h3>
                <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 15, color: '#6B6F76' }}>We'll get back to you within 24 hours.</p>
              </div>
            ) : (
              <form onSubmit={e => { e.preventDefault(); setSubmitted(true); }} style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                  <div>
                    <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>Full Name *</label>
                    <input required type="text" placeholder="Rahul Sharma" value={form.name} onChange={e => set('name', e.target.value)} style={inputStyle} onFocus={focusBorder} onBlur={blurBorder} />
                  </div>
                  <div>
                    <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>Email Address *</label>
                    <input required type="email" placeholder="hello@company.com" value={form.email} onChange={e => set('email', e.target.value)} style={inputStyle} onFocus={focusBorder} onBlur={blurBorder} />
                  </div>
                </div>
                <div>
                  <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>Phone Number</label>
                  <input type="tel" placeholder="+91 98765 43210" value={form.phone} onChange={e => set('phone', e.target.value)} style={inputStyle} onFocus={focusBorder} onBlur={blurBorder} />
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                  <div>
                    <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>App Type *</label>
                    <select required value={form.appType} onChange={e => set('appType', e.target.value)} style={{ ...inputStyle, cursor: 'pointer' }} onFocus={focusBorder} onBlur={blurBorder}>
                      <option value="">Select App Type</option>
                      {['Food Delivery', 'Taxi Booking', 'E-commerce', 'Healthcare', 'EdTech', 'On-Demand', 'Matrimonial', 'Custom App', 'Other'].map(o => <option key={o} value={o}>{o}</option>)}
                    </select>
                  </div>
                  <div>
                    <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>Budget Range</label>
                    <select value={form.budget} onChange={e => set('budget', e.target.value)} style={{ ...inputStyle, cursor: 'pointer' }} onFocus={focusBorder} onBlur={blurBorder}>
                      <option value="">Select Budget</option>
                      {['Under ₹1 Lakh', '₹1–3 Lakh', '₹3–5 Lakh', '₹5 Lakh+'].map(o => <option key={o} value={o}>{o}</option>)}
                    </select>
                  </div>
                </div>
                <div>
                  <label style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B6F76', display: 'block', marginBottom: 6 }}>App Description *</label>
                  <textarea required rows={4} placeholder="Describe your app idea, key features and target audience..." value={form.desc} onChange={e => set('desc', e.target.value)} style={{ ...inputStyle, resize: 'vertical' }} onFocus={focusBorder} onBlur={blurBorder} />
                </div>
                <button type="submit" style={{
                  fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 15,
                  background: '#F0F0F0', color: '#08090A', border: 'none', cursor: 'pointer',
                  padding: '15px', borderRadius: 6, width: '100%',
                  transition: 'background 0.2s ease',
                }}
                  onMouseEnter={e => (e.currentTarget.style.background = '#E8FF47')}
                  onMouseLeave={e => (e.currentTarget.style.background = '#F0F0F0')}
                >
                  Send My Requirements →
                </button>
                <p style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 11, color: '#3A3D42', textAlign: 'center' }}>
                  By submitting, you agree to our Privacy Policy. Your idea is safe with us. 🔒
                </p>
              </form>
            )}
          </div>
        </div>
      </div>
      <style>{`
        @media (max-width: 1024px) { .contact-form-grid { grid-template-columns: 1fr !important; gap: 48px !important; } }
      `}</style>
    </section>
  );
};

export default ContactForm;
