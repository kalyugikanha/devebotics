import { useEffect, useRef } from 'react';

const awardsData = [
  { year: '2025', title: 'Top App Development Company', organization: 'Clutch', desc: 'Recognized for delivering high-impact mobile solutions.' },
  { year: '2024', title: 'Best B2B Software Agency', organization: 'GoodFirms', desc: 'Ranked #1 for enterprise software development.' },
  { year: '2023', title: 'Innovation in AI', organization: 'Tech Excellence Awards', desc: 'Awarded for integrating cutting-edge LLMs into legacy systems.' },
  { year: '2024', title: 'Design of the Year', organization: 'Awwwards', desc: 'Recognized for stunning UI/UX in FinTech applications.' },
  { year: '2025', title: 'Top IT Services Provider', organization: 'UpCity', desc: 'Consistently rated 5 stars by enterprise clients.' },
];

export default function Awards() {
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    const fadeEls = document.querySelectorAll('.award-fade-up');
    fadeEls.forEach(el => observer.observe(el));

    return () => observer.disconnect();
  }, []);

  return (
    <section style={{ padding: '80px 0', background: '#F9FAFB', overflow: 'hidden' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
        <div className="award-fade-up" style={{ textAlign: 'center', marginBottom: 48, opacity: 0, transform: 'translateY(30px)', transition: 'all 0.6s ease' }}>
          <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 16 }}>// RECOGNITION</div>
          <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 48px)', color: '#111827', marginBottom: 16 }}>
            Awards & <span style={{ color: '#1F8844' }}>Accolades.</span>
          </h2>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#4B5563', maxWidth: 600, margin: '0 auto' }}>
            Our commitment to excellence has been recognized by industry leaders globally.
          </p>
        </div>
      </div>

      {/* Awards grid */}
      <div style={{ padding: '40px 0' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: 20 }}>
            {awardsData.map((award, i) => (
              <div key={i} className="award-fade-up" style={{ background: '#FFFFFF', borderRadius: 12, padding: 20, border: '1px solid #F1F5F9', boxShadow: '0 6px 18px rgba(3,7,18,0.04)', opacity: 0, transform: 'translateY(18px)', transition: 'all 0.5s ease' }}>
                <div style={{ display: 'flex', gap: 12, alignItems: 'center', marginBottom: 12 }}>
                  <div style={{ width: 44, height: 44, borderRadius: 10, background: 'rgba(31,136,68,0.08)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#1F8844', fontWeight: 800 }}>{award.year}</div>
                  <div>
                    <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 15, color: '#0F1724' }}>{award.title}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#1F8844', fontWeight: 700 }}>{award.organization}</div>
                  </div>
                </div>
                <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.6 }}>{award.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      <style>{`
        .award-fade-up.visible { opacity: 1 !important; transform: translateY(0) !important; }
      `}</style>
    </section>
  );
}
