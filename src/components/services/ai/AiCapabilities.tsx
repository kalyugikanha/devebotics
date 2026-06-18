import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { 
  Sparkles, Eye, TrendingUp, Zap, MessageSquare, Cpu, ArrowRight 
} from 'lucide-react';

const CAPABILITIES = [
  { id: '01', title: 'Generative AI Integration', desc: 'GPT-4 and Claude powered systems trained on your proprietary data, integrated seamlessly into your workflows.', icon: Sparkles, color: 'from-indigo-500 to-indigo-600', shadow: 'shadow-indigo-500/20', span: 2, slug: 'generative-ai' },
  { id: '02', title: 'Computer Vision', desc: 'Real-time image recognition, quality control, and visual object detection.', icon: Eye, color: 'from-cyan-500 to-cyan-600', shadow: 'shadow-cyan-500/20', span: 1, slug: 'computer-vision' },
  { id: '03', title: 'Predictive Analytics', desc: 'Advanced ML models forecasting sales and detecting anomalies.', icon: TrendingUp, color: 'from-emerald-500 to-emerald-600', shadow: 'shadow-emerald-500/20', span: 1, slug: 'predictive-analytics' },
  { id: '04', title: 'Intelligent Automation', desc: 'Self-learning workflow automation reducing manual operational work by up to 80%.', icon: Zap, color: 'from-amber-500 to-amber-600', shadow: 'shadow-amber-500/20', span: 2, slug: 'intelligent-automation' },
  { id: '05', title: 'Natural Language Processing', desc: 'Deep document understanding, sentiment analysis, and intelligent text extraction.', icon: MessageSquare, color: 'from-pink-500 to-pink-600', shadow: 'shadow-pink-500/20', span: 2, slug: 'natural-language-processing' },
  { id: '06', title: 'Custom Architectures', desc: 'Proprietary ML models built entirely from scratch for specific needs.', icon: Cpu, color: 'from-violet-500 to-violet-600', shadow: 'shadow-violet-500/20', span: 1, slug: 'custom-ai-architectures' }
];

const containerVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: { staggerChildren: 0.1 }
  }
};

const itemVariants = {
  hidden: { opacity: 0, y: 20 },
  visible: {
    opacity: 1, y: 0,
    transition: { duration: 0.6, ease: "easeOut" }
  }
};

export default function AiCapabilities() {
  return (
    <section id="capabilities" className="py-32 px-6 bg-white relative overflow-hidden">
      <div className="max-w-7xl mx-auto relative z-10">
        
        {/* Section Header */}
        <div className="flex flex-col md:flex-row justify-between items-end gap-8 mb-20">
          <div className="max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-600 text-xs font-bold tracking-wider uppercase mb-6">
              <Sparkles className="w-4 h-4" /> Capabilities
            </div>
            <h2 className="text-4xl md:text-5xl font-black text-slate-900 leading-[1.1] tracking-tight font-outfit">
              Bespoke AI,<br/>
              Beautifully Crafted.
            </h2>
          </div>
          <p className="text-lg text-slate-600 max-w-md leading-relaxed font-jakarta">
            We merge cutting-edge machine learning with exceptional design to create products that don't just work—they inspire.
          </p>
        </div>

        {/* Bento Grid */}
        <motion.div 
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          {CAPABILITIES.map((cap, i) => {
            const Icon = cap.icon;
            return (
              <motion.div 
                key={cap.id}
                variants={itemVariants}
                className={`group relative bg-slate-50 rounded-3xl p-8 md:p-10 transition-all duration-500 hover:bg-white hover:-translate-y-2 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.05)] border border-slate-100 ${cap.span === 2 ? 'md:col-span-2 lg:col-span-2' : ''}`}
              >
                {/* Decorative blob */}
                <div className={`absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br ${cap.color} opacity-0 group-hover:opacity-5 blur-2xl rounded-full transition-opacity duration-500`} />
                
                <div className={`w-14 h-14 rounded-2xl bg-white border border-slate-100 flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform duration-500 group-hover:shadow-md`}>
                  <Icon className={`w-7 h-7 text-transparent bg-clip-text bg-gradient-to-br ${cap.color} [&>path]:stroke-[url(#gradient-${cap.id})]`} />
                  
                  {/* SVG Gradient definition for Lucide icons */}
                  <svg width="0" height="0">
                    <linearGradient id={`gradient-${cap.id}`} x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop stopColor={cap.color.split(' ')[0].replace('from-', 'var(--tw-gradient-from)') || '#6366f1'} offset="0%" />
                      <stop stopColor={cap.color.split(' ')[1].replace('to-', 'var(--tw-gradient-to)') || '#a855f7'} offset="100%" />
                    </linearGradient>
                  </svg>
                </div>

                <div className="relative z-10 flex flex-col h-[calc(100%-88px)]">
                  <h3 className="text-2xl font-bold text-slate-900 mb-4 font-outfit">{cap.title}</h3>
                  <p className="text-slate-600 leading-relaxed font-jakarta flex-1 mb-8">
                    {cap.desc}
                  </p>
                  
                  <Link 
                    to={`/ai-services/${cap.slug}`} 
                    className="inline-flex items-center gap-2 font-bold text-sm tracking-wide uppercase transition-colors"
                  >
                    <span className="text-slate-900 group-hover:text-indigo-600 transition-colors">Explore Solution</span>
                    <ArrowRight className="w-4 h-4 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" />
                  </Link>
                </div>
              </motion.div>
            );
          })}
        </motion.div>

      </div>
    </section>
  );
}
