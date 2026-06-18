import { useState, useRef } from 'react';

const services = [
  {
    num: '01',
    name: 'App Development',
    desc: 'iOS, Android & Cross-Platform apps that users love — built with Flutter & React Native.',
    img: '/images/generic-2.png',
  },
  {
    num: '02',
    name: 'Web Development',
    desc: 'Shopify, WordPress, Next.js, PHP & custom web solutions that convert.',
    img: '/images/generic-1.png',
  },
  {
    num: '03',
    name: 'AI & Automation',
    desc: 'Custom AI models, chatbots & intelligent workflow automation powered by OpenAI.',
    img: '/images/generic-2.png',
  },
  {
    num: '04',
    name: 'Software Development',
    desc: 'Scalable CRM, ERP, HRM & enterprise software solutions that save hours daily.',
    img: '/images/generic-1.png',
  },
  {
    num: '05',
    name: 'Digital Marketing',
    desc: 'SEO, SMO, Influencer & YouTube growth strategies with measurable ROI.',
    img: '/images/generic-2.png',
  },
  {
    num: '06',
    name: 'Hire Developers',
    desc: 'Dedicated React, Flutter, Node, Python & Full Stack experts integrated into your team.',
    img: '/images/generic-1.png',
  },
];

const Services = () => {
  const [activeIdx, setActiveIdx] = useState(0);
  const [imgVisible, setImgVisible] = useState(true);
  const fadeTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const changeImage = (i: number) => {
    if (i === activeIdx) return;
    setImgVisible(false);
    if (fadeTimer.current) clearTimeout(fadeTimer.current);
    fadeTimer.current = setTimeout(() => {
      setActiveIdx(i);
      setImgVisible(true);
    }, 200);
  };

  return (
    <section style={{ padding: '120px 0', background: '#FFFFFF' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
        {/* Heading */}
        <div className="fade-up" style={{ marginBottom: 80 }}>
          <h2 style={{ fontSize: 'clamp(40px, 6vw, 64px)', lineHeight: 1.05, maxWidth: 600 }}>
            What We Build<br />For <span className="accent">You.</span>
          </h2>
        </div>

        {/* 2-column layout */}
        <div style={{
          display: 'grid',
          gridTemplateColumns: '40% 60%',
          gap: 80,
          alignItems: 'start',
        }} className="services-grid">
          {/* LEFT — sticky image */}
          <div style={{ position: 'sticky', top: 100 }}>
            <div style={{
              borderRadius: 12, overflow: 'hidden',
              aspectRatio: '4/3', position: 'relative',
              background: '#F9FAFB',
            }}>
              <img
                src={services[activeIdx].img}
                alt={services[activeIdx].name}
                loading="lazy"
                style={{
                  width: '100%', height: '100%', objectFit: 'cover',
                  opacity: imgVisible ? 1 : 0,
                  transition: 'opacity 0.3s ease',
                }}
              />
              <div style={{
                position: 'absolute', inset: 0,
                background: 'linear-gradient(to top, rgba(8,9,10,0.5) 0%, transparent 50%)',
              }} />
              {/* Active service label */}
              <div style={{
                position: 'absolute', bottom: 20, left: 20,
                fontFamily: 'Montserrat, sans-serif', fontWeight: 800,
                fontSize: 18, color: '#111827',
                opacity: imgVisible ? 1 : 0,
                transition: 'opacity 0.3s ease',
              }}>
                {services[activeIdx].name}
              </div>
            </div>
          </div>

          {/* RIGHT — service rows */}
          <div>
            {services.map((s, i) => (
              <div
                key={s.num}
                className="service-row fade-up"
                style={{ animationDelay: `${i * 0.06}s` }}
                onMouseEnter={() => changeImage(i)}
              >
                <span className="service-num">{s.num}</span>
                <div>
                  <div className="service-name">{s.name}</div>
                  <div className="service-desc">{s.desc}</div>
                </div>
                <span className="service-arrow">→</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 1024px) {
          .services-grid {
            grid-template-columns: 1fr !important;
          }
          .services-grid > div:first-child {
            position: relative !important;
            top: auto !important;
          }
        }
      `}</style>
    </section>
  );
};

export default Services;
