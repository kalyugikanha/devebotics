import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Plus, X, MessageSquare, Send } from 'lucide-react';

const FAQS = [
  { q: "What types of AI systems do you develop?", a: "We build everything from intelligent LLM wrappers to custom-trained computer vision models, predictive engines, and complex NLP pipelines using industry-leading frameworks." },
  { q: "How much data is required to train an AI model?", a: "For LLMs utilizing RAG (Retrieval-Augmented Generation), a few hundred high-quality documents suffice. For training custom ML models from scratch, thousands of well-labeled examples are typically needed." },
  { q: "Can you integrate AI into my existing platform?", a: "Absolutely. Over 90% of our projects involve architecting AI microservices that plug seamlessly into existing Node.js, Python, or Java backends via highly secure REST/GraphQL APIs." }
];

export default function AiContact() {
  const [openFaq, setOpenFaq] = useState<number | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleForm = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setTimeout(() => setIsSubmitting(false), 2000);
  };

  return (
    <section id="consultation" className="py-32 px-6 bg-white relative overflow-hidden">
      
      {/* Background glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-[600px] bg-indigo-500/5 blur-[120px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto relative z-10 flex flex-col lg:flex-row gap-16 lg:gap-24">
        
        {/* Left Side: Text and FAQs */}
        <div className="flex-1">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold tracking-wider uppercase mb-6">
            <MessageSquare className="w-4 h-4" /> Consultation
          </div>
          
          <h2 className="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 leading-[1.05] tracking-tight mb-8 font-outfit">
            Let's Build <br/>
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-cyan-500">
              The Future.
            </span>
          </h2>
          
          <p className="text-lg text-slate-600 mb-16 max-w-md leading-relaxed font-jakarta">
            Share your vision with our AI engineering team. We'll craft a bespoke strategy to bring it to life securely and at scale.
          </p>
          
          <div>
            <h4 className="font-bold text-xl mb-6 text-slate-900 font-outfit">Common Inquiries</h4>
            <div className="flex flex-col gap-4">
              {FAQS.map((f, i) => (
                <div key={i} className="bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden transition-colors hover:border-slate-200">
                  <button 
                    onClick={() => setOpenFaq(openFaq === i ? null : i)}
                    className="w-full text-left px-6 py-5 flex justify-between items-center font-bold text-lg text-slate-900 focus:outline-none"
                  >
                    <span className="font-outfit pr-4">{f.q}</span>
                    <div className={`flex-shrink-0 transition-transform duration-300 ${openFaq === i ? 'rotate-180 text-indigo-500' : 'text-slate-400'}`}>
                      {openFaq === i ? <X className="w-5 h-5" /> : <Plus className="w-5 h-5" />}
                    </div>
                  </button>
                  <AnimatePresence>
                    {openFaq === i && (
                      <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3 }}
                        className="overflow-hidden"
                      >
                        <div className="px-6 pb-6 text-slate-600 font-jakarta leading-relaxed">
                          {f.a}
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </div>
              ))}
            </div>
          </div>
        </div>
        
        {/* Right Side: Form */}
        <div className="flex-[0.8] w-full max-w-lg mx-auto lg:mx-0">
          <form 
            onSubmit={handleForm} 
            className="bg-white p-8 md:p-12 rounded-[2.5rem] border border-slate-100 shadow-[0_40px_80px_-20px_rgba(0,0,0,0.08)] relative overflow-hidden"
          >
            {/* Form Background Accent */}
            <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-500/10 to-transparent rounded-bl-full pointer-events-none" />

            <h3 className="text-3xl font-black mb-8 text-slate-900 font-outfit">Start a Conversation</h3>
            
            <div className="mb-6">
              <label className="block text-xs font-bold text-slate-500 mb-3 tracking-widest font-jakarta">YOUR NAME</label>
              <input 
                type="text" 
                required 
                className="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-slate-900 outline-none transition-all focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                placeholder="John Doe"
              />
            </div>
            
            <div className="mb-6">
              <label className="block text-xs font-bold text-slate-500 mb-3 tracking-widest font-jakarta">WORK EMAIL</label>
              <input 
                type="email" 
                required 
                className="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-slate-900 outline-none transition-all focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                placeholder="john@company.com"
              />
            </div>
            
            <div className="mb-8">
              <label className="block text-xs font-bold text-slate-500 mb-3 tracking-widest font-jakarta">PROJECT OVERVIEW</label>
              <textarea 
                required 
                rows={4} 
                className="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-slate-900 outline-none resize-y transition-all focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                placeholder="Tell us about your objectives..."
              ></textarea>
            </div>
            
            <button 
              type="submit" 
              disabled={isSubmitting} 
              className="group relative w-full flex items-center justify-center gap-2 bg-slate-900 text-white p-5 rounded-full font-bold text-lg overflow-hidden transition-all hover:shadow-[0_15px_30px_-10px_rgba(15,23,42,0.4)] hover:-translate-y-1 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none font-jakarta"
            >
              <span className="relative z-10">{isSubmitting ? 'Processing Request...' : 'Submit Inquiry'}</span>
              {!isSubmitting && <Send className="w-5 h-5 relative z-10 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />}
              <div className="absolute inset-0 bg-gradient-to-r from-indigo-600 to-cyan-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
            </button>
          </form>
        </div>
        
      </div>
    </section>
  );
}
