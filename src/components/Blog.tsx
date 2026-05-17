const posts = [
  {
    tag: 'Development',
    title: '10 Reasons to Choose Flutter for Your Next App in 2025',
    excerpt: 'Flutter has become the go-to framework for cross-platform mobile development. Here\'s why leading startups are choosing it.',
    date: 'Apr 18, 2025',
    img: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80&fit=crop',
    readTime: '5 min read',
  },
  {
    tag: 'AI & Automation',
    title: 'How GPT-4 APIs Are Changing Business Operations Forever',
    excerpt: 'From customer service to data processing — AI APIs are now accessible to any business. Here\'s how to use them right.',
    date: 'Apr 12, 2025',
    img: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&q=80&fit=crop',
    readTime: '7 min read',
  },
  {
    tag: 'Marketing',
    title: 'SEO in 2025: What Actually Moves the Needle',
    excerpt: 'Search engines are smarter than ever. We break down the SEO strategies that are still working and what to stop wasting time on.',
    date: 'Apr 5, 2025',
    img: 'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=800&q=80&fit=crop',
    readTime: '6 min read',
  },
];

const Blog = () => (
  <section style={{ padding: '120px 0', background: '#0E1012' }}>
    <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
      {/* Heading */}
      <div className="fade-up" style={{
        display: 'flex', justifyContent: 'space-between',
        alignItems: 'flex-end', flexWrap: 'wrap', gap: 24, marginBottom: 56,
      }}>
        <div>
          <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)' }}>
            From Our <span className="accent">Blog.</span>
          </h2>
          <p style={{
            fontFamily: 'Lexend, sans-serif', fontSize: 16,
            color: '#6B6F76', marginTop: 16,
          }}>
            Tech insights from the team building the future.
          </p>
        </div>
        <a href="#" style={{
          fontFamily: 'Lexend, sans-serif', fontWeight: 400,
          fontSize: 14, color: '#6B6F76', textDecoration: 'none',
          transition: 'color 0.2s ease',
        }}
          onMouseEnter={e => (e.currentTarget.style.color = '#F0F0F0')}
          onMouseLeave={e => (e.currentTarget.style.color = '#6B6F76')}
        >
          All articles →
        </a>
      </div>

      {/* Cards */}
      <div style={{
        display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 24,
      }} className="blog-grid">
        {posts.map((post, i) => (
          <article
            key={i}
            className="fade-up"
            style={{
              background: '#08090A', border: '1px solid #1C1E21', borderRadius: 12,
              overflow: 'hidden', cursor: 'pointer',
              transition: 'transform 0.2s ease, border-color 0.2s ease',
              animationDelay: `${i * 0.1}s`,
            }}
            onMouseEnter={e => {
              (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)';
              (e.currentTarget as HTMLElement).style.borderColor = '#3A3D42';
            }}
            onMouseLeave={e => {
              (e.currentTarget as HTMLElement).style.transform = 'translateY(0)';
              (e.currentTarget as HTMLElement).style.borderColor = '#1C1E21';
            }}
          >
            {/* Cover image */}
            <div style={{ aspectRatio: '16/9', overflow: 'hidden', position: 'relative' }}>
              <img
                src={post.img}
                alt={post.title}
                loading="lazy"
                style={{
                  width: '100%', height: '100%', objectFit: 'cover',
                  transition: 'transform 0.4s ease',
                }}
              />
              <div style={{
                position: 'absolute', inset: 0,
                background: 'rgba(8,9,10,0.2)',
              }} />
            </div>
            {/* Content */}
            <div style={{ padding: '24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 16 }}>
                <span style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 11,
                  color: '#E8FF47', background: 'rgba(232,255,71,0.08)',
                  padding: '3px 10px', borderRadius: 100,
                }}>
                  {post.tag}
                </span>
                <span style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#3A3D42',
                }}>
                  {post.readTime}
                </span>
              </div>
              <h3 style={{
                fontFamily: 'Montserrat, sans-serif', fontWeight: 700,
                fontSize: 17, color: '#F0F0F0', lineHeight: 1.4, marginBottom: 12,
              }}>
                {post.title}
              </h3>
              <p style={{
                fontFamily: 'Lexend, sans-serif', fontSize: 13,
                color: '#6B6F76', lineHeight: 1.7, marginBottom: 20,
                display: '-webkit-box', WebkitLineClamp: 3,
                WebkitBoxOrient: 'vertical', overflow: 'hidden',
              }}>
                {post.excerpt}
              </p>
              <div style={{
                display: 'flex', justifyContent: 'space-between',
                alignItems: 'center',
              }}>
                <span style={{
                  fontFamily: 'Lexend, sans-serif', fontSize: 12, color: '#3A3D42',
                }}>
                  {post.date}
                </span>
                <a href="#" style={{
                  fontFamily: 'Lexend, sans-serif', fontWeight: 500,
                  fontSize: 13, color: '#6B6F76', textDecoration: 'none',
                  transition: 'color 0.2s ease',
                }}
                  onMouseEnter={e => (e.currentTarget.style.color = '#F0F0F0')}
                  onMouseLeave={e => (e.currentTarget.style.color = '#6B6F76')}
                >
                  Read More →
                </a>
              </div>
            </div>
          </article>
        ))}
      </div>
    </div>

    <style>{`
      @media (max-width: 1024px) {
        .blog-grid { grid-template-columns: repeat(2, 1fr) !important; }
      }
      @media (max-width: 640px) {
        .blog-grid { grid-template-columns: 1fr !important; }
      }
    `}</style>
  </section>
);

export default Blog;
