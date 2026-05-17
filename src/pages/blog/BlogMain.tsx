import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const ARTICLES = [
  { slug: 'react-native-vs-flutter-2025', title: 'React Native vs Flutter in 2025: The Definitive Guide for Startups', excerpt: 'We\'ve built 80+ apps with both. Here\'s our honest breakdown.', time: '12 min', date: 'April 2, 2025', cat: 'Tech & Development', img: 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600&q=80' },
  { slug: 'building-rag-pipeline-lessons', title: 'Building a Production-Ready RAG Pipeline: Lessons Learned', excerpt: 'The mistakes we made and how to avoid them when deploying LLMs.', time: '9 min', date: 'March 28, 2025', cat: 'AI & Automation', img: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&q=80' },
  { slug: 'seo-0-to-50k-organic', title: 'How We Took a Client From 0 to 50K Monthly Organic Visitors', excerpt: 'The exact SEO playbook — content, technical fixes, and link strategy.', time: '7 min', date: 'March 20, 2025', cat: 'Digital Marketing', img: 'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=600&q=80' },
];

export default function BlogMain() {
  const [activeTab, setActiveTab] = useState('All');
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  useEffect(() => {
    document.title = 'Blog | DevBotics — Tech, AI & Growth Insights';
    const meta = document.querySelector('meta[name="description"]');
    if (meta) meta.setAttribute('content', 'Expert insights on mobile development, AI integration, and startup growth from the DevBotics team.');
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72 }}>
      {/* SECTION 1 — BLOG HEADER */}
      <section style={{ maxWidth: 1400, margin: '0 auto', padding: '60px 40px 0' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 24, marginBottom: 40 }}>
          <div>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// DEVBOTICS JOURNAL</div>
            <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px,6vw,80px)', color: '#F0F0F0', lineHeight: 0.95, margin: 0 }}>
              Insights.<br />Code. <span style={{ color: '#E8FF47' }}>Growth.</span>
            </h1>
          </div>
          <div style={{ position: 'relative', width: '100%', maxWidth: 300 }}>
            <input type="text" placeholder="Search articles..." style={{ width: '100%', background: 'transparent', border: 'none', borderBottom: '1px solid #1C1E21', padding: '12px 0 12px 32px', fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#F0F0F0', outline: 'none', transition: 'border-color 0.2s' }} onFocus={e => e.target.style.borderBottomColor = '#E8FF47'} onBlur={e => e.target.style.borderBottomColor = '#1C1E21'} />
            <svg style={{ position: 'absolute', left: 0, top: 12, color: '#3A3D42' }} width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          </div>
        </div>
        <div style={{ height: 1, background: '#1C1E21', width: '100%', marginBottom: 32 }} />
        <div style={{ display: 'flex', gap: 12, overflowX: 'auto', paddingBottom: 16 }} className="hide-scrollbar">
          {['All', 'Tech & Development', 'AI & Automation', 'Digital Marketing'].map(tab => (
            <button key={tab} onClick={() => setActiveTab(tab)} style={{
              background: activeTab === tab ? '#E8FF47' : 'transparent',
              color: activeTab === tab ? '#08090A' : '#6B6F76',
              border: activeTab === tab ? '1px solid #E8FF47' : '1px solid #1C1E21',
              borderRadius: 2, padding: '8px 16px', fontFamily: 'Lexend,sans-serif', fontSize: 13,
              cursor: 'pointer', flexShrink: 0, transition: 'all 0.2s'
            }} onMouseEnter={e => { if (activeTab !== tab) { e.currentTarget.style.borderColor = '#E8FF47'; e.currentTarget.style.color = '#F0F0F0'; } }} onMouseLeave={e => { if (activeTab !== tab) { e.currentTarget.style.borderColor = '#1C1E21'; e.currentTarget.style.color = '#6B6F76'; } }}>
              {tab}
            </button>
          ))}
        </div>
        <div style={{ height: 1, background: '#1C1E21', width: '100%', marginBottom: 60 }} />
      </section>

      {/* SECTION 2 — FEATURED ARTICLE */}
      <section style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', marginBottom: 80 }}>
        <Link to="/blog/how-we-built-custom-ai-chatbot" style={{ display: 'block', position: 'relative', minHeight: 500, borderRadius: 8, overflow: 'hidden', textDecoration: 'none' }} className="featured-art">
          <img src="https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=1400&q=80" alt="Featured" style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} />
          <div className="overlay" style={{ position: 'absolute', inset: 0, background: 'rgba(8,9,10,0.82)', transition: 'background 0.3s' }} />
          <div style={{ position: 'relative', zIndex: 2, padding: '60px 48px', display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', minHeight: 500, maxWidth: 800 }}>
            <div style={{ display: 'inline-block', background: '#E8FF47', color: 'black', fontFamily: 'Lexend,sans-serif', fontWeight: 600, fontSize: 11, padding: '4px 12px', borderRadius: 2, marginBottom: 24, alignSelf: 'flex-start' }}>AI & AUTOMATION</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(32px,5vw,52px)', color: '#F0F0F0', lineHeight: 1.0, marginBottom: 20 }}>
              How We Built a Custom AI<br />Chatbot That Handles 80%<br />of Customer Queries
            </h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#A0A0A0', lineHeight: 1.6, marginBottom: 24, maxWidth: 600 }}>
              A deep dive into our architecture decisions, model selection, and deployment strategy for a fintech client's support automation.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 32 }}>
              <div style={{ width: 32, height: 32, borderRadius: '50%', background: '#1C1E21', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#F0F0F0', fontFamily: 'Lexend', fontSize: 12 }}>DB</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 13, color: '#6B6F76' }}>
                DevBotics Team · March 15, 2025 · 8 min read
              </div>
            </div>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#E8FF47', fontWeight: 500 }}>Read Article →</div>
          </div>
        </Link>
      </section>

      {/* SECTION 3 — ARTICLE GRID */}
      <section style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', marginBottom: 100 }}>
        {/* ROW 1: 3 Columns */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 40 }} className="row-3col">
          {ARTICLES.map((art, i) => (
            <Link key={art.slug} to={`/blog/${art.slug}`} style={{ textDecoration: 'none', display: 'flex', flexDirection: 'column', borderRight: i < 2 ? '1px solid #1C1E21' : 'none', paddingRight: i < 2 ? 40 : 0 }} className="article-hover">
              <img src={art.img} alt={art.title} style={{ width: '100%', aspectRatio: '16/9', objectFit: 'cover', borderRadius: 4, marginBottom: 24, transition: 'transform 0.3s' }} className="art-img" />
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 12 }}>{art.cat}</div>
              <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 700, fontSize: 20, color: '#F0F0F0', lineHeight: 1.3, marginBottom: 12, transition: 'color 0.2s' }} className="art-title">{art.title}</h3>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76', lineHeight: 1.6, marginBottom: 16, flex: 1 }}>{art.excerpt}</p>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 12, color: '#3A3D42' }}>{art.time} · {art.date}</div>
            </Link>
          ))}
        </div>

        {/* Divider */}
        <div style={{ position: 'relative', height: 1, background: '#1C1E21', margin: '80px 0', display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
          <div style={{ background: '#08090A', padding: '0 8px', color: '#E8FF47', fontSize: 12 }}>✦</div>
        </div>

        {/* ROW 2: Split */}
        <div style={{ display: 'grid', gridTemplateColumns: '60% 40%', gap: 40 }} className="row-split">
          <Link to="/blog/scalable-nodejs-backend" style={{ textDecoration: 'none', borderRight: '1px solid #1C1E21', paddingRight: 40 }} className="article-hover">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80" alt="Node" style={{ width: '100%', height: 280, objectFit: 'cover', borderRadius: 4, marginBottom: 24 }} className="art-img" />
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 12 }}>TECH & DEVELOPMENT</div>
            <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 28, color: '#F0F0F0', lineHeight: 1.2, marginBottom: 12 }} className="art-title">The Complete Guide to Building<br />a Scalable Node.js Backend</h3>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#6B6F76', lineHeight: 1.6, marginBottom: 16 }}>Everything from project structure and middleware design to clustering and database pooling for high-traffic applications.</p>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 12, color: '#3A3D42' }}>15 min · March 10, 2025</div>
          </Link>
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <Link to="/blog/aso-checklist-2025" style={{ display: 'flex', gap: 20, textDecoration: 'none', paddingBottom: 32, borderBottom: '1px solid #1C1E21' }} className="article-hover">
              <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=200&q=80" alt="ASO" style={{ width: 100, height: 70, objectFit: 'cover', borderRadius: 4 }} className="art-img" />
              <div>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 6 }}>DIGITAL MARKETING</div>
                <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, color: '#F0F0F0', lineHeight: 1.4, marginBottom: 8 }} className="art-title">App Store Optimization: The 2025 ASO Checklist That Actually Works</h4>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 11, color: '#3A3D42' }}>6 min · March 8, 2025</div>
              </div>
            </Link>
            <Link to="/blog/flutter-performance-debugging" style={{ display: 'flex', gap: 20, textDecoration: 'none', paddingTop: 32 }} className="article-hover">
              <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=200&q=80" alt="Flutter" style={{ width: 100, height: 70, objectFit: 'cover', borderRadius: 4 }} className="art-img" />
              <div>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 6 }}>TECH & DEVELOPMENT</div>
                <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, color: '#F0F0F0', lineHeight: 1.4, marginBottom: 8 }} className="art-title">Why Your Flutter App Is Slow: A Performance Debugging Guide</h4>
                <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 11, color: '#3A3D42' }}>8 min · March 5, 2025</div>
              </div>
            </Link>
          </div>
        </div>

        {/* Divider */}
        <div style={{ position: 'relative', height: 1, background: '#1C1E21', margin: '80px 0', display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
          <div style={{ background: '#08090A', padding: '0 8px', color: '#E8FF47', fontSize: 12 }}>✦</div>
        </div>

        {/* ROW 3: Text Heavy */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 32 }} className="row-4col">
          {[
            { c: 'AI & AUTOMATION', t: '5 AI Tools Every Developer Should Be Using in 2025', e: 'Boost your productivity with these underrated coding assistants and automation scripts.' },
            { c: 'STARTUP INSIGHTS', t: 'The Real Cost of Building an App: A Founder\'s Breakdown', e: 'Stop guessing. Here is exactly where your development budget goes.' },
            { c: 'DIGITAL MARKETING', t: 'How SMO Changed a Restaurant\'s Monthly Revenue by 40%', e: 'A case study on social media optimization and hyper-local targeting.' },
            { c: 'TECH & DEVELOPMENT', t: 'Choosing Between AWS, GCP, and Azure for Your Startup', e: 'A no-nonsense comparison of cloud providers based on actual billing data.' }
          ].map((a, i) => (
            <Link key={i} to={`/blog/article-${i}`} style={{ textDecoration: 'none', borderRight: i < 3 ? '1px solid #1C1E21' : 'none', paddingRight: i < 3 ? 32 : 0, display: 'flex', flexDirection: 'column' }} className="article-text-hover">
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 12 }}>{a.c}</div>
              <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 16, color: '#F0F0F0', lineHeight: 1.4, marginBottom: 12 }} className="art-title">{a.t}</h4>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#6B6F76', lineHeight: 1.6, marginBottom: 16, display: '-webkit-box', WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>{a.e}</p>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 11, color: '#3A3D42', marginTop: 'auto', display: 'flex', justifyContent: 'space-between' }}>
                <span>Feb 2025</span>
                <span className="read-link" style={{ color: '#E8FF47', opacity: 0, transition: 'opacity 0.2s' }}>Read →</span>
              </div>
            </Link>
          ))}
        </div>

        {/* Divider */}
        <div style={{ position: 'relative', height: 1, background: '#1C1E21', margin: '80px 0', display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
          <div style={{ background: '#08090A', padding: '0 8px', color: '#E8FF47', fontSize: 12 }}>✦</div>
        </div>

        {/* ROW 4: Reverse */}
        <div style={{ display: 'grid', gridTemplateColumns: '70% 30%', gap: 60, alignItems: 'center' }} className="row-reverse">
          <div style={{ paddingRight: 40 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', textTransform: 'uppercase', marginBottom: 16 }}>DEEP DIVE</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(28px,4vw,36px)', color: '#F0F0F0', lineHeight: 1.1, marginBottom: 20 }}>
              Why Most App Projects Fail:<br />The 7 Mistakes We've Seen<br />in 200+ Builds
            </h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#6B6F76', lineHeight: 1.6, marginBottom: 32, maxWidth: 600 }}>
              From skipping discovery phases to hiring cheap agencies that write unmaintainable code. Learn what destroys a product before it even launches.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: 24 }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 13, color: '#3A3D42' }}>DevBotics Team · Feb 15, 2025 · 20 min read</div>
              <Link to="/blog/app-failure-mistakes" style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 500, fontSize: 14, color: '#E8FF47', textDecoration: 'none' }}>Read Deep Dive →</Link>
            </div>
          </div>
          <Link to="/blog/app-failure-mistakes" style={{ display: 'block', textDecoration: 'none', borderRadius: 8, overflow: 'hidden' }} className="article-hover">
            <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?w=500&q=80" alt="Code" style={{ width: '100%', aspectRatio: '1/1', objectFit: 'cover', transition: 'transform 0.4s' }} className="art-img" />
          </Link>
        </div>
      </section>

      {/* SECTION 4 — NEWSLETTER SUBSCRIBE */}
      <section style={{ background: '#0E1012', borderTop: '1px solid #1C1E21', borderBottom: '1px solid #1C1E21', padding: '80px 0' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 40px', display: 'flex', gap: 60, alignItems: 'center' }} className="news-split">
          <div style={{ flex: 1 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#E8FF47', letterSpacing: '0.2em', marginBottom: 16 }}>// WEEKLY DIGEST</div>
            <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 32, color: '#F0F0F0', lineHeight: 1.1, marginBottom: 12 }}>Get our best insights<br />every Monday morning.</h2>
            <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76' }}>No spam. Just genuinely useful articles on tech, AI, and growth.</p>
          </div>
          <div style={{ flex: 1 }}>
            {!subscribed ? (
              <form onSubmit={e => { e.preventDefault(); setSubscribed(true); }} style={{ display: 'flex', gap: 16, marginBottom: 16 }}>
                <input type="email" placeholder="Your email address" required value={email} onChange={e => setEmail(e.target.value)} style={{ flex: 1, background: 'transparent', border: 'none', borderBottom: '1px solid #1C1E21', padding: '12px 0', fontFamily: 'Lexend,sans-serif', fontSize: 15, color: '#F0F0F0', outline: 'none', transition: 'border-color 0.2s' }} onFocus={e => e.target.style.borderBottomColor = '#E8FF47'} onBlur={e => e.target.style.borderBottomColor = '#1C1E21'} />
                <button type="submit" style={{ background: '#F0F0F0', color: '#08090A', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 14, padding: '0 24px', cursor: 'pointer', transition: 'background 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#E8FF47'} onMouseLeave={e => e.currentTarget.style.background = '#F0F0F0'}>Subscribe →</button>
              </form>
            ) : (
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#E8FF47', padding: '12px 0', marginBottom: 16, animation: 'fadeIn 0.4s ease' }}>✓ You're subscribed!</div>
            )}
            <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 12, color: '#3A3D42' }}>📧 Join 2,400+ developers & founders</div>
          </div>
        </div>
      </section>

      {/* SECTION 5 — PAGINATION */}
      <section style={{ padding: '60px 0 100px', textAlign: 'center' }}>
        <div style={{ display: 'inline-flex', gap: 12, alignItems: 'center' }}>
          <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#3A3D42', cursor: 'not-allowed' }}>← Previous</span>
          {[1, 2, 3].map(p => (
            <button key={p} style={{ width: 28, height: 28, display: 'flex', alignItems: 'center', justifyContent: 'center', background: p === 1 ? '#E8FF47' : 'transparent', color: p === 1 ? '#08090A' : '#3A3D42', border: 'none', fontFamily: 'Lexend,sans-serif', fontSize: 14, cursor: 'pointer', transition: 'color 0.2s' }} onMouseEnter={e => { if(p!==1) e.currentTarget.style.color = '#F0F0F0' }} onMouseLeave={e => { if(p!==1) e.currentTarget.style.color = '#3A3D42' }}>{p}</button>
          ))}
          <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#3A3D42' }}>...</span>
          <button style={{ width: 28, height: 28, display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'transparent', color: '#3A3D42', border: 'none', fontFamily: 'Lexend,sans-serif', fontSize: 14, cursor: 'pointer', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#F0F0F0'} onMouseLeave={e => e.currentTarget.style.color = '#3A3D42'}>8</button>
          <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#6B6F76', cursor: 'pointer', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#F0F0F0'} onMouseLeave={e => e.currentTarget.style.color = '#6B6F76'}>Next →</span>
        </div>
      </section>

      <style>{`
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .featured-art:hover .overlay { background: rgba(8,9,10,0.7) !important; }
        .article-hover:hover .art-img { transform: scale(1.03); }
        .article-hover:hover .art-title { color: #E8FF47 !important; }
        .article-text-hover:hover .art-title { color: #E8FF47 !important; }
        .article-text-hover:hover .read-link { opacity: 1 !important; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @media(max-width:1024px) {
          .row-split { grid-template-columns: 1fr !important; }
          .row-split > a { border-right: none !important; border-bottom: 1px solid #1C1E21; padding-right: 0 !important; padding-bottom: 40px; margin-bottom: 40px; }
          .row-4col { grid-template-columns: repeat(2,1fr) !important; }
          .row-4col > a:nth-child(even) { border-right: none !important; padding-right: 0 !important; }
          .row-4col > a:nth-child(1), .row-4col > a:nth-child(2) { border-bottom: 1px solid #1C1E21; padding-bottom: 32px; }
          .row-reverse { grid-template-columns: 1fr !important; }
          .row-reverse > div { padding-right: 0 !important; order: 2; }
          .row-reverse > a { order: 1; margin-bottom: 24px; }
        }
        @media(max-width:768px) {
          .row-3col { grid-template-columns: 1fr !important; }
          .row-3col > a { border-right: none !important; padding-right: 0 !important; border-bottom: 1px solid #1C1E21; padding-bottom: 40px; }
          .row-3col > a:last-child { border-bottom: none; padding-bottom: 0; }
          .row-4col { grid-template-columns: 1fr !important; }
          .row-4col > a { border-right: none !important; padding-right: 0 !important; border-bottom: 1px solid #1C1E21 !important; padding-bottom: 32px !important; }
          .news-split { flex-direction: column; align-items: stretch !important; gap: 32px !important; }
        }
      `}</style>
    </main>
  );
}
