import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Sparkles, Brain, Cpu, Zap, ArrowRight, Activity } from 'lucide-react';

export default function AiHero() {
  const [chatStep, setChatStep] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setChatStep(s => (s + 1) % 4);
    }, 3500);
    return () => clearInterval(timer);
  }, []);

  return (
    <section className="relative min-h-screen flex items-center pt-28 pb-20 px-6 overflow-hidden bg-[#F8FAFC]">
      {/* Abstract Background Elements */}
      <div className="absolute inset-0 z-0">
        <div className="absolute top-0 -left-1/4 w-full h-full bg-indigo-500/10 blur-[120px] rounded-full mix-blend-multiply" />
        <div className="absolute bottom-0 -right-1/4 w-full h-full bg-cyan-500/10 blur-[120px] rounded-full mix-blend-multiply" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-1/2 h-1/2 bg-pink-500/10 blur-[120px] rounded-full mix-blend-multiply" />
        
        {/* Grid pattern */}
        <div className="absolute inset-0 bg-[linear-gradient(rgba(15,23,42,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,0.03)_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_40%,transparent_100%)] pointer-events-none" />
      </div>

      <div className="max-w-7xl mx-auto w-full relative z-10 flex flex-col lg:flex-row items-center gap-16">
        
        {/* Left Content */}
        <div className="flex-1 text-center lg:text-left">
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 shadow-sm mb-8"
          >
            <span className="w-2 h-2 rounded-full bg-indigo-500 animate-pulse" />
            <span className="text-xs font-bold tracking-wider text-indigo-600 uppercase">Next-Gen AI Agency</span>
          </motion.div>

          <motion.h1 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="text-5xl md:text-7xl font-black text-slate-900 leading-[1.05] tracking-tight mb-6 font-outfit"
          >
            Intelligence <br className="hidden md:block" />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-cyan-500 to-pink-500">
              By Design.
            </span><br />
            Engineered for You.
          </motion.h1>

          <motion.p 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-jakarta"
          >
            We design and engineer bespoke AI systems, beautiful intelligent interfaces, and highly capable autonomous agents for visionary companies.
          </motion.p>

          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.3 }}
            className="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4"
          >
            <a href="#consultation" className="group relative inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900 text-white rounded-full font-semibold overflow-hidden transition-all hover:scale-105 hover:shadow-[0_15px_30px_-5px_rgba(15,23,42,0.3)]">
              <span className="relative z-10">Initiate Project</span>
              <ArrowRight className="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform" />
              <div className="absolute inset-0 bg-gradient-to-r from-indigo-600 to-cyan-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
            </a>
            <a href="#capabilities" className="inline-flex items-center justify-center px-8 py-4 bg-white/80 backdrop-blur-md text-slate-900 border border-slate-200 rounded-full font-semibold transition-all hover:bg-white hover:shadow-lg hover:-translate-y-1">
              Explore Capabilities
            </a>
          </motion.div>

          {/* Social Proof */}
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 1, delay: 0.6 }}
            className="mt-12 flex items-center justify-center lg:justify-start gap-4"
          >
            <div className="flex -space-x-4">
              {[1, 2, 3, 4].map((i) => (
                <img 
                  key={i} 
                  src={`https://i.pravatar.cc/100?img=${i + 10}`} 
                  alt="User" 
                  className="w-10 h-10 rounded-full border-2 border-white shadow-sm relative z-10"
                  style={{ zIndex: 10 - i }}
                />
              ))}
            </div>
            <div className="text-sm font-medium text-slate-500 font-jakarta">
              Trusted by <span className="font-bold text-slate-900">50+</span> innovative teams
            </div>
          </motion.div>
        </div>

        {/* Right Content - Interactive UI Mockup */}
        <motion.div 
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.8, delay: 0.2 }}
          className="flex-1 relative w-full max-w-lg"
        >
          {/* Abstract floating shapes */}
          <motion.div 
            animate={{ rotate: 360 }}
            transition={{ duration: 40, repeat: Infinity, ease: "linear" }}
            className="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-indigo-500 to-cyan-500 rounded-[30%_70%_70%_30%/30%_30%_70%_70%] opacity-20 blur-xl"
          />

          {/* Glass Card Mockup */}
          <motion.div 
            animate={{ y: [-10, 10, -10] }}
            transition={{ duration: 6, repeat: Infinity, ease: "easeInOut" }}
            className="relative bg-white/60 backdrop-blur-2xl border border-white/80 rounded-[2rem] p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05),inset_0_1px_0_rgba(255,255,255,1)]"
          >
            {/* Mac Window Dots */}
            <div className="flex gap-2 pb-4 border-b border-slate-100 mb-6">
              <div className="w-3 h-3 rounded-full bg-red-400" />
              <div className="w-3 h-3 rounded-full bg-amber-400" />
              <div className="w-3 h-3 rounded-full bg-emerald-400" />
            </div>

            <div className="flex flex-col gap-4 min-h-[320px] font-jakarta">
              {chatStep > 0 && (
                <motion.div 
                  initial={{ opacity: 0, y: 10, scale: 0.95 }}
                  animate={{ opacity: 1, y: 0, scale: 1 }}
                  className="self-end bg-slate-900 text-white py-3 px-5 rounded-2xl rounded-tr-sm text-sm shadow-md max-w-[85%]"
                >
                  Analyze our Q3 sales data and project Q4.
                </motion.div>
              )}

              {chatStep > 1 && (
                <motion.div 
                  initial={{ opacity: 0, y: 10, scale: 0.95 }}
                  animate={{ opacity: 1, y: 0, scale: 1 }}
                  className="self-start bg-white border border-slate-100 p-5 rounded-2xl rounded-tl-sm shadow-xl shadow-slate-200/50 w-[95%]"
                >
                  <div className="flex items-center gap-3 mb-4">
                    <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-500 flex items-center justify-center text-white">
                      <Sparkles className="w-4 h-4" />
                    </div>
                    <span className="font-bold text-slate-900 font-outfit">DevBotics AI</span>
                  </div>
                  
                  <p className="text-slate-600 text-sm mb-4 leading-relaxed">
                    Q3 growth was strong. Based on trajectory and seasonality, Q4 projected revenue is <span className="font-bold text-emerald-500 bg-emerald-50 px-1 py-0.5 rounded">+22% higher</span>.
                  </p>

                  {/* Mini Chart visualization */}
                  <div className="flex items-end gap-2 h-24 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div className="flex-1 bg-slate-200 h-[40%] rounded-md" />
                    <div className="flex-1 bg-slate-300 h-[60%] rounded-md" />
                    <div className="flex-1 bg-slate-400 h-[85%] rounded-md" />
                    <div className="flex-1 bg-gradient-to-t from-indigo-500 to-cyan-400 h-[100%] rounded-md relative shadow-[0_0_15px_rgba(99,102,241,0.3)]">
                      <div className="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded shadow-md">
                        Q4
                      </div>
                    </div>
                  </div>
                </motion.div>
              )}

              {chatStep === 1 && (
                <motion.div 
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  className="self-start bg-white border border-slate-100 text-slate-500 py-3 px-5 rounded-2xl rounded-tl-sm text-sm flex items-center gap-2 shadow-sm"
                >
                  <div className="flex gap-1">
                    <span className="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style={{ animationDelay: '0ms' }} />
                    <span className="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style={{ animationDelay: '150ms' }} />
                    <span className="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style={{ animationDelay: '300ms' }} />
                  </div>
                  <span className="ml-1">Processing models...</span>
                </motion.div>
              )}
            </div>

            {/* Mock Input Area */}
            <div className="mt-6 bg-slate-50 border border-slate-100 rounded-full p-2 pl-5 flex justify-between items-center text-slate-400 text-sm">
              <span>Ask the intelligent agent...</span>
              <div className="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-md">
                <ArrowRight className="w-4 h-4 -rotate-45" />
              </div>
            </div>
          </motion.div>

          {/* Floating Processing Badge */}
          <motion.div 
            animate={{ y: [10, -10, 10] }}
            transition={{ duration: 5, repeat: Infinity, ease: "easeInOut", delay: 1 }}
            className="absolute -bottom-6 -left-6 bg-white/80 backdrop-blur-xl border border-white p-3 pr-5 rounded-full flex items-center gap-3 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)]"
          >
            <div className="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
              <Zap className="w-5 h-5 fill-current" />
            </div>
            <div>
              <div className="text-[10px] font-bold text-slate-400 tracking-wider">PROCESSING</div>
              <div className="text-sm font-bold text-slate-900 font-outfit">&lt; 50ms Latency</div>
            </div>
          </motion.div>

        </motion.div>
      </div>
    </section>
  );
}
