import { useState, useEffect, useRef } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';
import { getNavItems, NavItem } from '../lib/firestore';

const featuredData: Record<string, { img: string; title: string; cta: string }> = {
  'Development Hub': { img: '/images/dev-hub.png', title: 'Build your dream app', cta: 'Start building →' },
  'AI Development': { img: '/images/ai-dev.png', title: 'AI-powered solutions', cta: 'Explore AI →' },
  'Hire Us': { img: '/images/hire-us.png', title: 'Dedicated team ready', cta: 'Hire now →' },
  'Marketing': { img: '/images/marketing.png', title: 'Grow your brand', cta: 'See marketing →' },
};
const defaultFeatured = { img: '/images/dev-hub.png', title: 'Explore more', cta: 'Learn more →' };

const DEFAULT_NAV: NavItem[] = [
  { label: 'Home', type: 'link', href: '/', order: 0 },
  { label: 'About Us', type: 'simple', order: 1, children: [{ label: 'About Us', href: '/about-us', order: 0 }, { label: 'Careers', href: '/careers', order: 1 }] },
  { label: 'Development Hub', type: 'mega', order: 2, children: [{ label: 'Custom Development', href: '/development/custom-development', order: 0 }, { label: 'Web Development', href: '/web-development', order: 1 }, { label: 'App Development', href: '/app-development', order: 2 }] },
  { label: 'AI Development', type: 'mega', order: 3, children: [{ label: 'AI Website Development', href: '/ai-services/ai-website-development', order: 0 }, { label: 'AI Software', href: '/ai-services/ai-software', order: 1 }, { label: 'WhatsApp Admin Development', href: '/ai-services/whatsapp-admin-development', order: 2 }] },
  { label: 'Blog', type: 'link', href: '/blog', order: 4 },
  { label: 'Contact', type: 'link', href: '/contact', order: 5 },
];

const Navbar = () => {
  const [navItems, setNavItems] = useState<NavItem[]>(DEFAULT_NAV);
  const [scrolled, setScrolled] = useState(false);
  const [activeMenu, setActiveMenu] = useState<string | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [mobileExpanded, setMobileExpanded] = useState<string | null>(null);
  const closeTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const location = useLocation();

  useEffect(() => {
    getNavItems().then(items => {
      if (items && items.length > 0) {
        setNavItems(items);
      }
    }).catch(err => console.error("Error fetching nav items:", err));
  }, []);

  useEffect(() => {
    setActiveMenu(null);
    setMobileOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    const fn = () => setScrolled(window.scrollY > 50);
    window.addEventListener('scroll', fn, { passive: true });
    return () => window.removeEventListener('scroll', fn);
  }, []);

  useEffect(() => {
    document.body.style.overflow = mobileOpen ? 'hidden' : '';
    return () => { document.body.style.overflow = ''; };
  }, [mobileOpen]);

  const open = (n: string) => { if (closeTimer.current) clearTimeout(closeTimer.current); setActiveMenu(n); };
  const close = () => { closeTimer.current = setTimeout(() => setActiveMenu(null), 150); };
  const keep = () => { if (closeTimer.current) clearTimeout(closeTimer.current); };

  const navLinkStyle = (active = false): React.CSSProperties => ({
    fontFamily: 'Lexend, sans-serif', fontWeight: 400, fontSize: 13.5,
    color: active ? '#111827' : '#4B5563',
    background: 'transparent', border: 'none', cursor: 'pointer',
    display: 'flex', alignItems: 'center', gap: 5, padding: '8px 12px',
    transition: 'color 0.2s ease', whiteSpace: 'nowrap', textDecoration: 'none',
  });

  const chevron = (up: boolean) => (
    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
      style={{ transition: 'transform 0.2s ease', transform: up ? 'rotate(180deg)' : 'rotate(0deg)', flexShrink: 0 }}>
      <path d="M2 3.5l3 3 3-3" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );

  return (
    <>
      <nav style={{
        position: 'fixed', top: 0, left: 0, right: 0, zIndex: 900,
        backgroundColor: scrolled ? '#FFFFFF' : 'transparent',
        borderBottom: scrolled ? '1px solid #E5E7EB' : '1px solid transparent',
        transition: 'background-color 0.3s ease, border-color 0.3s ease',
      }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', padding: '0 32px', display: 'flex', alignItems: 'center', height: 72 }}>
          {/* Logo */}
          <Link to="/" style={{ textDecoration: 'none', flexShrink: 0 }}>
            <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 22, color: '#111827', letterSpacing: '-0.03em' }}>
              DevBotics<span style={{ color: '#1F8844' }}>.</span>
            </span>
          </Link>

          {/* Desktop links */}
          <div style={{ display: 'flex', alignItems: 'center', gap: 0, marginLeft: 36, flex: 1 }} className="desktop-nav">
            {navItems.map(item => {
              if (item.type === 'link') {
                const isActive = location.pathname === item.href;
                return (
                  <Link key={item.id || item.label} to={item.href || '/'} style={navLinkStyle(isActive)}
                    onMouseEnter={e => (e.currentTarget.style.color = '#111827')}
                    onMouseLeave={e => (e.currentTarget.style.color = isActive ? '#111827' : '#4B5563')}>
                    {item.label}
                  </Link>
                );
              }

              return (
                <div key={item.id || item.label} style={{ position: 'relative' }} onMouseEnter={() => open(item.label)} onMouseLeave={close}>
                  <button style={navLinkStyle(activeMenu === item.label)}
                    onMouseEnter={e => (e.currentTarget.style.color = '#111827')}
                    onMouseLeave={e => { if (activeMenu !== item.label) e.currentTarget.style.color = '#4B5563'; }}>
                    {item.label} {chevron(activeMenu === item.label)}
                  </button>
                  
                  {item.type === 'simple' && (
                    <div onMouseEnter={() => { open(item.label); keep(); }} onMouseLeave={close} style={{
                      position: 'absolute', top: 'calc(100% + 8px)', left: 0,
                      background: '#F9FAFB', border: '1px solid #E5E7EB', borderTop: '2px solid #1F8844',
                      minWidth: 200, zIndex: 800, padding: '8px 0',
                      opacity: activeMenu === item.label ? 1 : 0,
                      transform: activeMenu === item.label ? 'translateY(0)' : 'translateY(-8px)',
                      pointerEvents: activeMenu === item.label ? 'auto' : 'none',
                      transition: 'opacity 0.2s ease, transform 0.2s ease',
                      boxShadow: '0 16px 40px rgba(0,0,0,0.05)',
                    }}>
                      {(item.children || []).map(child => (
                        <Link key={child.label} to={child.href} className="mega-link" style={{ padding: '9px 20px' }}>
                          {child.label}
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              );
            })}
          </div>

          {/* CTA */}
          <Link to="/contact" className="desktop-cta" style={{
            fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 13,
            background: '#111827', color: '#FFFFFF', textDecoration: 'none',
            padding: '10px 22px', borderRadius: 6, transition: 'background 0.2s ease',
            flexShrink: 0, marginLeft: 'auto',
          }}
            onMouseEnter={e => { (e.currentTarget as HTMLElement).style.background = '#1F8844'; }}
            onMouseLeave={e => { (e.currentTarget as HTMLElement).style.background = '#111827'; }}>
            Get Free Quote
          </Link>

          {/* Mobile toggle */}
          <button onClick={() => setMobileOpen(!mobileOpen)} className="mobile-toggle" aria-label="Toggle menu"
            style={{ background: 'transparent', border: 'none', color: '#111827', cursor: 'pointer', padding: 8, marginLeft: 16 }}>
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
              {mobileOpen
                ? <path d="M4 4l14 14M18 4L4 18" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
                : <path d="M3 6h16M3 11h16M3 16h16" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />}
            </svg>
          </button>
        </div>

        {/* Mega panels rendering */}
        {navItems.filter(i => i.type === 'mega').map(item => {
          const isOpen = activeMenu === item.label;
          const feat = featuredData[item.label] || defaultFeatured;
          return (
            <div key={item.id || item.label} onMouseEnter={() => { open(item.label); keep(); }} onMouseLeave={close}
              style={{
                position: 'absolute', top: 'calc(100% + 8px)', left: '50%', marginLeft: '-300px', background: '#F9FAFB',
                border: '1px solid #E5E7EB', borderTop: '2px solid #1F8844', borderRadius: '8px', zIndex: 800,
                opacity: isOpen ? 1 : 0, transform: isOpen ? 'translateY(0)' : 'translateY(-8px)',
                pointerEvents: isOpen ? 'auto' : 'none',
                transition: 'opacity 0.2s ease, transform 0.2s ease',
                boxShadow: '0 16px 40px rgba(0,0,0,0.05)',
                width: '600px'
              }}>
              <div style={{ padding: '24px', display: 'grid', gridTemplateColumns: '1fr 200px', gap: 24 }}>
                <div>
                  <div style={{ marginBottom: 18 }}>
                     <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 600, fontSize: 11, color: '#111827', marginBottom: 8, letterSpacing: '0.07em', textTransform: 'uppercase' }}>
                       Services
                     </div>
                     <div style={{ width: 28, height: 2, background: '#1F8844', borderRadius: 1 }} />
                  </div>
                  <div style={{ display: 'flex', flexDirection: 'column', gap: 0 }}>
                    {(item.children || []).map(child => (
                      <Link key={child.label} to={child.href} className="mega-link" style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                        <ChevronRight size={14} color="#1F8844" />
                        {child.label}
                      </Link>
                    ))}
                  </div>
                </div>
                {/* Featured card */}
                <div style={{ background: '#F3F4F6', borderRadius: 10, overflow: 'hidden', border: '1px solid #E5E7EB' }}>
                  <div style={{ position: 'relative', height: 120 }}>
                    <img src={feat.img} alt="" loading="lazy" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                    <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to top, rgba(255,255,255,0.7), transparent 60%)' }} />
                  </div>
                  <div style={{ padding: '18px 18px 22px' }}>
                    <div style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 14, color: '#111827', marginBottom: 12, lineHeight: 1.3 }}>{feat.title}</div>
                    <Link to="/contact" style={{ fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 13, color: '#FFFFFF', background: '#1F8844', textDecoration: 'none', padding: '7px 14px', borderRadius: 5, display: 'inline-block', transition: 'opacity 0.2s ease' }}
                      onMouseEnter={e => (e.currentTarget.style.opacity = '0.85')}
                      onMouseLeave={e => (e.currentTarget.style.opacity = '1')}>
                      {feat.cta}
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </nav>

      {/* Mobile overlay */}
      <div style={{
        position: 'fixed', inset: 0, zIndex: 890, background: '#FFFFFF',
        opacity: mobileOpen ? 1 : 0, pointerEvents: mobileOpen ? 'auto' : 'none',
        transition: 'opacity 0.3s ease', overflowY: 'auto', paddingTop: 80,
      }}>
        <div style={{ padding: '24px 24px 48px' }}>
          {navItems.map(item => {
            if (item.type === 'link') {
              return (
                <div key={item.id || item.label} style={{ borderBottom: '1px solid #E5E7EB' }}>
                  <Link to={item.href || '/'} style={{ display: 'block', padding: '20px 0', fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 20, color: '#111827', textDecoration: 'none' }}>{item.label}</Link>
                </div>
              );
            }
            
            return (
              <div key={item.id || item.label} style={{ borderBottom: '1px solid #E5E7EB' }}>
                <button onClick={() => setMobileExpanded(mobileExpanded === item.label ? null : item.label)}
                  style={{ width: '100%', display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '20px 0', background: 'transparent', border: 'none', cursor: 'pointer', fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 20, color: '#111827' }}>
                  {item.label}
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style={{ transform: mobileExpanded === item.label ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s ease', flexShrink: 0 }}>
                    <path d="M3 6l5 5 5-5" stroke="#4B5563" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                </button>
                {mobileExpanded === item.label && (
                  <div style={{ paddingBottom: item.type === 'mega' ? 20 : 16 }}>
                    {item.type === 'mega' && (
                       <div style={{ fontFamily: 'Lexend, sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.1em', textTransform: 'uppercase', marginBottom: 8 }}>
                         Services
                       </div>
                    )}
                    {(item.children || []).map(child => (
                      <Link key={child.label} to={child.href} style={{ display: 'block', padding: item.type === 'mega' ? '6px 0' : '8px 0', fontFamily: 'Lexend, sans-serif', fontSize: 14, color: '#4B5563', textDecoration: 'none' }}>
                        {child.label}
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            );
          })}
          
          <div style={{ marginTop: 32 }}>
            <Link to="/contact" style={{ display: 'block', textAlign: 'center', fontFamily: 'Lexend, sans-serif', fontWeight: 600, fontSize: 15, background: '#1F8844', color: '#FFFFFF', textDecoration: 'none', padding: '14px', borderRadius: 8 }}>Get Free Quote</Link>
          </div>
        </div>
      </div>

      <style>{`
        .mega-link { display: block; padding: 7px 0; font-family: 'Lexend', sans-serif; font-size: 13px; color: #4B5563; text-decoration: none; transition: color 0.15s ease, transform 0.15s ease; }
        .mega-link:hover { color: #111827; transform: translateX(6px); }
        @media (max-width: 1100px) { .desktop-nav { display: none !important; } .desktop-cta { display: none !important; } .mobile-toggle { display: block !important; } }
        @media (min-width: 1101px) { .mobile-toggle { display: none !important; } }
      `}</style>
    </>
  );
};

export default Navbar;

