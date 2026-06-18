import { Link } from 'react-router-dom';
import { appTypeCards } from '../pages/app-development/appPageData';

function AppCard({ card, index }: { card: typeof appTypeCards[0]; index: number }) {
  return (
    <Link to={`/app-development/${card.slug}`} style={{ textDecoration: 'none', flexShrink: 0, width: 340, height: 440, position: 'relative', scrollSnapAlign: 'start', borderRight: '1px solid #E5E7EB', display: 'block', overflow: 'hidden', transition: 'border-color 0.3s ease' }}
      onMouseEnter={e => {
        (e.currentTarget as HTMLElement).style.borderRightColor = '#1F8844';
        const img = e.currentTarget.querySelector('img') as HTMLElement;
        if (img) img.style.transform = 'scale(1.05)';
        const ov = e.currentTarget.querySelector('.card-ov') as HTMLElement;
        if (ov) ov.style.background = 'linear-gradient(180deg,rgba(8,9,10,0.05) 0%,rgba(8,9,10,0.82) 60%)';
        const ct = e.currentTarget.querySelector('.card-ct') as HTMLElement;
        if (ct) ct.style.transform = 'translateY(-8px)';
        const ar = e.currentTarget.querySelector('.card-ar') as HTMLElement;
        if (ar) { ar.style.opacity = '1'; ar.style.transform = 'translateX(0)'; }
      }}
      onMouseLeave={e => {
        (e.currentTarget as HTMLElement).style.borderRightColor = '#E5E7EB';
        const img = e.currentTarget.querySelector('img') as HTMLElement;
        if (img) img.style.transform = 'scale(1)';
        const ov = e.currentTarget.querySelector('.card-ov') as HTMLElement;
        if (ov) ov.style.background = 'linear-gradient(180deg,rgba(8,9,10,0.1) 0%,rgba(8,9,10,0.95) 70%)';
        const ct = e.currentTarget.querySelector('.card-ct') as HTMLElement;
        if (ct) ct.style.transform = 'translateY(0)';
        const ar = e.currentTarget.querySelector('.card-ar') as HTMLElement;
        if (ar) { ar.style.opacity = '0'; ar.style.transform = 'translateX(-8px)'; }
      }}>
      <img src={card.img} alt={card.name} loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.4s ease', position: 'absolute', inset: 0 }} />
      <div className="card-ov" style={{ position: 'absolute', inset: 0, background: 'linear-gradient(180deg,rgba(8,9,10,0.1) 0%,rgba(8,9,10,0.95) 70%)', transition: 'background 0.3s ease' }} />
      <div style={{ position: 'absolute', top: 16, right: 16, fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 48, color: 'rgba(240,240,240,0.08)', lineHeight: 1 }}>{String(index + 1).padStart(2, '0')}</div>
      <div className="card-ct" style={{ position: 'absolute', bottom: 0, left: 0, right: 0, padding: 28, transition: 'transform 0.3s ease' }}>
        <span style={{ display: 'inline-block', background: 'rgba(232,255,71,0.15)', border: '1px solid rgba(232,255,71,0.3)', color: '#1F8844', fontSize: 10, fontFamily: 'Lexend,sans-serif', letterSpacing: '0.15em', textTransform: 'uppercase', padding: '3px 10px', borderRadius: 2, marginBottom: 10 }}>{card.name.split(' ')[0]}</span>
        <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 22, color: '#111827', marginBottom: 8, lineHeight: 1.2 }}>{card.name}</div>
        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', lineHeight: 1.55, marginBottom: 14, display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>{card.desc}</div>
        <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
          {card.tags.map(t => <span key={t} style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#4B5563', border: '1px solid #9CA3AF', padding: '2px 8px', borderRadius: 2 }}>{t}</span>)}
        </div>
        <div className="card-ar" style={{ position: 'absolute', bottom: 28, right: 28, color: '#1F8844', fontSize: 20, opacity: 0, transform: 'translateX(-8px)', transition: 'opacity 0.25s, transform 0.25s' }}>→</div>
      </div>
    </Link>
  );
}

export default function AppDevShowcase() {
  return (
    <section style={{ padding: '100px 0 0', background: '#F9FAFB', overflow: 'hidden' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', marginBottom: 40 }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', flexWrap: 'wrap', gap: 16 }}>
          <div>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 12 }}>// WHAT WE BUILD</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px,4vw,56px)', lineHeight: 1.05, color: '#111827', margin: 0 }}>
              Every App Type.<br />One Expert Team.
            </h2>
          </div>
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF' }}>← Scroll to explore →</div>
        </div>
      </div>
      <div style={{ display: 'flex', overflowX: 'auto', scrollSnapType: 'x mandatory', paddingBottom: 4 }} className="appdev-horiz-scroll">
        {appTypeCards.map((card, i) => <AppCard key={card.slug} card={card} index={i} />)}
      </div>
      <style>{`
        .appdev-horiz-scroll::-webkit-scrollbar { height: 3px; }
        .appdev-horiz-scroll::-webkit-scrollbar-track { background: #F9FAFB; }
        .appdev-horiz-scroll::-webkit-scrollbar-thumb { background: #1F8844; border-radius: 2px; }
        @media(max-width:640px) { .appdev-horiz-scroll > a { width: 85vw !important; } }
      `}</style>
    </section>
  );
}
