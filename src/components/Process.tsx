// Fix 6 — Process: Horizontal 5-card layout with images, watermark numbers, dotted connector
const steps = [
  {
    num: '01', name: 'Discovery & Planning',
    desc: 'We deep-dive into your business goals, audience, and competition to define a crystal-clear roadmap.',
    img: 'https://images.unsplash.com/photo-1531538606174-0f90ff5dce83?w=500&q=80',
  },
  {
    num: '02', name: 'UI/UX Design',
    desc: 'Our designers craft wireframes and pixel-perfect interfaces focused on conversion and user delight.',
    img: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80',
  },
  {
    num: '03', name: 'Development & Testing',
    desc: 'Engineers build with clean, scalable code. Every feature is tested rigorously before delivery.',
    img: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&q=80',
  },
  {
    num: '04', name: 'Launch & Deployment',
    desc: 'We handle server setup, CI/CD pipelines, and smooth deployment with zero downtime.',
    img: 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=500&q=80',
  },
  {
    num: '05', name: 'Support & Growth',
    desc: 'Post-launch monitoring, updates, and growth strategies to keep your product ahead of the curve.',
    img: 'https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?w=500&q=80',
  },
];

const Process = () => (
  <section style={{ padding: '120px 0', background: '#08090A' }}>
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
      {/* Heading */}
      <div className="fade-up" style={{ marginBottom: 72 }}>
        <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)', marginBottom: 16 }}>
          How We <span className="accent">Work.</span>
        </h2>
        <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#6B6F76', maxWidth: 480 }}>
          Our proven 5-step process takes your idea from concept to a shipped product.
        </p>
      </div>

      {/* 5 horizontal cards */}
      <div style={{ position: 'relative' }}>
        {/* Dotted connector line */}
        <div style={{
          position: 'absolute', top: 80, left: '10%', right: '10%', height: 1,
          borderTop: '1px dashed #1C1E21', zIndex: 0,
          display: 'none', // shown via class
        }} className="process-line" />

        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(5, 1fr)',
          gap: 16,
          position: 'relative', zIndex: 1,
        }} className="process-grid">
          {steps.map((step, i) => (
            <div
              key={step.num}
              className="fade-up process-card"
              style={{
                background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 12,
                padding: '0 0 24px 0', overflow: 'hidden',
                transition: 'border-color 0.3s ease, transform 0.3s ease',
                animationDelay: `${i * 0.1}s`,
                cursor: 'default',
              }}
              onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.borderColor = '#E8FF47';
                (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)';
                const img = (e.currentTarget as HTMLElement).querySelector('img') as HTMLElement;
                if (img) img.style.transform = 'scale(1.04)';
              }}
              onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.borderColor = '#1C1E21';
                (e.currentTarget as HTMLElement).style.transform = 'translateY(0)';
                const img = (e.currentTarget as HTMLElement).querySelector('img') as HTMLElement;
                if (img) img.style.transform = 'scale(1)';
              }}
            >
              {/* Image with badge */}
              <div style={{ position: 'relative', height: 160, overflow: 'hidden' }}>
                <img src={step.img} alt={step.name} loading="lazy"
                  style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.4s ease' }} />
                <div style={{ position: 'absolute', inset: 0, background: 'rgba(8,9,10,0.3)' }} />
                <span style={{
                  position: 'absolute', top: 12, left: 12,
                  fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 11,
                  background: '#E8FF47', color: '#08090A', padding: '4px 10px', borderRadius: 4,
                  letterSpacing: '0.05em',
                }}>
                  STEP {step.num}
                </span>
              </div>

              {/* Content */}
              <div style={{ padding: '20px 20px 0' }}>
                {/* Watermark number */}
                <div style={{
                  fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 72,
                  color: '#111214', lineHeight: 1, marginBottom: -16, userSelect: 'none',
                }}>
                  {step.num}
                </div>
                <div style={{
                  fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 17,
                  color: '#F0F0F0', marginBottom: 10, lineHeight: 1.3,
                }}>
                  {step.name}
                </div>
                <div style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#6B6F76', lineHeight: 1.7,
                }}>
                  {step.desc}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>

    <style>{`
      @media (min-width: 1025px) { .process-line { display: block !important; } }
      @media (max-width: 1024px) { .process-grid { grid-template-columns: repeat(2, 1fr) !important; } }
      @media (max-width: 600px) { .process-grid { grid-template-columns: 1fr !important; } }
    `}</style>
  </section>
);

export default Process;
