import { useState } from 'react';

// ─── FEATURED WORK ─────────────────────────────────────────────────
const projects = [
  { name: 'QuickBite', cat: 'Food Delivery', sub: '5,000+ daily orders. 3 cities.', tech: ['React Native', 'Node.js', 'Firebase'], img: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&q=80', size: 'large' },
  { name: 'RideSwift', cat: 'Taxi App', sub: '50,000+ rides. 4.8★ rating.', tech: ['Flutter', 'Google Maps'], img: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80', size: 'tall' },
  { name: 'MediCare+', cat: 'Healthcare', sub: 'HIPAA compliant. 12 hospitals.', tech: ['Flutter', 'WebRTC'], img: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&q=80', size: 'medium' },
  { name: 'LearnX', cat: 'EdTech', sub: '10,000 concurrent users.', tech: ['React Native', 'AWS'], img: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=500&q=80', size: 'medium' },
  { name: 'ShopEase', cat: 'E-commerce', sub: '3× conversion rate improvement.', tech: ['Next.js', 'Stripe'], img: '', size: 'wide' },
  { name: 'MatchMate', cat: 'Matrimonial', sub: 'AI matchmaking. 50K+ users.', tech: ['React Native', 'Python'], img: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400&q=80', size: 'small' },
];

function WorkCard({ p }: { p: typeof projects[0] }) {
  const isTypo = !p.img;
  return (
    <div style={{
      position: 'relative', overflow: 'hidden', borderRadius: 8, cursor: 'pointer',
      background: isTypo ? '#0E1012' : '#08090A',
      border: '1px solid #1C1E21',
      minHeight: p.size === 'tall' ? 520 : p.size === 'small' ? 200 : 280,
      display: 'flex', alignItems: 'flex-end',
    }}
      onMouseEnter={e => {
        const ov = e.currentTarget.querySelector('.work-overlay') as HTMLElement;
        if (ov) ov.style.background = 'rgba(8,9,10,0.55)';
        const ct = e.currentTarget.querySelector('.work-content') as HTMLElement;
        if (ct) ct.style.transform = 'translateY(0)';
      }}
      onMouseLeave={e => {
        const ov = e.currentTarget.querySelector('.work-overlay') as HTMLElement;
        if (ov) ov.style.background = 'rgba(8,9,10,0.78)';
        const ct = e.currentTarget.querySelector('.work-content') as HTMLElement;
        if (ct) ct.style.transform = 'translateY(16px)';
      }}>
      {p.img && <img src={p.img} alt={p.name} loading="lazy" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} />}
      {isTypo && (
        <div style={{ position: 'absolute', inset: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden' }}>
          <span style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px,8vw,96px)', color: 'rgba(240,240,240,0.06)', userSelect: 'none', whiteSpace: 'nowrap' }}>{p.name}</span>
        </div>
      )}
      <div className="work-overlay" style={{ position: 'absolute', inset: 0, background: 'rgba(8,9,10,0.78)', transition: 'background 0.3s ease' }} />
      <div className="work-content" style={{ position: 'relative', zIndex: 2, padding: '20px 24px', transform: 'translateY(16px)', transition: 'transform 0.3s ease', width: '100%' }}>
        <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#E8FF47', letterSpacing: '0.15em', textTransform: 'uppercase', background: 'rgba(232,255,71,0.12)', border: '1px solid rgba(232,255,71,0.25)', padding: '2px 8px', borderRadius: 2, marginBottom: 8, display: 'inline-block' }}>{p.cat}</span>
        <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 20, color: '#F0F0F0', marginBottom: 4 }}>{p.name}</div>
        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#6B6F76', marginBottom: 10 }}>{p.sub}</div>
        <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap', marginBottom: 10 }}>
          {p.tech.map(t => <span key={t} style={{ fontSize: 10, color: '#6B6F76', border: '1px solid #3A3D42', padding: '2px 7px', borderRadius: 2, fontFamily: 'Lexend,sans-serif' }}>{t}</span>)}
        </div>
        <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47' }}>View Case →</span>
      </div>
    </div>
  );
}

// ─── TESTIMONIALS ──────────────────────────────────────────────────
const testimonials = [
  { name: 'Rahul Sharma', role: 'CEO', company: 'FoodTech Startup', initials: 'RS', quote: 'DevBotics delivered our food delivery app 3 weeks ahead of schedule. The quality was exceptional — zero bugs at launch, and our users loved the UI.' },
  { name: 'Priya Mehta', role: 'Founder', company: 'EduLearn India', initials: 'PM', quote: 'They built what 2 other agencies failed to deliver. Our e-learning platform now handles 10,000 concurrent users without a single crash.' },
  { name: 'Arjun Patel', role: 'CTO', company: 'LogiTrack', initials: 'AP', quote: 'The codebase is so clean it feels like it was built by a team of 20. Just 4 engineers at DevBotics and it\'s the best software we\'ve ever shipped.' },
  { name: 'Sneha Joshi', role: 'Product Head', company: 'HealthFirst', initials: 'SJ', quote: 'HIPAA compliance, real-time video, EHR integration — all done in 14 weeks. I\'ve worked with agencies that take 14 months for less.' },
  { name: 'Karan Malhotra', role: 'Founder', company: 'SaaSify', initials: 'KM', quote: 'Post-launch support is where most agencies ghost you. DevBotics has been available 24/7 for 8 months since we launched. That\'s rare.' },
];

export default function AppDevProof() {
  const [activeT, setActiveT] = useState(0);
  const [fade, setFade] = useState(true);

  const switchT = (i: number) => {
    setFade(false);
    setTimeout(() => { setActiveT(i); setFade(true); }, 150);
  };

  const t = testimonials[activeT];

  return (
    <>
      <section id="portfolio" style={{ padding: '100px 0', background: '#0E1012' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ marginBottom: 56 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 12 }}>// SELECTED WORK</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(28px,4vw,52px)', color: '#F0F0F0', margin: 0 }}>Apps That Ship.<br />Products That Scale.</h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(12,1fr)', gridTemplateRows: 'auto', gap: 12 }} className="bento-grid">
            <div style={{ gridColumn: '1/8', gridRow: '1/2' }}><WorkCard p={projects[0]} /></div>
            <div style={{ gridColumn: '8/13', gridRow: '1/3' }}><WorkCard p={projects[1]} /></div>
            <div style={{ gridColumn: '1/5', gridRow: '2/3' }}><WorkCard p={projects[2]} /></div>
            <div style={{ gridColumn: '5/8', gridRow: '2/3' }}><WorkCard p={projects[3]} /></div>
            <div style={{ gridColumn: '1/9', gridRow: '3/4' }}><WorkCard p={projects[4]} /></div>
            <div style={{ gridColumn: '9/13', gridRow: '3/4' }}><WorkCard p={projects[5]} /></div>
          </div>
        </div>
        <style>{`@media(max-width:768px){.bento-grid>div{grid-column:1/-1!important;grid-row:auto!important;}}`}</style>
      </section>

      <section style={{ padding: '100px 0', background: '#08090A', position: 'relative' }}>
        <div style={{ position: 'absolute', inset: 0, opacity: 0.02, backgroundImage: 'url("data:image/svg+xml,%3Csvg viewBox=\'0 0 256 256\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.9\' numOctaves=\'4\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23n)\'/%3E%3C/svg%3E")', backgroundSize: '128px 128px', pointerEvents: 'none' }} />
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ display: 'grid', gridTemplateColumns: '35% 1fr', gap: 80, alignItems: 'start' }} className="testi-grid">
            <div>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 200, color: '#E8FF47', opacity: 0.15, lineHeight: 0.8, marginBottom: 32, userSelect: 'none' }}>"</div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                {testimonials.map((tt, i) => (
                  <button key={tt.name} onClick={() => switchT(i)} style={{
                    fontFamily: 'Lexend,sans-serif', fontSize: 15, fontWeight: i === activeT ? 500 : 400,
                    color: i === activeT ? '#F0F0F0' : '#3A3D42',
                    background: 'transparent', border: 'none', cursor: 'pointer',
                    borderLeft: i === activeT ? '2px solid #E8FF47' : '2px solid transparent',
                    paddingLeft: i === activeT ? 16 : 0, paddingRight: 0,
                    paddingTop: 10, paddingBottom: 10, textAlign: 'left',
                    transition: 'all 0.2s ease',
                  }}
                    onMouseEnter={e => { if (i !== activeT) (e.currentTarget as HTMLElement).style.color = '#6B6F76'; }}
                    onMouseLeave={e => { if (i !== activeT) (e.currentTarget as HTMLElement).style.color = '#3A3D42'; }}>
                    {tt.name}
                  </button>
                ))}
              </div>
            </div>
            <div style={{ opacity: fade ? 1 : 0, transition: 'opacity 0.15s ease' }}>
              <blockquote style={{ fontFamily: 'Lexend,sans-serif', fontStyle: 'italic', fontSize: 'clamp(16px,2vw,24px)', color: '#A0A0A0', lineHeight: 1.6, maxWidth: 600, margin: '0 0 36px' }}>
                "{t.quote}"
              </blockquote>
              <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
                <div style={{ width: 56, height: 56, borderRadius: '50%', background: '#E8FF47', display: 'flex', alignItems: 'center', justifyContent: 'center', fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 16, color: '#08090A', flexShrink: 0 }}>{t.initials}</div>
                <div>
                  <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 16, color: '#F0F0F0', marginBottom: 2 }}>{t.name}</div>
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#6B6F76' }}>{t.role}, {t.company}</div>
                  <div style={{ color: '#E8FF47', fontSize: 14, marginTop: 4 }}>★★★★★</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <style>{`@media(max-width:768px){.testi-grid{grid-template-columns:1fr!important;}}`}</style>
      </section>
    </>
  );
}
