import { useEffect } from 'react';
import { Link } from 'react-router-dom';

export default function OverviewPage() {
  useEffect(() => {
    document.title = 'About DevBotics | Company Overview';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72, overflow: 'hidden' }}>
      {/* Background Elements */}
      <div style={{ position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh', pointerEvents: 'none', zIndex: 0 }}>
        <div style={{ position: 'absolute', top: '-10%', left: '-10%', width: '40vw', height: '40vw', background: 'radial-gradient(circle, rgba(232,255,71,0.03) 0%, transparent 70%)' }} />
        <div style={{ position: 'absolute', bottom: '-20%', right: '-10%', width: '60vw', height: '60vw', background: 'radial-gradient(circle, rgba(255,255,255,0.02) 0%, transparent 70%)' }} />
      </div>

      <div style={{ position: 'relative', zIndex: 1 }}>
        {/* HERO */}
        <section style={{ maxWidth: 1400, margin: '0 auto', padding: '120px 40px 60px', textAlign: 'center' }}>
          <div style={{ animation: 'fadeInUp 0.8s ease-out forwards', opacity: 0 }}>
            <div style={{ display: 'inline-block', background: '#111214', border: '1px solid #1C1E21', padding: '6px 16px', borderRadius: 100, marginBottom: 32 }}>
              <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em' }}>✦ OVERVIEW</span>
            </div>
            <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 100px)', color: '#F0F0F0', lineHeight: 0.95, letterSpacing: '-0.03em', marginBottom: 32 }}>
              Engineering <span style={{ color: '#E8FF47' }}>Tomorrow.</span>
            </h1>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 'clamp(18px, 2vw, 24px)', color: '#6B6F76', lineHeight: 1.5, maxWidth: 800, margin: '0 auto' }}>
              We are a collective of world-class engineers, visionary designers, and growth hackers building the digital infrastructure for industry leaders.
            </p>
          </div>
        </section>

        {/* BENTO GRID STATS */}
        <section style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px 100px' }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gridTemplateRows: 'repeat(2, 240px)', gap: 20 }}>
            {/* Box 1 (Large) */}
            <div style={{ gridColumn: 'span 2', gridRow: 'span 2', background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 24, padding: 48, position: 'relative', overflow: 'hidden', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.1s forwards' }}>
              <div style={{ position: 'absolute', top: 0, right: 0, width: '100%', height: '100%', background: 'linear-gradient(135deg, rgba(232,255,71,0.05) 0%, transparent 100%)', pointerEvents: 'none' }} />
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 80, color: '#F0F0F0', lineHeight: 1, marginBottom: 16 }}>150+</h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#E8FF47', marginBottom: 24 }}>Enterprise Projects Delivered</div>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.6, maxWidth: 300 }}>From scalable SaaS platforms to complex AI integrations, we've successfully shipped for clients globally.</p>
            </div>

            {/* Box 2 */}
            <div style={{ gridColumn: 'span 1', gridRow: 'span 1', background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 24, padding: 32, display: 'flex', flexDirection: 'column', justifyContent: 'center', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.2s forwards' }}>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 56, color: '#F0F0F0', lineHeight: 1, marginBottom: 8 }}>40<span style={{ color: '#E8FF47' }}>+</span></h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76' }}>Global Tech Experts</div>
            </div>

            {/* Box 3 */}
            <div style={{ gridColumn: 'span 1', gridRow: 'span 1', background: '#0E1012', border: '1px solid #1C1E21', borderRadius: 24, padding: 32, display: 'flex', flexDirection: 'column', justifyContent: 'center', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.3s forwards' }}>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 56, color: '#F0F0F0', lineHeight: 1, marginBottom: 8 }}>15<span style={{ color: '#E8FF47' }}>+</span></h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76' }}>Countries Served</div>
            </div>

            {/* Box 4 (Wide) */}
            <div style={{ gridColumn: 'span 2', gridRow: 'span 1', background: '#E8FF47', border: '1px solid #E8FF47', borderRadius: 24, padding: 32, display: 'flex', alignItems: 'center', justifyContent: 'space-between', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.4s forwards' }}>
              <div>
                <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 40, color: '#08090A', lineHeight: 1.1, marginBottom: 8 }}>Obsessed with<br/>Quality.</h2>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(8,9,10,0.7)' }}>Zero templates. Zero compromises.</div>
              </div>
              <div style={{ width: 80, height: 80, background: '#08090A', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#E8FF47" strokeWidth="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
            </div>
          </div>
        </section>

        {/* MISSION */}
        <section style={{ padding: '100px 0', borderTop: '1px solid #1C1E21', background: '#0A0C0F' }}>
          <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', display: 'flex', gap: 80, alignItems: 'center', flexWrap: 'wrap' }}>
            <div style={{ flex: '1 1 500px', opacity: 0, animation: 'fadeInLeft 0.6s ease-out 0.2s forwards' }}>
              <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=80" alt="Team" style={{ width: '100%', height: 600, objectFit: 'cover', borderRadius: 24, filter: 'grayscale(30%)' }} />
            </div>
            <div style={{ flex: '1 1 500px', opacity: 0, animation: 'fadeInRight 0.6s ease-out 0.4s forwards' }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 24 }}>// THE MISSION</div>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 48px)', color: '#F0F0F0', lineHeight: 1.1, marginBottom: 32 }}>
                We bridge the gap between imagination and execution.
              </h2>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#6B6F76', lineHeight: 1.7, marginBottom: 24 }}>
                DevBotics was founded on a simple principle: most agencies treat software like an assembly line. We treat it like craftsmanship.
              </p>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#6B6F76', lineHeight: 1.7, marginBottom: 48 }}>
                Whether we are building a multi-tenant SaaS application, a native mobile app, or integrating cutting-edge LLMs, our goal is to deliver digital experiences that feel magical and perform flawlessly at scale.
              </p>
              <Link to="/contact" style={{ display: 'inline-block', background: 'transparent', color: '#F0F0F0', border: '1px solid #3A3D42', padding: '16px 32px', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, borderRadius: 8, textDecoration: 'none', transition: 'all 0.3s' }} onMouseEnter={e => { e.currentTarget.style.borderColor = '#E8FF47'; e.currentTarget.style.color = '#E8FF47'; }} onMouseLeave={e => { e.currentTarget.style.borderColor = '#3A3D42'; e.currentTarget.style.color = '#F0F0F0'; }}>
                Work With Us →
              </Link>
            </div>
          </div>
        </section>
      </div>

      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @media(max-width:1024px){
          section > div > div { grid-template-columns: 1fr !important; grid-template-rows: auto !important; }
          section > div > div > div { grid-column: span 1 !important; grid-row: auto !important; min-height: 240px; }
        }
      `}</style>
    </main>
  );
}
