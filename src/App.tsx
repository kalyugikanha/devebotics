import { useEffect } from 'react'
import { Routes, Route, useLocation, Link } from 'react-router-dom'
import CustomCursor from './components/CustomCursor'
import Navbar from './components/Navbar'
import Footer from './components/Footer'

// Homepage sections
import Hero from './components/Hero'
import TrustedBy from './components/TrustedBy'
import Services from './components/Services'
import Stats from './components/Stats'
import Process from './components/Process'
import WhyUs from './components/WhyUs'
import Industries from './components/Industries'
import Portfolio from './components/Portfolio'
import Team from './components/Team'
import Testimonials from './components/Testimonials'
import Blog from './components/Blog'
import ContactStrip from './components/ContactStrip'
import FAQ from './components/FAQ'
import CTA from './components/CTA'

// About Us pages
import OverviewPage from './pages/about/OverviewPage'
import JourneyPage from './pages/about/JourneyPage'
import WhyUsPage from './pages/about/WhyUsPage'
import CareersPage from './pages/about/CareersPage'

// App Development pages
import AppDevelopmentMain from './pages/app-development/AppDevelopmentMain'
import AppTypePage from './pages/app-development/AppTypePage'
import { appPageData } from './pages/app-development/appPageData'

// Web Development pages
import WebDevMain from './pages/web-development/WebDevMain'
import WebDevTypePage from './pages/web-development/WebDevTypePage'
import { webPageData } from './pages/web-development/webPageData'

// Contact & Blog pages
import ContactMain from './pages/contact/ContactMain'
import BlogMain from './pages/blog/BlogMain'
import BlogPost from './pages/blog/BlogPost'

// AI Services
import AiServices from './pages/services/AiServices'
import { aiPageData } from './pages/services/ai/aiPageData'
import AiServiceTypePage from './pages/services/ai/AiServiceTypePage'

// Floating buttons
import FloatingButtons from './components/FloatingButtons'

function ScrollToTop() {
  const { pathname } = useLocation()
  useEffect(() => { window.scrollTo(0, 0) }, [pathname])
  return null
}

function NotFound() {
  return (
    <div className="min-h-screen bg-[#08090A] flex flex-col items-center justify-center text-center px-6 selection:bg-[#E8FF47] selection:text-black">
      <h1 className="text-[clamp(100px,15vw,200px)] font-black text-[#E8FF47] leading-none mb-4 tracking-tighter" style={{ fontFamily: 'Montserrat, sans-serif' }}>404</h1>
      <h2 className="text-3xl sm:text-5xl font-bold text-[#F0F0F0] mb-6 tracking-tight">Page Not Found</h2>
      <p className="text-[#6B6F76] max-w-md mx-auto mb-10 text-lg">The page you are looking for doesn't exist, has been moved, or is currently under development.</p>
      <Link to="/" className="bg-[#E8FF47] text-[#08090A] font-bold px-10 py-4 rounded-xl hover:bg-white hover:scale-105 active:scale-95 transition-all duration-300 shadow-xl shadow-[#E8FF47]/10 flex items-center gap-2">
        Return to Homepage
      </Link>
    </div>
  )
}

function HomePage() {
  useEffect(() => {
    const observe = () => {
      const els = document.querySelectorAll('.fade-up:not(.visible)')
      const obs = new IntersectionObserver(
        (entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('visible')
              obs.unobserve(entry.target)
            }
          })
        },
        { threshold: 0.12 }
      )
      els.forEach(el => obs.observe(el))
      return obs
    }
    let obs = observe()
    const t = setTimeout(() => { obs.disconnect(); obs = observe() }, 500)
    return () => { obs.disconnect(); clearTimeout(t) }
  }, [])

  return (
    <main style={{ background: '#08090A', minHeight: '100vh' }}>
      <Hero />
      <TrustedBy />
      <Services />
      <Stats />
      <Process />
      <WhyUs />
      <Industries />
      <Portfolio />
      <Team />
      <Testimonials />
      <Blog />
      <ContactStrip />
      <FAQ />
      <CTA />
    </main>
  )
}

function App() {
  return (
    <>
      <div className="grain-overlay" />
      <CustomCursor />
      <FloatingButtons />
      <ScrollToTop />
      <Navbar />
      <Routes>
        <Route path="/" element={<HomePage />} />
        
        {/* About Us Routes */}
        <Route path="/overview" element={<OverviewPage />} />
        <Route path="/our-journey" element={<JourneyPage />} />
        <Route path="/why-choose-us" element={<WhyUsPage />} />
        <Route path="/careers" element={<CareersPage />} />

        <Route path="/contact" element={<ContactMain />} />
        <Route path="/blog" element={<BlogMain />} />
        <Route path="/blog/:slug" element={<BlogPost />} />
        <Route path="/app-development" element={<AppDevelopmentMain />} />
        {Object.keys(appPageData).map(slug => (
          <Route
            key={slug}
            path={`/app-development/${slug}`}
            element={<AppTypePage slug={slug} />}
          />
        ))}
        <Route path="/web-development" element={<WebDevMain />} />
        {Object.keys(webPageData).map(slug => (
          <Route
            key={slug}
            path={`/web-development/${slug}`}
            element={<WebDevTypePage slug={slug} />}
          />
        ))}
        
        {/* AI Services Route */}
        <Route path="/ai-services" element={<AiServices />} />
        {Object.keys(aiPageData).map(slug => (
          <Route
            key={slug}
            path={`/ai-services/${slug}`}
            element={<AiServiceTypePage slug={slug} />}
          />
        ))}

        {/* 404 Route */}
        <Route path="*" element={<NotFound />} />
      </Routes>
      <Footer />
    </>
  )
}

export default App
