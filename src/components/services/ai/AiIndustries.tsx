import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Activity, ShoppingCart, Stethoscope, Landmark, Briefcase, ArrowRight } from 'lucide-react';

const INDUSTRIES = [
  { id: 'ecommerce', name: 'E-commerce', icon: ShoppingCart, apps: ['Smart product recommendations', 'Visual search', 'Demand forecasting', 'Customer service chatbot'], metric: 'AI recommendations increased AOV by 47%', color: 'indigo' },
  { id: 'healthcare', name: 'Healthcare', icon: Stethoscope, apps: ['Medical image analysis', 'Patient triage automation', 'EHR parsing', 'Predictive readmission models'], metric: 'Reduced administrative overhead by 62%', color: 'cyan' },
  { id: 'finance', name: 'Finance', icon: Landmark, apps: ['Fraud detection', 'Automated credit scoring', 'Algorithmic trading', 'Document KYC automation'], metric: 'Fraud detection accuracy improved to 99.8%', color: 'emerald' },
  { id: 'saas', name: 'Enterprise SaaS', icon: Briefcase, apps: ['Churn prediction models', 'Automated ticket routing', 'Generative AI content', 'Smart search & indexing'], metric: 'Customer retention increased by 28%', color: 'amber' }
];

const colorMap: Record<string, string> = {
  indigo: 'bg-indigo-500 shadow-indigo-500/40 text-indigo-500 border-indigo-500',
  cyan: 'bg-cyan-500 shadow-cyan-500/40 text-cyan-500 border-cyan-500',
  emerald: 'bg-emerald-500 shadow-emerald-500/40 text-emerald-500 border-emerald-500',
  amber: 'bg-amber-500 shadow-amber-500/40 text-amber-500 border-amber-500',
};

const gradientMap: Record<string, string> = {
  indigo: 'from-indigo-500 to-indigo-700',
  cyan: 'from-cyan-500 to-cyan-700',
  emerald: 'from-emerald-500 to-emerald-700',
  amber: 'from-amber-500 to-amber-700',
};

export default function AiIndustries() {
  const [activeTab, setActiveTab] = useState(0);

  return (
    <section className="py-32 px-6 bg-[#F8FAFC] relative overflow-hidden">
      <div className="max-w-7xl mx-auto relative z-10">
        
        <div className="text-center mb-20">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold tracking-wider uppercase mb-6">
            <Activity className="w-4 h-4" /> Industries
          </div>
          <h2 className="text-4xl md:text-5xl font-black text-slate-900 tracking-tight font-outfit">
            Impact Across Sectors.
          </h2>
        </div>

        {/* Tabs */}
        <div className="flex justify-center gap-3 flex-wrap mb-16">
          {INDUSTRIES.map((ind, i) => {
            const Icon = ind.icon;
            const isActive = activeTab === i;
            return (
              <button
                key={ind.id}
                onClick={() => setActiveTab(i)}
                className={`flex items-center gap-2 px-6 py-4 rounded-full font-bold transition-all duration-300 font-jakarta ${
                  isActive 
                    ? 'bg-slate-900 text-white shadow-[0_10px_20px_rgba(15,23,42,0.15)] scale-105' 
                    : 'bg-white text-slate-500 hover:text-slate-900 hover:bg-slate-50 border border-slate-100 shadow-sm'
                }`}
              >
                <Icon className={`w-5 h-5 ${isActive ? 'text-white' : 'text-slate-400'}`} />
                {ind.name}
              </button>
            );
          })}
        </div>

        {/* Content Area */}
        <div className="bg-white rounded-[2.5rem] p-8 md:p-12 border border-slate-100 shadow-xl shadow-slate-200/50">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeTab}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              transition={{ duration: 0.4 }}
              className="flex flex-col lg:flex-row gap-12 lg:gap-20 items-stretch"
            >
              
              {/* Left List */}
              <div className="flex-1">
                <h3 className="text-3xl font-bold text-slate-900 mb-8 font-outfit">Key Applications</h3>
                <div className="flex flex-col gap-4">
                  {INDUSTRIES[activeTab].apps.map((app, i) => (
                    <motion.div 
                      key={i}
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      transition={{ duration: 0.4, delay: i * 0.1 }}
                      className="flex items-center gap-5 p-5 bg-slate-50 rounded-2xl border border-slate-100 group hover:bg-white hover:shadow-md transition-all"
                    >
                      <div className={`w-3 h-3 rounded-full ${colorMap[INDUSTRIES[activeTab].color].split(' ')[0]} shadow-lg ${colorMap[INDUSTRIES[activeTab].color].split(' ')[1]} transition-transform group-hover:scale-150`} />
                      <span className="text-lg text-slate-700 font-medium font-jakarta">{app}</span>
                    </motion.div>
                  ))}
                </div>
              </div>

              {/* Right Hero Metric */}
              <div className="flex-1 flex flex-col justify-center">
                <div className={`relative overflow-hidden bg-gradient-to-br ${gradientMap[INDUSTRIES[activeTab].color]} p-12 rounded-[2rem] text-white shadow-2xl ${colorMap[INDUSTRIES[activeTab].color].split(' ')[1]}`}>
                  <div className="absolute -top-10 -right-10 text-[200px] leading-none opacity-10 font-serif">&ldquo;</div>
                  <div className="relative z-10">
                    <div className="text-sm font-bold tracking-[0.2em] mb-6 opacity-80">MEASURABLE IMPACT</div>
                    <h4 className="text-4xl md:text-5xl font-black leading-tight mb-10 font-outfit">
                      {INDUSTRIES[activeTab].metric}
                    </h4>
                    <button className="group flex items-center gap-2 bg-white text-slate-900 px-8 py-4 rounded-full font-bold hover:scale-105 transition-transform">
                      Read Case Study
                      <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </button>
                  </div>
                </div>
              </div>

            </motion.div>
          </AnimatePresence>
        </div>

      </div>
    </section>
  );
}
