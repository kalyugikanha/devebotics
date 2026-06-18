import { useState } from 'react';

// Fix 9: Complete footer with newsletter, full contact details, 5 columns, updated bottom bar
const col2 = ['App Development', 'Web Development', 'Software Development', 'Custom Development', 'API Development', 'SaaS Development', 'Progressive Web App', 'Blockchain / dApp'];
const col3 = ['AI Development', 'Chatbot Development', 'AI Automation', 'EXE App Development', 'SEO Services', 'SMO Services', 'Performance Marketing', 'Influencer Marketing'];
const col4 = ['Hire React Developer', 'Hire Flutter Developer', 'Hire Node.js Developer', 'Hire Python Developer', 'Hire Full Stack Developer', 'Hire UI/UX Designer', 'Hire SEO Expert', 'Hire Digital Marketer'];
const col5 = ['About Us', 'Our Journey', 'Portfolio', 'Blog', 'Careers', 'Privacy Policy', 'Terms of Service', 'Refund Policy', 'Contact Us'];

const socials = [
  { label: 'LinkedIn', d: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z' },
  { label: 'Instagram', d: 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M7.5 20.5h9a5 5 0 005-5v-9a5 5 0 00-5-5h-9a5 5 0 00-5 5v9a5 5 0 005 5z' },
  { label: 'Twitter', d: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z' },
  { label: 'YouTube', d: 'M22.54 6.42a2.78 2.78 0 00-1.95-1.97C18.88 4 12 4 12 4s-6.88 0-8.59.45A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 001.95-1.97A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z' },
  { label: 'WhatsApp', d: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.522 5.827L0 24l6.335-1.51A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0z' },
];

const ColHeading = ({ children }: { children: string }) => (
  <div style={{ marginBottom: 20 }}>
    <div style={{
      fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 13,
      color: '#111827', letterSpacing: '0.12em', textTransform: 'uppercase', marginBottom: 8,
    }}>
      {children}
    </div>
    <div style={{ width: 24, height: 2, background: '#1F8844', borderRadius: 1 }} />
  </div>
);

const FLink = ({ children }: { children: string }) => (
  <a href="#" style={{
    fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 13,
    color: '#4B5563', textDecoration: 'none', display: 'block',
    transition: 'color 0.15s ease', lineHeight: 1,
  }}
    onMouseEnter={e => (e.currentTarget.style.color = '#111827')}
    onMouseLeave={e => (e.currentTarget.style.color = '#4B5563')}
  >
    {children}
  </a>
);

const Footer = () => {
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  return (
    <footer style={{ background: '#FFFFFF', borderTop: '1px solid #E5E7EB', position: 'relative', overflow: 'hidden' }}>
      {/* India map SVG watermark */}
      <div style={{ position: 'absolute', bottom: 0, right: 0, zIndex: 0, opacity: 0.04, pointerEvents: 'none' }}>
        <svg viewBox="0 0 400 450" width="400" height="450" fill="none">
          <path d="M200 20 L230 30 L260 50 L280 80 L300 90 L320 110 L330 140 L340 160 L350 180 L355 210 L340 230 L330 250 L310 270 L300 290 L285 310 L270 330 L255 350 L240 370 L230 390 L220 410 L215 430 L210 420 L205 400 L200 380 L190 360 L175 340 L160 320 L150 300 L135 280 L120 260 L105 240 L90 220 L80 200 L75 180 L80 160 L85 140 L100 120 L115 100 L130 80 L150 60 L170 40 L200 20 Z" fill="#111827" />
        </svg>
      </div>

      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px', position: 'relative', zIndex: 1 }}>
        {/* Newsletter row */}
        <div style={{ borderBottom: '1px solid #E5E7EB', padding: '40px 0', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 24 }}>
          <div>
            <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 20, color: '#111827', marginBottom: 6 }}>
              Stay updated with our latest insights &amp; launches
            </div>
            <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#4B5563' }}>
              Weekly digest. No spam. Unsubscribe anytime.
            </div>
          </div>
          {subscribed ? (
            <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#1F8844', display: 'flex', alignItems: 'center', gap: 8 }}>✓ You're subscribed!</div>
          ) : (
            <div style={{ display: 'flex', gap: 0, flexShrink: 0 }}>
              <input
                type="email" placeholder="Enter your email" value={email}
                onChange={e => setEmail(e.target.value)}
                style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 14,
                  background: '#F9FAFB', border: '1px solid #E5E7EB', borderRight: 'none',
                  color: '#111827', padding: '12px 20px', borderRadius: '6px 0 0 6px',
                  outline: 'none', minWidth: 260,
                }}
              />
              <button onClick={() => { if (email) setSubscribed(true); }}
                style={{
                  fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 14,
                  background: '#111827', color: '#FFFFFF', border: 'none',
                  padding: '12px 24px', borderRadius: '0 6px 6px 0', cursor: 'pointer',
                  transition: 'background 0.2s ease', whiteSpace: 'nowrap',
                }}
                onMouseEnter={e => (e.currentTarget.style.background = '#1F8844')}
                onMouseLeave={e => (e.currentTarget.style.background = '#111827')}
              >Subscribe</button>
            </div>
          )}
        </div>

        {/* 5-column grid */}
        <div style={{ display: 'grid', gridTemplateColumns: '1.6fr repeat(4, 1fr)', gap: 48, padding: '64px 0' }} className="footer-grid">
          {/* Col 1 — Brand */}
          <div>
            <a href="#" style={{ textDecoration: 'none', display: 'inline-block', marginBottom: 10 }}>
              <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#111827', letterSpacing: '-0.03em' }}>
                DevBotics<span style={{ color: '#1F8844' }}>.</span>
              </span>
            </a>
            <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563', lineHeight: 1.7, maxWidth: 240, marginBottom: 24 }}>
              Building Tomorrow's Digital World, Today.
            </p>
            {/* Contact details */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: 10, marginBottom: 24 }}>
              {[
                { icon: '📧', val: 'hello@devbotics.in' },
                { icon: '📞', val: '+91 98765 43210' },
                { icon: '📍', val: 'Jaipur, Rajasthan, India' },
              ].map(c => (
                <div key={c.val} style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                  <span style={{ fontSize: 13 }}>{c.icon}</span>
                  <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563' }}>{c.val}</span>
                </div>
              ))}
            </div>
            {/* Social icons */}
            <div style={{ display: 'flex', gap: 10 }}>
              {socials.map(s => (
                <a key={s.label} href="#" aria-label={s.label} style={{
                  width: 36, height: 36, display: 'flex', alignItems: 'center', justifyContent: 'center',
                  border: '1px solid #E5E7EB', borderRadius: 6,
                  transition: 'border-color 0.2s ease, background 0.2s ease',
                }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.background = 'rgba(232,255,71,0.06)'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.background = 'transparent'; }}
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4B5563" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                    <path d={s.d} />
                  </svg>
                </a>
              ))}
            </div>
          </div>

          {/* Col 2 — Development */}
          <div>
            <ColHeading>Development</ColHeading>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 11 }}>
              {col2.map(l => <FLink key={l}>{l}</FLink>)}
            </div>
          </div>

          {/* Col 3 — AI & Marketing */}
          <div>
            <ColHeading>AI & Marketing</ColHeading>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 11 }}>
              {col3.map(l => <FLink key={l}>{l}</FLink>)}
            </div>
          </div>

          {/* Col 4 — Hire */}
          <div>
            <ColHeading>Hire Developers</ColHeading>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 11 }}>
              {col4.map(l => <FLink key={l}>{l}</FLink>)}
            </div>
          </div>

          {/* Col 5 — Company */}
          <div>
            <ColHeading>Company</ColHeading>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 11 }}>
              {col5.map(l => <FLink key={l}>{l}</FLink>)}
            </div>
          </div>
        </div>

        {/* Bottom bar */}
        <div style={{
          borderTop: '1px solid #E5E7EB', padding: '24px 0',
          display: 'grid', gridTemplateColumns: '1fr auto 1fr',
          alignItems: 'center', gap: 16,
        }} className="footer-bottom">
          <span style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 12, color: '#9CA3AF' }}>
            © 2025 DevBotics. All Rights Reserved.
          </span>
          <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#9CA3AF', textAlign: 'center', whiteSpace: 'nowrap' }}>
            🇮🇳 Made with ❤️ in India
          </span>
          <div style={{ display: 'flex', gap: 20, justifyContent: 'flex-end' }}>
            {['Privacy Policy', 'Terms', 'Sitemap'].map(item => (
              <a key={item} href="#" style={{
                fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 12,
                color: '#9CA3AF', textDecoration: 'none', transition: 'color 0.2s ease',
              }}
                onMouseEnter={e => (e.currentTarget.style.color = '#4B5563')}
                onMouseLeave={e => (e.currentTarget.style.color = '#9CA3AF')}
              >{item}</a>
            ))}
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 1100px) { .footer-grid { grid-template-columns: repeat(3, 1fr) !important; } }
        @media (max-width: 768px) {
          .footer-grid { grid-template-columns: 1fr 1fr !important; }
          .footer-grid > div:first-child { grid-column: 1 / -1; }
          .footer-bottom { grid-template-columns: 1fr !important; text-align: center; }
          .footer-bottom > div { justify-content: center !important; }
        }
      `}</style>
    </footer>
  );
};

export default Footer;
