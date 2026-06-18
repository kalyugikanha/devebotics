import { useEffect, useState } from 'react';
import { motion } from 'framer-motion';

const words = ['We', 'Build', 'Digital', 'Products', 'That', 'Drive', 'Real'];
const floatingTags = ['React', 'Flutter', 'Next.js', 'OpenAI', 'Node.js', 'Python', 'TypeScript'];

const Hero = () => {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => setVisible(true), 100);
    return () => clearTimeout(t);
  }, []);

  return (
    <section style={{
      minHeight: '100vh', position: 'relative',
      background: '#FFFFFF', overflow: 'hidden',
      display: 'flex', alignItems: 'center',
    }}>
      {/* Grain overlay left side */}
      <div style={{
        position: 'absolute', inset: 0, pointerEvents: 'none', zIndex: 1,
        background: 'radial-gradient(ellipse 60% 80% at 30% 50%, rgba(232,255,71,0.03) 0%, transparent 70%)',
      }} />

      {/* Right image — clipped */}
      <div style={{
        position: 'absolute', top: 0, right: 0, width: '50%', height: '100%', zIndex: 0,
        clipPath: 'polygon(10% 0, 100% 0, 100% 100%, 0% 100%)',
      }}>
        <img
          src="/images/generic-1.png"
          alt="Tech workspace"
          loading="eager"
          style={{ width: '100%', height: '100%', objectFit: 'cover' }}
        />
        {/* Gradient bleed to left */}
        <div style={{
          position: 'absolute', inset: 0,
          background: 'linear-gradient(to right, #FFFFFF 0%, rgba(8,9,10,0.6) 40%, transparent 100%)',
        }} />
      </div>

      {/* Floating tags */}
      {floatingTags.map((tag, i) => (
        <motion.div
          key={tag}
          initial={{ opacity: 0 }}
          animate={visible ? { opacity: 1 } : {}}
          transition={{ delay: 2 + i * 0.15, duration: 0.4 }}
          style={{
            position: 'absolute', zIndex: 2,
            fontFamily: 'Lexend, sans-serif', fontSize: 11, fontWeight: 400,
            color: '#9CA3AF', background: '#F9FAFB',
            border: '1px solid #E5E7EB', borderRadius: 100,
            padding: '5px 12px', whiteSpace: 'nowrap',
            top: `${15 + (i % 3) * 25}%`,
            left: `${52 + (i % 4) * 10}%`,
            pointerEvents: 'none',
            animation: `float-tag ${4 + i * 0.5}s ease-in-out infinite`,
            animationDelay: `${i * 0.3}s`,
          }}
        >
          {tag}
        </motion.div>
      ))}

      {/* Left Content */}
      <div style={{
        position: 'relative', zIndex: 3,
        maxWidth: 1400, margin: '0 auto',
        padding: '120px 32px 80px',
        width: '100%',
      }}>
        <div style={{ maxWidth: 640 }}>
          {/* Eyebrow badge */}
          <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={visible ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.5, delay: 0.2 }}
            style={{ marginBottom: 36 }}
          >
            <span style={{
              display: 'inline-flex', alignItems: 'center', gap: 8,
              fontFamily: 'Lexend, sans-serif', fontWeight: 300,
              fontSize: 11, letterSpacing: '0.2em', textTransform: 'uppercase',
              color: '#4B5563', background: '#F9FAFB',
              border: '1px solid #E5E7EB', borderRadius: 100,
              padding: '7px 16px',
            }}>
              <span style={{ color: '#1F8844' }}>✦</span>
              INDIA'S PREMIUM DIGITAL AGENCY
            </span>
          </motion.div>

          {/* H1 */}
          <h1 style={{
            fontFamily: 'Montserrat, sans-serif', fontWeight: 900,
            fontSize: 'clamp(44px, 7vw, 80px)', lineHeight: 1.0,
            letterSpacing: '-0.03em', marginBottom: 32,
          }}>
            {words.map((word, i) => (
              <motion.span
                key={i}
                initial={{ opacity: 0, y: 24 }}
                animate={visible ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.55, delay: 0.4 + i * 0.08, ease: [0.22, 1, 0.36, 1] }}
                style={{ display: 'inline-block', marginRight: '0.22em', color: '#111827' }}
              >
                {word}
              </motion.span>
            ))}
            <br />
            <motion.span
              initial={{ opacity: 0, y: 24 }}
              animate={visible ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.55, delay: 0.4 + words.length * 0.08, ease: [0.22, 1, 0.36, 1] }}
              style={{ display: 'inline-block', color: '#1F8844' }}
            >
              Growth.
            </motion.span>
          </h1>

          {/* Sub */}
          <motion.p
            initial={{ opacity: 0 }}
            animate={visible ? { opacity: 1 } : {}}
            transition={{ duration: 0.6, delay: 1.3 }}
            style={{
              fontFamily: 'Lexend, sans-serif', fontWeight: 400,
              fontSize: 18, color: '#4B5563', maxWidth: 480,
              lineHeight: 1.65, marginBottom: 48,
            }}
          >
            From AI-powered apps to enterprise software — end-to-end solutions that actually ship on time.
          </motion.p>

          {/* CTA row */}
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={visible ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.5, delay: 1.5 }}
            style={{ display: 'flex', alignItems: 'center', gap: 24, flexWrap: 'wrap', marginBottom: 64 }}
          >
            <a href="#" style={{
              fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 14,
              background: '#111827', color: '#FFFFFF', textDecoration: 'none',
              padding: '14px 30px', borderRadius: 7,
              transition: 'background 0.2s ease',
              display: 'inline-block',
            }}
              onMouseEnter={e => (e.currentTarget.style.background = '#1F8844')}
              onMouseLeave={e => (e.currentTarget.style.background = '#111827')}
            >
              Start Your Project
            </a>
            <a href="#" style={{
              fontFamily: 'Lexend, sans-serif', fontWeight: 400, fontSize: 14,
              color: '#4B5563', textDecoration: 'none',
              transition: 'color 0.2s ease',
              display: 'inline-flex', alignItems: 'center', gap: 6,
            }}
              onMouseEnter={e => { e.currentTarget.style.color = '#111827'; }}
              onMouseLeave={e => { e.currentTarget.style.color = '#4B5563'; }}
            >
              See Our Work <span style={{ transition: 'transform 0.2s ease', display: 'inline-block' }}>→</span>
            </a>
          </motion.div>

          {/* Stats bar */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={visible ? { opacity: 1 } : {}}
            transition={{ duration: 0.5, delay: 1.8 }}
          >
            <div style={{ height: 1, background: '#E5E7EB', marginBottom: 24 }} />
            <div style={{ display: 'flex', gap: 40, flexWrap: 'wrap' }}>
              {[
                { val: '500+', label: 'Projects' },
                { val: '50+', label: 'Clients' },
                { val: '5★', label: 'Rated' },
                { val: '3+', label: 'Years' },
              ].map(s => (
                <div key={s.label}>
                  <div style={{
                    fontFamily: 'Montserrat, sans-serif', fontWeight: 900,
                    fontSize: 24, color: '#111827', letterSpacing: '-0.02em',
                  }}>
                    {s.val}
                  </div>
                  <div style={{
                    fontFamily: 'Lexend, sans-serif', fontWeight: 300,
                    fontSize: 12, color: '#4B5563', letterSpacing: '0.08em',
                    textTransform: 'uppercase', marginTop: 2,
                  }}>
                    {s.label}
                  </div>
                </div>
              ))}
            </div>
          </motion.div>
        </div>
      </div>

      {/* Scroll line */}
      <div style={{
        position: 'absolute', bottom: 40, left: '50%',
        transform: 'translateX(-50%)',
        width: 1, height: 48,
        background: 'linear-gradient(to bottom, transparent, #9CA3AF)',
        animation: 'pulse-line 2s ease-in-out infinite',
        zIndex: 3,
      }} />

      <style>{`
        @keyframes float-tag {
          0%, 100% { transform: translateY(0px); }
          50% { transform: translateY(-6px); }
        }
        @keyframes pulse-line {
          0%, 100% { opacity: 0.3; }
          50% { opacity: 1; }
        }
        @media (max-width: 768px) {
          .hero-image-right { display: none !important; }
        }
      `}</style>
    </section>
  );
};

export default Hero;
