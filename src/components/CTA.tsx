// Final CTA with rotating circular text
const CTA = () => (
  <section style={{
    background: '#1F8844', padding: '120px 32px',
    textAlign: 'center', position: 'relative', overflow: 'hidden',
  }}>
    {/* Rotating circular text — top right */}
    <div style={{
      position: 'absolute', top: -20, right: -20,
      width: 200, height: 200,
      animation: 'spin-slow 12s linear infinite',
    }}>
      <svg viewBox="0 0 200 200" width="200" height="200">
        <defs>
          <path id="circle-path" d="M 100, 100 m -75, 0 a 75,75 0 1,1 150,0 a 75,75 0 1,1 -150,0" />
        </defs>
        <text fill="rgba(8,9,10,0.25)" fontSize="13" fontFamily="Montserrat,sans-serif" fontWeight="700" letterSpacing="3">
          <textPath href="#circle-path">
            DEVBOTICS · BUILD · LAUNCH · GROW · DEVBOTICS ·
          </textPath>
        </text>
      </svg>
    </div>
    {/* Bottom left also */}
    <div style={{
      position: 'absolute', bottom: -20, left: -20,
      width: 160, height: 160,
      animation: 'spin-slow 16s linear infinite reverse',
      opacity: 0.15,
    }}>
      <svg viewBox="0 0 200 200" width="160" height="160">
        <defs>
          <path id="circle-path-2" d="M 100, 100 m -75, 0 a 75,75 0 1,1 150,0 a 75,75 0 1,1 -150,0" />
        </defs>
        <text fill="#FFFFFF" fontSize="13" fontFamily="Montserrat,sans-serif" fontWeight="700" letterSpacing="3">
          <textPath href="#circle-path-2">
            BUILD · LAUNCH · GROW · BUILD ·
          </textPath>
        </text>
      </svg>
    </div>

    <div style={{ maxWidth: 800, margin: '0 auto', position: 'relative', zIndex: 1 }}>
      <div className="fade-up">
        <h2 style={{
          fontFamily: 'Montserrat, sans-serif', fontWeight: 900,
          fontSize: 'clamp(48px, 8vw, 96px)', color: '#FFFFFF',
          lineHeight: 1.0, letterSpacing: '-0.03em', marginBottom: 24,
        }}>
          Ready to Build?
        </h2>
        <p style={{
          fontFamily: 'Lexend, sans-serif', fontWeight: 400,
          fontSize: 20, color: '#9CA3AF', marginBottom: 48, lineHeight: 1.6,
        }}>
          Let's talk about your project. No commitment, just a conversation.
        </p>
        <div style={{ display: 'flex', gap: 16, justifyContent: 'center', flexWrap: 'wrap' }}>
          <a href="#" style={{
            fontFamily: 'Lexend, sans-serif', fontWeight: 700, fontSize: 15,
            background: '#FFFFFF', color: '#111827', textDecoration: 'none',
            padding: '16px 36px', borderRadius: 8,
            transition: 'transform 0.2s ease, box-shadow 0.2s ease',
            display: 'inline-block',
          }}
            onMouseEnter={e => {
              (e.currentTarget as HTMLElement).style.transform = 'translateY(-2px)';
              (e.currentTarget as HTMLElement).style.boxShadow = '0 8px 24px rgba(0,0,0,0.3)';
            }}
            onMouseLeave={e => {
              (e.currentTarget as HTMLElement).style.transform = 'translateY(0)';
              (e.currentTarget as HTMLElement).style.boxShadow = 'none';
            }}
          >
            Start Now →
          </a>
          <a href="#" style={{
            fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 15,
            background: 'transparent', color: '#FFFFFF', textDecoration: 'none',
            padding: '16px 36px', borderRadius: 8,
            border: '2px solid rgba(8,9,10,0.2)',
            transition: 'border-color 0.2s ease',
            display: 'inline-block',
          }}
            onMouseEnter={e => (e.currentTarget.style.borderColor = 'rgba(8,9,10,0.5)')}
            onMouseLeave={e => (e.currentTarget.style.borderColor = 'rgba(8,9,10,0.2)')}
          >
            Schedule a Call
          </a>
        </div>
      </div>
    </div>

    <style>{`
      @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
    `}</style>
  </section>
);

export default CTA;
