// Improved Process Component
import { useEffect, useRef } from 'react';

const steps = [
  {
    num: '01', name: 'Discovery & Strategy',
    desc: 'We deep-dive into your business goals, target audience, and competition to define a crystal-clear, actionable roadmap for success.',
    img: '/images/generic-2.png',
  },
  {
    num: '02', name: 'UI/UX Design',
    desc: 'Our world-class designers craft intuitive wireframes and pixel-perfect interfaces focused strictly on conversion and user delight.',
    img: '/images/generic-1.png',
  },
  {
    num: '03', name: 'Agile Development',
    desc: 'Engineers build with clean, scalable code architectures. Every feature is rigorously tested in a staging environment before delivery.',
    img: '/images/generic-2.png',
  },
  {
    num: '04', name: 'Launch & QA',
    desc: 'We handle server setup, CI/CD pipelines, security audits, and smooth deployment to ensure zero downtime on launch day.',
    img: '/images/generic-1.png',
  },
  {
    num: '05', name: 'Growth & Support',
    desc: 'Post-launch monitoring, continuous updates, and data-driven growth strategies to keep your product ahead of the curve.',
    img: '/images/generic-2.png',
  },
];

const Process = () => {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-process-step');
        }
      });
    }, { threshold: 0.2 });

    const stepElements = document.querySelectorAll('.process-step-container');
    stepElements.forEach(el => observer.observe(el));

    return () => observer.disconnect();
  }, []);

  return (
    <section style={{ padding: '140px 0', background: '#0B0F19', color: '#FFFFFF', position: 'relative', overflow: 'hidden' }}>
      {/* Background ambient light */}
      <div style={{ position: 'absolute', top: '20%', left: '-10%', width: '50vw', height: '50vw', background: 'radial-gradient(circle, rgba(31,136,68,0.1) 0%, transparent 70%)', filter: 'blur(80px)', pointerEvents: 'none' }} />
      <div style={{ position: 'absolute', bottom: '10%', right: '-10%', width: '40vw', height: '40vw', background: 'radial-gradient(circle, rgba(31,136,68,0.08) 0%, transparent 70%)', filter: 'blur(80px)', pointerEvents: 'none' }} />

      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px', position: 'relative', zIndex: 1 }}>
        
        {/* Header */}
        <div style={{ textAlign: 'center', marginBottom: 100 }} className="fade-up">
          <div style={{ display: 'inline-block', background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(255,255,255,0.1)', padding: '6px 16px', borderRadius: 100, marginBottom: 24 }}>
            <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em' }}>✦ OUR PROCESS</span>
          </div>
          <h2 style={{ fontSize: 'clamp(40px, 6vw, 64px)', fontFamily: 'Montserrat, sans-serif', fontWeight: 900, marginBottom: 24, lineHeight: 1.1 }}>
            How We <span style={{ color: '#1F8844' }}>Build.</span>
          </h2>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 18, color: '#9CA3AF', maxWidth: 600, margin: '0 auto', lineHeight: 1.6 }}>
            A proven, battle-tested blueprint that transforms abstract ideas into dominant digital products.
          </p>
        </div>

        {/* Vertical Steps */}
        <div ref={containerRef} style={{ position: 'relative' }}>
          {/* Central Line */}
          <div className="process-central-line" style={{
            position: 'absolute', top: 0, bottom: 0, left: '50%', width: 2,
            background: 'linear-gradient(to bottom, transparent, rgba(31,136,68,0.5), transparent)',
            transform: 'translateX(-50%)',
          }} />

          <div style={{ display: 'flex', flexDirection: 'column', gap: 120 }}>
            {steps.map((step, i) => {
              const isEven = i % 2 === 0;
              return (
                <div key={step.num} className="process-step-container" style={{ 
                  display: 'flex', 
                  alignItems: 'center',
                  justifyContent: 'center',
                  flexDirection: isEven ? 'row' : 'row-reverse',
                  opacity: 0,
                  transform: 'translateY(50px)',
                  transition: 'all 0.8s cubic-bezier(0.16, 1, 0.3, 1)'
                }}>
                  
                  {/* Content Side */}
                  <div className="process-content-side" style={{ 
                    flex: 1, 
                    padding: isEven ? '0 80px 0 0' : '0 0 0 80px',
                    textAlign: isEven ? 'right' : 'left'
                  }}>
                    <div style={{ 
                      fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 100, 
                      color: 'rgba(255,255,255,0.03)', lineHeight: 0.8, marginBottom: -20, userSelect: 'none'
                    }}>
                      {step.num}
                    </div>
                    <div style={{ display: 'inline-block', background: 'rgba(31,136,68,0.1)', color: '#1F8844', padding: '4px 12px', borderRadius: 6, fontFamily: 'Lexend, sans-serif', fontSize: 12, fontWeight: 600, marginBottom: 16 }}>
                      PHASE {step.num}
                    </div>
                    <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 32, marginBottom: 16, color: '#FFFFFF' }}>
                      {step.name}
                    </h3>
                    <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#9CA3AF', lineHeight: 1.7 }}>
                      {step.desc}
                    </p>
                  </div>

                  {/* Center Node */}
                  <div style={{ position: 'relative', zIndex: 2, display: 'flex', alignItems: 'center', justifyItems: 'center', margin: '0 -24px' }} className="process-center-node">
                    <div style={{ width: 48, height: 48, borderRadius: '50%', background: '#0B0F19', border: '2px solid #1F8844', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 0 20px rgba(31,136,68,0.4)' }}>
                      <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#1F8844' }} />
                    </div>
                  </div>

                  {/* Image Side */}
                  <div className="process-image-side" style={{ flex: 1, padding: isEven ? '0 0 0 80px' : '0 80px 0 0' }}>
                    <div className="process-img-wrapper" style={{ 
                      position: 'relative', borderRadius: 24, overflow: 'hidden', 
                      boxShadow: '0 20px 40px rgba(0,0,0,0.4)', border: '1px solid rgba(255,255,255,0.1)',
                      aspectRatio: '16/10'
                    }}>
                      <div style={{ position: 'absolute', inset: 0, background: 'rgba(31,136,68,0.2)', mixBlendMode: 'overlay', zIndex: 1 }} />
                      <img src={step.img} alt={step.name} loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.5s ease' }} className="process-img" />
                    </div>
                  </div>

                </div>
              );
            })}
          </div>
        </div>
      </div>

      <style>{`
        .animate-process-step {
          opacity: 1 !important;
          transform: translateY(0) !important;
        }
        .process-img-wrapper:hover .process-img {
          transform: scale(1.05);
        }
        @media (max-width: 1024px) {
          .process-central-line { left: 40px !important; }
          .process-step-container { flex-direction: column !important; align-items: flex-start !important; gap: 40px; position: relative; }
          .process-content-side { padding: 0 0 0 80px !important; text-align: left !important; width: 100%; }
          .process-image-side { padding: 0 0 0 80px !important; width: 100%; }
          .process-center-node { position: absolute; left: 16px; top: 0; margin: 0 !important; }
        }
        @media (max-width: 600px) {
          .process-central-line { left: 24px !important; }
          .process-content-side, .process-image-side { padding: 0 0 0 64px !important; }
          .process-center-node { left: 0; }
        }
      `}</style>
    </section>
  );
};

export default Process;
