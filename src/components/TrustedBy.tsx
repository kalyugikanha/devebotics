// Fix 8 — TrustedBy: Larger pills, more companies, proper sizing, tripled for seamless loop
const companies = [
  'RETAILX', 'FINEDGE', 'TRAVELMATE', 'SAASIFY', 'BRANDBOOST',
  'HEALTHFIRST', 'EDULEARN', 'LOGITRACK', 'CRAFTSTORE', 'FOODTECH',
  'PROPVISTA', 'AUTOZONE', 'STYLEMART', 'CLINICORE', 'SKYBOOKS',
  'TECHVAULT', 'NEXUSAI', 'GROWTHLAB', 'DATAPLEX', 'CLOUDFORGE',
];

// Triple the array so there is no gap in the infinite loop
const marqueeItems = [...companies, ...companies, ...companies];

const TrustedBy = () => (
  <section style={{ padding: '64px 0 80px', background: '#FFFFFF', overflow: 'hidden', minHeight: 120 }}>
    <div style={{
      maxWidth: 1400, margin: '0 auto', padding: '0 32px',
      textAlign: 'center', marginBottom: 40,
    }}>
      <p style={{
        fontFamily: 'Lexend, sans-serif', fontWeight: 300,
        fontSize: 12, letterSpacing: '0.2em', textTransform: 'uppercase',
        color: '#9CA3AF',
      }}>
        Trusted by innovative companies
      </p>
    </div>

    <div style={{
      overflow: 'hidden',
      maskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
      WebkitMaskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
    }}>
      <div style={{
        display: 'flex', alignItems: 'center', gap: 16,
        animation: 'marquee 25s linear infinite',
        width: 'max-content',
      }}>
        {marqueeItems.map((company, i) => (
          <div
            key={i}
            style={{
              display: 'inline-flex', alignItems: 'center', gap: 10,
              minWidth: 160, padding: '12px 24px',
              background: '#F9FAFB',
              border: '1px solid #E5E7EB',
              borderRadius: 8,
              flexShrink: 0,
              transition: 'border-color 0.2s ease',
              cursor: 'default',
            }}
            onMouseEnter={e => ((e.currentTarget as HTMLElement).style.borderColor = '#1F8844')}
            onMouseLeave={e => ((e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB')}
          >
            <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#1F8844', flexShrink: 0, display: 'inline-block' }} />
            <span style={{
              fontFamily: 'Montserrat, sans-serif', fontWeight: 600,
              fontSize: 14, color: '#111827', letterSpacing: '0.08em',
              whiteSpace: 'nowrap',
            }}>
              {company}
            </span>
          </div>
        ))}
      </div>
    </div>
  </section>
);

export default TrustedBy;
