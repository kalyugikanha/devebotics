import { useEffect } from 'react';

const JOBS = [
  { t: 'Senior React / Next.js Engineer', dep: 'Engineering', loc: 'Remote', type: 'Full-time' },
  { t: 'Lead UI/UX Designer', dep: 'Design', loc: 'New York / Hybrid', type: 'Full-time' },
  { t: 'Node.js Backend Developer', dep: 'Engineering', loc: 'Remote', type: 'Contract' },
  { t: 'AI Solutions Architect', dep: 'AI & Data', loc: 'London / Remote', type: 'Full-time' },
  { t: 'Growth Marketing Manager', dep: 'Marketing', loc: 'Remote', type: 'Full-time' },
];

export default function CareersPage() {
  useEffect(() => {
    document.title = 'Careers | Join DevBotics';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72 }}>
      {/* HERO */}
      <section style={{ position: 'relative', overflow: 'hidden', padding: '120px 40px 100px' }}>
        <div style={{ position: 'absolute', top: 0, left: '50%', transform: 'translateX(-50%)', width: '100vw', height: '100%', background: 'radial-gradient(ellipse at top, rgba(232,255,71,0.08), transparent 70%)', pointerEvents: 'none' }} />
        <div style={{ maxWidth: 1000, margin: '0 auto', textAlign: 'center', position: 'relative', zIndex: 1 }}>
          <div style={{ opacity: 0, animation: 'fadeInUp 0.8s ease-out forwards' }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 24 }}>// CAREERS AT DEVBOTICS</div>
            <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 88px)', color: '#F0F0F0', lineHeight: 1.0, letterSpacing: '-0.03em', marginBottom: 32 }}>
              Do the best work of<br /><span style={{ color: '#E8FF47' }}>your life.</span>
            </h1>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#6B6F76', lineHeight: 1.6, maxWidth: 700, margin: '0 auto 48px' }}>
              We're a remote-first collective of obsessive engineers and designers. No bureaucracy, no micromanagement. Just brilliant people building incredible software.
            </p>
            <div style={{ display: 'flex', gap: 24, justifyContent: 'center', flexWrap: 'wrap' }}>
              <a href="#openings" style={{ background: '#F0F0F0', color: '#08090A', padding: '16px 32px', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, borderRadius: 8, textDecoration: 'none', transition: 'background 0.2s' }} onMouseEnter={e=>e.currentTarget.style.background='#E8FF47'} onMouseLeave={e=>e.currentTarget.style.background='#F0F0F0'}>View Openings</a>
              <a href="#perks" style={{ background: 'transparent', color: '#F0F0F0', border: '1px solid #3A3D42', padding: '16px 32px', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, borderRadius: 8, textDecoration: 'none', transition: 'border-color 0.2s' }} onMouseEnter={e=>e.currentTarget.style.borderColor='#E8FF47'} onMouseLeave={e=>e.currentTarget.style.borderColor='#3A3D42'}>See Perks</a>
            </div>
          </div>
        </div>
      </section>

      {/* PERKS */}
      <section id="perks" style={{ padding: '100px 0', background: '#0E1012', borderTop: '1px solid #1C1E21', borderBottom: '1px solid #1C1E21' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: '#F0F0F0' }}>Why Join Us?</h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: 24 }}>
            {[
              { i: '🌍', t: 'Work from Anywhere', d: 'We are 100% remote-first. Work from a beach in Bali or your couch in Berlin.' },
              { i: '💻', t: 'Top-Tier Equipment', d: 'M3 Max MacBook Pro? Done. 4K Monitor? Yours. We provide everything you need.' },
              { i: '⏰', t: 'Flexible Hours', d: 'We care about output, not hours logged. Work when you are most productive.' },
              { i: '🧠', t: 'Learning Budget', d: '$2,000/year to spend on courses, conferences, or books. Never stop growing.' }
            ].map((p, i) => (
              <div key={p.t} style={{ background: '#08090A', border: '1px solid #1C1E21', borderRadius: 16, padding: 32, opacity: 0, animation: `fadeInUp 0.5s ease-out ${i * 0.1}s forwards` }}>
                <div style={{ fontSize: 40, marginBottom: 20 }}>{p.i}</div>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 20, color: '#F0F0F0', marginBottom: 12 }}>{p.t}</h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#6B6F76', lineHeight: 1.6 }}>{p.d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* JOBS */}
      <section id="openings" style={{ padding: '100px 0' }}>
        <div style={{ maxWidth: 1000, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: '#F0F0F0', marginBottom: 16 }}>Open Positions.</h2>
            <div style={{ height: 2, background: '#1C1E21', width: '100%' }} />
          </div>

          <div style={{ display: 'flex', flexDirection: 'column' }}>
            {JOBS.map((j, i) => (
              <div key={j.t} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '32px 0', borderBottom: '1px solid #1C1E21', transition: 'padding 0.3s', opacity: 0, animation: `fadeInLeft 0.5s ease-out ${i * 0.1}s forwards` }} className="job-row" onMouseEnter={e => { e.currentTarget.style.paddingLeft = '20px'; e.currentTarget.style.paddingRight = '20px'; e.currentTarget.style.background = '#0E1012'; }} onMouseLeave={e => { e.currentTarget.style.paddingLeft = '0px'; e.currentTarget.style.paddingRight = '0px'; e.currentTarget.style.background = 'transparent'; }}>
                <div>
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 24, color: '#F0F0F0', marginBottom: 12, transition: 'color 0.2s' }} className="job-title">{j.t}</h3>
                  <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
                    <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#E8FF47', background: 'rgba(232,255,71,0.05)', padding: '4px 12px', borderRadius: 100 }}>{j.dep}</span>
                    <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#6B6F76', display: 'flex', alignItems: 'center', gap: 6 }}>📍 {j.loc}</span>
                    <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#6B6F76', display: 'flex', alignItems: 'center', gap: 6 }}>⏱️ {j.type}</span>
                  </div>
                </div>
                <button style={{ background: 'transparent', border: '1px solid #3A3D42', color: '#F0F0F0', padding: '12px 24px', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, borderRadius: 100, cursor: 'pointer', transition: 'all 0.2s' }} onMouseEnter={e => { e.currentTarget.style.background = '#F0F0F0'; e.currentTarget.style.color = '#08090A'; }} onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = '#F0F0F0'; }}>Apply →</button>
              </div>
            ))}
          </div>

          <div style={{ marginTop: 80, background: '#0E1012', border: '1px solid #1C1E21', padding: 40, borderRadius: 16, textAlign: 'center' }}>
            <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 24, color: '#F0F0F0', marginBottom: 16 }}>Don't see a fit?</h3>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', marginBottom: 24 }}>We're always looking for exceptional talent. Send your portfolio and let's talk.</p>
            <a href="mailto:careers@devbotics.com" style={{ color: '#E8FF47', fontFamily: 'Lexend,sans-serif', fontSize: 16, textDecoration: 'none' }}>careers@devbotics.com</a>
          </div>
        </div>
      </section>

      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        .job-row:hover .job-title { color: #E8FF47 !important; }
        @media(max-width: 768px) {
          .job-row { flex-direction: column; align-items: flex-start !important; gap: 24px; }
          .job-row button { width: 100%; }
        }
      `}</style>
    </main>
  );
}
