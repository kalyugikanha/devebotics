import { useState, useEffect, useRef } from 'react';
import { Link, useParams } from 'react-router-dom';

const MOCK_TOC = [
  { id: 'intro', label: 'Introduction' },
  { id: 'architecture', label: 'Architecture Decisions' },
  { id: 'models', label: 'Model Selection' },
  { id: 'deployment', label: 'Deployment Strategy' },
  { id: 'conclusion', label: 'Final Thoughts' }
];

export default function BlogPost() {
  const { slug } = useParams();
  const [activeId, setActiveId] = useState('intro');
  const [scrollPct, setScrollPct] = useState(0);
  const [copied, setCopied] = useState(false);
  const [commentName, setCommentName] = useState('');
  const [commentEmail, setCommentEmail] = useState('');
  const [commentText, setCommentText] = useState('');

  // Dynamic Content based on slug
  const title = slug ? slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'How We Built a Custom AI Chatbot That Handles 80% of Customer Queries';
  const tag = slug?.includes('ai') ? 'AI & AUTOMATION' : slug?.includes('react') || slug?.includes('node') || slug?.includes('flutter') ? 'TECH & DEVELOPMENT' : 'STARTUP INSIGHTS';


  useEffect(() => {
    document.title = `${title} | DevBotics Blog`;
    const meta = document.querySelector('meta[name="description"]');
    if (meta) meta.setAttribute('content', `Read about ${title} and our insights into modern tech and growth.`);
    window.scrollTo(0, 0); // Crucial! Scroll to top on navigation.

    const handleScroll = () => {
      const doc = document.documentElement;
      setScrollPct((window.scrollY / (doc.scrollHeight - doc.clientHeight)) * 100);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) setActiveId(e.target.id); });
    }, { rootMargin: '-20% 0px -70% 0px' });
    MOCK_TOC.forEach(t => {
      const el = document.getElementById(t.id);
      if (el) obs.observe(el);
    });
    return () => obs.disconnect();
  }, []);

  const handleCopy = () => {
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72 }}>
      {/* Scroll Progress Bar */}
      <div style={{ position: 'fixed', top: 72, left: 0, width: `${scrollPct}%`, height: 2, background: '#1F8844', zIndex: 1000, transition: 'width 0.1s linear' }} />

      <section style={{ maxWidth: 1400, margin: '0 auto', padding: '60px 40px', display: 'flex', gap: 60 }} className="post-grid">
        {/* LEFT — TABLE OF CONTENTS */}
        <aside style={{ width: '20%' }} className="toc-aside">
          <div style={{ position: 'sticky', top: 120 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.15em', marginBottom: 24 }}>IN THIS ARTICLE</div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              {MOCK_TOC.map(t => (
                <a key={t.id} href={`#${t.id}`} style={{
                  fontFamily: 'Lexend,sans-serif', fontSize: 13, textDecoration: 'none',
                  color: activeId === t.id ? '#111827' : '#4B5563',
                  borderLeft: activeId === t.id ? '2px solid #1F8844' : '2px solid transparent',
                  paddingLeft: activeId === t.id ? 10 : 0,
                  transition: 'all 0.2s'
                }} onMouseEnter={e => { if (activeId !== t.id) e.currentTarget.style.color = '#A0A0A0' }} onMouseLeave={e => { if (activeId !== t.id) e.currentTarget.style.color = '#4B5563' }}>
                  {t.label}
                </a>
              ))}
            </div>
          </div>
        </aside>

        {/* CENTER — ARTICLE CONTENT */}
        <article style={{ width: '55%' }} className="post-center">
          {/* Header */}
          <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', marginBottom: 32 }}>
            <Link to="/" style={{ color: 'inherit', textDecoration: 'none' }}>Home</Link> / <Link to="/blog" style={{ color: 'inherit', textDecoration: 'none' }}>Blog</Link> / <span style={{ color: '#4B5563' }}>{tag}</span> / <span style={{ color: '#111827' }}>{title}</span>
          </div>
          <div style={{ display: 'inline-block', background: '#1F8844', color: 'black', fontFamily: 'Lexend,sans-serif', fontWeight: 600, fontSize: 11, padding: '4px 12px', borderRadius: 2, marginBottom: 24 }}>{tag}</div>
          <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(36px,5vw,52px)', color: '#111827', lineHeight: 1.0, marginBottom: 24, animation: 'fadeWords 0.8s ease' }}>
            {title}
          </h1>
          <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 20, color: '#4B5563', lineHeight: 1.5, marginBottom: 32 }}>
            A deep dive into our architecture decisions, model selection, and deployment strategy for a fintech client's support automation.
          </p>
          
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderTop: '1px solid #E5E7EB', borderBottom: '1px solid #E5E7EB', padding: '16px 0', marginBottom: 48 }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
              <div style={{ width: 32, height: 32, borderRadius: '50%', background: '#E5E7EB', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#111827', fontFamily: 'Lexend', fontSize: 12 }}>DB</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563' }}><strong style={{ color: '#111827', fontWeight: 400 }}>DevBotics Team</strong> · April 2, 2025 · 12 min read</div>
            </div>
            <div style={{ display: 'flex', gap: 16 }}>
              {['Twitter', 'LinkedIn', 'Copy'].map(s => <span key={s} style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', cursor: 'pointer', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#9CA3AF'}>{s}</span>)}
            </div>
          </div>

          <img src="/images/generic-1.png" alt="Hero" style={{ width: '100%', height: 420, objectFit: 'cover', borderRadius: 8, marginBottom: 12 }} />
          <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 12, color: '#9CA3AF', textAlign: 'center', fontStyle: 'italic', marginBottom: 48 }}>The neural network architecture visualization used during the initial pitch.</div>

          {/* Body */}
          <div className="post-body">
            <h2 id="intro">Introduction</h2>
            <p>When a rapidly growing fintech startup approached us, their support team was drowning. They were receiving over 2,000 queries a day, and response times had stretched from minutes to hours. They needed an automated solution, but standard off-the-shelf bots were failing to understand the complex financial nuances of their product.</p>
            
            <h2 id="architecture">Architecture Decisions</h2>
            <p>We decided to build a custom Retrieval-Augmented Generation (RAG) pipeline. This allowed us to feed the LLM with the company's proprietary knowledge base without fine-tuning the model from scratch, which would have been cost-prohibitive and slow to update.</p>
            
            <blockquote>
              "The shift from rule-based chatbots to LLM-driven RAG pipelines is the most significant leap in customer service technology we've seen in a decade."
            </blockquote>

            <p>Our stack consisted of:</p>
            <ul>
              <li><strong>Frontend:</strong> React Native (integrated into their existing app)</li>
              <li><strong>Backend:</strong> Node.js microservices</li>
              <li><strong>Vector DB:</strong> Pinecone</li>
              <li><strong>LLM:</strong> OpenAI GPT-4o-mini</li>
            </ul>

            <h3 id="models">Model Selection & Prompting</h3>
            <p>Choosing the right model was critical. While GPT-4 was the most capable, its latency and cost were too high for a real-time chat interface processing thousands of messages. We opted for the <span className="inline-code">gpt-4o-mini</span> model, applying strict system prompts and few-shot examples to maintain accuracy.</p>

            <div className="code-block">
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 12, borderBottom: '1px solid #E5E7EB', paddingBottom: 12 }}>
                <span style={{ color: '#9CA3AF', fontFamily: 'Lexend,sans-serif', fontSize: 12 }}>src/services/llm.ts</span>
                <button onClick={handleCopy} style={{ background: 'transparent', border: 'none', color: copied ? '#1F8844' : '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 12, cursor: 'pointer' }}>{copied ? 'Copied ✓' : 'Copy'}</button>
              </div>
              <code>
<span style={{color: '#1F8844'}}>const</span> <span style={{color: '#111827'}}>generateResponse</span> = <span style={{color: '#1F8844'}}>async</span> (query: <span style={{color: '#A8FF78'}}>string</span>, context: <span style={{color: '#A8FF78'}}>string</span>) =&gt; {'{\n'}
{'  '}<span style={{color: '#9CA3AF'}}>// Combine user query with vector DB context</span>{'\n'}
{'  '}<span style={{color: '#1F8844'}}>const</span> prompt = <span style={{color: '#A8FF78'}}>{'`Use the following context to answer:\\n${context}\\n\\nQuery: ${query}`'}</span>;{'\n\n'}
{'  '}<span style={{color: '#1F8844'}}>return</span> <span style={{color: '#1F8844'}}>await</span> openai.chat.completions.<span style={{color: '#111827'}}>create</span>({'{\n'}
{'    '}model: <span style={{color: '#A8FF78'}}>'gpt-4o-mini'</span>,{'\n'}
{'    '}messages: [{'{'} role: <span style={{color: '#A8FF78'}}>'user'</span>, content: prompt {'}'}],{'\n'}
{'    '}temperature: <span style={{color: '#A8FF78'}}>0.2</span>,{'\n'}
{'  }'});{'\n'}
{'}'};
              </code>
            </div>

            <div className="section-divider"><div>✦</div></div>

            <h2 id="deployment">Deployment Strategy</h2>
            <p>We rolled out the bot in phases. Phase 1 was "Shadow Mode" where the bot drafted responses that human agents had to approve. This allowed us to measure accuracy safely. Phase 2 was autonomous responses for specific, low-risk categories (e.g., password resets, balance inquiries).</p>

            <div className="key-takeaway">
              <div className="label">KEY TAKEAWAY</div>
              <div className="text">Never deploy an LLM chatbot directly to 100% of your users on day one. Always use a shadow mode phase to validate the RAG pipeline's accuracy against real-world, unpredictable user queries.</div>
            </div>

            <h2 id="conclusion">Final Thoughts</h2>
            <p>The result? Within 30 days of full deployment, the bot successfully resolved 82% of incoming queries without human intervention. Average resolution time dropped from 4 hours to 12 seconds.</p>
          </div>

          {/* Article Footer */}
          <div style={{ marginTop: 60, paddingBottom: 40, borderBottom: '1px solid #E5E7EB' }}>
            <div style={{ display: 'flex', gap: 12, marginBottom: 32 }}>
              <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', alignSelf: 'center' }}>Tagged:</span>
              {['AI', 'Chatbots', 'LLM', 'Node.js'].map(t => (
                <span key={t} style={{ border: '1px solid #E5E7EB', borderRadius: 100, padding: '4px 12px', fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#4B5563', cursor: 'pointer', transition: 'all 0.2s' }} onMouseEnter={e => { e.currentTarget.style.borderColor = '#1F8844'; e.currentTarget.style.color = '#111827'; }} onMouseLeave={e => { e.currentTarget.style.borderColor = '#E5E7EB'; e.currentTarget.style.color = '#4B5563'; }}>{t}</span>
              ))}
            </div>
            <div style={{ background: '#F9FAFB', padding: 24, borderRadius: 8, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 16, color: '#111827' }}>Found this useful? Share it.</div>
              <div style={{ display: 'flex', gap: 12 }}>
                {['Twitter', 'LinkedIn', 'WhatsApp', 'Copy Link'].map(s => (
                  <button key={s} style={{ background: 'transparent', border: '1px solid #E5E7EB', color: '#4B5563', padding: '8px 16px', borderRadius: 4, fontFamily: 'Lexend,sans-serif', fontSize: 13, cursor: 'pointer', transition: 'all 0.2s' }} onMouseEnter={e => { e.currentTarget.style.borderColor = '#1F8844'; e.currentTarget.style.color = '#111827'; }} onMouseLeave={e => { e.currentTarget.style.borderColor = '#E5E7EB'; e.currentTarget.style.color = '#4B5563'; }}>{s}</button>
                ))}
              </div>
            </div>
          </div>

          {/* COMMENTS */}
          <div style={{ marginTop: 60 }}>
            <h3 style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 600, fontSize: 18, color: '#111827', marginBottom: 32 }}>Discussion (3)</h3>
            
            <div style={{ display: 'flex', flexDirection: 'column', gap: 24, marginBottom: 48 }}>
              {[
                { n: 'Alex Rivera', d: 'April 3, 2025', c: 'Great writeup. Did you face any issues with Pinecone latency during peak hours? We\'re considering a similar architecture.' },
                { n: 'Sarah Chen', d: 'April 3, 2025', c: 'The shadow mode approach is brilliant. We tried launching an LLM bot directly and the hallucination rate was a nightmare.' },
                { n: 'DevBotics Team', d: 'April 4, 2025', c: 'Thanks Sarah! Alex — Pinecone latency was mostly fine, but we implemented Redis caching for common queries which reduced DB hits by 40%.' }
              ].map(c => (
                <div key={c.n} style={{ borderBottom: '1px solid #E5E7EB', paddingBottom: 24 }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 12 }}>
                    <div style={{ width: 36, height: 36, borderRadius: '50%', background: '#111', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#1F8844', fontFamily: 'Montserrat,sans-serif', fontSize: 13 }}>{c.n.split(' ').map(x=>x[0]).join('')}</div>
                    <div>
                      <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 600, fontSize: 14, color: '#111827' }}>{c.n}</div>
                      <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 12, color: '#9CA3AF' }}>{c.d}</div>
                    </div>
                  </div>
                  <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.7, margin: '0 0 12px 48px' }}>{c.c}</p>
                  <div style={{ marginLeft: 48, fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', cursor: 'pointer', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#9CA3AF'}>Reply ↩</div>
                </div>
              ))}
            </div>

            {/* Add Comment */}
            <div style={{ background: '#F9FAFB', padding: 32, borderRadius: 8 }}>
              <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 18, color: '#111827', marginBottom: 24 }}>Leave a comment</h4>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 24, marginBottom: 24 }}>
                <div className="input-group">
                  <input type="text" required value={commentName} onChange={e => setCommentName(e.target.value)} />
                  <label>Name</label>
                </div>
                <div className="input-group">
                  <input type="email" required value={commentEmail} onChange={e => setCommentEmail(e.target.value)} />
                  <label>Email</label>
                </div>
              </div>
              <div className="input-group" style={{ marginBottom: 24 }}>
                <textarea required rows={5} value={commentText} onChange={e => setCommentText(e.target.value)} style={{ resize: 'none' }} />
                <label>Your comment</label>
              </div>
              <button style={{ background: '#111827', color: '#FFFFFF', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 14, padding: '12px 24px', cursor: 'pointer', transition: 'background 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#1F8844'} onMouseLeave={e => e.currentTarget.style.background = '#111827'}>Post Comment →</button>
            </div>
          </div>
        </article>

        {/* RIGHT — SIDEBAR */}
        <aside style={{ width: '25%' }} className="post-sidebar">
          <div style={{ position: 'sticky', top: 120, display: 'flex', flexDirection: 'column', gap: 40 }}>
            {/* Author Widget */}
            <div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 16, marginBottom: 16 }}>
                <div style={{ width: 56, height: 56, borderRadius: '50%', background: '#E5E7EB', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#111827', fontFamily: 'Lexend', fontSize: 16 }}>DB</div>
                <div>
                  <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, color: '#111827' }}>DevBotics Team</div>
                  <div style={{ display: 'flex', gap: 12, marginTop: 4 }}>
                    <a href="#" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', textDecoration: 'none' }}>LinkedIn</a>
                    <a href="#" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', textDecoration: 'none' }}>Twitter</a>
                  </div>
                </div>
              </div>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', lineHeight: 1.6, margin: '0 0 24px' }}>Engineers, designers & strategists building world-class digital products.</p>
              <div style={{ height: 1, background: '#E5E7EB' }} />
            </div>

            {/* Related Widget */}
            <div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#1F8844', textTransform: 'uppercase', letterSpacing: '0.15em', marginBottom: 20 }}>READ NEXT</div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                {[
                  { img: '/images/generic-2.png', t: 'React Native vs Flutter in 2025' },
                  { img: '/images/generic-1.png', t: 'The Complete Guide to Node.js Backend' },
                  { img: '/images/generic-2.png', t: 'SEO Playbook: 0 to 50K Visitors' }
                ].map((a, i) => (
                  <Link key={i} to={`/blog/post-${i}`} style={{ display: 'flex', gap: 16, textDecoration: 'none', borderBottom: i < 2 ? '1px solid #E5E7EB' : 'none', paddingBottom: i < 2 ? 16 : 0 }} className="related-hover">
                    <img src={a.img} alt="" style={{ width: 60, height: 44, objectFit: 'cover', borderRadius: 4 }} />
                    <div>
                      <div style={{ fontFamily: 'Lexend,sans-serif', fontWeight: 500, fontSize: 13, color: '#111827', lineHeight: 1.4, marginBottom: 4, transition: 'color 0.2s' }} className="rel-title">{a.t}</div>
                      <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#9CA3AF' }}>5 min read</div>
                    </div>
                  </Link>
                ))}
              </div>
            </div>

            {/* Newsletter Widget */}
            <div style={{ background: '#F9FAFB', border: '1px solid #E5E7EB', borderRadius: 6, padding: 20 }}>
              <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 16, color: '#111827', marginBottom: 8 }}>Weekly Digest</h4>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', marginBottom: 16 }}>Get articles like this every Monday.</p>
              <input type="email" placeholder="Email address" style={{ width: '100%', background: 'transparent', border: 'none', borderBottom: '1px solid #E5E7EB', padding: '10px 0', fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#111827', outline: 'none', marginBottom: 16, transition: 'border-color 0.2s' }} onFocus={e => e.target.style.borderBottomColor = '#1F8844'} onBlur={e => e.target.style.borderBottomColor = '#E5E7EB'} />
              <button style={{ width: '100%', background: '#111827', color: '#FFFFFF', border: 'none', padding: '10px 0', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 13, cursor: 'pointer', transition: 'background 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#1F8844'} onMouseLeave={e => e.currentTarget.style.background = '#111827'}>Subscribe</button>
            </div>

            {/* CTA Widget */}
            <div style={{ background: '#1F8844', borderRadius: 6, padding: 24 }}>
              <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 18, color: '#FFFFFF', marginBottom: 8 }}>Have a project in mind?</h4>
              <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: 'rgba(255,255,255,0.7)', marginBottom: 20 }}>Let's build it together.</p>
              <Link to="/contact" style={{ display: 'block', width: '100%', background: '#FFFFFF', color: '#1F8844', textAlign: 'center', padding: '12px 0', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 14, textDecoration: 'none', borderRadius: 4, transition: 'opacity 0.2s' }} onMouseEnter={e => e.currentTarget.style.opacity = '0.8'} onMouseLeave={e => e.currentTarget.style.opacity = '1'}>Start a Project →</Link>
            </div>
          </div>
        </aside>
      </section>

      {/* BELOW COMMENTS — MORE ARTICLES */}
      <section style={{ maxWidth: 1400, margin: '0 auto', padding: '60px 40px 100px', borderTop: '1px solid #E5E7EB' }}>
        <h3 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 800, fontSize: 28, color: '#111827', marginBottom: 40 }}>Continue Reading.</h3>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 40 }} className="more-articles">
          {[
            { t: 'React Native vs Flutter in 2025', c: 'Tech & Development', img: '/images/generic-1.png' },
            { t: 'Building a Production-Ready RAG Pipeline', c: 'AI & Automation', img: '/images/generic-2.png' },
            { t: 'How We Took a Client From 0 to 50K Visitors', c: 'Digital Marketing', img: '/images/generic-1.png' }
          ].map((a, i) => (
            <Link key={i} to="/blog/example" style={{ textDecoration: 'none', display: 'flex', flexDirection: 'column' }} className="article-hover">
              <img src={a.img} alt="" style={{ width: '100%', aspectRatio: '16/9', objectFit: 'cover', borderRadius: 4, marginBottom: 16, transition: 'transform 0.3s' }} className="art-img" />
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 10, color: '#1F8844', textTransform: 'uppercase', marginBottom: 8 }}>{a.c}</div>
              <h4 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 18, color: '#111827', lineHeight: 1.3, transition: 'color 0.2s' }} className="art-title">{a.t}</h4>
            </Link>
          ))}
        </div>
      </section>

      <style>{`
        .post-body h2 {
          font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 28px; color: #111827;
          border-left: 3px solid #1F8844; padding-left: 16px; margin: 48px 0 20px;
        }
        .post-body h3 { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 22px; color: #111827; margin: 32px 0 16px; }
        .post-body p { font-family: 'Lexend', sans-serif; font-size: 17px; color: #A0A0A0; line-height: 1.8; margin-bottom: 24px; }
        .post-body strong { color: #111827; }
        .post-body blockquote { background: #F9FAFB; border-left: 3px solid #1F8844; padding: 20px 24px; font-family: 'Lexend', sans-serif; font-style: italic; font-size: 18px; color: #4B5563; margin: 32px 0; }
        .post-body .inline-code { background: #F9FAFB; border: 1px solid #E5E7EB; padding: 2px 6px; border-radius: 3px; font-family: 'Lexend', monospace; font-size: 13px; color: #1F8844; }
        .post-body .code-block { background: #0A0C0F; border: 1px solid #E5E7EB; border-radius: 6px; padding: 20px; margin: 32px 0; overflow-x: auto; }
        .post-body .code-block code { font-family: 'Lexend', monospace; font-size: 14px; white-space: pre; line-height: 1.6; }
        .post-body ul { font-family: 'Lexend', sans-serif; font-size: 16px; color: #A0A0A0; line-height: 1.8; margin-bottom: 24px; padding-left: 20px; list-style-type: none; }
        .post-body ul li { position: relative; margin-bottom: 8px; }
        .post-body ul li::before { content: ''; position: absolute; left: -20px; top: 10px; width: 6px; height: 6px; background: #1F8844; }
        .post-body .section-divider { position: relative; height: 1px; background: #E5E7EB; margin: 60px 0; display: flex; justify-content: center; align-items: center; }
        .post-body .section-divider div { background: #FFFFFF; padding: 0 8px; color: #1F8844; font-size: 12px; }
        .post-body .key-takeaway { background: rgba(232,255,71,0.04); border: 1px solid rgba(232,255,71,0.15); border-radius: 6px; padding: 20px; margin: 40px 0; }
        .post-body .key-takeaway .label { color: #1F8844; font-size: 10px; font-family: 'Lexend', sans-serif; text-transform: uppercase; margin-bottom: 8px; }
        .post-body .key-takeaway .text { font-family: 'Lexend', sans-serif; font-weight: 500; font-size: 15px; color: #111827; }

        .input-group { position: relative; }
        .input-group input, .input-group textarea {
          width: 100%; background: transparent; border: none; border-bottom: 1px solid #E5E7EB;
          padding: 12px 0; font-family: 'Lexend', sans-serif; font-size: 15px; color: #111827; outline: none; transition: border-color 0.2s;
        }
        .input-group input:focus, .input-group textarea:focus { border-bottom-color: #1F8844; }
        .input-group label {
          position: absolute; left: 0; top: 12px; font-family: 'Lexend', sans-serif; font-size: 15px; color: #4B5563;
          transition: all 0.2s ease; pointer-events: none;
        }
        .input-group input:focus ~ label, .input-group input:valid ~ label,
        .input-group textarea:focus ~ label, .input-group textarea:valid ~ label {
          top: -16px; font-size: 12px; color: #1F8844;
        }

        .related-hover:hover .rel-title { color: #1F8844 !important; }
        .article-hover:hover .art-img { transform: scale(1.03); }
        .article-hover:hover .art-title { color: #1F8844 !important; }

        @media(max-width:1024px) {
          .post-grid { flex-direction: column; }
          .toc-aside, .post-center, .post-sidebar { width: 100% !important; }
          .toc-aside > div, .post-sidebar > div { position: static !important; }
          .toc-aside { border-bottom: 1px solid #E5E7EB; padding-bottom: 24px; }
          .more-articles { grid-template-columns: 1fr !important; }
        }
      `}</style>
    </main>
  );
}
