// NEW SECTION: Team / Culture
const Team = () => (
  <section style={{ padding: '120px 0', background: '#FFFFFF' }}>
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
      <div className="fade-up" style={{ marginBottom: 64 }}>
        <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)' }}>
          The People Behind<br />the <span className="accent">Products.</span>
        </h2>
      </div>

      <div style={{
        display: 'grid', gridTemplateColumns: '50% 1fr',
        gap: 80, alignItems: 'center',
      }} className="team-grid">
        {/* Left image */}
        <div className="fade-up">
          <div style={{ borderRadius: 16, overflow: 'hidden', lineHeight: 0, position: 'relative' }}>
            <img
              src="/images/generic-2.png"
              alt="DevBotics team"
              loading="lazy"
              style={{ width: '100%', aspectRatio: '4/3', objectFit: 'cover' }}
            />
            <div style={{ position: 'absolute', inset: 0, background: 'rgba(8,9,10,0.2)' }} />
          </div>
        </div>

        {/* Right content */}
        <div className="fade-up">
          <p style={{
            fontFamily: 'Lexend, sans-serif', fontSize: 18,
            color: '#4B5563', lineHeight: 1.75, marginBottom: 56,
          }}>
            We're a team of engineers, designers, and strategists obsessed with building things that work — beautifully, scalably, and on time.
          </p>

          {/* Culture stats */}
          <div style={{
            display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)',
            gap: 32, marginBottom: 48,
          }}>
            {[
              { val: '15+', label: 'Engineers on team' },
              { val: '8+', label: 'Years combined experience' },
              { val: '3', label: 'Countries served' },
            ].map(s => (
              <div key={s.label} style={{ borderTop: '2px solid #1F8844', paddingTop: 20 }}>
                <div style={{
                  fontFamily: 'Montserrat, sans-serif', fontWeight: 900,
                  fontSize: 40, color: '#111827', letterSpacing: '-0.03em',
                }}>
                  {s.val}
                </div>
                <div style={{
                  fontFamily: 'Lexend, sans-serif', fontWeight: 300,
                  fontSize: 13, color: '#4B5563', marginTop: 6,
                }}>
                  {s.label}
                </div>
              </div>
            ))}
          </div>

          <a href="#" style={{
            fontFamily: 'Lexend, sans-serif', fontWeight: 400,
            fontSize: 14, color: '#4B5563', textDecoration: 'none',
            display: 'inline-flex', alignItems: 'center', gap: 6,
            transition: 'color 0.2s ease',
          }}
            onMouseEnter={e => (e.currentTarget.style.color = '#111827')}
            onMouseLeave={e => (e.currentTarget.style.color = '#4B5563')}
          >
            Join our team →
          </a>
        </div>
      </div>
    </div>

    <style>{`
      @media (max-width: 1024px) {
        .team-grid { grid-template-columns: 1fr !important; }
      }
    `}</style>
  </section>
);

export default Team;
