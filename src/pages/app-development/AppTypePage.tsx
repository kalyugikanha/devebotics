import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { appPageData, appTypeCards } from './appPageData';
import ContactForm from '../../components/ContactForm';
import Testimonials from '../../components/Testimonials';

interface Props { slug: string; }

const steps = [
  { n: '01', t: 'Discovery & Scoping', d: 'We map your entire app flow, features, and technical requirements in detail.', img: '/images/generic-2.png' },
  { n: '02', t: 'UI/UX Design', d: 'Clickable Figma prototype before a single line of code is written.', img: '/images/generic-1.png' },
  { n: '03', t: 'Development', d: 'Agile sprints. Clean code. Regular builds shared with you.', img: '/images/generic-2.png' },
  { n: '04', t: 'QA & Testing', d: 'Manual + automated testing across 20+ real devices before launch.', img: '/images/generic-1.png' },
  { n: '05', t: 'Launch & Submit', d: 'We handle Play Store & App Store submission, ASO & post-launch support.', img: '/images/generic-2.png' },
];

export default function AppTypePage({ slug }: Props) {
  const data = appPageData[slug];
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  useEffect(() => {
    if (data) document.title = data.metaTitle;
    window.scrollTo(0, 0);
    const timer = setTimeout(() => {
      const els = document.querySelectorAll('.fade-up:not(.visible)');
      const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
      }, { threshold: 0.1 });
      els.forEach(el => obs.observe(el));
      return () => obs.disconnect();
    }, 100);
    return () => clearTimeout(timer);
  }, [slug, data]);

  if (!data) return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ fontFamily: 'Montserrat, sans-serif', fontSize: 24, color: '#111827' }}>Page not found</div>
    </main>
  );

  const relatedCards = appTypeCards.filter(c => data.relatedSlugs.includes(c.slug));

  return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72 }}>
      <meta name="description" content={data.metaDesc} />

      {/* CINEMATIC HERO */}
      <section style={{ minHeight: '90vh', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden', padding: '120px 40px' }}>
        {/* Abstract 3D Background */}
        <div style={{ position: 'absolute', inset: 0, zIndex: 0 }}>
          <img src={data.heroImage} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover', filter: 'brightness(0.3) contrast(1.2)' }} />
          {/* Deep gradient overlays for that premium 'colladome' case study feel */}
          <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to bottom, rgba(8,9,10,0.2) 0%, #FFFFFF 100%)' }} />
          <div style={{ position: 'absolute', inset: 0, background: 'radial-gradient(circle at center, transparent 0%, #FFFFFF 80%)' }} />
        </div>

        <div style={{ maxWidth: 1200, margin: '0 auto', textAlign: 'center', position: 'relative', zIndex: 1, opacity: 0, animation: 'fadeInUp 1s ease-out forwards' }}>
          <nav style={{ display: 'flex', gap: 8, alignItems: 'center', justifyContent: 'center', marginBottom: 32, flexWrap: 'wrap' }}>
            {[['Home', '/'], ['App Development', '/app-development'], [data.heroH1[0], null]].map(([label, href], i) => (
              <span key={i} style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                {href ? <Link to={href as string} style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#A0A0A0', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e=>e.currentTarget.style.color='#111827'} onMouseLeave={e=>e.currentTarget.style.color='#A0A0A0'}>{label}</Link>
                  : <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#1F8844' }}>{label}</span>}
                {i < 2 && <span style={{ color: '#9CA3AF', fontSize: 12 }}>/</span>}
              </span>
            ))}
          </nav>
          
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 100px)', lineHeight: 0.95, letterSpacing: '-0.03em', marginBottom: 32, color: '#111827' }}>
            {data.heroH1.map((line, i) => <span key={i} style={{ display: 'block' }}>{line}</span>)}
            <span style={{ color: '#1F8844', fontStyle: 'italic' }}>{data.heroAccentWord}</span>
          </h1>
          
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 'clamp(18px, 2vw, 24px)', color: '#A0A0A0', lineHeight: 1.6, maxWidth: 800, margin: '0 auto 48px' }}>
            {data.heroSub}
          </p>
          
          <div style={{ display: 'flex', gap: 24, justifyContent: 'center', flexWrap: 'wrap' }}>
            <Link to="/contact" style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 16, background: '#1F8844', color: '#FFFFFF', textDecoration: 'none', padding: '18px 40px', borderRadius: 100, transition: 'all 0.3s ease', boxShadow: '0 10px 30px rgba(232,255,71,0.2)' }} onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-2px)'; e.currentTarget.style.boxShadow = '0 15px 40px rgba(232,255,71,0.3)'; }} onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 30px rgba(232,255,71,0.2)'; }}>
              Start Your Project
            </Link>
          </div>
        </div>
      </section>

      {/* OVERVIEW FEATURES */}
      <section style={{ padding: '100px 0', background: '#F9FAFB' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
          <div className="fade-up" style={{ marginBottom: 56 }}>
            <h2 style={{ fontSize: 'clamp(28px, 4vw, 48px)', marginBottom: 12 }}>What's Included in Our <span className="accent">Solution.</span></h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 0 }} className="features-grid">
            {data.overviewFeatures.map((f, i) => (
              <div key={i} className="fade-up" style={{ padding: '28px 32px 28px 0', borderBottom: '1px solid #E5E7EB', borderRight: i % 3 !== 2 ? '1px solid #E5E7EB' : 'none', paddingRight: i % 3 !== 2 ? 32 : 0, paddingLeft: i % 3 !== 0 ? 32 : 0, animationDelay: `${i * 0.08}s` }}>
                <div style={{ width: 32, height: 2, background: '#1F8844', marginBottom: 16, borderRadius: 1 }} />
                <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 16, color: '#111827', marginBottom: 8 }}>{f.title}</div>
                <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563', lineHeight: 1.65 }}>{f.desc}</div>
              </div>
            ))}
          </div>
        </div>
        <style>{`@media(max-width:1024px){.features-grid{grid-template-columns:repeat(2,1fr)!important;}} @media(max-width:640px){.features-grid{grid-template-columns:1fr!important;}}`}</style>
      </section>

      {/* PANELS */}
      <section style={{ padding: '100px 0', background: '#FFFFFF' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
          <div className="fade-up" style={{ marginBottom: 56 }}>
            <h2 style={{ fontSize: 'clamp(28px, 4vw, 48px)', marginBottom: 12 }}>What's Inside the <span className="accent">System?</span></h2>
            <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#4B5563' }}>Complete ecosystem — not just one app.</p>
          </div>
          <div style={{ display: 'flex', gap: 20, flexWrap: 'wrap' }}>
            {data.panels.map((panel, i) => (
              <div key={panel} className="fade-up" style={{ flex: '1 1 180px', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 12, padding: '28px 24px', animationDelay: `${i * 0.08}s`, transition: 'border-color 0.2s ease' }}
                onMouseEnter={e => (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'}
                onMouseLeave={e => (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'}>
                <div style={{ width: 36, height: 36, background: 'rgba(232,255,71,0.08)', borderRadius: 8, marginBottom: 16, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><path d="M3 9h18M9 21V9" /></svg>
                </div>
                <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 15, color: '#111827' }}>{panel}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* TECH STACK */}
      <section style={{ padding: '80px 0', background: '#F9FAFB' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
          <div className="fade-up" style={{ marginBottom: 40 }}>
            <h2 style={{ fontSize: 'clamp(24px, 3vw, 40px)' }}>Technologies We <span className="accent">Use.</span></h2>
          </div>
          <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
            {data.techStack.map(t => (
              <span key={t} className="fade-up" style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 500, fontSize: 14, color: '#4B5563', background: '#FFFFFF', border: '1px solid #E5E7EB', borderRadius: 8, padding: '10px 18px', cursor: 'default', transition: 'border-color 0.2s ease, color 0.2s ease' }}
                onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#111827'; }}
                onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.color = '#4B5563'; }}>
                {t}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* PROCESS */}
      <section style={{ padding: '100px 0', background: '#FFFFFF' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
          <div className="fade-up" style={{ marginBottom: 56 }}>
            <h2 style={{ fontSize: 'clamp(28px, 4vw, 48px)', marginBottom: 12 }}>Our Development <span className="accent">Process.</span></h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: 16 }} className="proc-grid">
            {steps.map((s, i) => (
              <div key={s.n} className="fade-up" style={{ background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 12, overflow: 'hidden', transition: 'border-color 0.3s ease, transform 0.3s ease', animationDelay: `${i * 0.1}s` }}
                onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)'; }}
                onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.transform = 'translateY(0)'; }}>
                <div style={{ position: 'relative', height: 140, overflow: 'hidden' }}>
                  <img src={s.img} alt={s.t} loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                  <span style={{ position: 'absolute', top: 10, left: 10, fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 10, background: '#1F8844', color: '#FFFFFF', padding: '3px 8px', borderRadius: 3 }}>STEP {s.n}</span>
                </div>
                <div style={{ padding: '14px 16px 18px' }}>
                  <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 48, color: '#F3F4F6', lineHeight: 1, marginBottom: -6 }}>{s.n}</div>
                  <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 14, color: '#111827', marginBottom: 6 }}>{s.t}</div>
                  <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#4B5563', lineHeight: 1.65 }}>{s.d}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
        <style>{`@media(max-width:1024px){.proc-grid{grid-template-columns:repeat(2,1fr)!important;}} @media(max-width:640px){.proc-grid{grid-template-columns:1fr!important;}}`}</style>
      </section>

      {/* NO PRICING SECTION - Removed as per user request */}

      {/* RELATED */}
      {relatedCards.length > 0 && (
        <section id="portfolio" style={{ padding: '100px 0', background: '#FFFFFF' }}>
          <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
            <div className="fade-up" style={{ marginBottom: 48 }}>
              <h2 style={{ fontSize: 'clamp(28px, 4vw, 48px)' }}>You Might Also <span className="accent">Need.</span></h2>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 20 }} className="related-grid">
              {relatedCards.map(card => (
                <Link key={card.slug} to={`/app-development/${card.slug}`} style={{ textDecoration: 'none', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 12, overflow: 'hidden', display: 'block', transition: 'border-color 0.3s ease, transform 0.3s ease' }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.transform = 'translateY(0)'; }}>
                  <div style={{ height: 180, overflow: 'hidden' }}><img src={card.img} alt={card.name} loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover' }} /></div>
                  <div style={{ padding: '18px 20px 22px' }}>
                    <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 17, color: '#111827', marginBottom: 6 }}>{card.name}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563', marginBottom: 12 }}>{card.desc}</div>
                    <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563' }}>Explore →</span>
                  </div>
                </Link>
              ))}
            </div>
          </div>
          <style>{`@media(max-width:768px){.related-grid{grid-template-columns:1fr!important;}}`}</style>
        </section>
      )}

      {/* FAQ */}
      <section style={{ padding: '100px 0', background: '#F9FAFB' }}>
        <div style={{ maxWidth: 900, margin: '0 auto', padding: '0 32px' }}>
          <div className="fade-up" style={{ marginBottom: 48 }}>
            <h2 style={{ fontSize: 'clamp(28px, 4vw, 48px)', marginBottom: 12 }}>Frequently Asked <span className="accent">Questions.</span></h2>
          </div>
          {data.faq.map((f, i) => (
            <div key={i} className="accordion-item fade-up" style={{ animationDelay: `${i * 0.05}s` }}>
              <button className="accordion-trigger" onClick={() => setOpenFaq(openFaq === i ? null : i)}>
                <span>{f.q}</span>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" style={{ flexShrink: 0, transition: 'transform 0.2s ease', transform: openFaq === i ? 'rotate(45deg)' : 'rotate(0deg)' }}>
                  <path d="M9 4v10M4 9h10" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" />
                </svg>
              </button>
              <div className={`accordion-body${openFaq === i ? ' open' : ''}`}>{f.a}</div>
            </div>
          ))}
        </div>
      </section>

      <Testimonials />
      <div id="contact"><ContactForm preselectedApp={data.slug} /></div>
      <style>{`
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
      `}</style>
    </main>
  );
}
