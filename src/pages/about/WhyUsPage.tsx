import { useEffect } from 'react';
import { Link } from 'react-router-dom';

const ADVANTAGES = [
  { num: '01', title: 'Elite Engineering', desc: 'We do not hire juniors. Every line of code is written by senior architects who have built systems scaling to millions of users.' },
  { num: '02', title: 'Radical Speed', desc: 'Our proprietary component libraries and boilerplate architectures allow us to move 3x faster than traditional agencies without compromising quality.' },
  { num: '03', title: 'Design That Converts', desc: 'Beautiful UI is useless if it doesn\'t drive revenue. We blend stunning aesthetics with conversion-rate optimization (CRO) principles.' },
  { num: '04', title: 'Zero Vendor Lock-in', desc: 'You own everything. The codebase, the infrastructure, the IP. We build it, hand you the keys, and support you as you scale.' }
];

export default function WhyUsPage() {
  useEffect(() => {
    document.title = 'Why DevBotics? | The Unfair Advantage';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72, color: '#F0F0F0' }}>
      
      {/* 1. CINEMATIC HERO */}
      <section style={{ minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', position: 'relative', overflow: 'hidden' }}>
        {/* Glow */}
        <div style={{ position: 'absolute', top: '20%', left: '50%', transform: 'translate(-50%, -50%)', width: '60vw', height: '60vw', background: 'radial-gradient(circle, rgba(232,255,71,0.08) 0%, transparent 60%)', pointerEvents: 'none' }} />
        
        <div style={{ maxWidth: 1200, margin: '0 auto', padding: '0 40px', textAlign: 'center', position: 'relative', zIndex: 1, opacity: 0, animation: 'fadeInUp 1s ease-out forwards' }}>
          <div style={{ display: 'inline-block', border: '1px solid #1C1E21', background: '#0E1012', padding: '8px 24px', borderRadius: 100, marginBottom: 40 }}>
            <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.2em' }}>THE DEVBOTICS STANDARD</span>
          </div>
          <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 110px)', lineHeight: 0.95, letterSpacing: '-0.04em', marginBottom: 32 }}>
            We build what your<br />
            <span style={{ color: '#1C1E21', WebkitTextStroke: '2px #E8FF47' }}>competitors fear.</span>
          </h1>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#6B6F76', maxWidth: 600, margin: '0 auto' }}>
            No templates. No offshore juniors. No bloated timelines. Just elite engineering designed to dominate your market.
          </p>
        </div>
      </section>

      {/* 2. STICKY SCROLL SECTION (CREATIVE LAYOUT) */}
      <section style={{ padding: '100px 0', background: '#0E1012', borderTop: '1px solid #1C1E21', borderBottom: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', display: 'flex', gap: 80 }} className="sticky-layout">
          
          {/* Left: Sticky Title */}
          <div style={{ flex: '1 1 40%', position: 'relative' }} className="sticky-sidebar">
            <div style={{ position: 'sticky', top: 120 }}>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(40px, 5vw, 64px)', lineHeight: 1.1, marginBottom: 24 }}>
                The anatomy of<br />an <span style={{ color: '#E8FF47' }}>unfair</span><br />advantage.
              </h2>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.6, maxWidth: 400 }}>
                Here is exactly why the fastest growing startups and established enterprises trust DevBotics with their core infrastructure.
              </p>
            </div>
          </div>

          {/* Right: Scrolling Cards */}
          <div style={{ flex: '1 1 60%', display: 'flex', flexDirection: 'column', gap: 40, marginTop: 40 }}>
            {ADVANTAGES.map((adv, i) => (
              <div key={adv.num} style={{ background: '#08090A', border: '1px solid #1C1E21', borderRadius: 24, padding: 60, opacity: 0, animation: `fadeInRight 0.8s ease-out ${0.2 + (i * 0.1)}s forwards`, transition: 'all 0.3s ease' }} className="hover-card">
                <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 80, color: '#1C1E21', lineHeight: 0.8, marginBottom: 24 }}>
                  {adv.num}
                </div>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 32, marginBottom: 16 }}>{adv.title}</h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#6B6F76', lineHeight: 1.6 }}>{adv.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 3. ASYMMETRICAL BENTO GRID */}
      <section style={{ padding: '140px 0' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ textAlign: 'center', marginBottom: 80 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 4vw, 56px)' }}>Beyond the Code</h2>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gridTemplateRows: 'auto auto', gap: 24 }} className="bento-grid">
            
            {/* Block 1 */}
            <div style={{ gridColumn: 'span 2', background: 'linear-gradient(135deg, #0A0C0F 0%, #111214 100%)', border: '1px solid #1C1E21', borderRadius: 24, padding: 60, position: 'relative', overflow: 'hidden' }}>
              <div style={{ position: 'absolute', right: -50, top: -50, width: 200, height: 200, background: '#E8FF47', filter: 'blur(100px)', opacity: 0.1 }} />
              <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 36, marginBottom: 16 }}>Direct Access to Founders</h3>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#A0A0A0', lineHeight: 1.6, maxWidth: 400 }}>No project managers playing broken telephone. You get a direct Slack channel with the engineers and architects actually building your product.</p>
            </div>

            {/* Block 2 */}
            <div style={{ gridColumn: 'span 1', background: '#E8FF47', borderRadius: 24, padding: 60, display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
              <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: '#08090A', lineHeight: 1.1, marginBottom: 16 }}>100%<br/>In-House</h3>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#1C1E21', fontWeight: 500 }}>We never outsource. Ever.</p>
            </div>

            {/* Block 3 */}
            <div style={{ gridColumn: 'span 1', background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 24, padding: 60 }}>
              <div style={{ fontSize: 40, marginBottom: 24 }}>🚀</div>
              <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 24, marginBottom: 12 }}>Scale-Ready</h3>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#6B6F76', lineHeight: 1.6 }}>Architectures designed to handle your first 1M users without breaking a sweat.</p>
            </div>

            {/* Block 4 */}
            <div style={{ gridColumn: 'span 2', background: '#0A0C0F', border: '1px solid #1C1E21', borderRadius: 24, padding: 0, overflow: 'hidden', display: 'flex' }} className="split-bento">
              <div style={{ flex: 1, padding: 60 }}>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 32, marginBottom: 16 }}>Design Driven</h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.6 }}>We believe enterprise software shouldn't look like an Excel spreadsheet. We bring consumer-grade aesthetics to B2B platforms.</p>
              </div>
              <div style={{ flex: 1, background: 'url(https://images.unsplash.com/photo-1558655146-d09347e92766?w=800&q=80) center/cover' }} className="bento-img" />
            </div>

          </div>
        </div>
      </section>

      {/* 4. MASSIVE CTA */}
      <section style={{ padding: '140px 40px', textAlign: 'center', background: '#E8FF47', color: '#08090A' }}>
        <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(40px, 6vw, 80px)', lineHeight: 1.0, letterSpacing: '-0.03em', marginBottom: 40 }}>
          Stop compromising.<br />Start building.
        </h2>
        <Link to="/contact" style={{ display: 'inline-block', background: '#08090A', color: '#F0F0F0', padding: '20px 48px', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 18, borderRadius: 100, textDecoration: 'none', transition: 'transform 0.2s', boxShadow: '0 20px 40px rgba(8,9,10,0.3)' }} onMouseEnter={e => e.currentTarget.style.transform = 'scale(1.05)'} onMouseLeave={e => e.currentTarget.style.transform = 'scale(1)'}>
          Talk to our team
        </Link>
      </section>

      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        
        .hover-card:hover { border-color: #E8FF47 !important; transform: translateX(-10px); }
        
        @media(max-width: 1024px) {
          .sticky-layout { flex-direction: column; gap: 40px; }
          .sticky-sidebar { position: relative; }
          .sticky-sidebar > div { position: relative; top: 0; }
          .bento-grid { grid-template-columns: 1fr !important; }
          .bento-grid > div { grid-column: span 1 !important; }
          .split-bento { flex-direction: column; }
          .bento-img { min-height: 200px; }
        }
      `}</style>
    </main>
  );
}
