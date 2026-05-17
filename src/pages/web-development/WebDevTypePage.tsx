import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { webPageData } from './webPageData';

const PHASES = [
  { n: '01', t: 'Discovery & Strategy', d: 'We audit your business goals, competitors, target audience & conversion requirements.', i: 'https://images.unsplash.com/photo-1531538606174-0f90ff5dce83?w=600&q=80', c: ['Business & audience research', 'Competitor website audit', 'SEO & keyword strategy', 'Sitemap & page architecture'], time: '3–5 days' },
  { n: '02', t: 'Wireframing & UX Design', d: 'Every page wireframed in Figma before any visual design begins.', i: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&q=80', c: ['Low-fidelity wireframes', 'User flow mapping', 'Mobile-first approach', 'Client approval checkpoint'], time: '5–7 days' },
  { n: '03', t: 'UI Design & Prototyping', d: 'Pixel-perfect Figma designs with interactive prototype for review.', i: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&q=80', c: ['High-fidelity UI design', 'Design system & style guide', 'Responsive breakpoints', 'Clickable Figma prototype'], time: '7–10 days' },
  { n: '04', t: 'Development', d: 'Clean, commented, scalable code. Weekly live preview links shared.', i: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&q=80', c: ['Frontend development', 'Backend & API integration', 'CMS setup & content entry', 'Payment / third-party integration'], time: '2–8 weeks' },
  { n: '05', t: 'Testing & Optimisation', d: 'Cross-browser, cross-device testing. Lighthouse score 90+ guaranteed.', i: 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&q=80', c: ['Cross-browser QA testing', 'Mobile responsiveness check', 'Page speed optimisation', 'SEO on-page setup'], time: '3–5 days' },
  { n: '06', t: 'Launch & Handover', d: 'Deployment, domain setup, training & 60 days free post-launch support.', i: 'https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?w=600&q=80', c: ['Server setup & deployment', 'Domain & SSL configuration', 'Client training session', '60-day support period'], time: '1–2 days' }
];

const RELATED = [
  { s: 'shopify', t: 'Shopify Development', i: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=300&q=80' },
  { s: 'nextjs-react', t: 'Next.js & React', i: 'https://images.unsplash.com/photo-1627398242454-45a1465c2479?w=300&q=80' },
  { s: 'saas-development', t: 'SaaS Development', i: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&q=80' }
];

export default function WebDevTypePage({ slug }: { slug: string }) {
  const data = webPageData[slug];
  const [activePhase, setActivePhase] = useState(0);
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  useEffect(() => {
    if (data) {
      document.title = data.meta;
      const meta = document.querySelector('meta[name="description"]');
      if (meta) meta.setAttribute('content', data.sub);
    }
  }, [data]);

  useEffect(() => {
    const handleScroll = () => {
      const items = document.querySelectorAll('.phase-item');
      let currentIdx = 0;
      items.forEach((item, idx) => {
        const rect = item.getBoundingClientRect();
        if (rect.top <= window.innerHeight * 0.5) currentIdx = idx;
      });
      setActivePhase(currentIdx);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  if (!data) return <div style={{ color: 'white', padding: 100, textAlign: 'center' }}>Service not found</div>;

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72 }}>
      {/* 1. CINEMATIC HERO */}
      <section style={{ minHeight: '90vh', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden', padding: '120px 40px' }}>
        <div style={{ position: 'absolute', inset: 0, zIndex: 0 }}>
          <img src={data.heroImage} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover', filter: 'brightness(0.3) contrast(1.2)' }} />
          <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to bottom, rgba(8,9,10,0.2) 0%, #08090A 100%)' }} />
          <div style={{ position: 'absolute', inset: 0, background: 'radial-gradient(circle at center, transparent 0%, #08090A 80%)' }} />
        </div>

        <div style={{ maxWidth: 1200, margin: '0 auto', textAlign: 'center', position: 'relative', zIndex: 1, opacity: 0, animation: 'fadeInUp 1s ease-out forwards' }}>
          <nav style={{ display: 'flex', gap: 8, alignItems: 'center', justifyContent: 'center', marginBottom: 32, flexWrap: 'wrap' }}>
            {[['Home', '/'], ['Web Development', '/web-development'], [data.title, null]].map(([label, href], i) => (
              <span key={i} style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                {href ? <Link to={href as string} style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#A0A0A0', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e=>e.currentTarget.style.color='#F0F0F0'} onMouseLeave={e=>e.currentTarget.style.color='#A0A0A0'}>{label}</Link>
                  : <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#E8FF47' }}>{label}</span>}
                {i < 2 && <span style={{ color: '#3A3D42', fontSize: 12 }}>/</span>}
              </span>
            ))}
          </nav>
          
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 100px)', lineHeight: 0.95, letterSpacing: '-0.03em', marginBottom: 32, color: '#F0F0F0' }}>
            {data.h1.split('\n').map((line: string, i: number) => (
              <span key={i} style={{ display: 'block', color: i === 1 ? '#E8FF47' : '#F0F0F0', fontStyle: i === 1 ? 'italic' : 'normal' }}>{line}</span>
            ))}
          </h1>
          
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 'clamp(18px, 2vw, 24px)', color: '#A0A0A0', lineHeight: 1.6, maxWidth: 800, margin: '0 auto 48px' }}>
            {data.sub}
          </p>
          
          <div style={{ display: 'flex', gap: 24, justifyContent: 'center', flexWrap: 'wrap' }}>
            <Link to="/contact" style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, background: '#E8FF47', color: '#08090A', textDecoration: 'none', padding: '18px 40px', borderRadius: 100, transition: 'all 0.3s ease', boxShadow: '0 10px 30px rgba(232,255,71,0.2)' }} onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-2px)'; e.currentTarget.style.boxShadow = '0 15px 40px rgba(232,255,71,0.3)'; }} onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 30px rgba(232,255,71,0.2)'; }}>
              Start Your Project
            </Link>
          </div>
        </div>
      </section>

      {/* 2. WHAT WE OFFER */}
      <section style={{ borderTop: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '100px 40px' }}>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// WHAT'S INCLUDED</div>
          <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px,4vw,48px)', color: '#F0F0F0', lineHeight: 1.1, marginBottom: 60 }}>Everything in Our<br />{data.title} Package.</h2>
          <div style={{ borderTop: '1px solid #1C1E21' }}>
            {data.features.map((f: any, i: number) => (
              <div key={i} style={{ padding: '32px 0', borderBottom: '1px solid #1C1E21', display: 'flex', gap: 40, alignItems: 'center', transition: 'all 0.3s', borderLeft: '3px solid transparent' }} className="feature-row" onMouseEnter={e => { e.currentTarget.style.background = '#0A0C0F'; e.currentTarget.style.borderLeftColor = '#E8FF47'; e.currentTarget.style.paddingLeft = '32px'; }} onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.borderLeftColor = 'transparent'; e.currentTarget.style.paddingLeft = '0'; }}>
                <h3 style={{ flex: 1, fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 22, color: '#F0F0F0', margin: 0 }}>{f.name}</h3>
                <p style={{ flex: 1, fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', margin: 0 }}>{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 3. KEY FEATURES (Zigzag) */}
      <section style={{ background: '#0E1012', padding: '100px 0' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 60 }}>// KEY FEATURES</div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 100 }}>
            {data.keyFeatures.map((kf: any, i: number) => (
              <div key={i} style={{ display: 'flex', gap: 80, alignItems: 'center', flexDirection: i % 2 !== 0 ? 'row-reverse' : 'row' }} className="zigzag-row">
                <div style={{ flex: 1 }}>
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 32, color: '#F0F0F0', marginBottom: 20 }}>{kf.title}</h3>
                  <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.7 }}>{kf.desc}</p>
                </div>
                <div style={{ flex: 1 }}>
                  <img src={kf.img} alt={kf.title} style={{ width: '100%', borderRadius: 8, filter: 'grayscale(20%)' }} />
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. TECH STACK & 5. PLATFORMS INCLUDED */}
      <section style={{ borderTop: '1px solid #1C1E21', padding: '100px 0' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', display: 'flex', gap: 60 }} className="tech-plat-row">
          <div style={{ flex: 1 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 24 }}>// TECH STACK</div>
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
              {data.techStack.map((t: string) => (
                <span key={t} style={{ border: '1px solid #1C1E21', padding: '10px 20px', borderRadius: 4, fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#F0F0F0' }}>{t}</span>
              ))}
            </div>
          </div>
          <div style={{ flex: 1 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 24 }}>// DELIVERABLES</div>
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
              {data.platforms.map((p: string) => (
                <span key={p} style={{ background: '#1C1E21', padding: '10px 20px', borderRadius: 4, fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#E8FF47' }}>{p}</span>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* 6. PROCESS (Sticky) */}
      <section style={{ background: '#0E1012', borderTop: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '100px 40px', display: 'flex', gap: 60 }} className="process-split">
          <div style={{ flex: '0 0 55%' }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// BUILD PROCESS</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: '#F0F0F0', lineHeight: 1.1, marginBottom: 80 }}>Concept to Launch<br />in 6 Phases.</h2>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 100 }}>
              {PHASES.map((p, i) => (
                <div key={i} className="phase-item" style={{ opacity: activePhase >= i ? 1 : 0.3, transition: 'opacity 0.4s' }}>
                  <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 64, color: '#1C1E21', lineHeight: 0.8, marginBottom: 24 }}>{p.n}</div>
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 28, color: '#F0F0F0', marginBottom: 16 }}>{p.t}</h3>
                  <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.6, marginBottom: 24 }}>{p.d}</p>
                  <ul style={{ listStyle: 'none', padding: 0, margin: '0 0 24px', display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {p.c.map(c => <li key={c} style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#A0A0A0', display: 'flex', gap: 12 }}><span style={{ color: '#E8FF47' }}>✓</span> {c}</li>)}
                  </ul>
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#E8FF47', fontWeight: 500 }}>Duration: {p.time}</div>
                </div>
              ))}
            </div>
          </div>
          <div style={{ flex: '0 0 45%', position: 'relative' }}>
            <div style={{ position: 'sticky', top: 120, height: 'calc(100vh - 240px)', borderRadius: 12, overflow: 'hidden' }}>
              <img src={PHASES[activePhase].i} alt="Phase" style={{ width: '100%', height: '100%', objectFit: 'cover', filter: 'grayscale(20%)', transition: 'all 0.4s ease' }} />
              <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to top, rgba(8,9,10,0.8), transparent)' }} />
              <div style={{ position: 'absolute', bottom: 32, left: 32, fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 24, color: '#F0F0F0' }}>PHASE {PHASES[activePhase].n}</div>
            </div>
          </div>
        </div>
      </section>

      {/* NO PRICING SECTION - Removed as per user request */}

      {/* 9. FAQ */}
      <section style={{ background: '#0E1012', padding: '100px 0', borderTop: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// QUESTIONS</div>
          <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: '#F0F0F0', lineHeight: 1.1, marginBottom: 60 }}>Honest Answers.</h2>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 60px' }} className="faq-grid">
            {[0, 1].map(colIdx => (
              <div key={colIdx} style={{ display: 'flex', flexDirection: 'column' }}>
                {data.faq.filter((_: any, idx: number) => idx % 2 === colIdx).map((faq: any, idx: number) => {
                  const globalIdx = colIdx === 0 ? idx * 2 : idx * 2 + 1;
                  const isOpen = openFaq === globalIdx;
                  return (
                    <div key={idx} style={{ borderBottom: '1px solid #1C1E21', padding: '24px 0' }}>
                      <button onClick={() => setOpenFaq(isOpen ? null : globalIdx)} style={{ width: '100%', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: 'transparent', border: 'none', color: '#F0F0F0', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 18, cursor: 'pointer', textAlign: 'left', padding: 0 }}>
                        {faq.q}
                        <span style={{ color: isOpen ? '#E8FF47' : '#6B6F76', fontSize: 24, fontWeight: 300 }}>{isOpen ? '−' : '+'}</span>
                      </button>
                      <div style={{ maxHeight: isOpen ? 200 : 0, overflow: 'hidden', transition: 'max-height 0.3s ease' }}>
                        <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#6B6F76', lineHeight: 1.6, paddingTop: 16, margin: 0 }}>{faq.a}</p>
                      </div>
                    </div>
                  );
                })}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 10. RELATED SERVICES */}
      <section style={{ padding: '100px 0', borderTop: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 40 }}>// YOU MIGHT ALSO NEED</div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 32 }} className="related-grid">
            {RELATED.filter(r => r.s !== slug).slice(0,3).map((r, i) => (
              <Link key={i} to={`/web-development/${r.s}`} style={{ textDecoration: 'none', border: '1px solid #1C1E21', borderRadius: 8, overflow: 'hidden', transition: 'transform 0.2s, border-color 0.2s' }} className="related-card" onMouseEnter={e => e.currentTarget.style.borderColor = '#E8FF47'} onMouseLeave={e => e.currentTarget.style.borderColor = '#1C1E21'}>
                <img src={r.i} alt="" style={{ width: '100%', height: 160, objectFit: 'cover' }} />
                <div style={{ padding: 24 }}>
                  <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 18, color: '#F0F0F0', marginBottom: 8 }}>{r.t}</h4>
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#E8FF47' }}>Learn more →</div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* 12. FINAL CTA */}
      <section style={{ padding: '100px 60px', background: '#E8FF47', position: 'relative', overflow: 'hidden' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', position: 'relative', zIndex: 2 }}>
          <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 88px)', color: '#08090A', lineHeight: 0.95, margin: '0 0 24px', letterSpacing: '-0.02em' }}>
            Your Website.<br />Our Obsession.
          </h2>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 500, fontSize: 18, color: 'rgba(8,9,10,0.6)', margin: '0 0 48px' }}>
            Tell us what you need. Free quote within 24 hours.
          </p>
          <div style={{ display: 'flex', gap: 24, alignItems: 'center', flexWrap: 'wrap' }}>
            <Link to="/contact" style={{ display: 'inline-block', background: '#08090A', color: '#E8FF47', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 14, padding: '18px 36px', textDecoration: 'none', transition: 'transform 0.2s', border: '1px solid #08090A' }} onMouseEnter={e => e.currentTarget.style.transform = 'translateY(-4px)'} onMouseLeave={e => e.currentTarget.style.transform = 'translateY(0)'}>
              [ START YOUR PROJECT ]
            </Link>
            <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: 'rgba(8,9,10,0.6)' }}>or call: +91 98765 43210</span>
          </div>
        </div>
        <div className="rotate-text hide-mobile" style={{ position: 'absolute', top: -60, right: -60, width: 400, height: 400, borderRadius: '50%', border: '1px solid rgba(8,9,10,0.1)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 24, color: 'rgba(8,9,10,0.15)', letterSpacing: '0.2em' }}>DEVBOTICS · BUILD · GROW ·</div>
        </div>
      </section>

      <style>{`
        @keyframes rotateText { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .rotate-text { animation: rotateText 20s linear infinite; }
        .related-card:hover { transform: translateY(-4px); }
        @media(max-width:1024px) {
          .type-hero { flex-direction: column; }
          .feature-row { flex-direction: column; gap: 16px; align-items: flex-start !important; }
          .zigzag-row { flex-direction: column !important; gap: 40px !important; }
          .tech-plat-row { flex-direction: column; }
          .process-split { flex-direction: column; }
          .process-split > div:last-child { display: none; }
        }
        @media(max-width:768px) {
          .pricing-grid { grid-template-columns: 1fr; }
          .faq-grid { grid-template-columns: 1fr; }
          .related-grid { grid-template-columns: 1fr; }
          .hide-mobile { display: none !important; }
        }
      `}</style>
    </main>
  );
}
