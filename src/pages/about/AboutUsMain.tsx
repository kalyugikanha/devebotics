import { useEffect } from 'react';
import { Link } from 'react-router-dom';
import Awards from '../../components/Awards';
import FAQ from '../../components/FAQ';
const MILESTONES = [
  { y: '2020', m: 'The Spark', t: 'DevBotics is Born', d: 'Started by three engineers in a small garage, focused on building ultra-fast React websites for local startups. No funding, just passion.' },
  { y: '2021', m: 'Scale Up', t: 'First Enterprise SaaS', d: 'Landed our first enterprise contract. Expanded the team to 15 developers and moved into a proper office space. Revenue hit $1M.' },
  { y: '2023', m: 'The Shift', t: 'AI & Machine Learning', d: 'Recognized the AI wave early. Built custom LLM pipelines and RAG architectures for fintech and healthcare clients, completely revolutionizing their workflows.' },
  { y: '2025', m: 'Global Reach', t: 'World-Class Agency', d: 'Today, DevBotics is a powerhouse of 40+ experts delivering end-to-end digital transformation across 15 countries.' }
];

export default function AboutUsMain() {
  useEffect(() => {
    document.title = 'About DevBotics | Company Overview';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72, overflow: 'hidden' }}>
      {/* Background Elements */}
      <div style={{ position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh', pointerEvents: 'none', zIndex: 0 }}>
        <div style={{ position: 'absolute', top: '-10%', left: '-10%', width: '40vw', height: '40vw', background: 'radial-gradient(circle, rgba(31,136,68,0.03) 0%, transparent 70%)' }} />
        <div style={{ position: 'absolute', bottom: '-20%', right: '-10%', width: '60vw', height: '60vw', background: 'radial-gradient(circle, rgba(31,136,68,0.02) 0%, transparent 70%)' }} />
      </div>

      <div style={{ position: 'relative', zIndex: 1 }}>
        {/* HERO */}
        <section style={{ maxWidth: 1400, margin: '0 auto', padding: '120px 40px 60px', textAlign: 'center' }}>
          <div style={{ animation: 'fadeInUp 0.8s ease-out forwards', opacity: 0 }}>
            <div style={{ display: 'inline-block', background: '#F3F4F6', border: '1px solid #E5E7EB', padding: '6px 16px', borderRadius: 100, marginBottom: 32 }}>
              <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em' }}>✦ ABOUT US</span>
            </div>
            <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 100px)', color: '#111827', lineHeight: 0.95, letterSpacing: '-0.03em', marginBottom: 32 }}>
              Engineering <span style={{ color: '#1F8844' }}>Tomorrow.</span>
            </h1>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 'clamp(18px, 2vw, 24px)', color: '#4B5563', lineHeight: 1.5, maxWidth: 800, margin: '0 auto' }}>
              We are a collective of world-class engineers, visionary designers, and growth hackers building the digital infrastructure for industry leaders.
            </p>
          </div>
        </section>

        {/* BENTO GRID STATS */}
        <section style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px 100px' }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gridTemplateRows: 'repeat(2, 240px)', gap: 20 }}>
            {/* Box 1 (Large) */}
            <div style={{ gridColumn: 'span 2', gridRow: 'span 2', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 24, padding: 48, position: 'relative', overflow: 'hidden', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.1s forwards' }}>
              <div style={{ position: 'absolute', top: 0, right: 0, width: '100%', height: '100%', background: 'linear-gradient(135deg, rgba(31,136,68,0.05) 0%, transparent 100%)', pointerEvents: 'none' }} />
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 80, color: '#111827', lineHeight: 1, marginBottom: 16 }}>150+</h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#1F8844', marginBottom: 24 }}>Enterprise Projects Delivered</div>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#4B5563', lineHeight: 1.6, maxWidth: 300 }}>From scalable SaaS platforms to complex AI integrations, we've successfully shipped for clients globally.</p>
            </div>

            {/* Box 2 */}
            <div style={{ gridColumn: 'span 1', gridRow: 'span 1', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 24, padding: 32, display: 'flex', flexDirection: 'column', justifyContent: 'center', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.2s forwards' }}>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 56, color: '#111827', lineHeight: 1, marginBottom: 8 }}>40<span style={{ color: '#1F8844' }}>+</span></h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563' }}>Global Tech Experts</div>
            </div>

            {/* Box 3 */}
            <div style={{ gridColumn: 'span 1', gridRow: 'span 1', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 24, padding: 32, display: 'flex', flexDirection: 'column', justifyContent: 'center', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.3s forwards' }}>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 56, color: '#111827', lineHeight: 1, marginBottom: 8 }}>15<span style={{ color: '#1F8844' }}>+</span></h2>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563' }}>Countries Served</div>
            </div>

            {/* Box 4 (Wide) */}
            <div style={{ gridColumn: 'span 2', gridRow: 'span 1', background: '#1F8844', border: '1px solid #1F8844', borderRadius: 24, padding: 32, display: 'flex', alignItems: 'center', justifyContent: 'space-between', opacity: 0, animation: 'fadeInUp 0.6s ease-out 0.4s forwards' }}>
              <div>
                <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 40, color: '#FFFFFF', lineHeight: 1.1, marginBottom: 8 }}>Obsessed with<br/>Quality.</h2>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)' }}>Zero templates. Zero compromises.</div>
              </div>
              <div style={{ width: 80, height: 80, background: '#FFFFFF', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1F8844" strokeWidth="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
            </div>
          </div>
        </section>

        {/* OUR JOURNEY SECTION (from JourneyPage) */}
        <section style={{ maxWidth: 1200, margin: '0 auto', padding: '60px 40px 120px', position: 'relative' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 16 }}>// OUR EVOLUTION</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 5vw, 56px)', color: '#111827', lineHeight: 1.1 }}>
              From Idea to <span style={{ color: '#1F8844', fontStyle: 'italic' }}>Impact.</span>
            </h2>
          </div>
          
          <div style={{ position: 'absolute', top: 160, bottom: 120, left: '50%', width: 2, background: 'linear-gradient(to bottom, #E5E7EB, #1F8844, #E5E7EB)', transform: 'translateX(-50%)', opacity: 0.3 }} className="timeline-line" />

          <div style={{ display: 'flex', flexDirection: 'column', gap: 120 }}>
            {MILESTONES.map((item, i) => (
              <div key={item.y} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexDirection: i % 2 === 0 ? 'row' : 'row-reverse', opacity: 0, animation: `fadeInUp 0.6s ease-out ${i * 0.2}s forwards` }} className="timeline-row">
                
                <div style={{ flex: 1, padding: i % 2 === 0 ? '0 60px 0 0' : '0 0 0 60px', textAlign: i % 2 === 0 ? 'right' : 'left' }} className="timeline-content">
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#1F8844', letterSpacing: '0.1em', marginBottom: 8, textTransform: 'uppercase' }}>{item.m}</div>
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 36, color: '#111827', marginBottom: 16 }}>{item.t}</h3>
                  <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#4B5563', lineHeight: 1.7 }}>{item.d}</p>
                </div>

                <div style={{ position: 'relative', zIndex: 2, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  <div style={{ width: 80, height: 80, borderRadius: '50%', background: '#FFFFFF', border: '1px solid #E5E7EB', display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 20, color: '#111827', boxShadow: '0 8px 30px rgba(0,0,0,0.05)' }}>
                    {item.y}
                  </div>
                </div>

                <div style={{ flex: 1 }} className="timeline-spacer" />
              </div>
            ))}
          </div>
        </section>

        {/* MISSION (from OverviewPage) */}
        <section style={{ padding: '100px 0', borderTop: '1px solid #E5E7EB', background: '#F9FAFB' }}>
          <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', display: 'flex', gap: 80, alignItems: 'center', flexWrap: 'wrap' }}>
            <div style={{ flex: '1 1 500px', opacity: 0, animation: 'fadeInLeft 0.6s ease-out 0.2s forwards' }}>
              <img src="/images/generic-1.png" alt="Team" style={{ width: '100%', height: 600, objectFit: 'cover', borderRadius: 24 }} />
            </div>
            <div style={{ flex: '1 1 500px', opacity: 0, animation: 'fadeInRight 0.6s ease-out 0.4s forwards' }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 24 }}>// THE MISSION</div>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 48px)', color: '#111827', lineHeight: 1.1, marginBottom: 32 }}>
                We bridge the gap between imagination and execution.
              </h2>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#4B5563', lineHeight: 1.7, marginBottom: 24 }}>
                DevBotics was founded on a simple principle: most agencies treat software like an assembly line. We treat it like craftsmanship.
              </p>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#4B5563', lineHeight: 1.7, marginBottom: 48 }}>
                Whether we are building a multi-tenant SaaS application, a native mobile app, or integrating cutting-edge LLMs, our goal is to deliver digital experiences that feel magical and perform flawlessly at scale.
              </p>
              <Link to="/contact" style={{ display: 'inline-block', background: '#1F8844', color: '#FFFFFF', padding: '16px 32px', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, borderRadius: 8, textDecoration: 'none', transition: 'all 0.3s' }} onMouseEnter={e => { e.currentTarget.style.opacity = '0.85' }} onMouseLeave={e => { e.currentTarget.style.opacity = '1' }}>
                Work With Us →
              </Link>
            </div>
          </div>
        </section>
        
        <Awards />
        <FAQ />
      </div>

      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @media(max-width:1024px){
          section > div > div { grid-template-columns: 1fr !important; grid-template-rows: auto !important; }
          section > div > div > div { grid-column: span 1 !important; grid-row: auto !important; min-height: 240px; }
        }
        @media(max-width:768px) {
          .timeline-line { left: 40px !important; }
          .timeline-row { flexDirection: column !important; alignItems: flex-start !important; gap: 32px; position: relative; }
          .timeline-content { padding: 0 0 0 80px !important; text-align: left !important; }
          .timeline-row > div:nth-child(2) { position: absolute; left: 0; top: 0; width: 80px; }
          .timeline-spacer { display: none; }
        }
      `}</style>
    </main>
  );
}
