import { useEffect, useRef, useState } from 'react';

const TYPEWRITER_TEXT = 'React Native · Flutter · Swift · Kotlin';

export default function AppDevHero() {
  const [typed, setTyped] = useState('');
  const [showCursor, setShowCursor] = useState(true);
  const [scrollPct, setScrollPct] = useState(0);
  const started = useRef(false);

  useEffect(() => {
    if (started.current) return;
    started.current = true;
    let i = 0;
    const t = setTimeout(() => {
      const iv = setInterval(() => {
        i++;
        setTyped(TYPEWRITER_TEXT.slice(0, i));
        if (i >= TYPEWRITER_TEXT.length) clearInterval(iv);
      }, 40);
    }, 900);
    return () => clearTimeout(t);
  }, []);

  useEffect(() => {
    const iv = setInterval(() => setShowCursor(p => !p), 530);
    return () => clearInterval(iv);
  }, []);

  useEffect(() => {
    const onScroll = () => {
      const doc = document.documentElement;
      setScrollPct((window.scrollY / (doc.scrollHeight - doc.clientHeight)) * 100);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <section style={{ minHeight: '100vh', background: '#08090A', position: 'relative', display: 'flex', flexDirection: 'column', justifyContent: 'center', overflow: 'hidden' }}>
      {/* Progress bar */}
      <div style={{ position: 'fixed', top: 0, left: 0, width: `${scrollPct}%`, height: 2, background: '#E8FF47', zIndex: 9999, transition: 'width 0.1s linear' }} />
      {/* Scanline bg */}
      <div style={{ position: 'absolute', inset: 0, backgroundImage: 'repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(255,255,255,0.015) 2px,rgba(255,255,255,0.015) 4px)', pointerEvents: 'none' }} />
      {/* Grid overlay */}
      <div style={{ position: 'absolute', inset: 0, backgroundImage: 'linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px)', backgroundSize: '60px 60px', pointerEvents: 'none' }} />
      {/* Vertical glow line */}
      <div style={{ position: 'absolute', top: '20%', right: 120, width: 1, height: '60%', background: '#E8FF47', opacity: 0.4, boxShadow: '0 0 12px #E8FF47' }} />
      {/* Breadcrumb */}
      <div style={{ position: 'absolute', top: 88, left: 40, fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#3A3D42' }}>
        / app-development<span style={{ animation: 'blink-cur 1s step-end infinite', color: '#E8FF47' }}>▌</span>
      </div>

      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '80px 40px 120px', width: '100%' }}>
        <div style={{ maxWidth: 820 }}>
          {/* Tags */}
          <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginBottom: 40 }}>
            {['APP DEVELOPMENT', '200+ APPS SHIPPED', 'SINCE 2021'].map((t, i) => (
              <span key={t} style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B6F76', border: '1px solid #1C1E21', padding: '6px 14px', letterSpacing: '0.15em', borderRadius: 2, animation: `slideInLeft 0.5s ease ${i * 0.1}s both` }}>{t}</span>
            ))}
          </div>
          {/* Headline */}
          <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(52px,7vw,96px)', lineHeight: 0.95, letterSpacing: '-0.03em', marginBottom: 36, color: '#F0F0F0' }}>
            <span style={{ display: 'block', animation: 'fadeSlideUp 0.6s ease 0.3s both' }}>Mobile Apps</span>
            <span style={{ display: 'block', animation: 'fadeSlideUp 0.6s ease 0.42s both' }}>Built to</span>
            <span style={{ display: 'block', color: '#E8FF47', animation: 'fadeSlideUp 0.6s ease 0.54s both' }}>Dominate.</span>
          </h1>
          {/* Typewriter */}
          <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#6B6F76', marginBottom: 20, letterSpacing: '0.03em' }}>
            {typed}<span style={{ opacity: showCursor ? 1 : 0, color: '#E8FF47' }}>|</span>
          </div>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 18, color: '#6B6F76', lineHeight: 1.65, maxWidth: 600, marginBottom: 44 }}>
            We don't build apps. We build products that acquire users, retain them, and scale without breaking.
          </p>
          {/* CTAs */}
          <div style={{ display: 'flex', gap: 32, alignItems: 'center', flexWrap: 'wrap' }}>
            <a href="#contact" style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 13, background: '#F0F0F0', color: '#08090A', textDecoration: 'none', padding: '13px 28px', borderRadius: 4, letterSpacing: '0.05em', transition: 'background 0.15s ease' }}
              onMouseEnter={e => (e.currentTarget.style.background = '#E8FF47')}
              onMouseLeave={e => (e.currentTarget.style.background = '#F0F0F0')}>
              [ START YOUR PROJECT → ]
            </a>
            <a href="#portfolio" style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#3A3D42', textDecoration: 'none', transition: 'color 0.2s' }}
              onMouseEnter={e => (e.currentTarget.style.color = '#F0F0F0')}
              onMouseLeave={e => (e.currentTarget.style.color = '#3A3D42')}>
              View case studies ↓
            </a>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0, borderTop: '1px solid #1C1E21' }}>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)' }} className="hero-bottom-bar">
          {[['APPS IN DEVELOPMENT','3'],['AVG DELIVERY','8 WEEKS'],['TECH STACK','12+ TOOLS'],['CLIENT RATING','4.9 / 5']].map(([label, val], i) => (
            <div key={label} style={{ padding: '14px 24px', borderRight: i < 3 ? '1px solid #1C1E21' : 'none', display: 'flex', alignItems: 'center', gap: 10 }}>
              <span style={{ color: '#E8FF47', fontSize: 8, animation: 'pulse-dot 2s infinite' }}>⬤</span>
              <span style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 11, color: '#6B6F76', letterSpacing: '0.08em' }}>
                {label}&nbsp;&nbsp;<strong style={{ color: '#F0F0F0', fontWeight: 400 }}>{val}</strong>
              </span>
            </div>
          ))}
        </div>
      </div>

      <style>{`
        @keyframes blink-cur { 0%,100%{opacity:1}50%{opacity:0} }
        @keyframes slideInLeft { from{opacity:0;transform:translateX(-16px)}to{opacity:1;transform:translateX(0)} }
        @keyframes fadeSlideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }
        @keyframes pulse-dot { 0%,100%{transform:scale(1)}50%{transform:scale(1.4)} }
        @media(max-width:640px){.hero-bottom-bar{grid-template-columns:repeat(2,1fr)!important;}}
      `}</style>
    </section>
  );
}
