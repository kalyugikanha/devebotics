import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { aiPageData } from './aiPageData';

interface Props { slug: string; }

export default function AiServiceTypePage({ slug }: Props) {
  const data = aiPageData[slug];
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (data) document.title = data.metaTitle;
    window.scrollTo(0, 0);
  }, [slug, data]);

  if (!data) return (
    <main style={{ background: '#F8FAFC', minHeight: '100vh', paddingTop: 80, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ fontFamily: '"Plus Jakarta Sans", sans-serif', fontSize: 24, color: '#0F172A' }}>Service not found</div>
    </main>
  );

  const handleForm = (e: any) => {
    e.preventDefault();
    setIsSubmitting(true);
    setTimeout(() => setIsSubmitting(false), 2000);
  };

  const Demo = data.DemoComponent;

  return (
    <main style={{ background: '#F8FAFC', minHeight: '100vh', color: '#0F172A', fontFamily: '"Plus Jakarta Sans", sans-serif', overflowX: 'hidden' }}>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        .heading-font { font-family: 'Outfit', sans-serif; }
        
        .mesh-bg-sub {
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background-color: #F8FAFC;
          background-image: 
            radial-gradient(at 10% 20%, \${data.color}20 0px, transparent 60%),
            radial-gradient(at 90% 10%, \${data.color}30 0px, transparent 60%),
            radial-gradient(at 50% 60%, \${data.color}15 0px, transparent 60%);
          filter: blur(60px);
          z-index: 0;
        }

        .outline-text-sub {
          color: transparent;
          -webkit-text-stroke: 1.5px rgba(15, 23, 42, 0.15);
          font-weight: 900;
        }

        .btn-action {
          background: #0F172A;
          color: white;
          border-radius: 100px;
          transition: all 0.3s;
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
          border: none;
          cursor: pointer;
        }
        .btn-action:hover {
          background: \${data.color};
          transform: translateY(-2px);
          box-shadow: 0 15px 30px \${data.color}40;
        }

        .glass-panel {
          background: rgba(255, 255, 255, 0.7);
          backdrop-filter: blur(20px);
          -webkit-backdrop-filter: blur(20px);
          border: 1px solid rgba(255, 255, 255, 0.9);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
          border-radius: 32px;
        }
        
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
      `}</style>

      {/* HERO & DEMO */}
      <section style={{ minHeight: '100vh', position: 'relative', display: 'flex', alignItems: 'center', paddingTop: 100, paddingBottom: 80, paddingLeft: '5%', paddingRight: '5%' }}>
        <div className="mesh-bg-sub"></div>
        <div style={{ position: 'absolute', inset: 0, backgroundImage: 'linear-gradient(rgba(15,23,42,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.03) 1px, transparent 1px)', backgroundSize: '40px 40px', pointerEvents: 'none', zIndex: 1, maskImage: 'linear-gradient(to bottom, black 40%, transparent 100%)', WebkitMaskImage: 'linear-gradient(to bottom, black 40%, transparent 100%)' }} />

        <div style={{ maxWidth: 1400, margin: '0 auto', position: 'relative', zIndex: 2, display: 'flex', gap: 60, alignItems: 'center', width: '100%', flexWrap: 'wrap' }}>
          
          <div style={{ flex: '1 1 45%', minWidth: 300 }}>
            <nav style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 24, fontSize: 13, fontWeight: 600 }}>
              <Link to="/" style={{ color: '#64748B', textDecoration: 'none' }}>Home</Link>
              <span style={{ color: '#CBD5E1' }}>/</span>
              <Link to="/ai-services" style={{ color: '#64748B', textDecoration: 'none' }}>AI Services</Link>
              <span style={{ color: '#CBD5E1' }}>/</span>
              <span style={{ color: data.color }}>{data.heroH1[0]}</span>
            </nav>
            
            <h1 className="heading-font" style={{ fontSize: 'clamp(3.5rem, 6vw, 5.5rem)', fontWeight: 900, lineHeight: 1.05, letterSpacing: '-0.03em', marginBottom: 24, color: '#0F172A' }}>
              <span className="outline-text-sub">{data.heroH1[0]}</span><br/>
              <span style={{ color: data.color }}>{data.heroH1[1]}</span>
            </h1>
            
            <p style={{ fontSize: '1.25rem', color: '#475569', lineHeight: 1.6, maxWidth: 540, marginBottom: 40, fontWeight: 500 }}>
              {data.heroSub}
            </p>
            
            <a href="#contact" className="btn-action" style={{ padding: '18px 40px', fontSize: 16 }}>
              Discuss Implementation
            </a>
          </div>

          <div style={{ flex: '1 1 50%', minWidth: 320, animation: 'float 6s ease-in-out infinite' }}>
             <Demo />
          </div>
        </div>
      </section>

      {/* CORE CAPABILITIES */}
      <section style={{ padding: '120px 5%', background: '#FFFFFF' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: 60 }}>
            <div style={{ background: '#F8FAFC', border: '1px solid rgba(0,0,0,0.05)', borderRadius: 100, padding: '8px 20px', fontSize: 13, fontWeight: 700, color: data.color, display: 'inline-block', marginBottom: 16 }}>
              CORE FEATURES
            </div>
            <h2 className="heading-font" style={{ fontSize: 'clamp(2.5rem, 4vw, 3.5rem)', fontWeight: 800, color: '#0F172A' }}>
              Why Choose Our {data.heroH1[0]}?
            </h2>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))', gap: 24 }}>
            {data.features.map((f: any, i: number) => (
              <div key={i} className="glass-panel" style={{ padding: 40, background: '#F8FAFC' }}>
                <div style={{ width: 48, height: 48, borderRadius: 16, background: 'white', display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 24, boxShadow: '0 10px 20px rgba(0,0,0,0.03)', color: data.color, fontWeight: 'bold', fontSize: 20 }}>0{i + 1}</div>
                <h3 className="heading-font" style={{ fontSize: '1.5rem', fontWeight: 700, marginBottom: 16, color: '#0F172A' }}>{f.t}</h3>
                <p style={{ color: '#64748B', lineHeight: 1.6, fontSize: '1.05rem' }}>{f.d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* TECH STACK & RELATED */}
      <section style={{ padding: '120px 5%', background: '#F8FAFC' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'flex', gap: 60, flexWrap: 'wrap' }}>
          
          <div style={{ flex: '1 1 40%', minWidth: 300 }}>
            <h2 className="heading-font" style={{ fontSize: '2.5rem', fontWeight: 800, color: '#0F172A', marginBottom: 24 }}>The Engine Room.</h2>
            <p style={{ color: '#475569', fontSize: '1.1rem', marginBottom: 32, lineHeight: 1.6 }}>
              We utilize enterprise-grade frameworks to ensure your AI infrastructure is fast, scalable, and secure.
            </p>
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
              {data.tech.map((t: string) => (
                <span key={t} style={{ background: '#FFFFFF', border: '1px solid rgba(0,0,0,0.05)', padding: '10px 20px', borderRadius: 100, fontSize: 14, fontWeight: 600, color: '#0F172A', boxShadow: '0 4px 10px rgba(0,0,0,0.02)' }}>{t}</span>
              ))}
            </div>
          </div>

          <div style={{ flex: '1 1 50%', minWidth: 320, background: '#FFFFFF', borderRadius: 32, padding: 40, border: '1px solid rgba(0,0,0,0.05)', boxShadow: '0 20px 40px rgba(0,0,0,0.02)' }}>
            <h3 className="heading-font" style={{ fontSize: '1.75rem', fontWeight: 800, color: '#0F172A', marginBottom: 24 }}>Explore Related Services</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              {data.related.map((slug: string) => {
                const rel = aiPageData[slug];
                if(!rel) return null;
                return (
                  <Link key={slug} to={`/ai-services/${slug}`} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: 24, background: '#F8FAFC', borderRadius: 20, textDecoration: 'none', border: '1px solid rgba(0,0,0,0.03)', transition: 'all 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#F1F5F9'} onMouseLeave={e => e.currentTarget.style.background = '#F8FAFC'}>
                    <div>
                      <div style={{ fontSize: 12, fontWeight: 700, color: rel.color, marginBottom: 4 }}>AI SERVICE</div>
                      <div style={{ fontSize: 18, fontWeight: 700, color: '#0F172A', fontFamily: 'Outfit, sans-serif' }}>{rel.name || rel.heroH1.join(' ')}</div>
                    </div>
                    <div style={{ width: 40, height: 40, borderRadius: '50%', background: 'white', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 4px 10px rgba(0,0,0,0.05)', color: rel.color }}>→</div>
                  </Link>
                )
              })}
            </div>
          </div>
          
        </div>
      </section>

      {/* CONTACT */}
      <section id="contact" style={{ padding: '120px 5%', background: '#FFFFFF', position: 'relative' }}>
        <div style={{ maxWidth: 800, margin: '0 auto', background: '#F8FAFC', padding: 60, borderRadius: 40, border: '1px solid rgba(0,0,0,0.05)', boxShadow: '0 40px 80px rgba(0,0,0,0.03)' }}>
          <div style={{ textAlign: 'center', marginBottom: 40 }}>
            <h2 className="heading-font" style={{ fontSize: '2.5rem', fontWeight: 800, color: '#0F172A', marginBottom: 16 }}>Ready to Build?</h2>
            <p style={{ color: '#64748B', fontSize: '1.1rem' }}>Tell us about your data and objectives. We'll map out the technical feasibility.</p>
          </div>

          <form onSubmit={handleForm} style={{ display: 'flex', flexDirection: 'column', gap: 24 }}>
            <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap' }}>
              <div style={{ flex: '1 1 200px' }}>
                <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 8 }}>NAME</label>
                <input type="text" required style={{ width: '100%', background: '#FFFFFF', border: '1px solid rgba(0,0,0,0.1)', padding: '16px', borderRadius: 16, outline: 'none' }} />
              </div>
              <div style={{ flex: '1 1 200px' }}>
                <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 8 }}>EMAIL</label>
                <input type="email" required style={{ width: '100%', background: '#FFFFFF', border: '1px solid rgba(0,0,0,0.1)', padding: '16px', borderRadius: 16, outline: 'none' }} />
              </div>
            </div>
            <div>
              <label style={{ display: 'block', fontSize: 13, fontWeight: 700, color: '#64748B', marginBottom: 8 }}>HOW CAN WE HELP?</label>
              <textarea required rows={4} style={{ width: '100%', background: '#FFFFFF', border: '1px solid rgba(0,0,0,0.1)', padding: '16px', borderRadius: 16, outline: 'none', resize: 'vertical' }}></textarea>
            </div>
            <button type="submit" className="btn-action" style={{ padding: '20px', fontSize: 16, marginTop: 16, background: isSubmitting ? '#94A3B8' : data.color, width: '100%' }}>
              {isSubmitting ? 'Sending Request...' : 'Submit Inquiry'}
            </button>
          </form>
        </div>
      </section>

    </main>
  );
}
