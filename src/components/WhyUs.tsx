const reasons = [
  {
    n: '01',
    title: 'On-Time Delivery, Always',
    desc: 'We commit to deadlines and stick to them. Our project management ensures milestones are met without compromise on quality.',
  },
  {
    n: '02',
    title: 'Transparent Communication',
    desc: 'Weekly updates, open Slack channels, and a dedicated project manager keep you informed at every stage.',
  },
  {
    n: '03',
    title: 'Post-Launch Support',
    desc: '90-day post-launch warranty with guaranteed bug fixes. We stay with you long after deployment.',
  },
  {
    n: '04',
    title: 'End-to-End Expertise',
    desc: 'From strategy and design to code and marketing — one studio for your entire digital lifecycle.',
  },
  {
    n: '05',
    title: 'Scalable Architecture',
    desc: 'We build with growth in mind. Our solutions scale seamlessly from 100 to 1 million users.',
  },
  {
    n: '06',
    title: 'NDA & IP Protection',
    desc: 'Your ideas are safe with us. We sign NDAs before any discussion and you own 100% of the IP.',
  },
];

const WhyUs = () => (
  <section style={{ padding: '120px 0', background: '#FFFFFF' }}>
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
      {/* Heading */}
      <div className="fade-up" style={{ marginBottom: 80 }}>
        <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)' }}>
          Why Choose <span className="accent">Us.</span>
        </h2>
      </div>

      <div style={{
        display: 'grid', gridTemplateColumns: '45% 1fr',
        gap: 80, alignItems: 'start',
      }} className="whyus-grid">
        {/* LEFT — Real image */}
        <div style={{ position: 'sticky', top: 100 }}>
          <div style={{
            borderRadius: 16, overflow: 'hidden',
            position: 'relative', lineHeight: 0,
            borderLeft: '4px solid #1F8844',
          }}>
            <img
              src="/images/generic-1.png"
              alt="DevBotics team at work"
              loading="lazy"
              style={{ width: '100%', height: 'auto', display: 'block', aspectRatio: '4/3', objectFit: 'cover' }}
            />
            <div style={{
              position: 'absolute', inset: 0,
              background: 'rgba(8,9,10,0.25)',
            }} />
          </div>
          {/* Badge */}
          <div style={{
            marginTop: 24, padding: '16px 24px',
            background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 10,
            display: 'flex', alignItems: 'center', gap: 16,
          }}>
            <span style={{
              fontFamily: 'Montserrat, sans-serif', fontWeight: 900,
              fontSize: 36, color: '#1F8844',
            }}>5★</span>
            <div>
              <div style={{
                fontFamily: 'Montserrat, sans-serif', fontWeight: 700,
                fontSize: 15, color: '#111827',
              }}>
                Average Client Rating
              </div>
              <div style={{
                fontFamily: 'Lexend, sans-serif', fontSize: 12,
                color: '#4B5563', marginTop: 2,
              }}>
                Based on 50+ project reviews
              </div>
            </div>
          </div>
        </div>

        {/* RIGHT — Reasons list */}
        <div>
          {reasons.map((r, i) => (
            <div
              key={r.n}
              className="why-row fade-up"
              style={{
                animationDelay: `${i * 0.08}s`,
                display: 'grid', gridTemplateColumns: '48px 1fr',
                gap: 20, alignItems: 'start',
              }}
            >
              <div style={{
                fontFamily: 'Lexend, sans-serif', fontWeight: 300,
                fontSize: 11, color: '#9CA3AF', letterSpacing: '0.1em',
                paddingTop: 4,
              }}>
                {r.n}
              </div>
              <div>
                <div style={{
                  fontFamily: 'Montserrat, sans-serif', fontWeight: 700,
                  fontSize: 18, color: '#111827', marginBottom: 8,
                }}>
                  {r.title}
                </div>
                <div style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 14,
                  color: '#4B5563', lineHeight: 1.7,
                }}>
                  {r.desc}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>

    <style>{`
      @media (max-width: 1024px) {
        .whyus-grid { grid-template-columns: 1fr !important; }
        .whyus-grid > div:first-child { position: relative !important; top: auto !important; }
      }
    `}</style>
  </section>
);

export default WhyUs;
