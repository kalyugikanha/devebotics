// Contact Strip — 3-column contact info section
const ContactStrip = () => (
  <section style={{ background: '#F9FAFB', borderTop: '1px solid #E5E7EB', borderBottom: '1px solid #E5E7EB' }}>
    <div style={{
      maxWidth: 1400, margin: '0 auto', padding: '0 32px',
      display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)',
    }} className="contact-strip-grid">
      {[
        {
          icon: (
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
              <polyline points="22,6 12,13 2,6" />
            </svg>
          ),
          label: 'Email Us',
          value: 'hello@devbotics.in',
          link: 'mailto:hello@devbotics.in',
        },
        {
          icon: (
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012.18 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.72 6.72l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" />
            </svg>
          ),
          label: 'Call Us',
          value: '+91 XXXXXXXXXX',
          link: 'tel:+91XXXXXXXXXX',
        },
        {
          icon: (
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
              <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
            </svg>
          ),
          label: 'WhatsApp',
          value: 'Chat Now →',
          link: '#',
        },
      ].map((col, i) => (
        <a
          key={i}
          href={col.link}
          style={{
            display: 'flex', alignItems: 'center', gap: 20,
            padding: '36px 40px',
            textDecoration: 'none',
            borderRight: i < 2 ? '1px solid #E5E7EB' : 'none',
            transition: 'background 0.2s ease',
          }}
          onMouseEnter={e => ((e.currentTarget as HTMLElement).style.background = 'rgba(232,255,71,0.03)')}
          onMouseLeave={e => ((e.currentTarget as HTMLElement).style.background = 'transparent')}
        >
          <div style={{
            width: 48, height: 48, borderRadius: 10,
            background: 'rgba(232,255,71,0.08)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            flexShrink: 0,
          }}>
            {col.icon}
          </div>
          <div>
            <div style={{
              fontFamily: 'Lexend, sans-serif', fontWeight: 300,
              fontSize: 11, color: '#9CA3AF', letterSpacing: '0.15em',
              textTransform: 'uppercase', marginBottom: 4,
            }}>
              {col.label}
            </div>
            <div style={{
              fontFamily: 'Lexend, sans-serif', fontWeight: 500,
              fontSize: 15, color: '#111827',
            }}>
              {col.value}
            </div>
          </div>
        </a>
      ))}
    </div>

    <style>{`
      @media (max-width: 768px) {
        .contact-strip-grid { grid-template-columns: 1fr !important; }
        .contact-strip-grid > a { border-right: none !important; border-bottom: 1px solid #E5E7EB; }
      }
    `}</style>
  </section>
);

export default ContactStrip;
