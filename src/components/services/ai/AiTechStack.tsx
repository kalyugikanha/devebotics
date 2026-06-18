import { motion } from 'framer-motion';
import { Layers } from 'lucide-react';

const TECH_TAGS = ['GPT-4o', 'Claude 3.5', 'PyTorch', 'LangChain', 'Pinecone', 'Next.js', 'Vercel', 'AWS'];

export default function AiTechStack() {
  return (
    <section className="py-32 px-6 bg-white relative">
      <div className="max-w-7xl mx-auto flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">
        
        {/* Left Info */}
        <div className="flex-1">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-xs font-bold tracking-wider uppercase mb-6">
            <Layers className="w-4 h-4" /> Technology
          </div>
          <h2 className="text-4xl md:text-5xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8 font-outfit">
            Built on the Edge.
          </h2>
          <p className="text-lg text-slate-600 leading-relaxed mb-10 max-w-lg font-jakarta">
            We architect robust systems using the latest LLMs, vector databases, and scalable cloud infrastructure. No black boxes, just clean, performant engineering.
          </p>
          
          <div className="flex flex-wrap gap-3">
            {TECH_TAGS.map((t, i) => (
              <motion.span 
                key={t}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: i * 0.05 }}
                className="px-5 py-2.5 rounded-full bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-sm hover:border-slate-300 hover:bg-slate-100 transition-colors font-jakarta"
              >
                {t}
              </motion.span>
            ))}
          </div>
        </div>

        {/* Right Code Window */}
        <motion.div 
          initial={{ opacity: 0, x: 50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8 }}
          className="flex-1 w-full max-w-xl lg:max-w-none"
        >
          <div className="rounded-3xl overflow-hidden bg-slate-900 shadow-2xl shadow-slate-900/20 border border-slate-800">
            {/* Window Header */}
            <div className="bg-slate-800/50 px-6 py-4 border-b border-slate-700/50 flex items-center gap-2">
              <div className="flex gap-2">
                <div className="w-3 h-3 rounded-full bg-red-400" />
                <div className="w-3 h-3 rounded-full bg-amber-400" />
                <div className="w-3 h-3 rounded-full bg-emerald-400" />
              </div>
              <span className="ml-4 text-xs text-slate-400 font-mono">agent.ts</span>
            </div>
            
            {/* Code Content */}
            <div className="p-8 font-mono text-sm leading-loose overflow-x-auto text-slate-300">
              <p><span className="text-pink-400">import</span> {'{'} ChatOpenAI {'}'} <span className="text-pink-400">from</span> <span className="text-emerald-400">"langchain/chat_models"</span>;</p>
              <p className="mb-4"><span className="text-pink-400">import</span> {'{'} PineconeStore {'}'} <span className="text-pink-400">from</span> <span className="text-emerald-400">"langchain/vectorstores"</span>;</p>
              
              <p className="text-slate-500 italic">{'//'} Initialize intelligent agent</p>
              <p><span className="text-cyan-400">const</span> model = <span className="text-pink-400">new</span> ChatOpenAI({'{'}</p>
              <p className="pl-4">modelName: <span className="text-emerald-400">"gpt-4o"</span>,</p>
              <p className="pl-4">temperature: <span className="text-amber-400">0.1</span>,</p>
              <p className="mb-4">{'}'});</p>
              
              <p className="text-slate-500 italic">{'//'} Execute retrieval & generation</p>
              <p><span className="text-cyan-400">const</span> response = <span className="text-pink-400">await</span> agent.execute({'{'}</p>
              <p className="pl-4">task: <span className="text-emerald-400">"Analyze user sentiment and generate report"</span>,</p>
              <p className="pl-4">context: vectorStore.asRetriever()</p>
              <p>{'}'});</p>
            </div>
          </div>
        </motion.div>
        
      </div>
    </section>
  );
}
