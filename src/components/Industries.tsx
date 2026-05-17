import { useState } from 'react';

// ─── All industry cards hardcoded ─────────────────────────────────────────────
const allCards = [
  // Food & Restaurant
  { cat: 'Food & Restaurant', title: 'Food Delivery App', tech: 'React Native + Node.js', desc: 'On-demand food ordering with real-time tracking, restaurant panel & delivery partner app.', img: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80' },
  { cat: 'Food & Restaurant', title: 'Restaurant Management System', tech: 'PHP + MySQL', desc: 'Table booking, billing, kitchen order display & inventory management.', img: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&q=80' },
  { cat: 'Food & Restaurant', title: 'Cloud Kitchen Platform', tech: 'Next.js + Firebase', desc: 'Multi-brand cloud kitchen with centralized order management.', img: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&q=80' },
  // Healthcare
  { cat: 'Healthcare', title: 'Patient Management System', tech: 'React + Node.js', desc: 'Doctor appointments, EMR, billing & pharmacy integration.', img: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&q=80' },
  { cat: 'Healthcare', title: 'Telemedicine App', tech: 'Flutter + Firebase', desc: 'Video consultations, prescriptions & health tracking.', img: 'https://images.unsplash.com/photo-1559757175-5700dde675bc?w=400&q=80' },
  { cat: 'Healthcare', title: 'Hospital ERP Software', tech: 'Laravel + MySQL', desc: 'End-to-end hospital operations management system.', img: 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=400&q=80' },
  // Education
  { cat: 'Education', title: 'E-Learning Platform', tech: 'Next.js + MongoDB', desc: 'Course management, video streaming, quizzes & certificates.', img: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&q=80' },
  { cat: 'Education', title: 'School Management ERP', tech: 'React + Node.js', desc: 'Attendance, fees, timetable, parent portal & reports.', img: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&q=80' },
  { cat: 'Education', title: 'EdTech Mobile App', tech: 'Flutter', desc: 'Live classes, doubt solving & progress tracking for students.', img: 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=400&q=80' },
  // E-Commerce
  { cat: 'E-Commerce', title: 'Shopify Custom Store', tech: 'Shopify + Liquid', desc: 'High-converting store with custom theme & integrations.', img: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80' },
  { cat: 'E-Commerce', title: 'Multi-vendor Marketplace', tech: 'Next.js + Stripe', desc: 'Amazon-style marketplace with seller & buyer portals.', img: 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=400&q=80' },
  { cat: 'E-Commerce', title: 'B2B E-commerce Portal', tech: 'React + Laravel', desc: 'Wholesale ordering, bulk pricing & dealer management.', img: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=400&q=80' },
  // Finance & FinTech
  { cat: 'Finance & FinTech', title: 'Digital Wallet App', tech: 'Flutter + Node.js', desc: 'P2P transfers, bill payments & transaction history.', img: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=400&q=80' },
  { cat: 'Finance & FinTech', title: 'Loan Management System', tech: 'React + PHP', desc: 'Loan applications, EMI tracking & credit scoring.', img: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&q=80' },
  { cat: 'Finance & FinTech', title: 'Investment Dashboard', tech: 'Next.js + Python', desc: 'Portfolio tracking, market data & AI-based insights.', img: 'https://images.unsplash.com/photo-1642790551116-18e150f248e3?w=400&q=80' },
  // Travel & Tourism
  { cat: 'Travel & Tourism', title: 'Travel Booking App', tech: 'React Native + Node.js', desc: 'Flights, hotels & tour packages with live availability.', img: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400&q=80' },
  { cat: 'Travel & Tourism', title: 'Taxi Booking App', tech: 'Flutter + Firebase', desc: 'Uber-style ride booking with driver & rider apps.', img: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=400&q=80' },
  { cat: 'Travel & Tourism', title: 'Tour Management Platform', tech: 'WordPress + WooCommerce', desc: 'Itinerary builder, guide booking & payment gateway.', img: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80' },
];

const tabs = ['All', 'Food & Restaurant', 'Healthcare', 'Education', 'E-Commerce', 'Finance & FinTech', 'Travel & Tourism'];

const IndustryCard = ({ card }: { card: typeof allCards[0] }) => (
  <div
    style={{
      background: '#08090A', border: '1px solid #1C1E21', borderRadius: 12,
      overflow: 'hidden', cursor: 'pointer',
      transition: 'transform 0.3s ease, border-color 0.3s ease',
    }}
    onMouseEnter={e => {
      (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)';
      (e.currentTarget as HTMLElement).style.borderColor = '#E8FF47';
    }}
    onMouseLeave={e => {
      (e.currentTarget as HTMLElement).style.transform = 'translateY(0)';
      (e.currentTarget as HTMLElement).style.borderColor = '#1C1E21';
    }}
  >
    <div style={{ height: 180, overflow: 'hidden', position: 'relative' }}>
      <img src={card.img} alt={card.title} loading="lazy"
        style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.4s ease' }}
      />
      <div style={{ position: 'absolute', inset: 0, background: 'rgba(8,9,10,0.25)' }} />
      <span style={{
        position: 'absolute', top: 12, left: 12,
        fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#6B6F76',
        background: 'rgba(14,16,18,0.85)', border: '1px solid #1C1E21',
        borderRadius: 4, padding: '3px 10px',
      }}>{card.cat}</span>
    </div>
    <div style={{ padding: '18px 20px 22px' }}>
      <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 16, color: '#F0F0F0', marginBottom: 8 }}>{card.title}</div>
      <div style={{
        fontFamily: 'Lexend, sans-serif', fontSize: 13, color: '#6B6F76',
        lineHeight: 1.65, marginBottom: 14,
        display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden',
      }}>{card.desc}</div>
      <div style={{
        fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#3A3D42',
        marginBottom: 14,
      }}>{card.tech}</div>
      <a href="#" style={{
        fontFamily: 'Lexend, sans-serif', fontWeight: 500, fontSize: 13,
        color: '#6B6F76', textDecoration: 'none', transition: 'color 0.2s ease',
      }}
        onMouseEnter={e => (e.currentTarget.style.color = '#E8FF47')}
        onMouseLeave={e => (e.currentTarget.style.color = '#6B6F76')}
      >
        Explore →
      </a>
    </div>
  </div>
);

const Industries = () => {
  const [active, setActive] = useState('All');

  const filtered = active === 'All' ? allCards : allCards.filter(c => c.cat === active);

  return (
    <section style={{ padding: '120px 0', background: '#0E1012' }}>
      <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px' }}>
        <div className="fade-up" style={{ marginBottom: 48 }}>
          <h2 style={{ fontSize: 'clamp(36px, 5vw, 56px)', marginBottom: 16 }}>
            Industries We <span className="accent">Serve.</span>
          </h2>
          <p style={{ fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#6B6F76' }}>
            Tailored digital solutions for every vertical.
          </p>
        </div>

        {/* Tab filter */}
        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', marginBottom: 48 }}>
          {tabs.map(tab => (
            <button key={tab} onClick={() => setActive(tab)} className="pill-tab"
              style={{
                background: active === tab ? '#F0F0F0' : 'transparent',
                color: active === tab ? '#08090A' : '#6B6F76',
                borderColor: active === tab ? '#F0F0F0' : '#1C1E21',
              }}>
              {tab}
            </button>
          ))}
        </div>

        {/* Cards grid */}
        <div style={{
          display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 24,
        }} className="industries-grid">
          {filtered.map((card, i) => (
            <div
              key={`${active}-${card.cat}-${card.title}`}
              className="ind-card-wrap"
              style={{ animationDelay: `${i * 0.07}s` }}
            >
              <IndustryCard card={card} />
            </div>
          ))}
        </div>
      </div>

      <style>{`
        @keyframes ind-appear {
          from { opacity: 0; transform: translateY(20px); }
          to   { opacity: 1; transform: translateY(0); }
        }
        .ind-card-wrap {
          opacity: 0;
          animation: ind-appear 0.45s ease forwards;
        }
        @media (max-width: 1024px) { .industries-grid { grid-template-columns: repeat(2, 1fr) !important; } }
        @media (max-width: 600px)  { .industries-grid { grid-template-columns: 1fr !important; } }
      `}</style>
    </section>
  );
};

export default Industries;
