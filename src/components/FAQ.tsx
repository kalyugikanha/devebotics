import { useState } from 'react';
import AnimatedIllustration from './AnimatedIllustration';

const faqs = [
  { q: 'How much does it cost to build an app or website?', a: 'Project costs vary depending on complexity, features, and timeline. A simple landing page starts at ₹15,000, while a full-featured mobile app can range from ₹2–10 lakh. We provide a detailed quote after a free discovery call.' },
  { q: 'How long does development typically take?', a: 'Timelines depend on scope. A basic website takes 2–4 weeks, a standard app 6–12 weeks, and enterprise software 3–6 months. We provide a detailed project plan with milestones before we start.' },
  { q: 'Do you sign an NDA before starting?', a: 'Absolutely. We sign a Non-Disclosure Agreement before any project discussion. Your ideas, data, and business logic are fully protected throughout our engagement.' },
  { q: 'How many revisions are included?', a: 'We include 2 rounds of revisions for design and 1 round for development at no extra cost. Additional revisions are billed at a transparent hourly rate discussed upfront.' },
  { q: 'What technologies do you use?', a: 'We specialize in React, Next.js, Flutter, React Native, Node.js, Python (Django/FastAPI), PostgreSQL, MongoDB, AWS, Firebase, and OpenAI APIs. We choose the best stack for your specific needs.' },
  { q: 'Do you offer post-launch support?', a: 'Yes — all projects include a 90-day post-launch warranty covering bug fixes. We also offer monthly maintenance plans for ongoing support, updates, and performance monitoring.' },
  { q: 'Can you build AI-powered features into my app?', a: 'Absolutely. We integrate OpenAI, Claude, Gemini, and custom ML models into apps for chatbots, recommendations, automation, computer vision, and natural language processing.' },
  { q: 'Can I hire a dedicated developer from your team?', a: 'Yes! We offer dedicated developer hiring for React, Flutter, Node.js, Python, Full Stack, and UI/UX. Developers can be integrated into your team on a monthly retainer.' },
  { q: 'Do you work with startups or only large companies?', a: 'We love working with startups! In fact, most of our clients are early-stage founders. We offer flexible engagement models and MVPs designed to validate ideas quickly and cost-effectively.' },
  { q: 'What is your payment structure?', a: 'We typically follow a 40/40/20 milestone-based payment structure — 40% upfront, 40% at mid-point, and 20% upon final delivery. For longer projects, we can customize this accordingly.' },
];

const FAQ = () => {
  const [open, setOpen] = useState<number | null>(null);

  return (
    <section style={{ padding: '120px 0', background: '#FFFFFF' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
        <div style={{
          display: 'grid', gridTemplateColumns: '40% 1fr',
          gap: 80, alignItems: 'start',
        }} className="faq-grid" id="faq-section">
          {/* Left heading + image */}
          <div style={{ position: 'sticky', top: 100 }}>
            <div className="fade-up">
              <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)', marginBottom: 24 }}>
                Frequently Asked <span className="accent">Questions.</span>
              </h2>
              <p style={{
                fontFamily: 'Lexend, sans-serif', fontSize: 16,
                color: '#4B5563', lineHeight: 1.7, marginBottom: 40,
              }}>
                Everything you need to know before starting a project with us.
              </p>
              <a href="#" style={{
                fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 14,
                background: '#111827', color: '#FFFFFF', textDecoration: 'none',
                padding: '12px 24px', borderRadius: 6, display: 'inline-block',
                transition: 'background 0.2s ease',
              }}
                onMouseEnter={e => (e.currentTarget.style.background = '#1F8844')}
                onMouseLeave={e => (e.currentTarget.style.background = '#111827')}
              >
                Still have questions? Chat with us
              </a>
            </div>
            <div className="fade-up" style={{ marginTop: 48, borderRadius: 16, overflow: 'hidden', lineHeight: 0, minHeight: 240, background: '#F3F4F6', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
              <AnimatedIllustration />
            </div>
          </div>

          {/* Right accordion */}
          <div>
            {faqs.map((faq, i) => (
              <div key={i} className="accordion-item">
                <button
                  className="accordion-trigger"
                  onClick={() => setOpen(open === i ? null : i)}
                  style={{ fontSize: 16 }}
                >
                  <span>{faq.q}</span>
                  <svg
                    width="20" height="20" viewBox="0 0 20 20" fill="none"
                    style={{
                      transition: 'transform 0.2s ease', flexShrink: 0, marginLeft: 16,
                      transform: open === i ? 'rotate(45deg)' : 'rotate(0deg)',
                    }}
                  >
                    <path d="M10 4v12M4 10h12" stroke="#1F8844" strokeWidth="1.5" strokeLinecap="round" />
                  </svg>
                </button>
                <div
                  className={`accordion-body${open === i ? ' open' : ''}`}
                  style={{ fontSize: 15 }}
                >
                  {faq.a}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 1024px) {
          .faq-grid { grid-template-columns: 1fr !important; }
          .faq-grid > div:first-child { position: relative !important; top: auto !important; }
        }
      `}</style>
    </section>
  );
};

export default FAQ;
