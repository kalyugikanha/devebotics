import { useEffect, useState } from 'react';

import { Link } from 'react-router-dom';

const CAPABILITIES = [
  { id: '01', title: 'Generative AI Integration', desc: 'GPT-4 and Claude powered systems trained on your proprietary data, integrated seamlessly into your product workflows.', icon: '✨', color: '#6366F1', span: 2, slug: 'generative-ai' },
  { id: '02', title: 'Computer Vision', desc: 'Real-time image recognition, quality control, and visual object detection systems.', icon: '👁️', color: '#0EA5E9', span: 1, slug: 'computer-vision' },
  { id: '03', title: 'Predictive Analytics', desc: 'Advanced ML models forecasting sales, detecting anomalies, and analyzing behavior.', icon: '📈', color: '#10B981', span: 1, slug: 'predictive-analytics' },
  { id: '04', title: 'Intelligent Automation', desc: 'Self-learning workflow automation reducing manual operational work by up to 80%.', icon: '⚡', color: '#F59E0B', span: 2, slug: 'intelligent-automation' },
  { id: '05', title: 'Natural Language Processing', desc: 'Deep document understanding, sentiment analysis, and intelligent text extraction.', icon: '📝', color: '#EC4899', span: 2, slug: 'natural-language-processing' },
  { id: '06', title: 'Custom AI Architectures', desc: 'Proprietary ML models built entirely from scratch for your specific needs.', icon: '🧠', color: '#8B5CF6', span: 1, slug: 'custom-ai-architectures' }
];

const INDUSTRIES = [
  { name: 'E-commerce', apps: ['Smart product recommendations', 'Visual search', 'Demand forecasting', 'Customer service chatbot'], metric: 'AI recommendations increased AOV by 47%', color: '#6366F1' },
  { name: 'Healthcare', apps: ['Medical image analysis', 'Patient triage automation', 'EHR parsing', 'Predictive readmission models'], metric: 'Reduced administrative overhead by 62%', color: '#0EA5E9' },
  { name: 'Finance', apps: ['Fraud detection', 'Automated credit scoring', 'Algorithmic trading', 'Document KYC automation'], metric: 'Fraud detection accuracy improved to 99.8%', color: '#10B981' },
  { name: 'Enterprise SaaS', apps: ['Churn prediction models', 'Automated ticket routing', 'Generative AI content', 'Smart search & indexing'], metric: 'Customer retention increased by 28%', color: '#F59E0B' }
];

const PORTFOLIO = [
  { t: 'Support Auto-Pilot', ind: 'E-commerce', metric: '83% ticket automation', tech: ['GPT-4', 'Pinecone', 'LangChain'], img: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80', badge: 'LLM Agent' },
  { t: 'MedVision AI Diagnostics', ind: 'Healthcare', metric: '94% diagnostic accuracy', tech: ['TensorFlow', 'OpenCV'], img: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&q=80', badge: 'Computer Vision' },
  { t: 'Predictive Sales Engine', ind: 'SaaS', metric: '±3% forecast accuracy', tech: ['PyTorch', 'Time Series'], img: 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&q=80', badge: 'Data Science' }
];

const FAQS = [
  { q: "What types of AI systems do you develop?", a: "We build everything from intelligent LLM wrappers to custom-trained computer vision models, predictive engines, and complex NLP pipelines using industry-leading frameworks." },
  { q: "How much data is required to train an AI model?", a: "For LLMs utilizing RAG (Retrieval-Augmented Generation), a few hundred high-quality documents suffice. For training custom ML models from scratch, thousands of well-labeled examples are typically needed." },
  { q: "Can you integrate AI into my existing platform?", a: "Absolutely. Over 90% of our projects involve architecting AI microservices that plug seamlessly into existing Node.js, Python, or Java backends via highly secure REST/GraphQL APIs." },
  { q: "Do you ensure data privacy and security?", a: "Yes. We can deploy enterprise-grade, open-source models directly onto your private cloud or on-premise servers, ensuring your sensitive data never leaves your environment." }
];

export default function AiServices() {
  const [activeTab, setActiveTab] = useState(0);
  const [openFaq, setOpenFaq] = useState<number | null>(null);
  const [chatStep, setChatStep] = useState(0);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    const timer = setInterval(() => {
      setChatStep(s => (s + 1) % 4);
    }, 3000);
    return () => clearInterval(timer);
  }, []);

  const handleForm = (e: any) => {
    e.preventDefault();
    setIsSubmitting(true);
    setTimeout(() => setIsSubmitting(false), 2000);
  };

  useEffect(() => {
    document.title = 'AI Innovation | DevBotics';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main style={{ background: '#F8FAFC', minHeight: '100vh', color: '#0F172A', fontFamily: '"Plus Jakarta Sans", sans-serif', overflowX: 'hidden' }}>
      
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        .heading-font { font-family: 'Outfit', sans-serif; }
        
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
        @keyframes float-delayed { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes blob-bounce {
          0% { transform: translate(0, 0) scale(1); }
          33% { transform: translate(30px, -50px) scale(1.1); }
          66% { transform: translate(-20px, 20px) scale(0.9); }
          100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes shimmer {
          0% { background-position: -200% center; }
          100% { background-position: 200% center; }
        }
        
        .mesh-bg {
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background-color: #F8FAFC;
          background-image: 
            radial-gradient(at 10% 20%, hsla(250,100%,85%,0.8) 0px, transparent 50%),
            radial-gradient(at 90% 10%, hsla(189,100%,80%,0.8) 0px, transparent 50%),
            radial-gradient(at 50% 60%, hsla(333,100%,85%,0.6) 0px, transparent 50%),
            radial-gradient(at 80% 80%, hsla(33,100%,80%,0.8) 0px, transparent 50%),
            radial-gradient(at 20% 90%, hsla(22,100%,85%,0.7) 0px, transparent 50%);
          filter: blur(60px);
          z-index: 0;
          opacity: 0.9;
        }

        .glass-card {
          background: rgba(255, 255, 255, 0.6);
          backdrop-filter: blur(24px);
          -webkit-backdrop-filter: blur(24px);
          border: 1px solid rgba(255, 255, 255, 0.8);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 1);
        }
        
        .bento-grid {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 24px;
        }

        .hover-lift {
          transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .hover-lift:hover {
          transform: translateY(-8px);
          box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 1);
        }

        .btn-primary {
          background: #0F172A;
          color: white;
          border-radius: 100px;
          transition: all 0.3s;
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
        }
        .btn-primary:hover {
          background: #334155;
          transform: translateY(-2px);
          box-shadow: 0 15px 30px rgba(15, 23, 42, 0.2);
        }
        
        .btn-secondary {
          background: rgba(255,255,255,0.8);
          color: #0F172A;
          border-radius: 100px;
          transition: all 0.3s;
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
          border: 1px solid rgba(0,0,0,0.05);
          backdrop-filter: blur(10px);
        }
        .btn-secondary:hover {
          background: white;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
          transform: translateY(-2px);
        }

        .gradient-text {
          background: linear-gradient(135deg, #4F46E5 0%, #06B6D4 100%, #EC4899 200%);
          background-size: 200% auto;
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          animation: shimmer 5s linear infinite;
        }
        
        .outline-text {
          color: transparent;
          -webkit-text-stroke: 1.5px rgba(15, 23, 42, 0.15);
          font-weight: 900;
        }

        .pill {
          background: #FFFFFF;
          border: 1px solid rgba(0,0,0,0.05);
          border-radius: 100px;
          padding: 6px 16px;
          font-size: 13px;
          font-weight: 600;
          display: inline-flex;
          align-items: center;
          gap: 8px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @media (max-width: 1024px) {
          .bento-grid { grid-template-columns: repeat(2, 1fr); }
          .hero-split, .split-section { flex-direction: column !important; }
          .hero-title { font-size: 3.5rem !important; }
        }
        @media (max-width: 768px) {
          .bento-grid { grid-template-columns: 1fr; }
          .hero-title { font-size: 2.5rem !important; }
          .col-span-2 { grid-column: span 1 / span 1 !important; }
        }
      `}</style>

      {/* 1. HERO SECTION - CREATIVE LIGHT MODE */}
      <section style={{ minHeight: '100vh', position: 'relative', display: 'flex', alignItems: 'center', paddingTop: 80, paddingBottom: 60, paddingLeft: '5%', paddingRight: '5%', overflow: 'hidden' }}>
        <div className="mesh-bg"></div>
        
        {/* Decorative Grid Overlay */}
        <div style={{ position: 'absolute', inset: 0, backgroundImage: 'linear-gradient(rgba(15,23,42,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.03) 1px, transparent 1px)', backgroundSize: '40px 40px', pointerEvents: 'none', maskImage: 'linear-gradient(to bottom, black 40%, transparent 100%)', WebkitMaskImage: 'linear-gradient(to bottom, black 40%, transparent 100%)', zIndex: 1 }} />

        <div style={{ maxWidth: 1400, margin: '0 auto', position: 'relative', zIndex: 2, display: 'flex', gap: 60, alignItems: 'center', width: '100%' }} className="hero-split">
          
          <div style={{ flex: '1 1 50%' }}>
            <div className="pill" style={{ marginBottom: 24 }}>
              <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#6366F1', animation: 'blob-bounce 2s infinite' }} />
              <span style={{ color: '#4F46E5', letterSpacing: '0.05em' }}>NEXT-GEN AI AGENCY</span>
            </div>
            
            <h1 className="heading-font hero-title" style={{ fontSize: '5rem', fontWeight: 800, lineHeight: 1.05, letterSpacing: '-0.03em', marginBottom: 24, color: '#0F172A' }}>
              Intelligence<br/>
              <span className="outline-text">By Design.</span><br/>
              <span className="gradient-text">Engineered for You.</span>
            </h1>
            
            <p style={{ fontSize: '1.125rem', color: '#475569', lineHeight: 1.6, maxWidth: 540, marginBottom: 40 }}>
              We design and engineer bespoke AI systems, beautiful intelligent interfaces, and highly capable autonomous agents for visionary companies.
            </p>
            
            <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
              <a href="#consultation" className="btn-primary" style={{ padding: '16px 32px' }}>
                Initiate Project
              </a>
              <a href="#capabilities" className="btn-secondary" style={{ padding: '16px 32px' }}>
                Explore Capabilities
              </a>
            </div>
            
            <div style={{ marginTop: 48, display: 'flex', alignItems: 'center', gap: 16 }}>
              <div style={{ display: 'flex' }}>
                {[1,2,3,4].map((i) => (
                  <img key={i} src={`https://i.pravatar.cc/100?img=${i+10}`} alt="User" style={{ width: 40, height: 40, borderRadius: '50%', border: '2px solid white', marginLeft: i > 1 ? -15 : 0 }} />
                ))}
              </div>
              <div style={{ fontSize: 14, color: '#475569', fontWeight: 500 }}>
                Trusted by <span style={{ color: '#0F172A', fontWeight: 700 }}>50+</span> innovative teams
              </div>
            </div>
          </div>

          {/* Hero Interactive Visual */}
          <div style={{ flex: '1 1 50%', position: 'relative', display: 'flex', justifyContent: 'center' }}>
            {/* Abstract Decorative Elements */}
            <div style={{ position: 'absolute', top: -40, right: 40, width: 120, height: 120, background: 'linear-gradient(135deg, #6366F1, #0EA5E9)', borderRadius: '30% 70% 70% 30% / 30% 30% 70% 70%', animation: 'spin-slow 20s linear infinite', opacity: 0.2, filter: 'blur(10px)' }} />
            
            {/* Glass App Mockup */}
            <div className="glass-card" style={{ width: '100%', maxWidth: 500, borderRadius: 32, padding: 24, position: 'relative', animation: 'float 6s ease-in-out infinite' }}>
              
              <div style={{ display: 'flex', gap: 8, marginBottom: 24, paddingBottom: 16, borderBottom: '1px solid rgba(0,0,0,0.05)' }}>
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#EF4444' }} />
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#F59E0B' }} />
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#10B981' }} />
              </div>
              
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16, minHeight: 320 }}>
                {chatStep > 0 && (
                  <div style={{ alignSelf: 'flex-end', background: '#0F172A', color: 'white', padding: '14px 20px', borderRadius: '20px 20px 0 20px', fontSize: 14, boxShadow: '0 10px 20px rgba(15,23,42,0.1)' }}>
                    Analyze our Q3 sales data and project Q4.
                  </div>
                )}
                {chatStep > 1 && (
                  <div style={{ alignSelf: 'flex-start', background: 'white', border: '1px solid rgba(0,0,0,0.05)', padding: '20px', borderRadius: '20px 20px 20px 0', fontSize: 14, maxWidth: '90%', boxShadow: '0 10px 30px rgba(0,0,0,0.05)' }}>
                    <div style={{ display: 'flex', gap: 10, alignItems: 'center', marginBottom: 16 }}>
                      <div style={{ background: 'linear-gradient(135deg, #6366F1, #0EA5E9)', width: 28, height: 28, borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'white', fontSize: 14 }}>✨</div>
                      <strong className="heading-font" style={{ color: '#0F172A', fontSize: 16 }}>DevBotics AI</strong>
                    </div>
                    <p style={{ color: '#475569', marginBottom: 16 }}>Q3 growth was strong. Based on current trajectory and historical seasonality, Q4 projected revenue is <strong style={{ color: '#10B981' }}>+22% higher</strong>.</p>
                    
                    {/* Mini Chart */}
                    <div style={{ display: 'flex', alignItems: 'flex-end', gap: 8, height: 80, padding: '10px', background: '#F8FAFC', borderRadius: 12 }}>
                      <div style={{ flex: 1, background: '#E2E8F0', height: '40%', borderRadius: 4 }} />
                      <div style={{ flex: 1, background: '#CBD5E1', height: '60%', borderRadius: 4 }} />
                      <div style={{ flex: 1, background: '#94A3B8', height: '85%', borderRadius: 4 }} />
                      <div style={{ flex: 1, background: 'linear-gradient(to top, #6366F1, #0EA5E9)', height: '100%', borderRadius: 4, position: 'relative' }}>
                         <div style={{ position: 'absolute', top: -20, left: '50%', transform: 'translateX(-50%)', background: '#0F172A', color: 'white', fontSize: 10, padding: '2px 6px', borderRadius: 4 }}>Q4</div>
                      </div>
                    </div>
                  </div>
                )}
                {chatStep === 1 && (
                  <div style={{ alignSelf: 'flex-start', background: 'white', border: '1px solid rgba(0,0,0,0.05)', color: '#64748B', padding: '14px 20px', borderRadius: '20px 20px 20px 0', fontSize: 14, display: 'flex', gap: 8, alignItems: 'center' }}>
                    <span style={{ width: 6, height: 6, background: '#6366F1', borderRadius: '50%', animation: 'blob-bounce 1s infinite' }} /> Processing models...
                  </div>
                )}
              </div>
              
              <div style={{ marginTop: 24, background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.05)', borderRadius: 100, padding: '14px 20px', display: 'flex', justifyContent: 'space-between', color: '#94A3B8', fontSize: 14 }}>
                <span>Ask the intelligent agent...</span>
                <span style={{ background: '#0F172A', color: 'white', width: 28, height: 28, borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>↑</span>
              </div>
            </div>

            {/* Floating Badges */}
            <div className="glass-card" style={{ position: 'absolute', bottom: 40, left: -20, padding: '12px 20px', borderRadius: 100, display: 'flex', alignItems: 'center', gap: 10, animation: 'float-delayed 5s ease-in-out infinite' }}>
              <span style={{ fontSize: 20 }}>⚡</span>
              <div>
                <div style={{ fontSize: 10, fontWeight: 700, color: '#64748B', letterSpacing: '0.1em' }}>PROCESSING</div>
                <div style={{ fontSize: 14, fontWeight: 800, color: '#0F172A' }}>&lt; 50ms Latency</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 2. CREATIVE CAPABILITIES (BENTO GRID) */}
      <section id="capabilities" style={{ padding: '120px 5%', background: '#FFFFFF', position: 'relative' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: 80, flexWrap: 'wrap', gap: 24 }}>
            <div style={{ maxWidth: 600 }}>
              <div className="pill" style={{ marginBottom: 16, color: '#0EA5E9' }}>CAPABILITIES</div>
              <h2 className="heading-font" style={{ fontSize: 'clamp(2.5rem, 4vw, 4rem)', fontWeight: 800, color: '#0F172A', lineHeight: 1.1, letterSpacing: '-0.02em' }}>
                Bespoke AI,<br/>Beautifully Crafted.
              </h2>
            </div>
            <p style={{ color: '#475569', fontSize: '1.125rem', maxWidth: 400, lineHeight: 1.6 }}>
              We merge cutting-edge machine learning with exceptional design to create products that don't just work—they inspire.
            </p>
          </div>

          <div className="bento-grid">
            {CAPABILITIES.map((cap, i) => (
              <div key={i} className={`glass-card hover-lift ${cap.span === 2 ? 'col-span-2' : ''}`} style={{ padding: 40, borderRadius: 32, background: i % 2 === 0 ? '#F8FAFC' : '#FFFFFF', display: 'flex', flexDirection: 'column', position: 'relative', overflow: 'hidden' }}>
                
                {/* Decorative blob inside card */}
                <div style={{ position: 'absolute', top: -50, right: -50, width: 150, height: 150, background: cap.color, opacity: 0.05, borderRadius: '50%', filter: 'blur(30px)' }} />

                <div style={{ background: 'white', width: 64, height: 64, borderRadius: 20, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 32, marginBottom: 32, boxShadow: '0 10px 20px rgba(0,0,0,0.03)', border: '1px solid rgba(0,0,0,0.02)' }}>
                  {cap.icon}
                </div>
                
                <h3 className="heading-font" style={{ fontSize: '1.5rem', fontWeight: 700, color: '#0F172A', marginBottom: 16 }}>{cap.title}</h3>
                <p style={{ color: '#64748B', fontSize: '1.05rem', lineHeight: 1.6, flex: 1 }}>{cap.desc}</p>
                
                <Link to={`/ai-services/${cap.slug}`} style={{ marginTop: 32, display: 'flex', alignItems: 'center', gap: 8, color: cap.color, fontWeight: 600, fontSize: '0.9rem', cursor: 'pointer', textDecoration: 'none' }}>
                  Explore Solution <span style={{ transform: 'translateX(0)', transition: 'transform 0.2s' }} onMouseEnter={e => e.currentTarget.style.transform = 'translateX(5px)'} onMouseLeave={e => e.currentTarget.style.transform = 'translateX(0)'}>→</span>
                </Link>
              </div>
            ))}
          </div>

        </div>
      </section>

      {/* 3. INDUSTRY SOLUTIONS */}
      <section style={{ padding: '120px 5%', background: '#F8FAFC', position: 'relative', overflow: 'hidden' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <div className="pill" style={{ marginBottom: 16, color: '#10B981' }}>INDUSTRIES</div>
            <h2 className="heading-font" style={{ fontSize: 'clamp(2.5rem, 4vw, 3.5rem)', fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em' }}>Impact Across Sectors.</h2>
          </div>

          <div style={{ display: 'flex', justifyContent: 'center', gap: 12, marginBottom: 40, flexWrap: 'wrap' }}>
            {INDUSTRIES.map((ind, i) => (
              <button key={i} onClick={() => setActiveTab(i)} style={{ background: activeTab === i ? '#0F172A' : '#FFFFFF', color: activeTab === i ? 'white' : '#475569', padding: '14px 28px', borderRadius: 100, border: activeTab === i ? 'none' : '1px solid rgba(0,0,0,0.05)', fontWeight: 600, cursor: 'pointer', transition: 'all 0.3s', boxShadow: activeTab === i ? '0 10px 20px rgba(15,23,42,0.1)' : '0 4px 6px rgba(0,0,0,0.02)' }}>
                {ind.name}
              </button>
            ))}
          </div>

          <div className="glass-card split-section" style={{ borderRadius: 40, padding: '60px 5%', display: 'flex', gap: 60, background: 'linear-gradient(to bottom right, #FFFFFF, #F8FAFC)', border: '1px solid white', boxShadow: '0 30px 60px rgba(0,0,0,0.03)' }}>
            <div style={{ flex: 1 }}>
              <h3 className="heading-font" style={{ fontSize: '2rem', fontWeight: 800, color: '#0F172A', marginBottom: 32 }}>Key Applications</h3>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
                {INDUSTRIES[activeTab].apps.map((app, i) => (
                  <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 16, padding: '16px 24px', background: 'white', borderRadius: 16, boxShadow: '0 4px 10px rgba(0,0,0,0.02)', border: '1px solid rgba(0,0,0,0.02)' }}>
                    <div style={{ width: 10, height: 10, borderRadius: '50%', background: INDUSTRIES[activeTab].color, boxShadow: `0 0 10px ${INDUSTRIES[activeTab].color}` }} />
                    <span style={{ fontSize: '1.1rem', color: '#1E293B', fontWeight: 500 }}>{app}</span>
                  </div>
                ))}
              </div>
            </div>
            
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
              <div style={{ background: INDUSTRIES[activeTab].color, padding: '40px', borderRadius: 32, color: 'white', position: 'relative', overflow: 'hidden', boxShadow: `0 20px 40px ${INDUSTRIES[activeTab].color}40` }}>
                <div style={{ position: 'absolute', top: -20, right: -20, fontSize: 120, opacity: 0.1 }}>&ldquo;</div>
                <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '0.1em', marginBottom: 20, opacity: 0.9 }}>MEASURABLE IMPACT</div>
                <h4 className="heading-font" style={{ fontSize: '2.5rem', fontWeight: 800, lineHeight: 1.2, marginBottom: 32 }}>{INDUSTRIES[activeTab].metric}</h4>
                <button style={{ background: 'white', color: INDUSTRIES[activeTab].color, border: 'none', padding: '12px 24px', borderRadius: 100, fontWeight: 700, cursor: 'pointer', transition: 'transform 0.2s' }} onMouseEnter={e => e.currentTarget.style.transform = 'scale(1.05)'} onMouseLeave={e => e.currentTarget.style.transform = 'scale(1)'}>Read Case Study</button>
              </div>
            </div>
          </div>

        </div>
      </section>

      {/* 4. TECH STACK (Light Mode Code Editor) */}
      <section style={{ padding: '120px 5%', background: '#FFFFFF' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'flex', gap: 80, alignItems: 'center' }} className="split-section">
          
          <div style={{ flex: 1 }}>
            <div className="pill" style={{ marginBottom: 16, color: '#F43F5E' }}>TECHNOLOGY</div>
            <h2 className="heading-font" style={{ fontSize: 'clamp(2.5rem, 4vw, 3.5rem)', fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em', marginBottom: 24 }}>Built on the Edge.</h2>
            <p style={{ color: '#475569', fontSize: '1.125rem', lineHeight: 1.6, marginBottom: 40 }}>
              We architect robust systems using the latest LLMs, vector databases, and scalable cloud infrastructure. No black boxes, just clean, performant engineering.
            </p>
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
              {['GPT-4o', 'Claude 3.5', 'PyTorch', 'LangChain', 'Pinecone', 'Next.js', 'Vercel', 'AWS'].map(t => (
                <span key={t} style={{ border: '1px solid rgba(0,0,0,0.1)', background: '#F8FAFC', padding: '10px 20px', borderRadius: 100, fontSize: 14, color: '#334155', fontWeight: 600 }}>{t}</span>
              ))}
            </div>
          </div>

          <div style={{ flex: 1, width: '100%' }}>
            <div className="code-window" style={{ borderRadius: 24, overflow: 'hidden' }}>
              <div style={{ background: '#F8FAFC', padding: '16px 24px', borderBottom: '1px solid rgba(0,0,0,0.05)', display: 'flex', gap: 8, alignItems: 'center' }}>
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#EF4444' }} />
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#F59E0B' }} />
                <div style={{ width: 12, height: 12, borderRadius: '50%', background: '#10B981' }} />
                <span style={{ marginLeft: 16, fontSize: 12, color: '#94A3B8', fontFamily: 'monospace' }}>agent.ts</span>
              </div>
              <div style={{ padding: 32, fontFamily: '"Fira Code", monospace', fontSize: 14, lineHeight: 1.7, overflowX: 'auto', background: '#FFFFFF', color: '#334155' }}>
                <span style={{ color: '#D946EF' }}>import</span> {'{'} ChatOpenAI {'}'} <span style={{ color: '#D946EF' }}>from</span> <span style={{ color: '#10B981' }}>"langchain/chat_models"</span>;<br/>
                <span style={{ color: '#D946EF' }}>import</span> {'{'} PineconeStore {'}'} <span style={{ color: '#D946EF' }}>from</span> <span style={{ color: '#10B981' }}>"langchain/vectorstores"</span>;<br/><br/>
                <span style={{ color: '#94A3B8', fontStyle: 'italic' }}>// Initialize intelligent agent</span><br/>
                <span style={{ color: '#0EA5E9' }}>const</span> model = <span style={{ color: '#D946EF' }}>new</span> ChatOpenAI({'{'}<br/>
                {'  '}modelName: <span style={{ color: '#10B981' }}>"gpt-4o"</span>,<br/>
                {'  '}temperature: <span style={{ color: '#F59E0B' }}>0.1</span>,<br/>
                {'}'});<br/><br/>
                <span style={{ color: '#94A3B8', fontStyle: 'italic' }}>// Execute retrieval & generation</span><br/>
                <span style={{ color: '#0EA5E9' }}>const</span> response = <span style={{ color: '#D946EF' }}>await</span> agent.execute({'{'}<br/>
                {'  '}task: <span style={{ color: '#10B981' }}>"Analyze user sentiment and generate report"</span>,<br/>
                {'  '}context: vectorStore.asRetriever()<br/>
                {'}'});
              </div>
            </div>
          </div>
          
        </div>
      </section>

      {/* 5. PORTFOLIO SHOWCASE */}
      <section style={{ padding: '120px 5%', background: '#F8FAFC' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          
          <div style={{ textAlign: 'center', marginBottom: 80 }}>
            <div className="pill" style={{ marginBottom: 16, color: '#8B5CF6' }}>PORTFOLIO</div>
            <h2 className="heading-font" style={{ fontSize: 'clamp(2.5rem, 4vw, 3.5rem)', fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em' }}>Selected Works.</h2>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(380px, 1fr))', gap: 40 }}>
            {PORTFOLIO.map((p, i) => (
              <div key={i} className="hover-lift" style={{ background: '#FFFFFF', borderRadius: 32, overflow: 'hidden', border: '1px solid rgba(0,0,0,0.05)', cursor: 'pointer' }}>
                <div style={{ height: 260, position: 'relative', overflow: 'hidden' }}>
                  <img src={p.img} alt={p.t} style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.5s' }} onMouseEnter={e => e.currentTarget.style.transform = 'scale(1.05)'} onMouseLeave={e => e.currentTarget.style.transform = 'scale(1)'} />
                  <div style={{ position: 'absolute', top: 20, left: 20, background: 'rgba(255,255,255,0.9)', backdropFilter: 'blur(10px)', padding: '8px 16px', borderRadius: 100, fontSize: 12, fontWeight: 700, color: '#0F172A' }}>{p.badge}</div>
                </div>
                <div style={{ padding: 40 }}>
                  <div style={{ fontSize: 12, color: '#6366F1', fontWeight: 800, marginBottom: 12, textTransform: 'uppercase', letterSpacing: '0.1em' }}>{p.ind}</div>
                  <h3 className="heading-font" style={{ fontSize: '1.75rem', fontWeight: 800, marginBottom: 20, color: '#0F172A' }}>{p.t}</h3>
                  <div style={{ background: '#F8FAFC', padding: '16px 20px', borderRadius: 16, marginBottom: 24, border: '1px solid rgba(0,0,0,0.03)' }}>
                    <span style={{ color: '#64748B', fontSize: 14 }}>Result:</span> <span style={{ color: '#10B981', fontWeight: 800, fontSize: 16, marginLeft: 8 }}>{p.metric}</span>
                  </div>
                  <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                    {p.tech.map(t => <span key={t} style={{ fontSize: 13, background: 'white', border: '1px solid rgba(0,0,0,0.1)', padding: '6px 12px', borderRadius: 100, color: '#475569', fontWeight: 500 }}>{t}</span>)}
                  </div>
                </div>
              </div>
            ))}
          </div>
          
        </div>
      </section>

      {/* 6. CREATIVE CTA & CONTACT */}
      <section id="consultation" style={{ padding: '120px 5%', background: '#FFFFFF', position: 'relative', overflow: 'hidden' }}>
        
        <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', width: '100vw', height: '100%', background: 'radial-gradient(ellipse at center, rgba(99,102,241,0.05) 0%, transparent 70%)', zIndex: 0 }} />

        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'flex', gap: 80, position: 'relative', zIndex: 1 }} className="split-section">
          
          <div style={{ flex: 1 }}>
            <h2 className="heading-font" style={{ fontSize: 'clamp(3rem, 5vw, 5rem)', fontWeight: 800, color: '#0F172A', lineHeight: 1, letterSpacing: '-0.03em', marginBottom: 24 }}>
              Let's Build<br/>
              <span className="gradient-text">The Future.</span>
            </h2>
            <p style={{ fontSize: '1.25rem', color: '#475569', marginBottom: 40, maxWidth: 450, lineHeight: 1.6 }}>
              Share your vision with our AI engineering team. We'll craft a bespoke strategy to bring it to life.
            </p>
            
            <div style={{ marginTop: 60 }}>
              <h4 className="heading-font" style={{ fontWeight: 800, fontSize: '1.5rem', marginBottom: 24, color: '#0F172A' }}>Common Inquiries</h4>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                {FAQS.slice(0,3).map((f, i) => (
                  <div key={i} style={{ background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.05)', borderRadius: 20, overflow: 'hidden' }}>
                    <button onClick={() => setOpenFaq(openFaq === i ? null : i)} style={{ width: '100%', textAlign: 'left', padding: '24px', background: 'transparent', border: 'none', display: 'flex', justifyContent: 'space-between', alignItems: 'center', cursor: 'pointer', fontWeight: 700, fontSize: '1.1rem', color: '#0F172A' }}>
                      {f.q}
                      <span style={{ color: '#6366F1', transform: openFaq === i ? 'rotate(45deg)' : 'rotate(0deg)', transition: 'transform 0.3s ease', fontSize: 24 }}>+</span>
                    </button>
                    {openFaq === i && (
                      <div style={{ padding: '0 24px 24px', color: '#475569', lineHeight: 1.6, fontSize: '1rem' }}>
                        {f.a}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>
          </div>
          
          <div style={{ flex: '0 0 500px', width: '100%' }}>
            <form onSubmit={handleForm} style={{ background: '#FFFFFF', padding: 48, borderRadius: 32, border: '1px solid rgba(0,0,0,0.08)', boxShadow: '0 40px 80px rgba(0,0,0,0.05)' }}>
              <h3 className="heading-font" style={{ fontWeight: 800, fontSize: '2rem', marginBottom: 40, color: '#0F172A' }}>Start a Conversation</h3>
              
              <div style={{ marginBottom: 24 }}>
                <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 12 }}>YOUR NAME</label>
                <input type="text" required style={{ width: '100%', background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.1)', padding: '16px 20px', borderRadius: 16, color: '#0F172A', outline: 'none', transition: 'all 0.3s', fontSize: 16 }} onFocus={e=> { e.currentTarget.style.borderColor='#6366F1'; e.currentTarget.style.background='#FFFFFF'; e.currentTarget.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'; }} onBlur={e=>{ e.currentTarget.style.borderColor='rgba(0,0,0,0.1)'; e.currentTarget.style.background='#F8FAFC'; e.currentTarget.style.boxShadow='none'; }} />
              </div>
              
              <div style={{ marginBottom: 24 }}>
                <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 12 }}>WORK EMAIL</label>
                <input type="email" required style={{ width: '100%', background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.1)', padding: '16px 20px', borderRadius: 16, color: '#0F172A', outline: 'none', transition: 'all 0.3s', fontSize: 16 }} onFocus={e=> { e.currentTarget.style.borderColor='#6366F1'; e.currentTarget.style.background='#FFFFFF'; e.currentTarget.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'; }} onBlur={e=>{ e.currentTarget.style.borderColor='rgba(0,0,0,0.1)'; e.currentTarget.style.background='#F8FAFC'; e.currentTarget.style.boxShadow='none'; }} />
              </div>
              
              <div style={{ marginBottom: 40 }}>
                <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 12 }}>PROJECT OVERVIEW</label>
                <textarea required rows={4} style={{ width: '100%', background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.1)', padding: '16px 20px', borderRadius: 16, color: '#0F172A', outline: 'none', resize: 'vertical', transition: 'all 0.3s', fontSize: 16 }} onFocus={e=> { e.currentTarget.style.borderColor='#6366F1'; e.currentTarget.style.background='#FFFFFF'; e.currentTarget.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'; }} onBlur={e=>{ e.currentTarget.style.borderColor='rgba(0,0,0,0.1)'; e.currentTarget.style.background='#F8FAFC'; e.currentTarget.style.boxShadow='none'; }}></textarea>
              </div>
              
              <button type="submit" disabled={isSubmitting} style={{ width: '100%', background: isSubmitting ? '#94A3B8' : '#0F172A', color: '#FFFFFF', padding: '20px', borderRadius: 100, fontWeight: 800, fontSize: 16, border: 'none', cursor: isSubmitting ? 'not-allowed' : 'pointer', transition: 'all 0.3s', boxShadow: isSubmitting ? 'none' : '0 10px 20px rgba(15,23,42,0.2)' }} onMouseEnter={e => { if(!isSubmitting) { e.currentTarget.style.transform = 'translateY(-2px)'; e.currentTarget.style.boxShadow = '0 15px 30px rgba(15,23,42,0.3)'; } }} onMouseLeave={e => { if(!isSubmitting) { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 20px rgba(15,23,42,0.2)'; } }}>
                {isSubmitting ? 'Processing Request...' : 'Submit Inquiry'}
              </button>
            </form>
          </div>
          
        </div>
      </section>

    </main>
  );
}
