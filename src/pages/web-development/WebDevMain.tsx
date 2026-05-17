import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { motion, useScroll, useTransform, useInView, AnimatePresence } from 'framer-motion';
import { 
  ArrowRight, Check, Code, Cpu, Globe, Layers, 
  Layout, Monitor, Smartphone, Zap, ChevronDown, 
  Send, BarChart2, Server, Terminal, Settings,
  CheckCircle2, ArrowUpRight, Star, ShoppingBag, Heart, Building2, Briefcase, BookOpen, Clock
} from 'lucide-react';

declare global {
  namespace JSX {
    interface IntrinsicElements {
      'spline-viewer': any;
    }
  }
}

const FAQ_ITEMS = [
  { q: "How long does a website take to build?", a: "A standard business site takes 3–4 weeks. An e-commerce or custom web app takes 6–12 weeks depending on features and complexity." },
  { q: "Do you work with startups or only enterprises?", a: "We work with both. Our solutions are scalable, meaning we can build MVPs for startups and complex architecture for enterprises." },
  { q: "What's included in your development process?", a: "Everything from technical architecture, UI/UX design, frontend & backend development, QA testing, SEO setup, to launch and 60-day post-launch support." },
  { q: "Can you redesign an existing website?", a: "Absolutely. We perform a technical and visual audit, then redesign the platform to improve conversion rates and technical performance." },
  { q: "Do you provide hosting and maintenance?", a: "We set up optimal hosting (Vercel, AWS, etc.) owned by you, and offer optional maintenance retainers for ongoing updates and scaling." },
  { q: "What technologies do you specialize in?", a: "We specialize in the modern JavaScript ecosystem (React, Next.js, Node.js), headless CMS, Shopify, and robust PHP frameworks like Laravel." },
  { q: "How do you ensure website performance?", a: "We use a performance-first approach: edge caching, image optimization, code splitting, and strict adherence to Core Web Vitals to guarantee 90+ Lighthouse scores." },
  { q: "What is your pricing structure?", a: "Projects start at $5k for standard sites and $15k+ for custom apps. We offer transparent, milestone-based pricing with no hidden fees." }
];

const SERVICES = [
  { t: "Shopify Stores", img: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80", tags: ["Liquid", "Headless", "Hydrogen"] },
  { t: "Next.js Apps", img: "https://images.unsplash.com/photo-1555421689-491a97ff2040?w=800&q=80", tags: ["React", "SSR", "Vercel"] },
  { t: "SaaS Platforms", img: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80", tags: ["Node.js", "Stripe", "Auth"] },
  { t: "WordPress Sites", img: "https://images.unsplash.com/photo-1616423640778-28d1b53229bd?w=800&q=80", tags: ["Custom Themes", "ACF"] },
  { t: "E-commerce", img: "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800&q=80", tags: ["WooCommerce", "B2B"] },
  { t: "Custom Web Apps", img: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&q=80", tags: ["Full-Stack", "API"] }
];

const PORTFOLIO = [
  { name: "Fintech Dashboard", industry: "Finance", metric: "340% revenue increase", stack: ["Next.js", "Tailwind"], img: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80" },
  { name: "Eco Commerce", industry: "Retail", metric: "1.2s avg load time", stack: ["Shopify", "Liquid"], img: "https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&q=80" },
  { name: "Health Tech Portal", industry: "Healthcare", metric: "HIPAA Compliant", stack: ["React", "Node.js"], img: "https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80" },
];

const BLOG_POSTS = [
  { category: "Performance", title: "The Architecture Behind Sub-Second Page Loads", date: "Oct 12", readTime: "5 min read", img: "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&q=80" },
  { category: "Design Systems", title: "Building Scalable Component Libraries in React", date: "Oct 08", readTime: "8 min read", img: "https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=800&q=80" },
  { category: "E-Commerce", title: "Headless Shopify vs Standard: What to Choose", date: "Sep 28", readTime: "6 min read", img: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80" }
];

const TECH_CATEGORIES = [
  { name: 'Frontend', count: 6, tools: [{ n: 'Next.js', d: 'Primary React framework', p: 95 }, { n: 'React.js', d: 'Component-based UI', p: 98 }, { n: 'Tailwind CSS', d: 'Utility-first styling', p: 97 }, { n: 'TypeScript', d: 'Type-safe JavaScript', p: 90 }] },
  { name: 'Backend', count: 5, tools: [{ n: 'Node.js', d: 'Scalable server-side logic', p: 95 }, { n: 'Laravel', d: 'PHP MVC framework', p: 88 }, { n: 'Express.js', d: 'Minimal Node web framework', p: 93 }, { n: 'GraphQL', d: 'Flexible API queries', p: 82 }] },
  { name: 'CMS & E-commerce', count: 4, tools: [{ n: 'Shopify', d: 'Custom headless stores', p: 95 }, { n: 'WordPress', d: 'Custom themes & plugins', p: 92 }, { n: 'Strapi', d: 'Headless CMS', p: 85 }, { n: 'WooCommerce', d: 'WP e-commerce', p: 90 }] }
];

const INDUSTRIES = [
  { icon: ShoppingBag, name: 'E-commerce & Retail', desc: 'High-converting headless storefronts and robust multi-vendor platforms.' },
  { icon: Briefcase, name: 'B2B & SaaS', desc: 'Complex web applications with multi-tenant architecture and secure billing.' },
  { icon: Heart, name: 'Healthcare Tech', desc: 'HIPAA-compliant patient portals and secure medical dashboards.' },
  { icon: Building2, name: 'Finance & Fintech', desc: 'Highly secure, real-time financial dashboards and banking interfaces.' }
];

const TECH_LOGOS = [
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mongodb/mongodb-original.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/amazonwebservices/amazonwebservices-original-wordmark.svg",
  "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/wordpress/wordpress-plain.svg"
];

function Hero3DMockup() {
  return (
    <motion.div 
      className="relative w-full aspect-[4/3] max-w-[600px] mx-auto z-10"
      style={{ perspective: 1200 }}
      animate={{ y: [-10, 10, -10] }}
      transition={{ repeat: Infinity, duration: 6, ease: "easeInOut" }}
    >
      <motion.div 
        className="w-full h-full relative"
        animate={{ rotateX: [2, -2, 2], rotateY: [-5, 5, -5] }}
        transition={{ repeat: Infinity, duration: 15, ease: "easeInOut" }}
        style={{ transformStyle: 'preserve-3d' }}
      >
        {/* Soft shadow */}
        <div className="absolute -bottom-20 left-10 right-10 h-10 bg-black/5 blur-2xl rounded-full" style={{ transform: 'translateZ(-150px)' }} />
        
        {/* Main Glass Screen */}
        <div className="absolute inset-0 rounded-2xl border border-white/60 bg-white/40 backdrop-blur-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden" style={{ transform: 'translateZ(0px)' }}>
           {/* Header */}
           <div className="h-10 bg-white/50 backdrop-blur-md border-b border-white/30 flex items-center px-4 gap-2">
             <div className="w-3 h-3 rounded-full bg-[#FF5F56] shadow-sm" />
             <div className="w-3 h-3 rounded-full bg-[#FFBD2E] shadow-sm" />
             <div className="w-3 h-3 rounded-full bg-[#27C93F] shadow-sm" />
             <div className="ml-4 px-3 py-1 bg-white/40 rounded-md text-[10px] font-mono text-gray-500 border border-white/20">devbotics.in/build</div>
           </div>
           {/* Content */}
           <div className="relative w-full h-[calc(100%-40px)]">
             <img src="https://images.unsplash.com/photo-1614729939124-032f0b56c9ce?w=800&q=80" className="w-full h-full object-cover opacity-90 mix-blend-multiply" alt="Dashboard" />
             <div className="absolute inset-0 bg-gradient-to-t from-white/80 via-transparent to-transparent" />
           </div>
        </div>

        {/* Floating UI Element 1 */}
        <motion.div 
          className="absolute -right-12 top-1/4 w-48 bg-white/90 backdrop-blur-xl p-4 rounded-xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)] border border-white"
          style={{ transform: 'translateZ(60px)' }}
          animate={{ y: [-5, 5, -5] }}
          transition={{ repeat: Infinity, duration: 4, ease: "easeInOut", delay: 1 }}
        >
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 rounded-full bg-[#FAFAFA] border border-gray-100 flex items-center justify-center shadow-inner">
              <BarChart2 size={18} className="text-[#0A0B0D]" />
            </div>
            <div>
              <div className="text-[10px] font-medium text-[#6B6F76] uppercase tracking-wider">Revenue</div>
              <div className="text-sm font-black text-[#0A0B0D]">+$12,450</div>
            </div>
          </div>
          <div className="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
            <motion.div 
              className="h-full bg-[#E8FF47] w-3/4 rounded-full"
              initial={{ width: 0 }}
              animate={{ width: "75%" }}
              transition={{ duration: 1.5, delay: 0.5 }}
            />
          </div>
        </motion.div>

        {/* Floating Code Snippet */}
        <motion.div 
          className="absolute -left-16 bottom-1/4 w-56 bg-[#0A0B0D]/95 backdrop-blur-xl p-4 rounded-xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.2)] border border-[#1C1E21]"
          style={{ transform: 'translateZ(90px)' }}
          animate={{ y: [5, -5, 5] }}
          transition={{ repeat: Infinity, duration: 5, ease: "easeInOut", delay: 0.5 }}
        >
          <div className="flex gap-2 mb-3">
             <div className="w-2.5 h-2.5 rounded-full bg-[#FF5F56]/80" />
             <div className="w-2.5 h-2.5 rounded-full bg-[#FFBD2E]/80" />
             <div className="w-2.5 h-2.5 rounded-full bg-[#27C93F]/80" />
          </div>
          <div className="font-mono text-[11px] leading-relaxed text-[#A0AEC0]">
            <span className="text-[#C678DD]">import</span> {'{'} optimize {'}'} <span className="text-[#C678DD]">from</span> <span className="text-[#98C379]">'@devbotics/core'</span>;<br/><br/>
            <span className="text-[#C678DD]">const</span> <span className="text-[#E5C07B]">build</span> = <span className="text-[#56B6C2]">async</span> () =&gt; {'{\n'}
            &nbsp;&nbsp;<span className="text-[#C678DD]">await</span> system.<span className="text-[#61AFEF]">optimize</span>();{'\n'}
            &nbsp;&nbsp;<span className="text-[#C678DD]">return</span> <span className="text-[#98C379]">'production-ready'</span>;{'\n'}
            {'}'}
          </div>
        </motion.div>

        {/* Floating Accent Spheres */}
        <motion.div 
          className="absolute -top-12 -right-8 w-24 h-24 rounded-full bg-gradient-to-tr from-[#E8FF47] to-white opacity-60 blur-md mix-blend-multiply"
          style={{ transform: 'translateZ(-60px)' }}
          animate={{ scale: [1, 1.1, 1] }}
          transition={{ repeat: Infinity, duration: 3 }}
        />
        <motion.div 
          className="absolute -bottom-8 -left-8 w-16 h-16 rounded-full bg-gradient-to-tr from-[#E8FF47] to-transparent opacity-40 blur-lg"
          style={{ transform: 'translateZ(40px)' }}
        />
      </motion.div>
    </motion.div>
  );
}

function Hero3DSpline() {
  return (
    <div className="relative w-full h-[500px] flex items-center justify-center -mr-10 xl:-mr-20">
      <div className="absolute inset-0 z-0 opacity-20 bg-[radial-gradient(circle_at_center,rgba(232,255,71,0.5),transparent_70%)] blur-3xl" />
      <div className="w-full h-full relative z-10 scale-110">
        <spline-viewer url="https://prod.spline.design/6Wq1Q7YGyM-iab9i/scene.splinecode"></spline-viewer>
      </div>
    </div>
  );
}

function OrbitingLogos() {
  return (
    <div className="relative w-full aspect-square max-w-[400px] mx-auto z-10 flex items-center justify-center scale-[0.6] sm:scale-75 md:scale-90 lg:scale-100 origin-center">
      <div className="absolute inset-4 rounded-full border border-gray-200/50" />
      <div className="absolute inset-16 rounded-full border border-gray-200/30" />
      
      <motion.div 
        className="absolute inset-0"
        animate={{ rotate: 360 }}
        transition={{ repeat: Infinity, duration: 25, ease: "linear" }}
      >
        {TECH_LOGOS.map((logo, i) => {
          const angle = (i / TECH_LOGOS.length) * Math.PI * 2;
          const radius = 170; // 2D radius
          const x = Math.sin(angle) * radius;
          const y = Math.cos(angle) * radius;
          
          return (
            <motion.div 
              key={i}
              className="absolute top-1/2 left-1/2 w-14 h-14 -mt-7 -ml-7 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center overflow-hidden transition-all hover:scale-125 hover:shadow-xl"
              style={{ x, y }}
              animate={{ rotate: -360 }}
              transition={{ repeat: Infinity, duration: 25, ease: "linear" }}
            >
              <img src={logo} alt="Tech" className="w-7 h-7 object-contain" />
            </motion.div>
          );
        })}
      </motion.div>
      {/* Center Element */}
      <div className="relative w-24 h-24 bg-gradient-to-br from-[#FAFAFA] to-gray-100 rounded-full shadow-xl border border-white flex items-center justify-center z-20">
         <Globe className="text-[#0A0B0D] w-8 h-8 opacity-20" />
      </div>
    </div>
  );
}

function Portfolio3DGrid() {
  return (
    <div className="grid lg:grid-cols-3 gap-8 max-w-[1400px] mx-auto px-6">
      {PORTFOLIO.map((item, i) => (
        <motion.div 
          key={i} 
          className="relative group perspective-[1500px]"
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8, delay: i * 0.2 }}
        >
          <motion.div 
            className="bg-white rounded-[2rem] p-4 shadow-xl shadow-gray-200/50 border border-gray-100 transform-style-3d transition-all duration-500 hover:shadow-2xl hover:shadow-[#E8FF47]/20"
            whileHover={{ rotateX: 5, rotateY: -5, translateZ: 20 }}
          >
            {/* 3D Animated Device Mockup */}
            <div className="relative w-full aspect-[4/3] bg-gray-900 rounded-2xl overflow-hidden mb-6 border-4 border-gray-900 shadow-inner group-hover:border-black transition-colors">
              <div className="absolute top-0 left-0 w-full h-6 bg-gray-800 flex items-center px-3 gap-1.5 z-10">
                 <div className="w-2 h-2 rounded-full bg-red-400" />
                 <div className="w-2 h-2 rounded-full bg-yellow-400" />
                 <div className="w-2 h-2 rounded-full bg-green-400" />
              </div>
              <div className="absolute inset-0 pt-6 bg-black overflow-hidden">
                {/* Scrolling website effect */}
                <motion.div 
                  className="w-full"
                  animate={{ y: ["0%", "-40%", "0%"] }}
                  transition={{ repeat: Infinity, duration: 15 + i * 2, ease: "linear" }}
                >
                  <img src={item.img} alt={item.name} className="w-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />
                  <img src={item.img} alt={item.name} className="w-full object-cover opacity-80 group-hover:opacity-100 transition-opacity scale-y-[-1]" />
                </motion.div>
              </div>
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-10 pointer-events-none" />
              <div className="absolute bottom-4 left-4 z-20">
                <div className="bg-[#E8FF47] text-[#0A0B0D] px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1 shadow-lg">
                  <ArrowUpRight size={14} /> {item.metric}
                </div>
              </div>
            </div>
            
            {/* Content */}
            <div className="px-2 pb-2" style={{ transform: 'translateZ(30px)' }}>
              <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-widest mb-2">{item.industry}</div>
              <h3 className="text-2xl font-black text-[#0A0B0D] mb-4">{item.name}</h3>
              <div className="flex gap-2 flex-wrap">
                {item.stack.map(s => (
                  <span key={s} className="text-xs px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg font-medium border border-gray-100">{s}</span>
                ))}
              </div>
            </div>
          </motion.div>
        </motion.div>
      ))}
    </div>
  );
}

export default function WebDevMain() {
  const [openFaq, setOpenFaq] = useState<number | null>(0);
  const [activeTech, setActiveTech] = useState(0);
  const [codeTyping, setCodeTyping] = useState('');
  const fullCode = `// System Architecture\nconst stack = {\n  frontend: 'Next.js 14',\n  styling: 'Tailwind CSS',\n  state: 'Zustand',\n  backend: 'Node/Express',\n  db: 'PostgreSQL'\n};\n\nasync function buildPlatform() {\n  await Audit.run();\n  const sys = new DesignSystem();\n  return compile(sys);\n}\n\nbuildPlatform().then(launch);`;

  useEffect(() => {
    let i = 0;
    const interval = setInterval(() => {
      setCodeTyping(fullCode.slice(0, i));
      i++;
      if (i > fullCode.length) i = 0; // loop for demo
    }, 50);
    return () => clearInterval(interval);
  }, [fullCode]);

  useEffect(() => {
    document.title = 'Premium Web Development | DevBotics';
    window.scrollTo(0, 0);

    // Load Spline viewer
    if (!document.querySelector('script[src="https://unpkg.com/@splinetool/viewer@1.9.72/build/spline-viewer.js"]')) {
      const script = document.createElement('script');
      script.type = 'module';
      script.src = 'https://unpkg.com/@splinetool/viewer@1.9.72/build/spline-viewer.js';
      document.head.appendChild(script);
    }
  }, []);

  return (
    <div className="bg-[#FAFAFA] min-h-screen text-[#0A0B0D] font-sans selection:bg-[#E8FF47] selection:text-black overflow-x-hidden pt-20">
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;700&display=swap');
        
        .font-sans { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .glass-card {
          background: rgba(255, 255, 255, 0.7);
          backdrop-filter: blur(20px);
          -webkit-backdrop-filter: blur(20px);
          border: 1px solid rgba(255, 255, 255, 0.8);
          box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }
        
        .gradient-mesh {
          position: absolute;
          inset: 0;
          background-image: 
            radial-gradient(at 10% 10%, rgba(232, 255, 71, 0.1) 0px, transparent 50%),
            radial-gradient(at 90% 10%, rgba(100, 150, 255, 0.05) 0px, transparent 50%),
            radial-gradient(at 50% 90%, rgba(200, 100, 255, 0.05) 0px, transparent 50%);
          z-index: 0;
          pointer-events: none;
        }

        .text-gradient {
          background: linear-gradient(135deg, #0A0B0D 0%, #3A3D42 100%);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      `}</style>

      {/* 1. HERO SECTION */}
      <section className="relative min-h-[calc(100vh-80px)] flex items-center pt-10 pb-20 overflow-hidden">
        <div className="gradient-mesh" />
        <div className="max-w-[1400px] mx-auto px-6 w-full relative z-10 grid lg:grid-cols-12 gap-12 items-center">
          <div className="lg:col-span-7">
            <div className="flex items-center gap-3 mb-6">
              <span className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em]">Web Development Services</span>
              <div className="h-px bg-gray-200 w-12" />
            </div>
            
            <h1 className="text-[clamp(48px,6vw,80px)] leading-[1.05] font-black text-[#0A0B0D] tracking-tight mb-8">
              Engineering the Next <br/>Digital <span className="italic relative inline-block">Standard.<span className="absolute bottom-2 left-0 w-full h-4 bg-[#E8FF47] -z-10 mix-blend-multiply rounded-sm" /></span>
            </h1>
            
            <p className="text-lg text-[#6B6F76] leading-[1.7] max-w-xl mb-10 font-medium">
              Leveraging precision-built design systems to reduce complexity. We transform bloated websites into lean, scalable, and performant platforms.
            </p>
            
            <div className="flex flex-wrap items-center gap-6">
              <Link to="/portfolio" className="bg-[#0A0B0D] text-white px-8 py-4 font-bold text-sm tracking-wide rounded-none hover:bg-[#E8FF47] hover:text-[#0A0B0D] transition-colors duration-300 flex items-center gap-2 group">
                VIEW OUR WORK <ArrowRight size={16} className="group-hover:translate-x-1 transition-transform" />
              </Link>
              <div className="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                <CheckCircle2 size={16} className="text-[#E8FF47] fill-black" />
                <span className="text-xs font-bold uppercase tracking-wider text-[#0A0B0D]">Tech Accredited</span>
              </div>
            </div>
          </div>
          
          <div className="lg:col-span-5 relative w-full h-full mt-16 lg:mt-0">
            <Hero3DMockup />
          </div>
        </div>
      </section>

      {/* 2. METRICS BAR */}
      <section className="border-y border-gray-200 bg-white relative z-20">
        <div className="max-w-[1400px] mx-auto flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gray-200">
          {[
            { num: "150+", label: "Websites Shipped", icon: <Globe className="text-[#E8FF47] w-6 h-6 fill-black/10" /> },
            { num: "98%", label: "Lighthouse Score", icon: <Zap className="text-[#E8FF47] w-6 h-6 fill-black/10" /> },
            { num: "4.9★", label: "Client Rated", icon: <Star className="text-[#E8FF47] w-6 h-6 fill-black/10" /> }
          ].map((m, i) => (
            <div key={i} className="flex-1 py-8 px-6 flex items-center justify-center gap-6 group cursor-default">
              <div className="p-3 bg-gray-50 rounded-xl group-hover:scale-110 transition-transform duration-300">
                {m.icon}
              </div>
              <div>
                <div className="text-4xl font-black text-[#0A0B0D] mb-1 tracking-tight">{m.num}</div>
                <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-widest">{m.label}</div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* 3. CORE EXPERTISE */}
      <section className="py-32 relative">
        <div className="max-w-[1400px] mx-auto px-6">
          <div className="mb-20">
            <div className="inline-block relative">
              <span className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em]">Core Expertise</span>
              <motion.div 
                className="absolute -bottom-2 left-0 h-0.5 bg-[#E8FF47]"
                initial={{ width: 0 }}
                whileInView={{ width: "100%" }}
                viewport={{ once: true }}
                transition={{ duration: 0.8, delay: 0.2 }}
              />
            </div>
          </div>

          <div className="grid md:grid-cols-3 gap-8 relative">
            {/* Connection lines (hidden on mobile) */}
            <div className="absolute top-12 left-[15%] right-[15%] h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent hidden md:block -z-10" />

            {[
              { title: "Architecture", desc: "Scalable foundations built on modern headless principles.", points: ["Microservices", "Headless CMS", "API-First"], icon: "blocks" },
              { title: "Interface", desc: "Precision-crafted UI with obsessive attention to micro-interactions.", points: ["Design Systems", "Framer Motion", "Accessibility"], icon: "layers" },
              { title: "Intelligence", desc: "Data-driven performance optimization and technical SEO.", points: ["Edge Caching", "Core Web Vitals", "Analytics"], icon: "brain" }
            ].map((exp, i) => (
              <motion.div 
                key={i}
                className="bg-white rounded-2xl p-8 border border-gray-100 shadow-[0_10px_40px_-20px_rgba(0,0,0,0.05)] hover:shadow-[0_20px_60px_-20px_rgba(0,0,0,0.1)] transition-all duration-500 group relative z-10 hover:-translate-y-2"
              >
                <div className="w-20 h-20 mb-8 relative" style={{ perspective: 800 }}>
                  <motion.div 
                    className="w-full h-full"
                    style={{ transformStyle: 'preserve-3d' }}
                    whileHover={{ rotateY: 180 }}
                    transition={{ duration: 0.8 }}
                  >
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 flex items-center justify-center shadow-inner" style={{ transform: 'translateZ(20px)' }}>
                      {exp.icon === 'blocks' && <Layout className="w-8 h-8 text-[#0A0B0D]" />}
                      {exp.icon === 'layers' && <Layers className="w-8 h-8 text-[#0A0B0D]" />}
                      {exp.icon === 'brain' && <Cpu className="w-8 h-8 text-[#0A0B0D]" />}
                    </div>
                    {/* 3D layers effect */}
                    <div className="absolute inset-0 bg-[#E8FF47]/20 rounded-xl border border-[#E8FF47]/50" style={{ transform: 'translateZ(-10px) rotateX(10deg) rotateY(10deg)' }} />
                  </motion.div>
                </div>
                
                <h3 className="text-xl font-black text-[#0A0B0D] mb-4">{exp.title}</h3>
                <p className="text-[#6B6F76] leading-relaxed mb-6">{exp.desc}</p>
                <ul className="space-y-3">
                  {exp.points.map(p => (
                    <li key={p} className="flex items-center gap-3 text-sm font-medium text-[#0A0B0D]">
                      <div className="w-5 h-5 rounded-full bg-[#E8FF47]/30 flex items-center justify-center">
                        <Check size={12} className="text-black" />
                      </div>
                      {p}
                    </li>
                  ))}
                </ul>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. SERVICES SHOWCASE */}
      <section className="py-32 bg-[#0A0B0D] text-white">
        <div className="max-w-[1400px] mx-auto px-6">
          <div className="mb-16">
            <div className="text-xs font-bold text-[#E8FF47] uppercase tracking-[0.2em] mb-4">WHAT WE BUILD</div>
            <h2 className="text-[clamp(40px,5vw,64px)] font-black leading-tight">Full-Stack <br/>Digital Products</h2>
          </div>

          <div className="grid md:grid-cols-12 gap-6">
            {/* 2 Large Cards */}
            <div className="md:col-span-12 lg:col-span-6 grid sm:grid-cols-2 gap-6">
              {SERVICES.slice(0, 2).map((s, i) => (
                <div key={i} className="group relative aspect-[4/5] rounded-2xl overflow-hidden bg-gray-900 border border-gray-800">
                  <img src={s.img} alt={s.t} className="w-full h-full object-cover opacity-60 group-hover:scale-110 group-hover:opacity-40 transition-all duration-700" />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent" />
                  <div className="absolute inset-0 p-8 flex flex-col justify-end">
                    <h3 className="text-2xl font-black mb-4 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">{s.t}</h3>
                    <div className="flex flex-wrap gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
                      {s.tags.map(t => (
                        <span key={t} className="px-3 py-1 bg-white/10 backdrop-blur-md rounded-md text-xs font-medium border border-white/10">{t}</span>
                      ))}
                    </div>
                  </div>
                </div>
              ))}
            </div>
            {/* 4 Small Cards */}
            <div className="md:col-span-12 lg:col-span-6 grid sm:grid-cols-2 gap-6">
              {SERVICES.slice(2).map((s, i) => (
                <div key={i} className="group relative aspect-[4/3] rounded-2xl overflow-hidden bg-gray-900 border border-gray-800">
                  <img src={s.img} alt={s.t} className="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-all duration-700" />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent" />
                  <div className="absolute inset-0 p-6 flex flex-col justify-end">
                    <h3 className="text-xl font-bold">{s.t}</h3>
                    <ArrowUpRight className="absolute top-6 right-6 opacity-0 -translate-y-2 translate-x-2 group-hover:opacity-100 group-hover:translate-y-0 group-hover:translate-x-0 transition-all text-[#E8FF47]" />
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* 5. PORTFOLIO 3D GRID */}
      <section className="py-32 overflow-hidden bg-[#FAFAFA]">
        <div className="max-w-[1400px] mx-auto px-6 mb-20 text-center">
          <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em] mb-4">RECENT WORK</div>
          <h2 className="text-[clamp(40px,5vw,64px)] font-black text-[#0A0B0D] leading-tight">Websites That Perform</h2>
        </div>
        <Portfolio3DGrid />
        <div className="text-center mt-20">
          <Link to="/portfolio" className="inline-flex items-center gap-2 text-sm font-bold border-b-2 border-[#0A0B0D] pb-1 hover:text-[#6B6F76] hover:border-[#6B6F76] transition-colors">
            VIEW FULL PORTFOLIO <ArrowRight size={16} />
          </Link>
        </div>
      </section>

      {/* 6. TECHNOLOGY STACK - CREATIVE BENTO */}
      <section className="py-32 bg-[#0A0B0D] border-y border-gray-800 relative overflow-hidden">
        {/* Glow Effects */}
        <div className="absolute top-0 right-1/4 w-[500px] h-[500px] bg-[#E8FF47]/5 blur-[120px] rounded-full pointer-events-none" />
        <div className="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-blue-500/5 blur-[100px] rounded-full pointer-events-none" />
        
        <div className="max-w-[1400px] mx-auto px-6 relative z-10">
          <div className="mb-16">
            <div className="text-xs font-bold text-[#E8FF47] uppercase tracking-[0.2em] mb-4">TECHNOLOGIES WE MASTER</div>
            <h2 className="text-[clamp(40px,5vw,56px)] font-black text-white leading-tight mb-8">
              The Modern<br/>Web Ecosystem
            </h2>
          </div>
          
          <div className="grid lg:grid-cols-12 gap-6">
            {/* Left: 3D Orbiting Logos in a glowing container */}
            <div className="lg:col-span-5 bg-[#141517] rounded-3xl p-4 sm:p-8 border border-gray-800 shadow-2xl relative overflow-hidden flex flex-col justify-center min-h-[400px] group hover:border-[#E8FF47]/30 transition-colors duration-500">
               <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(232,255,71,0.05),transparent_60%)]" />
               <OrbitingLogos />
               <div className="absolute bottom-8 left-8 right-8 flex justify-between bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                  <div>
                    <div className="text-xl font-black text-white">99.9%</div>
                    <div className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Uptime</div>
                  </div>
                  <div className="text-right">
                    <div className="text-xl font-black text-white">Global</div>
                    <div className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Edge Delivery</div>
                  </div>
               </div>
            </div>
            
            {/* Right: Neon Tabbed Tech Stack */}
            <div className="lg:col-span-7 bg-[#141517] rounded-3xl p-8 border border-gray-800 shadow-2xl flex flex-col group hover:border-gray-700 transition-colors duration-500">
              <div className="bg-black/50 rounded-2xl p-2 border border-white/5 flex gap-2 mb-8 overflow-x-auto hide-scrollbar">
                {TECH_CATEGORIES.map((c, i) => (
                  <button 
                    key={c.name} 
                    onClick={() => setActiveTech(i)}
                    className={`flex-1 py-3 px-4 text-sm font-bold rounded-xl whitespace-nowrap transition-all duration-300 ${activeTech === i ? 'bg-[#E8FF47] text-black shadow-[0_0_20px_rgba(232,255,71,0.2)]' : 'text-gray-400 hover:bg-white/5 hover:text-white'}`}
                  >
                    {c.name}
                  </button>
                ))}
              </div>
              
              <div className="flex-1 relative">
                <AnimatePresence mode="wait">
                  <motion.div 
                    key={activeTech}
                    initial={{ opacity: 0, scale: 0.98, filter: 'blur(4px)' }}
                    animate={{ opacity: 1, scale: 1, filter: 'blur(0px)' }}
                    exit={{ opacity: 0, scale: 0.98, filter: 'blur(4px)' }}
                    transition={{ duration: 0.3 }}
                    className="grid sm:grid-cols-2 gap-4 h-full"
                  >
                    {TECH_CATEGORIES[activeTech].tools.map((tool, idx) => (
                      <div key={tool.n} className="bg-white/5 border border-white/5 rounded-2xl p-6 flex flex-col justify-between hover:bg-white/10 transition-colors">
                        <div>
                          <h4 className="font-bold text-white text-lg mb-1 flex items-center justify-between">
                            {tool.n}
                            <span className="text-[#E8FF47] text-sm">{tool.p}%</span>
                          </h4>
                          <p className="text-xs text-gray-400">{tool.d}</p>
                        </div>
                        <div className="h-1 w-full bg-black/50 rounded-full mt-6 overflow-hidden">
                          <motion.div 
                            className="h-full bg-gradient-to-r from-transparent to-[#E8FF47] rounded-full shadow-[0_0_10px_#E8FF47]"
                            initial={{ width: 0 }}
                            animate={{ width: `${tool.p}%` }}
                            transition={{ duration: 1, delay: 0.1 + idx * 0.1 }}
                          />
                        </div>
                      </div>
                    ))}
                  </motion.div>
                </AnimatePresence>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 6.5 INDUSTRIES WE SERVE */}
      <section className="py-24 bg-[#0A0B0D] text-white">
        <div className="max-w-[1400px] mx-auto px-6">
          <div className="text-center mb-16">
            <div className="text-xs font-bold text-[#E8FF47] uppercase tracking-[0.2em] mb-4">INDUSTRIES</div>
            <h2 className="text-[clamp(32px,4vw,48px)] font-black leading-tight">Tailored Web Solutions.</h2>
          </div>
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {INDUSTRIES.map((ind, i) => (
              <div key={i} className="bg-[#1C1E21] border border-gray-800 rounded-2xl p-8 hover:border-[#E8FF47]/50 transition-colors group">
                <ind.icon className="w-10 h-10 text-[#E8FF47] mb-6 group-hover:scale-110 transition-transform" />
                <h3 className="text-xl font-bold mb-3">{ind.name}</h3>
                <p className="text-gray-400 text-sm leading-relaxed">{ind.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 7. METHODOLOGY */}
      <section className="py-0 relative bg-[#0A0B0D] text-white">
        <div className="max-w-[1400px] mx-auto flex flex-col lg:flex-row">
          {/* Left Timeline */}
          <div className="lg:w-[45%] p-10 lg:p-20 border-r border-gray-800">
            <div className="text-xs font-bold text-[#E8FF47] uppercase tracking-[0.2em] mb-4">THE METHODOLOGY</div>
            <h2 className="text-[clamp(40px,5vw,56px)] font-black leading-tight mb-16">How We Build</h2>
            
            <div className="space-y-12 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-800 before:to-transparent">
              {[
                { n: "01", t: "Technical Audit", d: "Deep dive into requirements, architecture planning, and tech stack selection." },
                { n: "02", t: "Systems Design", d: "Creating the UI/UX design system and scalable database architecture." },
                { n: "03", t: "Rapid Iteration", d: "Agile development sprints with weekly visible progress and staging links." },
                { n: "04", t: "Hard Launch", d: "Performance testing, SEO verification, and deploying to global edge networks." }
              ].map((step, i) => (
                <div key={i} className="relative flex items-start group">
                  <div className="absolute left-0 w-10 h-10 rounded-full bg-gray-900 border-2 border-gray-700 flex items-center justify-center text-xs font-bold font-mono group-hover:border-[#E8FF47] group-hover:text-[#E8FF47] transition-colors z-10 bg-[#0A0B0D]">
                    {step.n}
                  </div>
                  <div className="ml-16">
                    <h3 className="text-xl font-bold mb-2 group-hover:text-[#E8FF47] transition-colors">{step.t}</h3>
                    <p className="text-gray-400 leading-relaxed text-sm">{step.d}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
          
          {/* Right Code Editor Mockup */}
          <div className="lg:w-[55%] p-10 lg:p-20 flex items-center justify-center relative overflow-hidden">
            <div className="absolute inset-0 bg-gradient-to-br from-[#1C1E21] to-[#0A0B0D] -z-10" />
            
            <motion.div 
              className="w-full max-w-xl bg-[#1C1E21] rounded-xl overflow-hidden border border-gray-700 shadow-2xl relative"
              style={{ transformStyle: 'preserve-3d', transform: 'perspective(1000px) rotateY(-5deg) rotateX(5deg)' }}
            >
              {/* Floating "compiling" cube */}
              <motion.div 
                className="absolute -right-6 -top-6 w-16 h-16 bg-[#E8FF47] rounded-lg shadow-[0_0_30px_rgba(232,255,71,0.3)] z-20 flex items-center justify-center border border-white/50"
                animate={{ rotateX: 360, rotateY: 360 }}
                transition={{ repeat: Infinity, duration: 8, ease: "linear" }}
              >
                <Settings className="text-black" />
              </motion.div>

              <div className="h-10 bg-[#282C34] flex items-center px-4 justify-between border-b border-gray-700">
                <div className="flex gap-2">
                  <div className="w-3 h-3 rounded-full bg-[#FF5F56]" />
                  <div className="w-3 h-3 rounded-full bg-[#FFBD2E]" />
                  <div className="w-3 h-3 rounded-full bg-[#27C93F]" />
                </div>
                <div className="text-xs text-gray-400 font-mono">architecture.ts</div>
                <div />
              </div>
              <div className="p-6 font-mono text-sm leading-[1.8] text-[#ABB2BF] bg-[#282C34]">
                <pre className="whitespace-pre-wrap">
                  <code>
                    {codeTyping}
                    <span className="inline-block w-2 h-4 ml-1 bg-[#E8FF47] animate-pulse" />
                  </code>
                </pre>
              </div>
              <div className="h-10 bg-[#1C1E21] border-t border-gray-700 flex items-center px-4 text-xs font-mono text-[#98C379]">
                <Terminal size={14} className="mr-2" />
                Terminal: build successful in 1.2s
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* 8. PERFORMANCE METRICS - CREATIVE CIRCULAR DESIGN */}
      <section className="py-32 bg-[#FAFAFA] border-b border-gray-200">
        <div className="max-w-[1400px] mx-auto px-6">
          <div className="text-center mb-24">
            <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em] mb-4">PERFORMANCE OBSESSED</div>
            <h2 className="text-[clamp(40px,5vw,56px)] font-black text-[#0A0B0D] leading-tight">Speed is a Feature.</h2>
          </div>

          <div className="grid md:grid-cols-3 gap-12 max-w-5xl mx-auto">
            {[
              { label: "Page Load", val: "1.2s", pct: 90, color: "text-[#0A0B0D]", stroke: "#0A0B0D" },
              { label: "Lighthouse", val: "98", pct: 98, color: "text-green-600", stroke: "#16a34a" },
              { label: "Web Vitals", val: "Green", pct: 100, color: "text-blue-600", stroke: "#2563eb" }
            ].map((metric, i) => {
              const radius = 60;
              const circumference = 2 * Math.PI * radius;
              return (
                <div key={i} className="flex flex-col items-center group">
                  <div className="relative w-48 h-48 mb-8">
                    {/* Background Ring */}
                    <svg className="w-full h-full transform -rotate-90" viewBox="0 0 140 140">
                      <circle cx="70" cy="70" r={radius} className="stroke-gray-200 fill-none" strokeWidth="6" />
                      <motion.circle 
                        cx="70" cy="70" r={radius} 
                        className="fill-none drop-shadow-md" 
                        stroke={metric.stroke} 
                        strokeWidth="8"
                        strokeLinecap="round"
                        strokeDasharray={circumference}
                        initial={{ strokeDashoffset: circumference }}
                        whileInView={{ strokeDashoffset: circumference - (metric.pct / 100) * circumference }}
                        viewport={{ once: true }}
                        transition={{ duration: 1.5, delay: i * 0.2, ease: "easeOut" }}
                      />
                    </svg>
                    {/* Inner Value */}
                    <div className="absolute inset-0 flex items-center justify-center flex-col">
                      <div className={`text-3xl font-black ${metric.color} group-hover:scale-110 transition-transform duration-300`}>
                        {metric.val}
                      </div>
                    </div>
                  </div>
                  <div className="text-sm font-bold text-[#0A0B0D] uppercase tracking-widest text-center border-b-2 border-transparent group-hover:border-[#E8FF47] pb-1 transition-colors">{metric.label}</div>
                  <p className="text-xs text-[#6B6F76] mt-3 text-center w-32">Ranked top 1% across all modern benchmarks.</p>
                </div>
              );
            })}
          </div>
          
          <div className="mt-16 text-center">
             <div className="inline-flex items-center gap-3 bg-white px-6 py-3 rounded-full border border-gray-200 shadow-sm text-sm font-medium text-[#0A0B0D]">
               <CheckCircle2 className="text-green-500" size={18} />
               Consistently outperforming industry standards by 3x.
             </div>
          </div>
        </div>
      </section>

      {/* 9. BLOG PREVIEW */}
      <section className="py-32 relative overflow-hidden bg-white border-b border-gray-200">
        <div className="max-w-[1400px] mx-auto px-6">
          <div className="flex flex-col md:flex-row justify-between items-end gap-6 mb-16">
             <div>
                <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em] mb-4">ENGINEERING BLOG</div>
                <h2 className="text-[clamp(32px,4vw,48px)] font-black text-[#0A0B0D] leading-tight">Inside Our Process.</h2>
             </div>
             <Link to="/blog" className="font-bold text-sm text-[#0A0B0D] border-b-2 border-[#0A0B0D] pb-1 hover:text-[#E8FF47] hover:bg-[#0A0B0D] transition-colors inline-flex items-center gap-2">
                READ ALL ARTICLES <ArrowRight size={16} />
             </Link>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {BLOG_POSTS.map((post, i) => (
              <Link to="/blog" key={i} className="group flex flex-col h-full border border-gray-200 rounded-2xl overflow-hidden hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-300 bg-white hover:-translate-y-2">
                 <div className="relative aspect-[16/10] overflow-hidden">
                   <img src={post.img} alt={post.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                   <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase shadow-sm">
                     {post.category}
                   </div>
                 </div>
                 <div className="p-8 flex flex-col flex-1">
                   <h3 className="text-xl font-black text-[#0A0B0D] mb-4 line-clamp-2 group-hover:text-blue-600 transition-colors leading-snug">
                     {post.title}
                   </h3>
                   <div className="mt-auto pt-6 border-t border-gray-100 flex items-center justify-between text-xs font-bold text-[#6B6F76]">
                     <span className="flex items-center gap-1.5"><Clock size={14} /> {post.readTime}</span>
                     <span className="flex items-center gap-1.5"><BookOpen size={14} /> {post.date}</span>
                   </div>
                 </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* 10. FAQ */}
      <section className="py-32 bg-[#FAFAFA] border-t border-gray-200">
        <div className="max-w-[1400px] mx-auto px-6 grid lg:grid-cols-12 gap-16 items-start">
          <div className="lg:col-span-5 sticky top-32">
            <div className="text-xs font-bold text-[#6B6F76] uppercase tracking-[0.2em] mb-4">COMMON QUESTIONS</div>
            <h2 className="text-[clamp(40px,5vw,56px)] font-black text-[#0A0B0D] leading-tight mb-8">
              Everything You Need to Know.
            </h2>
            <div className="w-32 h-32 relative hidden lg:block" style={{ perspective: 1000 }}>
               <motion.div 
                 className="w-full h-full text-[#E8FF47] border-8 border-current rounded-full flex items-center justify-center text-6xl font-black font-serif bg-white shadow-xl"
                 animate={{ rotateY: [0, 360] }}
                 transition={{ repeat: Infinity, duration: 10, ease: "linear" }}
                 style={{ transformStyle: 'preserve-3d' }}
               >
                 <span className="text-[#0A0B0D]" style={{ transform: 'translateZ(1px)' }}>?</span>
               </motion.div>
            </div>
            
            {/* Contact Card to fill empty space */}
            <div className="hidden lg:block bg-white border border-gray-200 p-8 rounded-2xl shadow-sm mt-12 w-4/5 relative overflow-hidden group hover:border-[#E8FF47]/50 transition-colors">
              <div className="absolute top-0 right-0 w-24 h-24 bg-[#E8FF47]/10 rounded-bl-full -z-10 group-hover:scale-150 transition-transform duration-500" />
              <h3 className="font-black text-xl text-[#0A0B0D] mb-2">Still have questions?</h3>
              <p className="text-sm text-[#6B6F76] mb-6 leading-relaxed">Our technical team is ready to help you plan your architecture and clarify any doubts.</p>
              <Link to="/contact" className="inline-flex items-center gap-2 text-sm font-bold bg-[#0A0B0D] text-white px-6 py-3 rounded-xl hover:bg-[#E8FF47] hover:text-[#0A0B0D] transition-colors shadow-md hover:shadow-lg active:scale-95 duration-200 w-max">
                Ask a Question <ArrowRight size={16} />
              </Link>
            </div>
          </div>
          
          <div className="lg:col-span-7 space-y-4">
            {FAQ_ITEMS.map((faq, i) => {
              const isOpen = openFaq === i;
              return (
                <div key={i} className="bg-white border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md relative">
                  {isOpen && <div className="absolute left-0 top-0 bottom-0 w-1 bg-[#E8FF47]" />}
                  <button 
                    onClick={() => setOpenFaq(isOpen ? null : i)}
                    className="w-full px-6 py-6 flex justify-between items-center text-left"
                  >
                    <span className="text-lg font-bold text-[#0A0B0D] pr-8">{faq.q}</span>
                    <ChevronDown className={`shrink-0 transition-transform duration-300 ${isOpen ? 'rotate-180 text-[#0A0B0D]' : 'text-gray-400'}`} />
                  </button>
                  <AnimatePresence>
                    {isOpen && (
                      <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3 }}
                      >
                        <div className="px-6 pb-6 text-[#6B6F76] leading-relaxed text-base">
                          {faq.a}
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* 11. FINAL CTA */}
      <section className="bg-[#E8FF47] py-32 border-t-4 border-[#0A0B0D] relative overflow-hidden">
        <div className="max-w-[1400px] mx-auto px-6 grid lg:grid-cols-12 gap-8 lg:gap-16 items-center relative z-10">
          <div className="lg:col-span-7">
            <h2 className="text-[clamp(48px,6vw,80px)] font-black text-[#0A0B0D] leading-[1] mb-6 tracking-tight">
              Ready to Build<br/>Something Great?
            </h2>
            <p className="text-xl text-[#0A0B0D]/70 font-medium max-w-lg mb-8">
              Free consultation. Quote within 24 hours. No commitment required.
            </p>
            <div className="space-y-2 text-[#0A0B0D]/80 font-medium">
              <div>Email: <a href="mailto:hello@devbotics.in" className="font-bold text-[#0A0B0D] hover:underline">hello@devbotics.in</a></div>
              <div>Call: <a href="tel:+919876543210" className="font-bold text-[#0A0B0D] hover:underline">+91 98765 43210</a></div>
            </div>
          </div>
          
          <div className="lg:col-span-5 bg-white/40 backdrop-blur-xl p-6 sm:p-8 rounded-2xl border border-white/50 shadow-2xl relative">
            {/* Floating Paper Plane */}
            <motion.div 
              className="absolute -top-10 -right-10 bg-[#0A0B0D] w-20 h-20 rounded-full flex items-center justify-center shadow-2xl text-white"
              animate={{ y: [-10, 10, -10], rotate: [-5, 5, -5] }}
              transition={{ repeat: Infinity, duration: 4 }}
            >
              <Send size={32} className="ml-1 mt-1" />
            </motion.div>

            <form className="space-y-4 relative z-10" onSubmit={e => e.preventDefault()}>
              <input type="text" placeholder="Your Name" className="w-full px-5 py-4 bg-white/80 border border-white focus:border-[#0A0B0D] outline-none rounded-xl text-[#0A0B0D] font-medium placeholder-gray-500 transition-colors shadow-sm" />
              <input type="email" placeholder="Work Email" className="w-full px-5 py-4 bg-white/80 border border-white focus:border-[#0A0B0D] outline-none rounded-xl text-[#0A0B0D] font-medium placeholder-gray-500 transition-colors shadow-sm" />
              <select className="w-full px-5 py-4 bg-white/80 border border-white focus:border-[#0A0B0D] outline-none rounded-xl text-[#0A0B0D] font-medium appearance-none shadow-sm cursor-pointer">
                <option value="">Project Type</option>
                <option value="ecommerce">E-commerce / Shopify</option>
                <option value="web-app">Custom Web App (React/Next)</option>
                <option value="corporate">Corporate Website</option>
                <option value="other">Other</option>
              </select>
              <textarea placeholder="Tell us about your project" rows={3} className="w-full px-5 py-4 bg-white/80 border border-white focus:border-[#0A0B0D] outline-none rounded-xl text-[#0A0B0D] font-medium placeholder-gray-500 transition-colors shadow-sm resize-none"></textarea>
              <button className="w-full bg-[#0A0B0D] text-white py-4 rounded-xl font-bold tracking-wide hover:bg-black transition-colors shadow-xl active:scale-[0.98]">
                SUBMIT PROPOSAL
              </button>
            </form>
          </div>
        </div>
      </section>

    </div>
  );
}
