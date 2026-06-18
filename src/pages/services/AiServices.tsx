import { useEffect } from 'react';
import AiHero from '../../components/services/ai/AiHero';
import AiCapabilities from '../../components/services/ai/AiCapabilities';
import AiIndustries from '../../components/services/ai/AiIndustries';
import AiTechStack from '../../components/services/ai/AiTechStack';
import AiPortfolio from '../../components/services/ai/AiPortfolio';
import AiContact from '../../components/services/ai/AiContact';

export default function AiServices() {
  useEffect(() => {
    document.title = 'AI Innovation | DevBotics';
    window.scrollTo(0, 0);
  }, []);

  return (
    <main className="bg-white min-h-screen text-slate-900 overflow-x-hidden selection:bg-indigo-500/20">
      <AiHero />
      <AiCapabilities />
      <AiIndustries />
      <AiTechStack />
      <AiPortfolio />
      <AiContact />
    </main>
  );
}
