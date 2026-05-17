import { useEffect } from 'react';

const MILESTONES = [
  { y: '2020', m: 'The Spark', t: 'DevBotics is Born', d: 'Started by three engineers in a small garage, focused on building ultra-fast React websites for local startups. No funding, just passion.' },
  { y: '2021', m: 'Scale Up', t: 'First Enterprise SaaS', d: 'Landed our first enterprise contract. Expanded the team to 15 developers and moved into a proper office space. Revenue hit $1M.' },
  { y: '2023', m: 'The Shift', t: 'AI & Machine Learning', d: 'Recognized the AI wave early. Built custom LLM pipelines and RAG architectures for fintech and healthcare clients, completely revolutionizing their workflows.' },
  { y: '2025', m: 'Global Reach', t: 'World-Class Agency', d: 'Today, DevBotics is a powerhouse of 40+ experts delivering end-to-end digital transformation across 15 countries.' }
];

export default function JourneyPage() {
  useEffect(() => {
    document.title = 'Our Journey | DevBotics';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72 }}>
      <section style={{ maxWidth: 1000, margin: '0 auto', padding: '120px 40px 60px', textAlign: 'center' }}>
        <div style={{ animation: 'fadeInUp 0.8s ease-out forwards', opacity: 0 }}>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 24 }}>// OUR EVOLUTION</div>
          <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 88px)', color: '#F0F0F0', lineHeight: 1.0, letterSpacing: '-0.03em', marginBottom: 32 }}>
            From Idea to <span style={{ color: '#E8FF47', fontStyle: 'italic' }}>Impact.</span>
          </h1>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#6B6F76', lineHeight: 1.6, maxWidth: 600, margin: '0 auto' }}>
            A timeline of relentless execution, late nights, and breaking the boundaries of technology.
          </p>
        </div>
      </section>

      <section style={{ maxWidth: 1200, margin: '0 auto', padding: '60px 40px 120px', position: 'relative' }}>
        {/* Glow Line */}
        <div style={{ position: 'absolute', top: 60, bottom: 120, left: '50%', width: 2, background: 'linear-gradient(to bottom, #1C1E21, #E8FF47, #1C1E21)', transform: 'translateX(-50%)', opacity: 0.3 }} className="timeline-line" />

        <div style={{ display: 'flex', flexDirection: 'column', gap: 120 }}>
          {MILESTONES.map((item, i) => (
            <div key={item.y} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexDirection: i % 2 === 0 ? 'row' : 'row-reverse', opacity: 0, animation: `fadeInUp 0.6s ease-out ${i * 0.2}s forwards` }} className="timeline-row">
              
              <div style={{ flex: 1, padding: i % 2 === 0 ? '0 60px 0 0' : '0 0 0 60px', textAlign: i % 2 === 0 ? 'right' : 'left' }} className="timeline-content">
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.1em', marginBottom: 8, textTransform: 'uppercase' }}>{item.m}</div>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 36, color: '#F0F0F0', marginBottom: 16 }}>{item.t}</h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.7 }}>{item.d}</p>
              </div>

              <div style={{ position: 'relative', zIndex: 2, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <div style={{ width: 80, height: 80, borderRadius: '50%', background: '#0E1012', border: '1px solid #3A3D42', display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 20, color: '#F0F0F0', boxShadow: '0 0 30px rgba(232,255,71,0.1)' }}>
                  {item.y}
                </div>
              </div>

              <div style={{ flex: 1 }} className="timeline-spacer" />
            </div>
          ))}
        </div>
      </section>

      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
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
