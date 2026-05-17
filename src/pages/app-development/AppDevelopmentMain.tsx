import { useEffect } from 'react';
import AppDevHero from '../../components/AppDevHero';
import AppDevShowcase from '../../components/AppDevShowcase';
import AppDevTechProcess from '../../components/AppDevTechProcess';
import AppDevProof from '../../components/AppDevProof';
import AppDevBottom from '../../components/AppDevBottom';

export default function AppDevelopmentMain() {
  useEffect(() => {
    document.title = 'Mobile App Development Company | DevBotics';
    const meta = document.querySelector('meta[name="description"]');
    if (meta) meta.setAttribute('content', 'Expert mobile app development — React Native, Flutter, iOS & Android. 200+ apps shipped. On-time delivery guaranteed.');

    // Observe .fade-up elements for hero-free sections that still use it
    const obs = new IntersectionObserver(
      entries => entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } }),
      { threshold: 0.12 }
    );
    document.querySelectorAll('.fade-up:not(.visible)').forEach(el => obs.observe(el));
    return () => obs.disconnect();
  }, []);

  return (
    <main style={{ background: '#08090A', minHeight: '100vh', paddingTop: 72 }}>
      <AppDevHero />
      <AppDevShowcase />
      <AppDevTechProcess />
      <AppDevProof />
      <AppDevBottom />
    </main>
  );
}
