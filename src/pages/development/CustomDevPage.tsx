import { useEffect, useState, useRef } from 'react';
import { Link } from 'react-router-dom';

export default function CustomDevPage() {
  const [expandedFaq, setExpandedFaq] = useState<number | null>(null);
  const [mousePos, setMousePos] = useState({ x: 0, y: 0 });
  const [activeTab, setActiveTab] = useState(0);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    document.title = 'Custom Development | DevBotics';
    window.scrollTo(0, 0);

    const handleMouseMove = (e: MouseEvent) => {
      setMousePos({ x: e.clientX, y: e.clientY });
    };
    window.addEventListener('mousemove', handleMouseMove);
    return () => window.removeEventListener('mousemove', handleMouseMove);
  }, []);

  return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72, color: '#0A0E27', position: 'relative', overflow: 'hidden' }}>
      {/* Animated Background */}
      <div style={{ position: 'fixed', inset: 0, pointerEvents: 'none', zIndex: 0 }}>
        <div style={{ position: 'absolute', width: '500px', height: '500px', background: 'radial-gradient(circle, rgba(31,136,68,0.1) 0%, transparent 70%)', borderRadius: '50%', filter: 'blur(80px)', transform: `translate(${mousePos.x - 250}px, ${mousePos.y - 250}px)`, transition: 'transform 0.2s ease-out' }} />
        <svg style={{ position: 'absolute', width: '100%', height: '100%', opacity: 0.05 }} viewBox="0 0 1200 800">
          <defs>
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#1F8844" strokeWidth="1"/>
            </pattern>
          </defs>
          <rect width="1200" height="800" fill="url(#grid)" />
        </svg>
      </div>

      {/* Hero Section */}
      <section style={{ maxWidth: '100%', position: 'relative', zIndex: 1, padding: '120px 32px 80px', textAlign: 'center', minHeight: '90vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <div style={{ maxWidth: 1000, animation: 'fadeInUp 1s ease-out forwards', opacity: 0 }}>
          <div style={{ display: 'inline-flex', alignItems: 'center', gap: 12, background: 'rgba(31,136,68,0.06)', border: '1px solid rgba(31,136,68,0.12)', padding: '10px 24px', borderRadius: 100, marginBottom: 32 }}>
            <span style={{ width: 8, height: 8, background: '#1F8844', borderRadius: '50%' }} />
            <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#1F8844', letterSpacing: '0.12em', fontWeight: 700 }}>AI-POWERED DEVELOPMENT</span>
          </div>

          <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px, 8vw, 96px)', color: '#0A0E27', lineHeight: 1.1, letterSpacing: '-0.02em', marginBottom: 24 }}>
            Next-Gen Software Built For <span style={{ color: '#1F8844' }}>Tomorrow</span>
          </h1>

          <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 'clamp(16px, 2vw, 20px)', color: '#374151', lineHeight: 1.8, maxWidth: 700, margin: '0 auto 32px', letterSpacing: '0.2px' }}>
            We don't build apps—we architect intelligent systems. Leveraging AI, machine learning, and cloud infrastructure to create software that learns and scales.
          </p>

          <div style={{ display: 'flex', gap: 16, justifyContent: 'center', flexWrap: 'wrap', marginBottom: 48 }}>
            <Link to="/contact" style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: '#1F8844', color: '#FFFFFF', padding: '14px 36px', borderRadius: 12, fontFamily: 'Montserrat,sans-serif', fontWeight: 700, textDecoration: 'none', transition: 'all 0.3s', boxShadow: '0 10px 30px rgba(31,136,68,0.12)', cursor: 'pointer', border: 'none' }} onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-3px)'; e.currentTarget.style.boxShadow = '0 18px 40px rgba(31,136,68,0.15)'; }} onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 30px rgba(31,136,68,0.12)'; }}>
              Start Building <span>→</span>
            </Link>
            <a href="#tech" style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: 'transparent', color: '#1F8844', padding: '14px 36px', borderRadius: 12, fontFamily: 'Montserrat,sans-serif', fontWeight: 700, textDecoration: 'none', transition: 'all 0.3s', border: '2px solid rgba(31,136,68,0.18)', cursor: 'pointer' }} onMouseEnter={e => { e.currentTarget.style.background = 'rgba(31,136,68,0.04)'; e.currentTarget.style.transform = 'translateY(-2px)'; }} onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.transform = 'translateY(0)'; }}>
              Explore Tech <span>↓</span>
            </a>
          </div>

          {/* Stats */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: 24, maxWidth: 700, margin: '0 auto', padding: '32px', background: 'rgba(31,136,68,0.05)', border: '1px solid rgba(31,136,68,0.1)', borderRadius: 16, backdropFilter: 'blur(10px)' }}>
            {[
              { num: '500+', label: 'AI Integrations' },
              { num: '99.99%', label: 'Uptime SLA' },
              { num: '2s', label: 'Load Time' },
              { num: '24/7', label: 'Support' },
            ].map((stat, i) => (
              <div key={i} style={{ textAlign: 'center' }}>
                <div style={{ fontSize: 28, fontWeight: 900, color: '#1F8844', marginBottom: 4, fontFamily: 'Montserrat,sans-serif' }}>{stat.num}</div>
                <div style={{ fontSize: 12, color: 'rgba(255,255,255,0.6)', fontFamily: 'Lexend,sans-serif' }}>{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Hero Image with Glassmorphism */}
      <section style={{ maxWidth: 1200, margin: '0 auto', padding: '0 32px 100px', position: 'relative', zIndex: 1 }}>
        <div style={{ borderRadius: 24, overflow: 'hidden', position: 'relative', border: '1px solid rgba(31,136,68,0.2)', background: 'rgba(31,136,68,0.05)', backdropFilter: 'blur(10px)', boxShadow: '0 0 60px rgba(31,136,68,0.2)', opacity: 0, animation: 'fadeInUp 1s ease-out 0.3s forwards' }}>
          <img src="/images/generic-1.png" alt="AI Development" style={{ width: '100%', height: 'auto', display: 'block', opacity: 0.9 }} />
          <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to top, rgba(10,14,39,0.8), transparent 40%)' }} />
        </div>
      </section>

      {/* AI Capabilities Section */}
      <section id="tech" style={{ background: 'linear-gradient(180deg, rgba(31,136,68,0.05) 0%, rgba(31,136,68,0.02) 100%)', padding: '100px 32px', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#0A0E27', marginBottom: 16, letterSpacing: '-0.01em' }}>
              AI-First Architecture
            </h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#4B5563', maxWidth: 600, margin: '0 auto' }}>
              Every line of code optimized for intelligence, performance, and scale.
            </p>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: 24 }}>
            {[
              { t: '🤖 LLM Integration', d: 'GPT-4, Claude, Llama, and custom models. Fine-tuning, prompt engineering, RAG systems, and agentic AI workflows.' },
              { t: '🧠 Machine Learning', d: 'TensorFlow, PyTorch pipelines. Predictive models, anomaly detection, NLP, computer vision in production.' },
              { t: '⚡ Real-Time Processing', d: 'Kafka, RabbitMQ, WebSockets. Low-latency streaming, live analytics, and instant data pipelines.' },
              { t: '🔗 Blockchain & Web3', d: 'Smart contracts, DeFi protocols, NFT systems. Solidity, Rust, and multi-chain architecture.' },
              { t: '☁️ Cloud-Native Scale', d: 'Kubernetes, microservices, serverless. Auto-scaling, multi-region deployment, 99.99% uptime.' },
              { t: '🔐 Zero-Trust Security', d: 'Military-grade encryption, biometric auth, blockchain verification. Compliance: SOC 2, HIPAA, GDPR.' },
            ].map((item, i) => (
              <div 
                key={i} 
                style={{ 
                  background: 'rgba(31,136,68,0.08)', 
                  padding: 32, 
                  borderRadius: 16, 
                  border: '1px solid rgba(31,136,68,0.2)', 
                  backdropFilter: 'blur(10px)',
                  transition: 'all 0.4s ease',
                  cursor: 'pointer',
                  position: 'relative',
                  overflow: 'hidden'
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.15)';
                  e.currentTarget.style.transform = 'translateY(-8px)';
                  e.currentTarget.style.boxShadow = '0 20px 50px rgba(31,136,68,0.2)';
                  e.currentTarget.style.borderColor = 'rgba(31,136,68,0.4)';
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.08)';
                  e.currentTarget.style.transform = 'translateY(0)';
                  e.currentTarget.style.boxShadow = 'none';
                  e.currentTarget.style.borderColor = 'rgba(31,136,68,0.2)';
                }}
              >
                <div style={{ position: 'absolute', top: '-50%', right: '-50%', width: '300px', height: '300px', background: 'radial-gradient(circle, rgba(31,136,68,0.1) 0%, transparent 70%)', borderRadius: '50%', pointerEvents: 'none' }} />
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 20, color: '#1F8844', marginBottom: 12, position: 'relative', zIndex: 1 }}>{item.t}</h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: 'rgba(255,255,255,0.7)', lineHeight: 1.7, position: 'relative', zIndex: 1 }}>{item.d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Tech Stack Interactive */}
      <section style={{ padding: '100px 32px', background: '#0A0E27', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#0A0E27', marginBottom: 16 }}>
              Enterprise Tech Stack
            </h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#4B5563', maxWidth: 600, margin: '0 auto' }}>
              Cutting-edge technologies, handpicked for your architecture.
            </p>
          </div>

          {/* Tab Navigation */}
          <div style={{ display: 'flex', gap: 12, marginBottom: 40, overflowX: 'auto', justifyContent: 'center', flexWrap: 'wrap' }}>
            {[
              { name: 'Frontend', id: 0 },
              { name: 'Backend', id: 1 },
              { name: 'AI/ML', id: 2 },
              { name: 'Cloud', id: 3 },
              { name: 'Data', id: 4 },
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                style={{
                  padding: '12px 24px',
                  borderRadius: 8,
                  fontFamily: 'Montserrat,sans-serif',
                  fontWeight: 700,
                  fontSize: 14,
                  border: activeTab === tab.id ? '2px solid #1F8844' : '2px solid rgba(31,136,68,0.2)',
                  background: activeTab === tab.id ? 'rgba(31,136,68,0.15)' : 'transparent',
                  color: activeTab === tab.id ? '#1F8844' : 'rgba(255,255,255,0.6)',
                  cursor: 'pointer',
                  transition: 'all 0.3s',
                  backdropFilter: 'blur(10px)',
                }}
                onMouseEnter={e => activeTab !== tab.id && (e.currentTarget.style.borderColor = 'rgba(31,136,68,0.4)')}
                onMouseLeave={e => activeTab !== tab.id && (e.currentTarget.style.borderColor = 'rgba(31,136,68,0.2)')}
              >
                {tab.name}
              </button>
            ))}
          </div>

          {/* Tab Content */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: 20 }}>
            {[
              {
                name: 'Frontend',
                techs: [
                  { name: 'React 19', badge: 'Best-in-class' },
                  { name: 'Next.js', badge: 'SSR/SSG' },
                  { name: 'TypeScript', badge: 'Type-safe' },
                  { name: 'Tailwind CSS', badge: 'Utility-first' },
                  { name: 'Framer Motion', badge: 'Animations' },
                  { name: 'Web3.js', badge: 'Blockchain' },
                ]
              },
              {
                name: 'Backend',
                techs: [
                  { name: 'Node.js', badge: 'JavaScript' },
                  { name: 'Python', badge: 'Data Science' },
                  { name: 'Go', badge: 'Performance' },
                  { name: 'Rust', badge: 'Safety' },
                  { name: 'GraphQL', badge: 'Query' },
                  { name: 'gRPC', badge: 'Real-time' },
                ]
              },
              {
                name: 'AI/ML',
                techs: [
                  { name: 'Claude API', badge: 'LLM' },
                  { name: 'GPT-4', badge: 'OpenAI' },
                  { name: 'LangChain', badge: 'Orchestration' },
                  { name: 'TensorFlow', badge: 'ML Framework' },
                  { name: 'Hugging Face', badge: 'Models' },
                  { name: 'RAG Systems', badge: 'Knowledge' },
                ]
              },
              {
                name: 'Cloud',
                techs: [
                  { name: 'AWS', badge: 'EC2, Lambda, RDS' },
                  { name: 'Google Cloud', badge: 'BigQuery' },
                  { name: 'Azure', badge: 'Enterprise' },
                  { name: 'Kubernetes', badge: 'Orchestration' },
                  { name: 'Docker', badge: 'Containers' },
                  { name: 'Terraform', badge: 'IaC' },
                ]
              },
              {
                name: 'Data',
                techs: [
                  { name: 'PostgreSQL', badge: 'Relational' },
                  { name: 'MongoDB', badge: 'NoSQL' },
                  { name: 'Redis', badge: 'Cache' },
                  { name: 'Elasticsearch', badge: 'Search' },
                  { name: 'Apache Kafka', badge: 'Streaming' },
                  { name: 'Data Warehouse', badge: 'Analytics' },
                ]
              },
            ][activeTab].techs.map((tech, i) => (
              <div
                key={i}
                style={{
                  background: 'rgba(31,136,68,0.1)',
                  border: '1px solid rgba(31,136,68,0.2)',
                  borderRadius: 12,
                  padding: 16,
                  textAlign: 'center',
                  backdropFilter: 'blur(10px)',
                  transition: 'all 0.3s',
                  cursor: 'pointer',
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.2)';
                  e.currentTarget.style.transform = 'translateY(-4px)';
                  e.currentTarget.style.boxShadow = '0 10px 30px rgba(31,136,68,0.2)';
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.1)';
                  e.currentTarget.style.transform = 'translateY(0)';
                  e.currentTarget.style.boxShadow = 'none';
                }}
              >
                <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 16, color: '#1F8844', marginBottom: 8 }}>
                  {tech.name}
                </div>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: 'rgba(31,136,68,0.8)', background: 'rgba(31,136,68,0.1)', padding: '4px 8px', borderRadius: 4, display: 'inline-block' }}>
                  {tech.badge}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Modern Development Pipeline */}
      <section style={{ padding: '100px 32px', background: 'linear-gradient(180deg, rgba(31,136,68,0.05) 0%, transparent 100%)', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#FFFFFF', marginBottom: 16 }}>
              Development Pipeline
            </h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)', maxWidth: 600, margin: '0 auto' }}>
              Agile methodology with continuous integration, testing, and deployment.
            </p>
          </div>

          {/* Timeline */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: 20, position: 'relative' }}>
            {[
              { phase: 'Phase 1', title: 'Discovery', desc: 'Requirements analysis, architecture design, tech stack selection', icon: '🎯', time: 'Week 1-2' },
              { phase: 'Phase 2', title: 'MVP Dev', desc: 'Core features, API setup, database architecture', icon: '⚡', time: 'Week 3-6' },
              { phase: 'Phase 3', title: 'AI Integration', desc: 'LLM integration, ML pipelines, automation', icon: '🤖', time: 'Week 7-10' },
              { phase: 'Phase 4', title: 'Testing', desc: 'QA, security audit, performance optimization', icon: '✓', time: 'Week 11-12' },
              { phase: 'Phase 5', title: 'Launch', desc: 'Deployment, monitoring, post-launch support', icon: '🚀', time: 'Week 13' },
              { phase: 'Phase 6', title: 'Scale', desc: 'Scaling, feature expansion, AI enhancement', icon: '📈', time: 'Ongoing' },
            ].map((step, i) => (
              <div
                key={i}
                style={{
                  background: 'rgba(31,136,68,0.08)',
                  border: '1px solid rgba(31,136,68,0.2)',
                  borderRadius: 16,
                  padding: 24,
                  textAlign: 'center',
                  backdropFilter: 'blur(10px)',
                  position: 'relative',
                  transition: 'all 0.4s ease',
                  cursor: 'pointer',
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.15)';
                  e.currentTarget.style.transform = 'translateY(-6px)';
                  e.currentTarget.style.boxShadow = '0 20px 50px rgba(31,136,68,0.2)';
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.08)';
                  e.currentTarget.style.transform = 'translateY(0)';
                  e.currentTarget.style.boxShadow = 'none';
                }}
              >
                <div style={{ fontSize: 36, marginBottom: 12 }}>{step.icon}</div>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', fontWeight: 700, marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.1em' }}>
                  {step.phase}
                </div>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 18, color: '#FFFFFF', marginBottom: 8 }}>
                  {step.title}
                </h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: 'rgba(255,255,255,0.7)', marginBottom: 12, lineHeight: 1.5 }}>
                  {step.desc}
                </p>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#1F8844', fontWeight: 600 }}>
                  {step.time}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Security & Performance */}
      <section style={{ padding: '100px 32px', background: '#0A0E27', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 60, alignItems: 'center' }}>
            <div>
              <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px, 4vw, 48px)', color: '#FFFFFF', marginBottom: 24, lineHeight: 1.2 }}>
                Bank-Level Security
              </h2>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)', marginBottom: 32, lineHeight: 1.8 }}>
                Security is woven into every layer of your application architecture.
              </p>
              <div style={{ display: 'grid', gap: 16 }}>
                {[
                  '🔐 256-bit end-to-end encryption',
                  '🛡️ Military-grade authentication & MFA',
                  '📋 GDPR, HIPAA, SOC 2 compliance ready',
                  '🔍 Regular penetration testing & audits',
                  '⚡ Real-time threat detection & response',
                  '🔄 Automated backup & disaster recovery',
                ].map((item, i) => (
                  <div key={i} style={{ display: 'flex', gap: 12, fontFamily: 'Lexend,sans-serif', fontSize: 15, color: 'rgba(255,255,255,0.8)' }}>
                    <span style={{ color: '#1F8844', fontWeight: 700, flexShrink: 0 }}>→</span>
                    <span>{item}</span>
                  </div>
                ))}
              </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
              {[
                { metric: '99.99%', label: 'Uptime SLA' },
                { metric: '< 2s', label: 'Page Load' },
                { metric: '< 100ms', label: 'API Response' },
                { metric: '98+', label: 'Lighthouse' },
              ].map((item, i) => (
                <div
                  key={i}
                  style={{
                    background: 'linear-gradient(135deg, rgba(31,136,68,0.2) 0%, rgba(31,136,68,0.05) 100%)',
                    border: '1px solid rgba(31,136,68,0.3)',
                    borderRadius: 16,
                    padding: 28,
                    textAlign: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  <div style={{ fontSize: 32, fontWeight: 900, color: '#1F8844', marginBottom: 8, fontFamily: 'Montserrat,sans-serif' }}>
                    {item.metric}
                  </div>
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: 'rgba(255,255,255,0.6)' }}>
                    {item.label}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Industries */}
      <section style={{ background: 'linear-gradient(180deg, rgba(31,136,68,0.05) 0%, transparent 100%)', padding: '100px 32px', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#FFFFFF', marginBottom: 16 }}>Industries We Transform</h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)', maxWidth: 600, margin: '0 auto' }}>Deep expertise across sectors. We speak your industry's language.</p>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: 16 }}>
            {[
              '🏦 FinTech & Banking',
              '🏥 Healthcare & MedTech',
              '🛍️ E-Commerce & Retail',
              '🏠 Real Estate & PropTech',
              '🎓 Education & EdTech',
              '🏭 Manufacturing & IoT',
              '✈️ Travel & Logistics',
              '🎬 Media & Entertainment',
              '📊 Analytics & BI',
              '⚖️ Legal Tech',
              '🌍 Web3 & Blockchain',
              '🤖 Enterprise Automation',
            ].map((industry, i) => (
              <div
                key={i}
                style={{
                  background: 'rgba(31,136,68,0.1)',
                  border: '1px solid rgba(31,136,68,0.2)',
                  padding: 20,
                  borderRadius: 12,
                  textAlign: 'center',
                  fontFamily: 'Lexend,sans-serif',
                  color: 'rgba(255,255,255,0.8)',
                  fontWeight: 600,
                  transition: 'all 0.3s',
                  cursor: 'pointer',
                  backdropFilter: 'blur(10px)',
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.2)';
                  e.currentTarget.style.transform = 'translateY(-4px)';
                  e.currentTarget.style.color = '#1F8844';
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.1)';
                  e.currentTarget.style.transform = 'translateY(0)';
                  e.currentTarget.style.color = 'rgba(255,255,255,0.8)';
                }}
              >
                {industry}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section style={{ padding: '100px 32px', background: '#0A0E27', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1000, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#FFFFFF', marginBottom: 16 }}>Common Questions</h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)' }}>Everything you need to know about custom development.</p>
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
            {[
              { q: 'How do you approach AI integration?', a: 'We assess your data, use cases, and goals to build custom AI solutions. From LLM fine-tuning to proprietary models, we engineer AI that drives real business value, not just buzzwords.' },
              { q: 'What about scalability and infrastructure?', a: 'Cloud-native from day one. We architect on Kubernetes, use serverless where optimal, and implement auto-scaling. Your app grows with your business, not against it.' },
              { q: 'How long does a project typically take?', a: 'MVP in 8-12 weeks. Complex enterprise systems in 4-6 months. We use agile with 2-week sprints, so you see progress constantly. Timelines depend on scope, not arbitrary estimates.' },
              { q: 'Do you own the code?', a: 'Absolutely. 100% code ownership, full documentation, and source code delivered. No vendor lock-in. We provide smooth handoff or ongoing support—your choice.' },
              { q: 'What are your engagement models?', a: 'Fixed project cost for defined scope, Time & Materials for evolving projects, or Dedicated Teams for long-term partnerships. Transparency in pricing, no hidden fees.' },
              { q: 'Can you integrate with legacy systems?', a: 'Yes. API gateways, microservices, database migrations—we modernize without disrupting operations. Many clients run old and new systems in parallel during transition.' },
            ].map((item, i) => (
              <div
                key={i}
                style={{
                  border: '1px solid rgba(31,136,68,0.2)',
                  borderRadius: 12,
                  overflow: 'hidden',
                  background: 'rgba(31,136,68,0.05)',
                  backdropFilter: 'blur(10px)',
                }}
              >
                <button
                  onClick={() => setExpandedFaq(expandedFaq === i ? null : i)}
                  style={{
                    width: '100%',
                    padding: '24px 32px',
                    background: expandedFaq === i ? 'rgba(31,136,68,0.15)' : 'transparent',
                    border: 'none',
                    cursor: 'pointer',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    transition: 'all 0.3s',
                  }}
                  onMouseEnter={e => expandedFaq !== i && (e.currentTarget.style.background = 'rgba(31,136,68,0.08)')}
                  onMouseLeave={e => expandedFaq !== i && (e.currentTarget.style.background = 'transparent')}
                >
                  <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 16, color: '#FFFFFF', textAlign: 'left' }}>{item.q}</h3>
                  <span style={{ fontSize: 20, color: '#1F8844', transition: 'transform 0.3s', transform: expandedFaq === i ? 'rotate(180deg)' : 'rotate(0)', flexShrink: 0, marginLeft: 16 }}>⌄</span>
                </button>
                {expandedFaq === i && (
                  <div style={{ padding: '0 32px 24px', background: 'rgba(31,136,68,0.08)', borderTop: '1px solid rgba(31,136,68,0.2)' }}>
                    <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: 'rgba(255,255,255,0.7)', lineHeight: 1.8 }}>{item.a}</p>
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Engagement Models */}
      <section style={{ background: 'linear-gradient(180deg, rgba(31,136,68,0.05) 0%, transparent 100%)', padding: '100px 32px', borderTop: '1px solid rgba(31,136,68,0.1)', position: 'relative', zIndex: 1 }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px, 5vw, 56px)', color: '#FFFFFF', marginBottom: 16 }}>How We Work Together</h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: 'rgba(255,255,255,0.7)', maxWidth: 600, margin: '0 auto' }}>Choose what works for your business model and growth stage.</p>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: 32 }}>
            {[
              { 
                title: 'Project-Based', 
                desc: 'Fixed scope, fixed timeline, fixed budget. Ideal when you know exactly what you need.', 
                icon: '✓',
                features: ['Clear deliverables', 'Predictable costs', 'Milestone-based', 'Quality guarantees'] 
              },
              { 
                title: 'Time & Materials', 
                desc: 'Flexibility for changing requirements. Pay for hours worked with transparent reporting.', 
                icon: '⚡',
                features: ['Flexible scope', 'Weekly updates', 'Scale up/down', 'Innovation space'] 
              },
              { 
                title: 'Dedicated Team', 
                desc: 'Build a long-term partnership. Your extended engineering team, working exclusively for you.', 
                icon: '🚀',
                features: ['Full ownership', 'Long-term focus', 'Rapid scaling', 'Deep context'] 
              },
            ].map((model, i) => (
              <div
                key={i}
                style={{
                  background: 'rgba(31,136,68,0.08)',
                  border: '1px solid rgba(31,136,68,0.2)',
                  borderRadius: 20,
                  padding: 40,
                  backdropFilter: 'blur(10px)',
                  display: 'flex',
                  flexDirection: 'column',
                  transition: 'all 0.4s ease',
                  cursor: 'pointer',
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.15)';
                  e.currentTarget.style.transform = 'translateY(-8px)';
                  e.currentTarget.style.boxShadow = '0 20px 50px rgba(31,136,68,0.2)';
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.background = 'rgba(31,136,68,0.08)';
                  e.currentTarget.style.transform = 'translateY(0)';
                  e.currentTarget.style.boxShadow = 'none';
                }}
              >
                <div style={{ fontSize: 32, marginBottom: 16 }}>{model.icon}</div>
                <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 24, color: '#FFFFFF', marginBottom: 12 }}>
                  {model.title}
                </h3>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: 'rgba(255,255,255,0.7)', marginBottom: 24, lineHeight: 1.6, flex: 1 }}>
                  {model.desc}
                </p>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 12, marginBottom: 24 }}>
                  {model.features.map((feature, j) => (
                    <div key={j} style={{ display: 'flex', gap: 10, fontFamily: 'Lexend,sans-serif', fontSize: 13, color: 'rgba(255,255,255,0.7)' }}>
                      <span style={{ color: '#1F8844', fontWeight: 700, flexShrink: 0 }}>→</span>
                      <span>{feature}</span>
                    </div>
                  ))}
                </div>
                <Link 
                  to="/contact" 
                  style={{ 
                    display: 'block', 
                    textAlign: 'center', 
                    background: 'linear-gradient(135deg, #1F8844 0%, #4AE085 100%)',
                    color: '#0A0E27', 
                    padding: '12px 24px', 
                    borderRadius: 8, 
                    fontFamily: 'Montserrat,sans-serif', 
                    fontWeight: 700, 
                    textDecoration: 'none', 
                    transition: 'all 0.3s',
                    border: 'none',
                    cursor: 'pointer',
                  }} 
                  onMouseEnter={e => {
                    e.currentTarget.style.transform = 'translateY(-2px)';
                    e.currentTarget.style.boxShadow = '0 10px 30px rgba(31,136,68,0.3)';
                  }}
                  onMouseLeave={e => {
                    e.currentTarget.style.transform = 'translateY(0)';
                    e.currentTarget.style.boxShadow = 'none';
                  }}
                >
                  Get Started
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Final CTA */}
      <section style={{ background: 'linear-gradient(135deg, #1F8844 0%, #4AE085 100%)', padding: '100px 32px', color: '#0A0E27', textAlign: 'center', position: 'relative', zIndex: 1, overflow: 'hidden' }}>
        <div style={{ position: 'absolute', inset: 0, opacity: 0.05, backgroundImage: 'url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")', pointerEvents: 'none' }} />
        <div style={{ maxWidth: 900, margin: '0 auto', position: 'relative', zIndex: 1 }}>
          <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(40px, 6vw, 64px)', color: '#0A0E27', marginBottom: 24, lineHeight: 1.2 }}>
            Ready to Build Something Extraordinary?
          </h2>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: 'rgba(10,14,39,0.8)', marginBottom: 40, lineHeight: 1.6 }}>
            Let's turn your vision into intelligent, scalable software. From concept to deployment and beyond.
          </p>
          <div style={{ display: 'flex', gap: 16, justifyContent: 'center', flexWrap: 'wrap' }}>
            <Link to="/contact" style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: '#0A0E27', color: '#1F8844', padding: '14px 36px', borderRadius: 12, fontFamily: 'Montserrat,sans-serif', fontWeight: 700, textDecoration: 'none', transition: 'all 0.3s', boxShadow: '0 8px 20px rgba(0,0,0,0.2)' }} onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-4px)'; e.currentTarget.style.boxShadow = '0 15px 40px rgba(0,0,0,0.3)'; }} onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 8px 20px rgba(0,0,0,0.2)'; }}>
              Start Your Project <span>→</span>
            </Link>
            <a href="#" style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: 'transparent', color: '#0A0E27', padding: '14px 36px', borderRadius: 12, fontFamily: 'Montserrat,sans-serif', fontWeight: 700, textDecoration: 'none', transition: 'all 0.3s', border: '2px solid #0A0E27' }} onMouseEnter={e => { e.currentTarget.style.backgroundColor = 'rgba(10,14,39,0.1)'; e.currentTarget.style.transform = 'translateY(-2px)'; }} onMouseLeave={e => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.transform = 'translateY(0)'; }}>
              Schedule Call <span>→</span>
            </a>
          </div>
        </div>
      </section>
      
      <style>{`
        @keyframes fadeInUp { 
          from { 
            opacity: 0; 
            transform: translateY(40px); 
          } 
          to { 
            opacity: 1; 
            transform: translateY(0); 
          } 
        }
        @keyframes pulse {
          0%, 100% { opacity: 1; }
          50% { opacity: 0.5; }
        }
        @media (max-width: 768px) {
          table { font-size: 12px; }
          table td, table th { padding: 16px 12px !important; }
          h1 { font-size: 36px !important; }
        }
      `}</style>
    </main>
  );
}
