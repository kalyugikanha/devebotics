import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { ChevronRight, ArrowRight, ArrowUpRight, Send, CheckCircle2, Zap, TrendingUp } from 'lucide-react';
import { aiPageData } from './aiPageData';

interface Props { slug: string; }

const defaultProcessSteps = [
  { n: '01', title: 'Discovery & Audit', desc: 'We assess your data infrastructure, define KPIs, and map measurable success criteria for the AI implementation.' },
  { n: '02', title: 'Architecture Design', desc: 'Our engineers design the optimal model pipeline, selecting the right frameworks, data flows, and integration points.' },
  { n: '03', title: 'Build & Train', desc: 'We develop, fine-tune, and rigorously validate the model against your domain-specific benchmarks and edge cases.' },
  { n: '04', title: 'Deploy & Monitor', desc: 'Production deployment on your preferred cloud with real-time monitoring, alerting, and auto-scaling built in.' },
];

export default function AiServiceTypePage({ slug }: Props) {
  const data = aiPageData[slug];
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  useEffect(() => {
    if (data) document.title = data.metaTitle;
    window.scrollTo(0, 0);
  }, [slug, data]);

  if (!data) return (
    <main style={{ minHeight: '100vh', background: '#070B14', paddingTop: 80, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ fontFamily: 'Montserrat, sans-serif', fontSize: 24, color: '#FFFFFF', fontWeight: 900 }}>Service not found</div>
    </main>
  );

  const handleForm = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setTimeout(() => { setIsSubmitting(false); setSubmitted(true); }, 2000);
  };

  const Demo = data.DemoComponent;
  const processSteps = data.processSteps || defaultProcessSteps;

  return (
    <main style={{ minHeight: '100vh', background: '#FFFFFF', color: '#111827', overflowX: 'hidden' }}>

      {/* ══════════════════════════════════════
          HERO — Dark Cinematic
      ══════════════════════════════════════ */}
      <section style={{ background: '#070B14', minHeight: '100vh', display: 'flex', alignItems: 'center', paddingTop: 120, paddingBottom: 80, paddingLeft: 24, paddingRight: 24, position: 'relative', overflow: 'hidden' }}>

        {/* Animated grid background */}
        <div style={{ position: 'absolute', inset: 0, backgroundImage: 'linear-gradient(rgba(31,136,68,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(31,136,68,0.04) 1px, transparent 1px)', backgroundSize: '60px 60px', pointerEvents: 'none' }} />

        {/* Glow orbs */}
        <div style={{ position: 'absolute', top: '15%', right: '8%', width: 600, height: 600, background: 'radial-gradient(circle, rgba(31,136,68,0.1) 0%, transparent 65%)', pointerEvents: 'none' }} />
        <div style={{ position: 'absolute', bottom: '10%', left: '3%', width: 360, height: 360, background: 'radial-gradient(circle, rgba(31,136,68,0.06) 0%, transparent 65%)', pointerEvents: 'none' }} />

        <div style={{ maxWidth: 1400, margin: '0 auto', width: '100%', position: 'relative', zIndex: 1 }}>

          {/* Breadcrumb */}
          <nav style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 12, fontFamily: 'Lexend, sans-serif', fontWeight: 600, letterSpacing: '0.1em', textTransform: 'uppercase' as const, color: '#6B7280', marginBottom: 56 }}>
            <Link to="/" style={{ color: '#6B7280', textDecoration: 'none', transition: 'color 0.2s' }}
              onMouseEnter={e => (e.currentTarget.style.color = '#1F8844')}
              onMouseLeave={e => (e.currentTarget.style.color = '#6B7280')}>
              Home
            </Link>
            <ChevronRight size={14} />
            <Link to="/ai-services" style={{ color: '#6B7280', textDecoration: 'none', transition: 'color 0.2s' }}
              onMouseEnter={e => (e.currentTarget.style.color = '#1F8844')}
              onMouseLeave={e => (e.currentTarget.style.color = '#6B7280')}>
              AI Services
            </Link>
            <ChevronRight size={14} />
            <span style={{ color: '#1F8844' }}>{data.heroH1.join(' ')}</span>
          </nav>

          {/* Two-column layout */}
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 80, alignItems: 'center' }} className="ai-hero-grid">

            {/* Left: Content */}
            <motion.div initial={{ opacity: 0, y: 40 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.7 }}>

              {/* Pill label */}
              <div style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: 'rgba(31,136,68,0.08)', border: '1px solid rgba(31,136,68,0.2)', borderRadius: 100, padding: '6px 18px', marginBottom: 36 }}>
                <span style={{ width: 6, height: 6, borderRadius: '50%', background: '#1F8844', display: 'inline-block', boxShadow: '0 0 8px rgba(31,136,68,0.8)' }} />
                <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase' as const }}>AI Service</span>
              </div>

              {/* Headline */}
              <h1 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(40px, 5.5vw, 76px)', lineHeight: 1.0, letterSpacing: '-0.03em', color: '#FFFFFF', marginBottom: 28 }}>
                {data.heroH1[0]}<br />
                <span style={{ color: '#1F8844' }}>{data.heroH1[1]}</span>
              </h1>

              {/* Subtext */}
              <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 17, color: '#9CA3AF', lineHeight: 1.8, marginBottom: 48, maxWidth: 500 }}>
                {data.heroSub}
              </p>

              {/* CTAs */}
              <div style={{ display: 'flex', alignItems: 'center', gap: 16, flexWrap: 'wrap' as const }}>
                <a href="#contact"
                  style={{ display: 'inline-flex', alignItems: 'center', gap: 10, padding: '15px 32px', background: '#1F8844', color: '#FFFFFF', fontFamily: 'Lexend, sans-serif', fontWeight: 700, fontSize: 15, borderRadius: 8, textDecoration: 'none', transition: 'all 0.2s ease', letterSpacing: '0.01em' }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.background = '#166534'; (e.currentTarget as HTMLElement).style.transform = 'translateY(-2px)'; (e.currentTarget as HTMLElement).style.boxShadow = '0 8px 24px rgba(31,136,68,0.3)'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.background = '#1F8844'; (e.currentTarget as HTMLElement).style.transform = 'none'; (e.currentTarget as HTMLElement).style.boxShadow = 'none'; }}>
                  Start a Project <ArrowRight size={18} />
                </a>
                <a href="#capabilities"
                  style={{ display: 'inline-flex', alignItems: 'center', gap: 10, padding: '15px 32px', background: 'transparent', color: '#E5E7EB', fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 15, borderRadius: 8, border: '1px solid rgba(255,255,255,0.12)', textDecoration: 'none', transition: 'all 0.2s ease' }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = 'rgba(255,255,255,0.35)'; (e.currentTarget as HTMLElement).style.color = '#FFFFFF'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = 'rgba(255,255,255,0.12)'; (e.currentTarget as HTMLElement).style.color = '#E5E7EB'; }}>
                  View Capabilities
                </a>
              </div>

              {/* Stats strip */}
              <div style={{ display: 'flex', gap: 36, marginTop: 56, paddingTop: 32, borderTop: '1px solid rgba(255,255,255,0.06)', flexWrap: 'wrap' as const }}>
                {[['50+', 'Enterprise Clients'], ['99.9%', 'Uptime SLA'], ['24hr', 'Response Time']].map(([val, label], i) => (
                  <div key={i}>
                    <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#1F8844' }}>{val}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#6B7280', marginTop: 2 }}>{label}</div>
                  </div>
                ))}
              </div>
            </motion.div>

            {/* Right: Interactive Demo */}
            <motion.div initial={{ opacity: 0, scale: 0.95, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} transition={{ duration: 0.8, delay: 0.2 }}>
              <div style={{ position: 'relative' }}>
                <div style={{ position: 'absolute', inset: -2, background: 'linear-gradient(135deg, rgba(31,136,68,0.2), transparent 55%, rgba(31,136,68,0.08))', borderRadius: 32, filter: 'blur(28px)', zIndex: 0 }} />
                <div style={{ position: 'relative', zIndex: 1 }}>
                  <Demo />
                </div>
              </div>
            </motion.div>
          </div>
        </div>

        <style>{`
          @media (max-width: 1024px) {
            .ai-hero-grid { grid-template-columns: 1fr !important; gap: 56px !important; }
          }
        `}</style>
      </section>

      {/* ══════════════════════════════════════
          CAPABILITIES — Bento Grid
      ══════════════════════════════════════ */}
      <section id="capabilities" style={{ padding: '120px 24px', background: '#F9FAFB' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>

          {/* Section heading */}
          <div style={{ marginBottom: 64 }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 18 }}>
              <div style={{ width: 32, height: 2, background: '#1F8844', borderRadius: 2 }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.2em', textTransform: 'uppercase' as const }}>Core Capabilities</span>
            </div>
            <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 52px)', color: '#111827', letterSpacing: '-0.02em', lineHeight: 1.1 }}>
              What We Build For You
            </h2>
          </div>

          {/* Bento grid */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 20 }} className="ai-bento-grid">

            {/* Card 1 — Dark, spans 2 cols */}
            <motion.div className="bento-wide"
              initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6 }}
              style={{ gridColumn: 'span 2', background: '#111827', borderRadius: 24, padding: '52px 52px', position: 'relative', overflow: 'hidden' }}>
              <div style={{ position: 'absolute', top: -60, right: -60, width: 250, height: 250, background: 'radial-gradient(circle, rgba(31,136,68,0.12) 0%, transparent 70%)', pointerEvents: 'none' }} />
              <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 96, color: 'rgba(255,255,255,0.025)', lineHeight: 1, position: 'absolute', bottom: 12, right: 32, userSelect: 'none' as const }}>01</div>
              <div style={{ width: 48, height: 48, background: 'rgba(31,136,68,0.12)', border: '1px solid rgba(31,136,68,0.2)', borderRadius: 14, display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 32 }}>
                <Zap size={22} style={{ color: '#1F8844' }} />
              </div>
              <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 28, color: '#FFFFFF', marginBottom: 16, letterSpacing: '-0.02em' }}>{data.features[0].t}</h3>
              <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#9CA3AF', lineHeight: 1.8, maxWidth: 460 }}>{data.features[0].d}</p>
            </motion.div>

            {/* Card 2 — White, right column */}
            <motion.div
              initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6, delay: 0.1 }}
              style={{ background: '#FFFFFF', borderRadius: 24, padding: 40, border: '1px solid #E5E7EB', display: 'flex', flexDirection: 'column' as const }}>
              <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 64, color: '#F3F4F6', lineHeight: 1, marginBottom: 4 }}>02</div>
              <div style={{ width: 36, height: 3, background: '#1F8844', borderRadius: 2, marginBottom: 24 }} />
              <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#111827', marginBottom: 14, letterSpacing: '-0.02em', lineHeight: 1.2, flex: 1 }}>{data.features[1].t}</h3>
              <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.8, marginBottom: 28 }}>{data.features[1].d}</p>
              <div style={{ display: 'flex', alignItems: 'center', gap: 8, paddingTop: 20, borderTop: '1px solid #F3F4F6' }}>
                <CheckCircle2 size={15} style={{ color: '#1F8844', flexShrink: 0 }} />
                <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#6B7280' }}>Included in all plans</span>
              </div>
            </motion.div>

            {/* Card 3 — White, bottom left */}
            <motion.div
              initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6, delay: 0.2 }}
              style={{ background: '#FFFFFF', borderRadius: 24, padding: 40, border: '1px solid #E5E7EB', position: 'relative', overflow: 'hidden' }}>
              <div style={{ position: 'absolute', bottom: 0, right: 0, width: 100, height: 100, background: 'linear-gradient(135deg, transparent, rgba(31,136,68,0.04))', borderRadius: '24px 0' }} />
              <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 48, color: '#F3F4F6', lineHeight: 1, marginBottom: 16 }}>03</div>
              <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#111827', marginBottom: 12, letterSpacing: '-0.02em' }}>{data.features[2].t}</h3>
              <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.8 }}>{data.features[2].d}</p>
            </motion.div>

            {/* Card 4 — Green stats, spans 2 cols */}
            <motion.div className="bento-wide"
              initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6, delay: 0.3 }}
              style={{ gridColumn: 'span 2', background: '#1F8844', borderRadius: 24, padding: 40, display: 'flex', alignItems: 'center', gap: 48, flexWrap: 'wrap' as const }}>
              <div style={{ flex: 1, minWidth: 200 }}>
                <TrendingUp size={28} style={{ color: 'rgba(255,255,255,0.5)', marginBottom: 16 }} />
                <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 24, color: '#FFFFFF', marginBottom: 10, letterSpacing: '-0.02em' }}>Proven Enterprise Results</h3>
                <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 15, color: 'rgba(255,255,255,0.7)', lineHeight: 1.7 }}>Measurable ROI across 50+ enterprise deployments worldwide.</p>
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 12 }}>
                {[['95%+', 'Model Accuracy'], ['80%', 'Cost Reduction'], ['3×', 'Faster to Market']].map(([n, l], i) => (
                  <div key={i} style={{ background: 'rgba(0,0,0,0.15)', borderRadius: 14, padding: '18px 20px', textAlign: 'center' as const }}>
                    <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#FFFFFF' }}>{n}</div>
                    <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 12, color: 'rgba(255,255,255,0.6)', marginTop: 4 }}>{l}</div>
                  </div>
                ))}
              </div>
            </motion.div>
          </div>
        </div>
        <style>{`
          @media (max-width: 900px) {
            .ai-bento-grid { grid-template-columns: 1fr !important; }
            .bento-wide { grid-column: span 1 !important; }
          }
        `}</style>
      </section>

      {/* ══════════════════════════════════════
          PROCESS TIMELINE — Dark
      ══════════════════════════════════════ */}
      <section style={{ padding: '120px 24px', background: '#111827', position: 'relative', overflow: 'hidden' }}>
        <div style={{ position: 'absolute', top: 0, left: 0, right: 0, height: 1, background: 'linear-gradient(to right, transparent, rgba(31,136,68,0.5), transparent)' }} />
        <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0, height: 1, background: 'linear-gradient(to right, transparent, rgba(31,136,68,0.2), transparent)' }} />

        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center' as const, marginBottom: 80 }}>
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 12, marginBottom: 20 }}>
              <div style={{ width: 40, height: 1, background: 'rgba(31,136,68,0.4)' }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.2em', textTransform: 'uppercase' as const }}>Our Process</span>
              <div style={{ width: 40, height: 1, background: 'rgba(31,136,68,0.4)' }} />
            </div>
            <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 52px)', color: '#FFFFFF', letterSpacing: '-0.02em' }}>
              How We Build It
            </h2>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)' }} className="ai-process-grid">
            {processSteps.map((step: any, i: number) => (
              <motion.div
                key={i}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: i * 0.1 }}
                style={{
                  padding: '48px 36px',
                  borderLeft: i === 0 ? 'none' : '1px solid rgba(255,255,255,0.06)',
                  background: i === 0 ? 'rgba(31,136,68,0.05)' : 'transparent',
                  cursor: 'default',
                  position: 'relative',
                }}
              >
                <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 64, color: 'rgba(255,255,255,0.03)', lineHeight: 1, marginBottom: 24, userSelect: 'none' as const }}>{step.n}</div>
                <div style={{ width: 28, height: 3, background: '#1F8844', borderRadius: 2, marginBottom: 24 }} />
                <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 20, color: '#FFFFFF', marginBottom: 14, letterSpacing: '-0.01em', lineHeight: 1.2 }}>{step.title}</h3>
                <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#6B7280', lineHeight: 1.8 }}>{step.desc}</p>
              </motion.div>
            ))}
          </div>
        </div>
        <style>{`
          @media (max-width: 900px) { .ai-process-grid { grid-template-columns: 1fr 1fr !important; } }
          @media (max-width: 600px) { .ai-process-grid { grid-template-columns: 1fr !important; } }
        `}</style>
      </section>

      {/* ══════════════════════════════════════
          TECH STACK + RELATED SERVICES
      ══════════════════════════════════════ */}
      <section style={{ padding: '120px 24px', background: '#FFFFFF' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 80 }} className="ai-tech-grid">

          {/* Left: Tech Stack */}
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 18 }}>
              <div style={{ width: 32, height: 2, background: '#1F8844' }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.2em', textTransform: 'uppercase' as const }}>Technology</span>
            </div>
            <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(28px, 3.5vw, 44px)', color: '#111827', letterSpacing: '-0.02em', marginBottom: 20, lineHeight: 1.1 }}>
              The Engine Room
            </h2>
            <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#4B5563', lineHeight: 1.8, marginBottom: 40 }}>
              Enterprise-grade frameworks and battle-tested infrastructure ensure your AI solution is fast, scalable, and production-ready from day one.
            </p>
            <div style={{ display: 'flex', flexWrap: 'wrap' as const, gap: 10, marginBottom: 40 }}>
              {data.tech.map((t: string, i: number) => (
                <motion.span
                  key={t}
                  initial={{ opacity: 0, scale: 0.9 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.3, delay: i * 0.05 }}
                  style={{ padding: '10px 20px', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 100, fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 14, color: '#111827', cursor: 'default', transition: 'all 0.2s ease', display: 'inline-block' }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.color = '#1F8844'; (e.currentTarget as HTMLElement).style.background = 'rgba(31,136,68,0.04)'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.color = '#111827'; (e.currentTarget as HTMLElement).style.background = '#F9FAFB'; }}
                >
                  {t}
                </motion.span>
              ))}
            </div>
            <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' as const }}>
              {[['🔒', 'SOC 2 Compliant'], ['🛡️', 'GDPR Ready'], ['⚡', 'Auto-Scaling'], ['🌐', 'Multi-Region']].map(([icon, label], i) => (
                <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '8px 14px', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 8 }}>
                  <span>{icon}</span>
                  <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#4B5563', fontWeight: 500, whiteSpace: 'nowrap' as const }}>{label}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Right: Related Services */}
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 18 }}>
              <div style={{ width: 32, height: 2, background: '#1F8844' }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.2em', textTransform: 'uppercase' as const }}>Also Consider</span>
            </div>
            <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(28px, 3.5vw, 44px)', color: '#111827', letterSpacing: '-0.02em', marginBottom: 32, lineHeight: 1.1 }}>
              Related Services
            </h2>
            <div style={{ display: 'flex', flexDirection: 'column' as const, gap: 16 }}>
              {data.related.map((relSlug: string) => {
                const rel = aiPageData[relSlug];
                if (!rel) return null;
                return (
                  <Link
                    key={relSlug}
                    to={`/ai-services/${relSlug}`}
                    style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '24px 28px', background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 16, textDecoration: 'none', transition: 'all 0.2s ease' }}
                    onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#1F8844'; (e.currentTarget as HTMLElement).style.background = 'rgba(31,136,68,0.02)'; (e.currentTarget as HTMLElement).style.transform = 'translateX(4px)'; }}
                    onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#E5E7EB'; (e.currentTarget as HTMLElement).style.background = '#F9FAFB'; (e.currentTarget as HTMLElement).style.transform = 'none'; }}
                  >
                    <div>
                      <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#9CA3AF', fontWeight: 600, letterSpacing: '0.1em', textTransform: 'uppercase' as const, marginBottom: 6 }}>AI Service</div>
                      <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 18, color: '#111827', lineHeight: 1.2 }}>
                        {rel.heroH1.join(' ')}
                      </div>
                    </div>
                    <div style={{ width: 44, height: 44, borderRadius: '50%', border: '1px solid #E5E7EB', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                      <ArrowUpRight size={18} style={{ color: '#1F8844' }} />
                    </div>
                  </Link>
                );
              })}
            </div>
          </div>
        </div>
        <style>{`@media (max-width: 900px) { .ai-tech-grid { grid-template-columns: 1fr !important; gap: 60px !important; } }`}</style>
      </section>

      {/* ══════════════════════════════════════
          CONTACT CTA — Dark
      ══════════════════════════════════════ */}
      <section id="contact" style={{ padding: '120px 24px', background: '#070B14', position: 'relative', overflow: 'hidden' }}>
        <div style={{ position: 'absolute', inset: 0, backgroundImage: 'linear-gradient(rgba(31,136,68,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(31,136,68,0.03) 1px, transparent 1px)', backgroundSize: '60px 60px', pointerEvents: 'none' }} />
        <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', width: 700, height: 400, background: 'radial-gradient(ellipse, rgba(31,136,68,0.07) 0%, transparent 70%)', pointerEvents: 'none' }} />

        <div style={{ maxWidth: 860, margin: '0 auto', position: 'relative', zIndex: 1 }}>

          {/* Heading */}
          <div style={{ textAlign: 'center' as const, marginBottom: 60 }}>
            <div style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: 'rgba(31,136,68,0.08)', border: '1px solid rgba(31,136,68,0.2)', borderRadius: 100, padding: '6px 18px', marginBottom: 28 }}>
              <span style={{ width: 6, height: 6, borderRadius: '50%', background: '#1F8844', display: 'inline-block' }} />
              <span style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase' as const }}>Ready to Start?</span>
            </div>
            <h2 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 60px)', color: '#FFFFFF', letterSpacing: '-0.03em', marginBottom: 20, lineHeight: 1.05 }}>
              Let's Build Your<br /><span style={{ color: '#1F8844' }}>AI Solution</span>
            </h2>
            <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 17, color: '#6B7280', lineHeight: 1.7 }}>
              Tell us about your project. We respond within 24 hours with a free technical feasibility assessment.
            </p>
          </div>

          {submitted ? (
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }} animate={{ opacity: 1, scale: 1 }}
              style={{ textAlign: 'center' as const, padding: '80px 40px', background: 'rgba(31,136,68,0.06)', border: '1px solid rgba(31,136,68,0.2)', borderRadius: 24 }}
            >
              <CheckCircle2 size={56} style={{ color: '#1F8844', margin: '0 auto 24px', display: 'block' }} />
              <h3 style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 28, color: '#FFFFFF', marginBottom: 12 }}>Inquiry Received</h3>
              <p style={{ fontFamily: 'Lexend, sans-serif', color: '#6B7280', fontSize: 16 }}>Our solutions team will contact you within 24 hours with a free assessment.</p>
            </motion.div>
          ) : (
            <form onSubmit={handleForm}
              style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)', borderRadius: 24, padding: '52px 56px', backdropFilter: 'blur(20px)' }}
              className="ai-contact-form">
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, marginBottom: 20 }} className="ai-form-row">
                {[
                  ['Full Name', 'text', 'John Smith'],
                  ['Work Email', 'email', 'john@company.com'],
                ].map(([label, type, placeholder], i) => (
                  <div key={i}>
                    <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B7280', fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase' as const, marginBottom: 10 }}>{label}</label>
                    <input
                      type={type as string}
                      required
                      placeholder={placeholder as string}
                      style={{ width: '100%', background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.09)', borderRadius: 10, padding: '14px 18px', color: '#FFFFFF', fontFamily: 'Lexend, sans-serif', fontSize: 15, outline: 'none', transition: 'border-color 0.2s', boxSizing: 'border-box' as const }}
                      onFocus={e => (e.currentTarget.style.borderColor = 'rgba(31,136,68,0.5)')}
                      onBlur={e => (e.currentTarget.style.borderColor = 'rgba(255,255,255,0.09)')}
                    />
                  </div>
                ))}
              </div>
              <div style={{ marginBottom: 20 }}>
                <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B7280', fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase' as const, marginBottom: 10 }}>Company / Organisation</label>
                <input type="text" placeholder="Acme Corp"
                  style={{ width: '100%', background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.09)', borderRadius: 10, padding: '14px 18px', color: '#FFFFFF', fontFamily: 'Lexend, sans-serif', fontSize: 15, outline: 'none', transition: 'border-color 0.2s', boxSizing: 'border-box' as const }}
                  onFocus={e => (e.currentTarget.style.borderColor = 'rgba(31,136,68,0.5)')}
                  onBlur={e => (e.currentTarget.style.borderColor = 'rgba(255,255,255,0.09)')} />
              </div>
              <div style={{ marginBottom: 32 }}>
                <label style={{ display: 'block', fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B7280', fontWeight: 700, letterSpacing: '0.15em', textTransform: 'uppercase' as const, marginBottom: 10 }}>Project Brief</label>
                <textarea required rows={5} placeholder="Describe your use case, data sources, and goals..."
                  style={{ width: '100%', background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.09)', borderRadius: 10, padding: '14px 18px', color: '#FFFFFF', fontFamily: 'Lexend, sans-serif', fontSize: 15, outline: 'none', resize: 'vertical', transition: 'border-color 0.2s', boxSizing: 'border-box' as const, lineHeight: 1.7 }}
                  onFocus={e => (e.currentTarget.style.borderColor = 'rgba(31,136,68,0.5)')}
                  onBlur={e => (e.currentTarget.style.borderColor = 'rgba(255,255,255,0.09)')} />
              </div>
              <button
                type="submit"
                disabled={isSubmitting}
                style={{ width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 12, padding: '18px 32px', background: '#1F8844', color: '#FFFFFF', fontFamily: 'Lexend, sans-serif', fontWeight: 700, fontSize: 16, borderRadius: 10, border: 'none', cursor: isSubmitting ? 'not-allowed' : 'pointer', transition: 'all 0.2s ease', letterSpacing: '0.02em', opacity: isSubmitting ? 0.7 : 1 }}
                onMouseEnter={e => { if (!isSubmitting) (e.currentTarget as HTMLElement).style.background = '#166534'; }}
                onMouseLeave={e => { if (!isSubmitting) (e.currentTarget as HTMLElement).style.background = '#1F8844'; }}
              >
                {isSubmitting ? 'Submitting Inquiry...' : 'Submit Project Inquiry'}
                {!isSubmitting && <Send size={18} />}
              </button>
            </form>
          )}
        </div>
        <style>{`
          @media (max-width: 640px) {
            .ai-contact-form { padding: 32px 24px !important; }
            .ai-form-row { grid-template-columns: 1fr !important; }
          }
        `}</style>
      </section>

    </main>
  );
}
