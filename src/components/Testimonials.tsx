import { useState } from 'react';

const reviews = [
  { name: 'Rahul Sharma', company: 'FoodTech Startup', text: 'DevBotics built our food delivery app in 45 days. Absolutely world-class quality and communication.', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80&fit=crop&crop=face' },
  { name: 'Priya Mehta', company: 'EduLearn India', text: 'Our e-learning platform has served 10,000+ students. DevBotics nailed every single detail.', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80&fit=crop&crop=face' },
  { name: 'Arjun Patel', company: 'LogiTrack', text: 'The logistics software they built saves us 6 hours daily. We saw ROI in the very first month.', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80&fit=crop&crop=face' },
  { name: 'Sneha Joshi', company: 'HealthFirst Clinic', text: 'Our patient management system is flawless. Team was responsive 24/7 throughout the project.', avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80&fit=crop&crop=face' },
  { name: 'Vikram Singh', company: 'RetailX', text: 'Shopify store built and optimized — our revenue doubled in just 3 months. Incredible ROI.', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80&fit=crop&crop=face' },
  { name: 'Ananya Roy', company: 'TravelMate', text: 'Booking app works perfectly across iOS and Android. Zero bugs post-launch — remarkable.', avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&q=80&fit=crop&crop=face' },
  { name: 'Karan Malhotra', company: 'FinEdge', text: 'Their AI chatbot handles 80% of our customer queries automatically. Game changer for us.', avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80&fit=crop&crop=face' },
  { name: 'Divya Nair', company: 'BrandBoost Agency', text: 'SEO results in 60 days — we\'re on page 1 for 15+ keywords. Exceeded all expectations.', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80&fit=crop&crop=face' },
  { name: 'Rohan Gupta', company: 'SaaSify', text: 'Clean code, on-time delivery, great communication throughout. Will definitely work again.', avatar: 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&q=80&fit=crop&crop=face' },
  { name: 'Meera Kapoor', company: 'CraftStore', text: 'Our custom e-commerce solution is fast, beautiful, and converts 3x better than our old site.', avatar: 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=100&q=80&fit=crop&crop=face' },
];

const ReviewCard = ({ review }: { review: typeof reviews[0] }) => (
  <div style={{
    minWidth: 320, maxWidth: 360, flexShrink: 0,
    background: '#0E1012', border: '1px solid #1C1E21',
    borderRadius: 12, padding: '24px 28px',
    margin: '0 10px',
  }}>
    {/* Stars */}
    <div style={{ display: 'flex', gap: 3, marginBottom: 14 }}>
      {[1,2,3,4,5].map(s => (
        <span key={s} style={{ color: '#E8FF47', fontSize: 12 }}>★</span>
      ))}
    </div>
    {/* Quote */}
    <p style={{
      fontFamily: 'Lexend, sans-serif', fontStyle: 'italic',
      fontSize: 14, color: '#A0A0A0', lineHeight: 1.75,
      marginBottom: 20, display: '-webkit-box',
      WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden',
    }}>
      "{review.text}"
    </p>
    {/* Author */}
    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
      <img
        src={review.avatar}
        alt={review.name}
        loading="lazy"
        style={{
          width: 40, height: 40, borderRadius: '50%', objectFit: 'cover',
          border: '2px solid #1C1E21', flexShrink: 0,
        }}
      />
      <div>
        <div style={{
          fontFamily: 'Lexend, sans-serif', fontWeight: 500,
          fontSize: 14, color: '#F0F0F0',
        }}>
          {review.name}
        </div>
        <div style={{
          fontFamily: 'Lexend, sans-serif', fontWeight: 300,
          fontSize: 12, color: '#6B6F76', marginTop: 1,
        }}>
          {review.company}
        </div>
      </div>
    </div>
  </div>
);

const Testimonials = () => {
  const row1 = [...reviews, ...reviews];
  const row2 = [...reviews.slice(5), ...reviews.slice(5), ...reviews.slice(0,5), ...reviews.slice(0,5)];
  const [paused, setPaused] = useState(false);

  return (
    <section style={{ padding: '120px 0', background: '#08090A', overflow: 'hidden' }}>
      <div style={{
        maxWidth: 1400, margin: '0 auto', padding: '0 32px', marginBottom: 64,
      }}>
        <div className="fade-up">
          <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)' }}>
            What Clients <span className="accent">Say.</span>
          </h2>
          <p style={{
            fontFamily: 'Lexend, sans-serif', fontSize: 16,
            color: '#6B6F76', marginTop: 16,
          }}>
            10+ reviews from founders and teams across India.
          </p>
        </div>
      </div>

      {/* Row 1 — scrolls left */}
      <div
        style={{
          overflow: 'hidden', marginBottom: 16,
          maskImage: 'linear-gradient(to right, transparent, black 5%, black 95%, transparent)',
          WebkitMaskImage: 'linear-gradient(to right, transparent, black 5%, black 95%, transparent)',
        }}
        onMouseEnter={() => setPaused(true)}
        onMouseLeave={() => setPaused(false)}
      >
        <div style={{
          display: 'flex',
          animation: `marquee 40s linear infinite${paused ? ' paused' : ''}`,
          width: 'max-content',
        }}>
          {row1.map((r, i) => <ReviewCard key={i} review={r} />)}
        </div>
      </div>

      {/* Row 2 — scrolls right */}
      <div
        style={{
          overflow: 'hidden',
          maskImage: 'linear-gradient(to right, transparent, black 5%, black 95%, transparent)',
          WebkitMaskImage: 'linear-gradient(to right, transparent, black 5%, black 95%, transparent)',
        }}
        onMouseEnter={() => setPaused(true)}
        onMouseLeave={() => setPaused(false)}
      >
        <div style={{
          display: 'flex',
          animation: `marquee-rev 44s linear infinite${paused ? ' paused' : ''}`,
          width: 'max-content',
        }}>
          {row2.map((r, i) => <ReviewCard key={i} review={r} />)}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
