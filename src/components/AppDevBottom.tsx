import { useState } from 'react';

// ─── FAQ ──────────────────────────────────────────────────────────
const faqs = [
  { q: 'How long does it take to build a mobile app?', a: 'A basic MVP takes 4–8 weeks. A full-featured app takes 3–5 months depending on complexity.' },
  { q: 'Do you build for iOS and Android both?', a: 'Yes. We use React Native and Flutter to build cross-platform apps that run natively on both.' },
  { q: 'Will I own the source code after delivery?', a: 'Absolutely. Full source code, IP rights, and all assets are transferred to you upon final payment.' },
  { q: 'Do you sign an NDA before discussion?', a: 'Yes. We sign an NDA before any project discussion begins.' },
  { q: 'What is your development methodology?', a: 'Agile sprints — 2-week cycles with working builds, demos, and open communication at every stage.' },
  { q: 'Can I see progress during development?', a: 'Yes. We share working builds every 2 weeks so you can test and give feedback throughout.' },
  { q: 'Do you provide post-launch support?', a: 'Yes. All plans include 3 months free support after launch. Extended plans are also available.' },
  { q: 'Can you integrate any payment gateway?', a: 'Yes. Razorpay, Stripe, PayU, PayPal, and all major Indian and international gateways.' },
  { q: 'How do you handle app store submission?', a: 'We handle the complete submission, ASO optimization, and approval process for both stores.' },
  { q: 'Can you add AI features to my app?', a: 'Yes. We integrate AI chatbots, recommendation engines, image recognition, and custom ML models.' },
  { q: 'What if I need changes after launch?', a: 'Minor tweaks are covered under post-launch support. For larger features, we offer retainer plans.' },
  { q: 'Do you work with international clients?', a: 'Yes. We work with clients across USA, UK, UAE, Australia, and Southeast Asia with flexible timezones.' },
];

export default function AppDevBottom() {
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  const left = faqs.slice(0, 6);
  const right = faqs.slice(6);

  const FaqItem = ({ f, idx }: { f: typeof faqs[0]; idx: number }) => (
    <div style={{ borderBottom: '1px solid #1C1E21', borderLeft: openFaq === idx ? '2px solid #E8FF47' : '2px solid transparent', paddingLeft: openFaq === idx ? 14 : 0, transition: 'all 0.2s ease' }}>
      <button onClick={() => setOpenFaq(openFaq === idx ? null : idx)} style={{
        width: '100%', display: 'flex', justifyContent: 'space-between', alignItems: 'center',
        padding: '22px 0', background: 'transparent', border: 'none', cursor: 'pointer', textAlign: 'left', gap: 12,
      }}>
        <span style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 16, color: '#F0F0F0' }}>{f.q}</span>
        <span style={{ color: '#E8FF47', fontSize: 20, flexShrink: 0, lineHeight: 1 }}>{openFaq === idx ? '−' : '+'}</span>
      </button>
      {openFaq === idx && <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76', lineHeight: 1.75, paddingBottom: 20 }}>{f.a}</div>}
    </div>
  );

  return (
    <>
      <section style={{ padding: '100px 0', background: '#08090A' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px' }}>
          <div style={{ marginBottom: 64 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 12 }}>// QUESTIONS</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(28px,4vw,52px)', color: '#F0F0F0', margin: 0 }}>Everything You Want<br />to Know.</h2>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 80px' }} className="faq-grid">
            <div>{left.map((f, i) => <FaqItem key={i} f={f} idx={i} />)}</div>
            <div>{right.map((f, i) => <FaqItem key={i + 6} f={f} idx={i + 6} />)}</div>
          </div>
        </div>
        <style>{`@media(max-width:768px){.faq-grid{grid-template-columns:1fr!important;}}`}</style>
      </section>

      <section id="contact" style={{ padding: '100px 60px', background: '#E8FF47', position: 'relative', overflow: 'hidden' }}>
        <div style={{ maxWidth: 900, margin: '0 auto', position: 'relative', zIndex: 2 }}>
          <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 11, color: '#08090A', letterSpacing: '0.2em', marginBottom: 24 }}>READY TO BUILD?</div>
          <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px,8vw,88px)', color: '#08090A', lineHeight: 0.95, marginBottom: 28 }}>
            Your App.<br />Our Obsession.
          </h2>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 500, fontSize: 18, color: 'rgba(8,9,10,0.6)', marginBottom: 44, lineHeight: 1.5 }}>
            Tell us your idea today.<br />Working prototype in 2 weeks.
          </p>
          <div style={{ display: 'flex', gap: 32, alignItems: 'center', flexWrap: 'wrap' }}>
            <a href="mailto:hello@devbotics.in" style={{
              fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 14,
              background: '#08090A', color: '#E8FF47', textDecoration: 'none',
              padding: '14px 32px', borderRadius: 4, letterSpacing: '0.05em',
              transition: 'opacity 0.2s',
            }}
              onMouseEnter={e => (e.currentTarget.style.opacity = '0.85')}
              onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
              [ START YOUR PROJECT ]
            </a>
            <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: 'rgba(8,9,10,0.5)' }}>or call us: +91 98765 43210</span>
          </div>
        </div>
        <div style={{ position: 'absolute', bottom: 40, right: 60, width: 120, height: 120, animation: 'spin-slow 20s linear infinite' }}>
          <svg viewBox="0 0 120 120" style={{ width: '100%', height: '100%' }}>
            <defs><path id="circ" d="M 60,60 m -45,0 a 45,45 0 1,1 90,0 a 45,45 0 1,1 -90,0" /></defs>
            <text style={{ fill: 'rgba(8,9,10,0.2)', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 10, letterSpacing: '0.15em' }}>
              <textPath href="#circ">DEVBOTICS · BUILD · SHIP · SCALE · REPEAT · </textPath>
            </text>
          </svg>
        </div>
      </section>

      <a href="https://wa.me/919876543210" target="_blank" rel="noopener noreferrer" style={{
        position: 'fixed', bottom: 28, left: 28, zIndex: 999,
        display: 'flex', alignItems: 'center', gap: 8,
        background: '#25D366', color: '#fff', textDecoration: 'none',
        fontFamily: 'Lexend,sans-serif', fontWeight: 600, fontSize: 13,
        padding: '10px 18px', borderRadius: 4,
        boxShadow: '0 4px 20px rgba(37,211,102,0.3)',
        transition: 'transform 0.2s ease',
      }}
        onMouseEnter={e => (e.currentTarget.style.transform = 'translateY(-2px)')}
        onMouseLeave={e => (e.currentTarget.style.transform = 'translateY(0)')}>
        💬 Chat on WhatsApp
      </a>
    </>
  );
}
