import { useEffect, useRef, useState } from 'react';

const stats = [
  { value: 500, suffix: '+', label: 'Projects Delivered' },
  { value: 50, suffix: '+', label: 'Happy Clients' },
  { value: 5, suffix: '★', label: 'Average Rating' },
  { value: 3, suffix: '+', label: 'Years Experience' },
];

// Fix 7: Row 1 — ALL white bold. Row 2 — ALL muted regular. Separator dot is #E8FF47
const techItems = ['React', 'Flutter', 'Next.js', 'Node.js', 'Python', 'OpenAI', 'TypeScript', 'AWS', 'MongoDB', 'PostgreSQL', 'Docker', 'Kubernetes', 'GraphQL', 'Firebase', 'TailwindCSS', 'Redux'];
const serviceItems = ['App Development', 'Web Development', 'AI Solutions', 'Software Development', 'Digital Marketing', 'UI/UX Design', 'Shopify', 'SaaS Development', 'SEO Services', 'Chatbot Dev', 'ERP Software', 'Cloud Solutions'];

// Triplicate to ensure zero-gap seamless loop
const techRow = [...techItems, ...techItems, ...techItems];
const serviceRow = [...serviceItems, ...serviceItems, ...serviceItems];

function useCountUp(target: number, duration = 1800) {
  const [count, setCount] = useState(0);
  const started = useRef(false);
  const elRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const el = elRef.current;
    if (!el) return;
    const obs = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting && !started.current) {
        started.current = true;
        const t0 = performance.now();
        const tick = (now: number) => {
          const p = Math.min((now - t0) / duration, 1);
          setCount(Math.floor(p * target));
          if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }
    }, { threshold: 0.5 });
    obs.observe(el);
    return () => obs.disconnect();
  }, [target, duration]);

  return { count, elRef };
}

const StatItem = ({ stat }: { stat: typeof stats[0] }) => {
  const { count, elRef } = useCountUp(stat.value);
  return (
    <div ref={elRef} style={{ textAlign: 'center' }}>
      <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 6vw, 80px)', color: '#F0F0F0', lineHeight: 1, letterSpacing: '-0.03em' }}>
        {count}{stat.suffix}
      </div>
      <div style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 300, fontSize: 13, color: '#6B6F76', letterSpacing: '0.1em', textTransform: 'uppercase', marginTop: 8 }}>
        {stat.label}
      </div>
    </div>
  );
};

const Dot = () => (
  <span style={{ color: '#E8FF47', fontSize: 8, lineHeight: 1, display: 'inline-flex', alignItems: 'center', padding: '0 14px' }}>●</span>
);

const Stats = () => (
  <section style={{ padding: '120px 0', background: '#0E1012', overflow: 'hidden' }}>
    {/* Count-up stats */}
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px', marginBottom: 80 }}>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 48 }} className="stats-grid fade-up">
        {stats.map(s => <StatItem key={s.label} stat={s} />)}
      </div>
    </div>

    {/* Row 1 — ALL #F0F0F0, Montserrat SemiBold 18px, scrolls LEFT */}
    <div style={{
      overflow: 'hidden', marginBottom: 16,
      maskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
      WebkitMaskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
    }}>
      <div style={{ display: 'flex', alignItems: 'center', animation: 'marquee 28s linear infinite', width: 'max-content' }}>
        {techRow.map((t, i) => (
          <span key={i} style={{ display: 'inline-flex', alignItems: 'center', whiteSpace: 'nowrap' }}>
            <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 18, color: '#F0F0F0' }}>{t}</span>
            <Dot />
          </span>
        ))}
      </div>
    </div>

    {/* Row 2 — ALL #6B6F76, Montserrat Regular 16px, scrolls RIGHT */}
    <div style={{
      overflow: 'hidden',
      maskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
      WebkitMaskImage: 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)',
    }}>
      <div style={{ display: 'flex', alignItems: 'center', animation: 'marquee-rev 34s linear infinite', width: 'max-content' }}>
        {serviceRow.map((t, i) => (
          <span key={i} style={{ display: 'inline-flex', alignItems: 'center', whiteSpace: 'nowrap' }}>
            <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 400, fontSize: 16, color: '#6B6F76' }}>{t}</span>
            <Dot />
          </span>
        ))}
      </div>
    </div>

    <style>{`
      @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr) !important; } }
    `}</style>
  </section>
);

export default Stats;
