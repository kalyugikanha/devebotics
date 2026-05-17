import { useState, useEffect, useRef } from 'react';

// ─── TECH STACK ────────────────────────────────────────────────────
const techCategories = [
  {
    label: 'Frontend / Mobile', count: 6,
    items: [
      { name: 'React Native', ctx: 'Cross-platform iOS & Android apps', pct: 95, projects: '80+ projects' },
      { name: 'Flutter', ctx: 'High-performance native UI for all platforms', pct: 90, projects: '60+ projects' },
      { name: 'Swift', ctx: 'Native iOS development & Apple ecosystem', pct: 80, projects: '20+ projects' },
      { name: 'Kotlin', ctx: 'Native Android development', pct: 78, projects: '18+ projects' },
      { name: 'Next.js', ctx: 'Web apps & progressive web apps (PWA)', pct: 88, projects: '40+ projects' },
      { name: 'Expo', ctx: 'Rapid React Native prototyping & deployment', pct: 85, projects: '35+ projects' },
    ],
  },
  {
    label: 'Backend & APIs', count: 5,
    items: [
      { name: 'Node.js', ctx: 'Scalable REST & GraphQL API servers', pct: 95, projects: '120+ projects' },
      { name: 'Python', ctx: 'ML pipelines, data APIs & automation', pct: 82, projects: '30+ projects' },
      { name: 'Laravel', ctx: 'PHP-based rapid backend development', pct: 75, projects: '25+ projects' },
      { name: 'GraphQL', ctx: 'Flexible client-driven data fetching', pct: 80, projects: '40+ projects' },
      { name: 'Firebase', ctx: 'Real-time database, auth & push notifications', pct: 92, projects: '90+ projects' },
    ],
  },
  {
    label: 'Database & Storage', count: 4,
    items: [
      { name: 'MongoDB', ctx: 'Flexible NoSQL for app data', pct: 90, projects: '100+ projects' },
      { name: 'PostgreSQL', ctx: 'Relational data for complex apps', pct: 85, projects: '45+ projects' },
      { name: 'MySQL', ctx: 'Traditional relational database', pct: 80, projects: '35+ projects' },
      { name: 'Redis', ctx: 'Caching, sessions & real-time leaderboards', pct: 78, projects: '30+ projects' },
    ],
  },
  {
    label: 'Cloud & DevOps', count: 5,
    items: [
      { name: 'AWS', ctx: 'EC2, S3, Lambda, RDS & CloudFront', pct: 88, projects: '70+ projects' },
      { name: 'Google Cloud', ctx: 'Firebase hosting, GKE & GCS', pct: 82, projects: '40+ projects' },
      { name: 'Docker', ctx: 'Containerised app deployment', pct: 85, projects: '55+ projects' },
      { name: 'GitHub Actions', ctx: 'CI/CD pipelines for continuous delivery', pct: 90, projects: '80+ projects' },
      { name: 'Nginx', ctx: 'Reverse proxy & load balancing', pct: 80, projects: '50+ projects' },
    ],
  },
  {
    label: 'AI & Intelligence', count: 4,
    items: [
      { name: 'TensorFlow', ctx: 'Custom ML model training & inference', pct: 72, projects: '12+ projects' },
      { name: 'OpenAI API', ctx: 'GPT-powered features & chatbots', pct: 85, projects: '20+ projects' },
      { name: 'Google Vision', ctx: 'Image recognition & OCR', pct: 78, projects: '15+ projects' },
      { name: 'LangChain', ctx: 'LLM app orchestration & RAG pipelines', pct: 70, projects: '8+ projects' },
    ],
  },
];

function TechItem({ item, animate }: { item: typeof techCategories[0]['items'][0]; animate: boolean }) {
  const [barW, setBarW] = useState(0);
  useEffect(() => {
    if (!animate) { setBarW(0); return; }
    const t = setTimeout(() => setBarW(item.pct), 50);
    return () => clearTimeout(t);
  }, [animate, item.pct]);

  return (
    <div style={{ padding: '20px 0', borderBottom: '1px solid #1C1E21' }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 6 }}>
        <div>
          <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 18, color: '#F0F0F0', marginBottom: 4 }}>{item.name}</div>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#6B6F76' }}>{item.ctx}</div>
        </div>
        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#3A3D42', flexShrink: 0, marginLeft: 16, paddingTop: 4 }}>{item.projects}</div>
      </div>
      <div style={{ height: 2, background: '#1C1E21', borderRadius: 1, marginTop: 12 }}>
        <div style={{ height: '100%', background: '#E8FF47', borderRadius: 1, width: `${barW}%`, transition: 'width 0.6s ease' }} />
      </div>
    </div>
  );
}

// ─── BUILD PROCESS ─────────────────────────────────────────────────
const sprints = [
  {
    num: '01', title: 'Discovery &\nScoping', pct: 20,
    desc: 'We map every screen, user flow, and API before writing one line of code.',
    checks: ['Requirements doc', 'User flow mapping', 'Tech architecture plan', 'Timeline & milestone doc'],
    duration: '1 week',
    img: 'https://images.unsplash.com/photo-1531538606174-0f90ff5dce83?w=600&q=80',
  },
  {
    num: '02', title: 'UI/UX\nDesign', pct: 40,
    desc: 'Pixel-perfect Figma prototype reviewed and approved before development starts.',
    checks: ['Wireframes & user flows', 'High-fidelity UI screens', 'Clickable prototype', 'Design system & assets'],
    duration: '1–2 weeks',
    img: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&q=80',
  },
  {
    num: '03', title: 'Development\nSprints', pct: 60,
    desc: 'Agile 2-week sprints. You get a working build to test after every sprint.',
    checks: ['Frontend + backend dev', 'API integration', 'Bi-weekly build delivery', 'Code reviews & docs'],
    duration: '4–10 weeks',
    img: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&q=80',
  },
  {
    num: '04', title: 'QA &\nTesting', pct: 80,
    desc: 'Manual + automated testing across 20+ real devices. Zero bugs at launch.',
    checks: ['Functional testing', 'Performance testing', 'Security audit', '20+ device testing'],
    duration: '1–2 weeks',
    img: 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&q=80',
  },
  {
    num: '05', title: 'Launch &\nSubmit', pct: 100,
    desc: 'We handle Play Store & App Store submission, ASO and 90-day post-launch support.',
    checks: ['App Store submission', 'Play Store submission', 'ASO optimization', '3 months free support'],
    duration: '1 week',
    img: 'https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?w=600&q=80',
  },
];

export default function AppDevTechProcess() {
  const [activeTab, setActiveTab] = useState(0);
  const [animate, setAnimate] = useState(true);
  const [activeStep, setActiveStep] = useState(0);
  const stepRefs = useRef<(HTMLDivElement | null)[]>([]);

  const switchTab = (i: number) => {
    setAnimate(false);
    setTimeout(() => { setActiveTab(i); setAnimate(true); }, 150);
  };

  useEffect(() => {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          const idx = stepRefs.current.indexOf(e.target as HTMLDivElement);
          if (idx >= 0) setActiveStep(idx);
        }
      });
    }, { threshold: 0.4, rootMargin: '-20% 0px -20% 0px' });
    stepRefs.current.forEach(el => el && obs.observe(el));
    return () => obs.disconnect();
  }, []);

  const cat = techCategories[activeTab];

  return (
    <>
      <section style={{ padding: '100px 0', background: '#0E1012' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ marginBottom: 56 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 12 }}>// TECH STACK — CLICK TO EXPLORE</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(28px,4vw,52px)', color: '#F0F0F0', margin: 0 }}>Battle-Tested Technologies.</h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: '30% 1fr', gap: 60 }} className="tech-grid">
            <div>
              {techCategories.map((cat, i) => (
                <div key={cat.label} onClick={() => switchTab(i)} style={{
                  padding: '18px 0 18px 20px', borderLeft: i === activeTab ? '2px solid #E8FF47' : '2px solid transparent',
                  cursor: 'pointer', transition: 'border-color 0.2s ease',
                  display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                }}>
                  <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: i === activeTab ? '#F0F0F0' : '#3A3D42', fontWeight: i === activeTab ? 500 : 400, transition: 'color 0.2s' }}
                    onMouseEnter={e => { if (i !== activeTab) (e.currentTarget as HTMLElement).style.color = '#6B6F76'; }}
                    onMouseLeave={e => { if (i !== activeTab) (e.currentTarget as HTMLElement).style.color = '#3A3D42'; }}>
                    {cat.label}
                  </span>
                  <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#3A3D42', background: '#111', border: '1px solid #1C1E21', padding: '2px 8px', borderRadius: 2 }}>{cat.count} tools</span>
                </div>
              ))}
            </div>
            <div style={{ opacity: animate ? 1 : 0, transition: 'opacity 0.15s ease' }}>
              {cat.items.map(item => <TechItem key={item.name} item={item} animate={animate} />)}
            </div>
          </div>
        </div>
        <style>{`@media(max-width:768px){.tech-grid{grid-template-columns:1fr!important;}}`}</style>
      </section>

      <section style={{ padding: '100px 0', background: '#08090A' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ display: 'grid', gridTemplateColumns: '55% 45%', gap: 80, alignItems: 'start' }} className="process-sticky-grid">
            <div>
              <div style={{ marginBottom: 60 }}>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// BUILD PROCESS</div>
                <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px,4vw,52px)', lineHeight: 1.0, color: '#F0F0F0', margin: 0 }}>
                  From Zero to<br />App Store in<br /><span style={{ color: '#E8FF47' }}>5 Sprints.</span>
                </h2>
              </div>
              <div style={{ height: 1, background: '#1C1E21', marginBottom: 0 }} />
              {sprints.map((s, i) => (
                <div key={s.num} ref={el => { stepRefs.current[i] = el; }} style={{ minHeight: 300, padding: '48px 0', borderBottom: '1px solid #1C1E21', position: 'relative', paddingLeft: 24 }}>
                  <div style={{ position: 'absolute', left: 0, top: 0, bottom: 0, width: 2, background: i <= activeStep ? '#E8FF47' : '#1C1E21', transition: 'background 0.4s ease' }} />
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: i === activeStep ? '#E8FF47' : '#3A3D42', letterSpacing: '0.2em', marginBottom: 12, transition: 'color 0.3s' }}>SPRINT {s.num}</div>
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 40, color: '#F0F0F0', lineHeight: 1.0, marginBottom: 20, whiteSpace: 'pre-line' }}>{s.title}</h3>
                  <div style={{ height: 2, background: '#1C1E21', marginBottom: 20, borderRadius: 1 }}>
                    <div style={{ height: '100%', background: '#E8FF47', width: i <= activeStep ? `${s.pct}%` : '0%', transition: 'width 0.8s ease', borderRadius: 1 }} />
                  </div>
                  <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#6B6F76', lineHeight: 1.7, marginBottom: 24 }}>{s.desc}</p>
                  <div style={{ display: 'flex', flexDirection: 'column', gap: 10, marginBottom: 20 }}>
                    {s.checks.map(c => (
                      <div key={c} style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                        <span style={{ color: '#E8FF47', flexShrink: 0, fontSize: 13 }}>✓</span>
                        <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76' }}>{c}</span>
                      </div>
                    ))}
                  </div>
                  <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#3A3D42', background: '#0E1012', border: '1px solid #1C1E21', padding: '4px 12px', borderRadius: 2, letterSpacing: '0.1em' }}>DURATION: {s.duration}</span>
                </div>
              ))}
            </div>

            <div style={{ position: 'sticky', top: '10vh' }} className="process-sticky-img">
              <div style={{ position: 'relative', aspectRatio: '4/5', borderRadius: 16, overflow: 'hidden' }}>
                {sprints.map((s, i) => (
                  <img key={s.num} src={s.img} alt={`Sprint ${s.num}`} loading="lazy" style={{
                    position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover',
                    opacity: i === activeStep ? 1 : 0, transition: 'opacity 0.4s ease',
                  }} />
                ))}
                <div style={{ position: 'absolute', top: 20, left: 20, background: '#E8FF47', color: '#08090A', fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 13, padding: '6px 14px', borderRadius: 4 }}>
                  SPRINT {sprints[activeStep].num} / 05
                </div>
              </div>
            </div>
          </div>
        </div>
        <style>{`
          @media(max-width:1024px){
            .process-sticky-grid{grid-template-columns:1fr!important;}
            .process-sticky-img{position:relative!important;top:auto!important;margin-bottom:40px;order:-1;}
          }
        `}</style>
      </section>
    </>
  );
}
