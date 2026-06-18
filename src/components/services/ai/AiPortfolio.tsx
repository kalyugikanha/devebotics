import { motion } from 'framer-motion';
import { Briefcase } from 'lucide-react';

const PORTFOLIO = [
  { t: 'Support Auto-Pilot', ind: 'E-commerce', metric: '83% ticket automation', tech: ['GPT-4', 'Pinecone', 'LangChain'], img: '/images/generic-1.png', badge: 'LLM Agent' },
  { t: 'MedVision AI Diagnostics', ind: 'Healthcare', metric: '94% diagnostic accuracy', tech: ['TensorFlow', 'OpenCV'], img: '/images/generic-2.png', badge: 'Computer Vision' },
  { t: 'Predictive Sales Engine', ind: 'SaaS', metric: '±3% forecast accuracy', tech: ['PyTorch', 'Time Series'], img: '/images/generic-1.png', badge: 'Data Science' }
];

export default function AiPortfolio() {
  return (
    <section className="py-32 px-6 bg-[#F8FAFC] relative">
      <div className="max-w-7xl mx-auto">
        
        <div className="text-center mb-20">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 text-violet-600 text-xs font-bold tracking-wider uppercase mb-6">
            <Briefcase className="w-4 h-4" /> Portfolio
          </div>
          <h2 className="text-4xl md:text-5xl font-black text-slate-900 tracking-tight font-outfit">
            Selected Works.
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {PORTFOLIO.map((p, i) => (
            <motion.div 
              key={i}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: i * 0.1 }}
              className="group bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 cursor-pointer hover:-translate-y-2"
            >
              {/* Image Container */}
              <div className="h-64 relative overflow-hidden bg-slate-100">
                <img 
                  src={p.img} 
                  alt={p.t} 
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-xs font-bold text-slate-900 shadow-sm font-jakarta">
                  {p.badge}
                </div>
              </div>

              {/* Content */}
              <div className="p-8">
                <div className="text-xs font-bold text-indigo-600 mb-3 tracking-widest uppercase">{p.ind}</div>
                <h3 className="text-2xl font-bold text-slate-900 mb-6 font-outfit leading-tight group-hover:text-indigo-600 transition-colors">
                  {p.t}
                </h3>
                
                <div className="bg-[#F8FAFC] p-4 rounded-2xl mb-6 border border-slate-100 flex items-center justify-between">
                  <span className="text-slate-500 text-sm font-medium font-jakarta">Result:</span>
                  <span className="text-emerald-500 font-black text-lg">{p.metric}</span>
                </div>
                
                <div className="flex flex-wrap gap-2">
                  {p.tech.map(t => (
                    <span key={t} className="text-xs bg-white border border-slate-200 px-3 py-1.5 rounded-full text-slate-600 font-medium font-jakarta">
                      {t}
                    </span>
                  ))}
                </div>
              </div>
            </motion.div>
          ))}
        </div>
        
      </div>
    </section>
  );
}
