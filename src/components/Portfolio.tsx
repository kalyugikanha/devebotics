const projects = [
  {
    id: 'p1', title: 'FoodFleet App', category: 'Mobile App', span: 'large',
    img: '/images/generic-2.png',
    desc: 'On-demand food delivery platform with real-time tracking',
    tags: ['Flutter', 'Node.js', 'Maps API'],
  },
  {
    id: 'p2', title: 'Commerce Studio', category: 'Web Platform', span: 'large',
    img: '/images/generic-1.png',
    desc: 'Custom Shopify + Next.js e-commerce with 3x conversion lift',
    tags: ['Next.js', 'Shopify', 'Stripe'],
  },
  {
    id: 'p3', title: 'GrowthDash', category: 'Marketing Dashboard', span: 'small',
    img: '/images/generic-2.png',
    desc: 'Performance marketing analytics platform',
    tags: ['React', 'Python', 'D3.js'],
  },
  {
    id: 'p4', title: 'MediCore ERP', category: 'Enterprise Software', span: 'small',
    img: '/images/generic-1.png',
    desc: 'Hospital management system with AI-powered scheduling',
    tags: ['React', 'Django', 'PostgreSQL'],
  },
  {
    id: 'p5', title: 'LearnLane LMS', category: 'EdTech', span: 'small',
    img: '/images/generic-2.png',
    desc: 'E-learning platform serving 10,000+ students',
    tags: ['Next.js', 'AWS', 'Video.js'],
  },
  {
    id: 'p6', title: 'FinEdge AI', category: 'FinTech', span: 'small',
    img: '/images/generic-1.png',
    desc: 'AI-powered trading dashboard with predictive analytics',
    tags: ['Python', 'React', 'OpenAI'],
  },
];

const Portfolio = () => (
  <section style={{ padding: '120px 0', background: '#FFFFFF' }}>
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
      {/* Heading */}
      <div className="fade-up" style={{
        display: 'flex', justifyContent: 'space-between',
        alignItems: 'flex-end', flexWrap: 'wrap', gap: 24, marginBottom: 56,
      }}>
        <div>
          <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)' }}>
            Our <span className="accent">Work.</span>
          </h2>
          <p style={{
            fontFamily: 'Lexend, sans-serif', fontSize: 16,
            color: '#4B5563', marginTop: 16,
          }}>
            A selection of projects we're proud of.
          </p>
        </div>
        <a href="#" style={{
          fontFamily: 'Lexend, sans-serif', fontWeight: 400,
          fontSize: 14, color: '#4B5563', textDecoration: 'none',
          transition: 'color 0.2s ease',
        }}
          onMouseEnter={e => (e.currentTarget.style.color = '#111827')}
          onMouseLeave={e => (e.currentTarget.style.color = '#4B5563')}
        >
          View all projects →
        </a>
      </div>

      {/* Bento grid */}
      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(4, 1fr)',
        gridTemplateRows: 'auto auto',
        gap: 16,
      }} className="portfolio-grid">
        {projects.map((p, i) => (
          <div
            key={p.id}
            className="fade-up"
            style={{
              gridColumn: i < 2 ? 'span 2' : 'span 1',
              borderRadius: 12, overflow: 'hidden',
              position: 'relative', cursor: 'pointer',
              aspectRatio: i < 2 ? '16/10' : '1/1',
              animationDelay: `${i * 0.07}s`,
            }}
          >
            <img
              src={p.img}
              alt={p.title}
              loading="lazy"
              style={{
                width: '100%', height: '100%', objectFit: 'cover',
                transition: 'transform 0.5s ease',
              }}
            />
            {/* Default overlay */}
            <div style={{
              position: 'absolute', inset: 0,
              background: 'linear-gradient(to top, rgba(8,9,10,0.85) 0%, rgba(8,9,10,0.2) 50%, transparent 100%)',
            }} />
            {/* Category badge */}
            <div style={{
              position: 'absolute', top: 16, left: 16,
              fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#4B5563',
              background: 'rgba(255,255,255,0.7)', border: '1px solid #E5E7EB',
              borderRadius: 100, padding: '4px 12px',
              backdropFilter: 'blur(4px)',
            }}>
              {p.category}
            </div>
            {/* Bottom content */}
            <div style={{
              position: 'absolute', bottom: 0, left: 0, right: 0, padding: '24px',
            }}>
              <div style={{
                fontFamily: 'Montserrat, sans-serif', fontWeight: 800,
                fontSize: i < 2 ? 22 : 16, color: '#111827', marginBottom: 6,
              }}>
                {p.title}
              </div>
              <div style={{
                fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#A0A0A0',
                marginBottom: 12,
              }}>
                {p.desc}
              </div>
              <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                {p.tags.map(tag => (
                  <span key={tag} style={{
                    fontFamily: 'Lexend, sans-serif', fontSize: 10,
                    color: '#4B5563', background: 'rgba(14,16,18,0.8)',
                    border: '1px solid #E5E7EB', borderRadius: 100,
                    padding: '2px 8px',
                  }}>
                    {tag}
                  </span>
                ))}
              </div>
            </div>

            {/* Hover overlay */}
            <div style={{
              position: 'absolute', inset: 0,
              background: 'rgba(8,9,10,0)',
              transition: 'background 0.3s ease',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
            }}
              onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.background = 'rgba(8,9,10,0.4)';
                const img = (e.currentTarget as HTMLElement).previousElementSibling as HTMLElement;
                if (img?.tagName === 'IMG') img.style.transform = 'scale(1.05)';
              }}
              onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.background = 'rgba(8,9,10,0)';
                const img = (e.currentTarget as HTMLElement).previousElementSibling as HTMLElement;
                if (img?.tagName === 'IMG') img.style.transform = 'scale(1)';
              }}
            >
              <span style={{
                fontFamily: 'Lexend, sans-serif', fontWeight: 600,
                fontSize: 13, color: '#FFFFFF', background: '#1F8844',
                padding: '8px 20px', borderRadius: 6,
                opacity: 0, transition: 'opacity 0.3s ease',
              }}
                className="portfolio-view-cta"
              >
                View Project →
              </span>
            </div>
          </div>
        ))}
      </div>
    </div>

    <style>{`
      div:hover > .portfolio-view-cta { opacity: 1 !important; }
      @media (max-width: 1024px) {
        .portfolio-grid { grid-template-columns: repeat(2, 1fr) !important; }
        .portfolio-grid > div:nth-child(-n+2) { grid-column: span 1 !important; aspect-ratio: 1/1 !important; }
      }
      @media (max-width: 600px) {
        .portfolio-grid { grid-template-columns: 1fr !important; }
        .portfolio-grid > div { grid-column: span 1 !important; }
      }
    `}</style>
  </section>
);

export default Portfolio;
