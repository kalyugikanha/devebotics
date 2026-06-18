import { useEffect } from 'react'
import { Routes, Route, useLocation, Link, Navigate } from 'react-router-dom'
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
import Awards from './components/Awards'

// About Us pages
import AboutUsMain from './pages/about/AboutUsMain'
import WhyUsPage from './pages/about/WhyUsPage'
import CareersPage from './pages/about/CareersPage'

// App Development pages
import AppDevelopmentMain from './pages/app-development/AppDevelopmentMain'
import AppTypePage from './pages/app-development/AppTypePage'
import { appPageData } from './pages/app-development/appPageData'

// Custom Development
import CustomDevPage from './pages/development/CustomDevPage'

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

// Admin
import AdminLogin from './admin/AdminLogin'
import AdminLayout from './admin/AdminLayout'
import AdminDashboard from './admin/AdminDashboard'
import ProtectedRoute from './admin/ProtectedRoute'
import BlogCategoryManager from './admin/modules/BlogCategoryManager'
import { BlogManager, BlogEditor } from './admin/modules/BlogManager'
import ContactManager from './admin/modules/ContactManager'
import FooterManager from './admin/modules/FooterManager'
import NavigationManager from './admin/modules/NavigationManager'

function ScrollToTop() {
  const { pathname } = useLocation()
  useEffect(() => { window.scrollTo(0, 0) }, [pathname])
  return null
}

function NotFound() {
  return (
    <div className="min-h-screen bg-[#FFFFFF] flex flex-col items-center justify-center text-center px-6 selection:bg-[#1F8844] selection:text-black">
      <h1 className="text-[clamp(100px,15vw,200px)] font-black text-[#1F8844] leading-none mb-4 tracking-tighter" style={{ fontFamily: 'Montserrat, sans-serif' }}>404</h1>
      <h2 className="text-3xl sm:text-5xl font-bold text-[#111827] mb-6 tracking-tight">Page Not Found</h2>
      <p className="text-[#4B5563] max-w-md mx-auto mb-10 text-lg">The page you are looking for doesn't exist, has been moved, or is currently under development.</p>
      <Link to="/" className="bg-[#1F8844] text-[#FFFFFF] font-bold px-10 py-4 rounded-xl hover:bg-white hover:scale-105 active:scale-95 transition-all duration-300 shadow-xl shadow-[#1F8844]/10 flex items-center gap-2">
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
    <main style={{ background: '#FFFFFF', minHeight: '100vh' }}>
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
      <Awards />
      <FAQ />
      <CTA />
    </main>
  )
}

function App() {
  const { pathname } = useLocation();
  const isAdminRoute = pathname.startsWith('/admin');

  return (
    <>
      <div className="grain-overlay" />
      <CustomCursor />
      {!isAdminRoute && <FloatingButtons />}
      <ScrollToTop />
      {!isAdminRoute && <Navbar />}
      <Routes>
        <Route path="/" element={<HomePage />} />
        
        {/* About Us Routes */}
        <Route path="/about-us" element={<AboutUsMain />} />
        <Route path="/why-choose-us" element={<WhyUsPage />} />
        <Route path="/careers" element={<CareersPage />} />

        <Route path="/contact" element={<ContactMain />} />
        <Route path="/blog" element={<BlogMain />} />
        <Route path="/blog/:slug" element={<BlogPost />} />
        <Route path="/development/custom-development" element={<CustomDevPage />} />
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

        {/* Admin Routes */}
        <Route path="/admin/login" element={<AdminLogin />} />
        <Route path="/admin" element={<ProtectedRoute />}>
          <Route element={<AdminLayout />}>
            <Route index element={<Navigate to="/admin/dashboard" replace />} />
            <Route path="dashboard" element={<AdminDashboard />} />
            <Route path="navigation" element={<NavigationManager />} />
            <Route path="blogs" element={<BlogManager />} />
            <Route path="blogs/new" element={<BlogEditor />} />
            <Route path="blogs/:id/edit" element={<BlogEditor />} />
            <Route path="categories" element={<BlogCategoryManager />} />
            <Route path="footer" element={<FooterManager />} />
            <Route path="contact" element={<ContactManager />} />
          </Route>
        </Route>

        {/* 404 Route */}
        <Route path="*" element={<NotFound />} />
      </Routes>
      {!isAdminRoute && <Footer />}
    </>
  )
}

export default App
