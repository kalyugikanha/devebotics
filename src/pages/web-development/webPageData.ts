export const webPageData: Record<string, any> = {
  'shopify': {
    title: 'Shopify',
    h1: 'Shopify Development\nCompany.',
    sub: 'Custom Shopify stores engineered for conversions, speed & brand identity.',
    stats: '50+ Stores · Avg 3× Conversion · 1.1s Load Time · 4.9★ Rating',
    heroImage: '/images/generic-1.png',
    meta: 'Shopify Development Company | DevBotics',
    features: [
      { name: 'Custom Shopify Theme Development', desc: 'Zero templates. Fully bespoke design.' },
      { name: 'Shopify App Integration & Development', desc: 'Custom apps for unique store functionality.' },
      { name: 'Headless Shopify (Hydrogen/Oxygen)', desc: 'Blazing fast React-based headless stores.' },
      { name: 'Product Page Conversion Optimisation', desc: 'Data-driven layouts to maximize ATC rates.' },
      { name: 'Checkout Customisation', desc: 'Frictionless checkout experiences.' },
      { name: 'Multi-currency & Multi-language', desc: 'Global selling made easy.' },
      { name: 'Shopify Migration', desc: 'Seamless migration from WooCommerce or Magento.' },
      { name: 'Performance & Speed Optimisation', desc: 'Sub-second load times.' }
    ],
    keyFeatures: [
      { title: 'Custom Theme — Zero Templates', desc: 'We build themes from scratch to perfectly match your brand identity without the bloat of standard themes.', img: '/images/generic-2.png' },
      { title: 'Conversion-Optimised Product Pages', desc: 'Our layouts are designed based on user testing to maximize your Add To Cart and checkout rates.', img: '/images/generic-1.png' },
      { title: 'Headless Shopify with Next.js', desc: 'Decouple your frontend for blazing fast load times and limitless design possibilities.', img: '/images/generic-2.png' }
    ],
    techStack: ['Shopify', 'Liquid', 'Hydrogen', 'Next.js', 'Tailwind', 'Klaviyo', 'Razorpay', 'Stripe', 'Metafields'],
    platforms: ['Customer Store', 'Admin Panel', 'Mobile-Optimised', 'Analytics', 'App Integrations'],
    pricing: [
      { name: 'Starter', price: '₹45,000', desc: 'Basic custom theme, up to 50 products' },
      { name: 'Professional', price: '₹1,10,000', desc: 'Full custom + apps + headless option' },
      { name: 'Enterprise', price: 'Custom', desc: 'Multi-store, B2B, headless' }
    ],
    faq: [
      { q: 'Can you migrate my WooCommerce store to Shopify?', a: 'Yes, we handle complete data migration including products, customers, and order history.' },
      { q: 'Do you build custom Shopify apps?', a: 'Yes, we build private apps using Node.js and React for specific functionalities.' },
      { q: 'What is headless Shopify and do I need it?', a: 'Headless separates the frontend from Shopify backend. You need it if you want sub-second load times or extreme customisation.' },
      { q: 'Can you integrate my existing ERP/CRM?', a: 'Absolutely, we can connect Shopify to NetSuite, Salesforce, Zoho, etc.' },
      { q: 'Will my Shopify store be SEO-optimised?', a: 'Yes, we configure all technical SEO elements out of the box.' },
      { q: 'Can you set up international selling?', a: 'Yes, we configure Shopify Markets for multi-currency and localization.' },
      { q: 'Do you provide Shopify training?', a: 'Yes, 60 days of support and training are included.' },
      { q: 'What Shopify plan do I need?', a: 'We will recommend the right plan (Basic, Shopify, Advanced, or Plus) based on your traffic.' }
    ]
  },
  'wordpress': {
    title: 'WordPress',
    h1: 'WordPress Development\nCompany.',
    sub: 'Blazing-fast, custom WordPress websites — no page builders, no compromises.',
    stats: '60+ Sites · 95 Lighthouse · 0.8s Load · 100% Custom',
    heroImage: '/images/generic-1.png',
    meta: 'WordPress Development Company | DevBotics',
    features: [
      { name: 'Custom WordPress Theme Development', desc: 'Bespoke themes coded from scratch.' },
      { name: 'WooCommerce Store Development', desc: 'Scalable e-commerce solutions.' },
      { name: 'Custom Plugin Development', desc: 'Tailored functionality for your exact needs.' },
      { name: 'WordPress Speed Optimisation', desc: '90+ Core Web Vitals scores.' },
      { name: 'WordPress Security Hardening', desc: 'Protection against malware and attacks.' },
      { name: 'Gutenberg Block Development', desc: 'Custom editorial experiences.' },
      { name: 'WordPress Multisite Setup', desc: 'Manage multiple sites from one dashboard.' },
      { name: 'WordPress to Custom Migration', desc: 'Move away from bloated templates.' }
    ],
    keyFeatures: [
      { title: 'Zero Bloat Architecture', desc: 'We never use generic page builders like Elementor unless requested. We code custom Gutenberg blocks for max speed.', img: '/images/generic-2.png' },
      { title: 'Advanced Custom Fields (ACF)', desc: 'We build complex content architectures making it easy for your team to update the site.', img: '/images/generic-1.png' },
      { title: 'Enterprise Security', desc: 'Custom login URLs, 2FA, firewall integration, and strict file permissions.', img: '/images/generic-2.png' }
    ],
    techStack: ['WordPress', 'PHP', 'ACF', 'WooCommerce', 'MySQL', 'Redis', 'Cloudflare', 'Gutenberg'],
    platforms: ['Public Website', 'WP Admin Panel', 'Custom Post Types', 'REST API', 'WooCommerce'],
    pricing: [
      { name: 'Starter', price: '₹30,000', desc: 'Business site, up to 10 pages' },
      { name: 'Professional', price: '₹75,000', desc: 'WooCommerce + custom theme + plugins' },
      { name: 'Enterprise', price: 'Custom', desc: 'Multisite, headless WordPress' }
    ],
    faq: [
      { q: 'Is WordPress secure?', a: 'Yes, when properly configured with security plugins, regular updates, and strict server rules.' },
      { q: 'Will my site be fast?', a: 'Our custom themes typically score 95+ on Google PageSpeed Insights.' },
      { q: 'Do you use Elementor?', a: 'We prefer custom Gutenberg blocks for speed, but can use Elementor if your team requires it.' },
      { q: 'Can you build a membership site on WP?', a: 'Yes, we use plugins like MemberPress or custom solutions.' },
      { q: 'How do I update content?', a: 'We provide a heavily customized, easy-to-use backend tailored to your data.' },
      { q: 'Do you provide ongoing maintenance?', a: 'Yes, we offer monthly retainer packages for updates and backups.' },
      { q: 'Can WP handle high traffic?', a: 'Absolutely, with proper caching (Redis) and a good CDN (Cloudflare), WP scales massively.' },
      { q: 'Do you integrate third-party APIs?', a: 'Yes, we frequently connect WP to external CRMs and ERPs.' }
    ]
  },
  'nextjs-react': {
    title: 'Next.js & React',
    h1: 'Next.js & React\nDevelopment Company.',
    sub: 'Server-side rendered, edge-optimised React applications built to scale globally.',
    stats: '80+ Apps · 98 Lighthouse · Sub-1s Load · TypeScript Always',
    heroImage: '/images/generic-1.png',
    meta: 'Next.js Development Company | DevBotics',
    features: [
      { name: 'Next.js App Router Development', desc: 'Leveraging the latest React server components.' },
      { name: 'React Component Library Development', desc: 'Reusable, accessible UI systems.' },
      { name: 'Server-Side Rendering (SSR) & SSG', desc: 'SEO-perfect web applications.' },
      { name: 'API Routes & Serverless Functions', desc: 'Backend logic integrated seamlessly.' },
      { name: 'Next.js E-commerce Development', desc: 'Headless storefronts for Shopify/Swell.' },
      { name: 'Headless CMS Integration', desc: 'Sanity, Strapi, or Contentful backends.' },
      { name: 'Authentication', desc: 'NextAuth, Clerk, or Auth0 integration.' },
      { name: 'Deployment on Vercel / AWS', desc: 'Edge network deployment.' }
    ],
    keyFeatures: [
      { title: 'Server Components Architecture', desc: 'We utilize Next.js 14 App Router to ship zero JS to the client where possible, maximizing performance.', img: '/images/generic-2.png' },
      { title: 'Headless Integration', desc: 'We connect Next.js frontends to any backend: Shopify, Strapi, WordPress, or custom APIs.', img: '/images/generic-1.png' },
      { title: 'Edge Network Deployment', desc: 'Deployed globally on Vercel so your users experience single-digit millisecond latency.', img: '/images/generic-2.png' }
    ],
    techStack: ['Next.js 14', 'React 18', 'TypeScript', 'Tailwind CSS', 'Prisma', 'tRPC', 'Vercel', 'Supabase', 'Stripe'],
    platforms: ['Web App', 'Admin Dashboard', 'API Routes', 'Edge Functions', 'Headless CMS'],
    pricing: [
      { name: 'Starter', price: '₹50,000', desc: 'Marketing site / landing page' },
      { name: 'Professional', price: '₹1,20,000', desc: 'Full web app with auth + DB' },
      { name: 'Enterprise', price: 'Custom', desc: 'SaaS, marketplace, enterprise' }
    ],
    faq: [
      { q: 'Why choose Next.js over plain React?', a: 'Next.js provides Server-Side Rendering (SSR) which is crucial for SEO and initial load speed.' },
      { q: 'Do you use TypeScript?', a: 'Yes, 100% of our Next.js projects are built with strict TypeScript.' },
      { q: 'What CMS do you recommend with Next.js?', a: 'We typically recommend Sanity, Strapi, or headless WordPress depending on your team.' },
      { q: 'Can you migrate my React app to Next.js?', a: 'Yes, we routinely help companies upgrade from CRA (Create React App) to Next.js.' },
      { q: 'Where do you deploy Next.js apps?', a: 'Usually Vercel, but we can also deploy to AWS (Amplify/EC2) or DigitalOcean.' },
      { q: 'Do you use the App Router?', a: 'Yes, all new projects utilize the latest Next.js App Router and Server Components.' },
      { q: 'Is Next.js good for e-commerce?', a: 'It is the best choice for headless e-commerce due to its incredible speed and SSG capabilities.' },
      { q: 'How do you handle authentication?', a: 'We use NextAuth (Auth.js), Clerk, or Supabase Auth depending on requirements.' }
    ]
  },
  'php-laravel': {
    title: 'PHP & Laravel',
    h1: 'PHP & Laravel\nDevelopment Company.',
    sub: 'Enterprise-grade web applications on Laravel\'s battle-tested MVC framework.',
    stats: '40+ Laravel Apps · MVC Architecture · Robust APIs · Eloquent ORM',
    heroImage: '/images/generic-1.png',
    meta: 'PHP Laravel Development Company | DevBotics',
    features: [
      { name: 'Custom Laravel Application Development', desc: 'From scratch, tailored business logic.' },
      { name: 'Laravel REST API Development', desc: 'Robust APIs for mobile and web frontends.' },
      { name: 'Laravel Admin Panel', desc: 'Custom Filament or Nova dashboards.' },
      { name: 'Multi-tenant SaaS on Laravel', desc: 'Isolate client data securely.' },
      { name: 'Laravel E-commerce Development', desc: 'Custom shopping cart and checkout flows.' },
      { name: 'Laravel Job Boards / Portals', desc: 'Scalable data-heavy directories.' },
      { name: 'Laravel ERP / CRM Systems', desc: 'Internal tooling for enterprises.' },
      { name: 'PHP Legacy Code Migration', desc: 'Modernizing old PHP codebases to Laravel 11.' }
    ],
    keyFeatures: [
      { title: 'Filament Admin Panels', desc: 'We build beautiful, highly functional admin interfaces using Filament PHP rapidly.', img: '/images/generic-2.png' },
      { title: 'Robust API Architecture', desc: 'Sanctum-secured REST or GraphQL APIs to power your mobile apps or JS frontends.', img: '/images/generic-1.png' },
      { title: 'Multi-tenant SaaS', desc: 'We configure database routing and subdomains for scalable SaaS applications.', img: '/images/generic-2.png' }
    ],
    techStack: ['Laravel 11', 'PHP 8.2', 'MySQL', 'Redis', 'Livewire', 'Filament', 'AWS', 'Docker', 'Sanctum'],
    platforms: ['Web App', 'Admin Dashboard', 'REST API', 'Queue Workers', 'Cron Jobs'],
    pricing: [
      { name: 'Starter', price: '₹55,000', desc: 'Web application, up to 10 modules' },
      { name: 'Professional', price: '₹1,30,000', desc: 'SaaS / marketplace / portal' },
      { name: 'Enterprise', price: 'Custom', desc: 'Enterprise ERP/CRM, HIPAA compliant' }
    ],
    faq: [
      { q: 'Is Laravel good for large applications?', a: 'Yes, Laravel is highly scalable and used by companies like Twitch and Disney.' },
      { q: 'Do you use Livewire or Inertia?', a: 'We use both depending on the project. Livewire for rapid full-stack, Inertia (Vue/React) for SPA feel.' },
      { q: 'Can you rescue an old PHP project?', a: 'Yes, we specialize in migrating legacy spaghetti PHP to clean Laravel architecture.' },
      { q: 'How do you handle background tasks?', a: 'We utilize Laravel Horizon and Redis queues for robust background job processing.' },
      { q: 'Is Laravel secure?', a: 'Extremely. It has built-in protection against SQL injection, CSRF, and XSS attacks.' },
      { q: 'Can you build APIs with Laravel?', a: 'Yes, we use Laravel Sanctum for lightweight API authentication.' },
      { q: 'Where do you host Laravel apps?', a: 'Typically on AWS via Laravel Forge, or DigitalOcean droplets.' },
      { q: 'Do you write automated tests?', a: 'Yes, we use Pest or PHPUnit for feature and unit testing.' }
    ]
  },
  'ecommerce': {
    title: 'E-commerce',
    h1: 'E-commerce Website\nDevelopment Company.',
    sub: 'Multi-vendor marketplaces, B2B portals & custom shopping experiences that convert.',
    stats: '100+ Stores · ₹50Cr+ GMV Processed · 3.5% Avg CVR · Multi-currency',
    heroImage: '/images/generic-1.png',
    meta: 'E-commerce Website Development | DevBotics',
    features: [
      { name: 'Multi-vendor Marketplace Development', desc: 'Like Amazon or Etsy.' },
      { name: 'B2B E-commerce Portal', desc: 'Wholesale pricing, bulk ordering, net terms.' },
      { name: 'Custom Cart & Checkout Flow', desc: 'Frictionless, one-page checkouts.' },
      { name: 'Product Configurator / 3D Viewer', desc: 'Interactive shopping experiences.' },
      { name: 'Subscription & Membership System', desc: 'Recurring revenue models.' },
      { name: 'Inventory Management Integration', desc: 'Sync with ERPs and warehouses.' },
      { name: 'Multi-currency & Multi-language', desc: 'Sell globally out of the box.' },
      { name: 'Progressive Web App (PWA) Commerce', desc: 'App-like experience on the web.' }
    ],
    keyFeatures: [
      { title: 'Omnichannel Integrations', desc: 'We connect your store to Instagram Shopping, Google Merchant Center, and POS systems.', img: '/images/generic-2.png' },
      { title: 'Advanced Search & Filtering', desc: 'Using Algolia or ElasticSearch for lightning-fast, typo-tolerant product discovery.', img: '/images/generic-1.png' },
      { title: 'Headless Commerce', desc: 'Decoupled frontends using Next.js to provide unparalleled speed and UX.', img: '/images/generic-2.png' }
    ],
    techStack: ['Next.js', 'Node.js', 'MongoDB', 'Stripe', 'Razorpay', 'Algolia', 'Redis', 'AWS', 'Cloudflare'],
    platforms: ['Customer Storefront', 'Vendor Dashboard', 'Super Admin', 'PWA', 'Analytics'],
    pricing: [
      { name: 'Starter', price: '₹70,000', desc: 'Single-vendor, up to 500 products' },
      { name: 'Professional', price: '₹1,80,000', desc: 'Multi-vendor marketplace' },
      { name: 'Enterprise', price: 'Custom', desc: 'B2B, headless, global' }
    ],
    faq: [
      { q: 'Which platform do you use for E-commerce?', a: 'Depending on your needs: Shopify, WooCommerce, or Custom Node/React stacks.' },
      { q: 'Can you build a multi-vendor marketplace?', a: 'Yes, complete with vendor dashboards, commission splits, and payouts.' },
      { q: 'Do you integrate local payment gateways?', a: 'Yes, Razorpay, PayU, CCavenue, Stripe, PayPal, etc.' },
      { q: 'How do you handle shipping integrations?', a: 'We integrate with Shiprocket, Delhivery, FedEx, or any API-enabled courier.' },
      { q: 'Can the store handle 10,000+ products?', a: 'Yes, our custom database architectures scale to millions of SKUs.' },
      { q: 'Do you build B2B wholesale portals?', a: 'Yes, with custom pricing tiers, MOQ rules, and invoice generation.' },
      { q: 'Is the checkout customizable?', a: 'Yes, we optimize checkouts to reduce abandoned carts drastically.' },
      { q: 'Do you offer post-launch support?', a: 'Yes, 60 days free support and SLA packages for ongoing maintenance.' }
    ]
  },
  'saas-development': {
    title: 'SaaS Development',
    h1: 'SaaS Web Application\nDevelopment Company.',
    sub: 'Scalable, multi-tenant SaaS platforms with auth, billing & beautiful dashboards.',
    stats: '30+ SaaS Launched · MRR Ready · Multi-tenant · SOC2 Compliant architecture',
    heroImage: '/images/generic-1.png',
    meta: 'SaaS Development Company India | DevBotics',
    features: [
      { name: 'Multi-tenant Architecture Design', desc: 'Secure data isolation between accounts.' },
      { name: 'Authentication & Authorisation', desc: 'OAuth, SAML, Magic Links, 2FA.' },
      { name: 'Subscription Billing', desc: 'Stripe integration with metered usage.' },
      { name: 'Admin & User Dashboard', desc: 'Beautiful, data-dense UI/UX.' },
      { name: 'Role-based Access Control (RBAC)', desc: 'Granular permissions.' },
      { name: 'API Development & Documentation', desc: 'Let your users integrate your SaaS.' },
      { name: 'Analytics & Reporting Module', desc: 'Custom charting and data export.' },
      { name: 'SaaS Onboarding Flow Design', desc: 'Frictionless signup to activation.' }
    ],
    keyFeatures: [
      { title: 'Robust Billing Engine', desc: 'Complex Stripe integrations including proration, seat-based pricing, and usage metering.', img: '/images/generic-2.png' },
      { title: 'Enterprise Security', desc: 'Built with SOC2 compliance in mind. Encrypted data, audit logs, and secure sessions.', img: '/images/generic-1.png' },
      { title: 'Real-time Features', desc: 'WebSockets for live collaboration, notifications, and real-time dashboard updates.', img: '/images/generic-2.png' }
    ],
    techStack: ['Next.js', 'Node.js', 'PostgreSQL', 'Stripe', 'Auth.js', 'Prisma', 'Redis', 'AWS', 'Vercel'],
    platforms: ['User Dashboard', 'Super Admin', 'Marketing Site', 'Developer API', 'Webhooks'],
    pricing: [
      { name: 'Starter', price: '₹1,50,000', desc: 'MVP SaaS, core features' },
      { name: 'Professional', price: '₹3,50,000', desc: 'Full SaaS with billing + dashboard' },
      { name: 'Enterprise', price: 'Custom', desc: 'Enterprise SaaS, white-label' }
    ],
    faq: [
      { q: 'How long does it take to build a SaaS MVP?', a: 'Typically 6 to 10 weeks depending on the complexity of the core feature.' },
      { q: 'Do you help with SaaS architecture?', a: 'Yes, we design the entire database schema and cloud architecture.' },
      { q: 'How do you handle multi-tenancy?', a: 'Either via schema separation or row-level security (RLS) in PostgreSQL.' },
      { q: 'Can you integrate Stripe billing?', a: 'Yes, we handle the full Stripe lifecycle including webhooks and customer portals.' },
      { q: 'Do you build the marketing site too?', a: 'Yes, we deliver the public landing page alongside the actual app.' },
      { q: 'Who owns the intellectual property?', a: 'You own 100% of the IP, source code, and assets from day one.' },
      { q: 'What tech stack do you use for SaaS?', a: 'Usually Next.js frontend with Node.js/PostgreSQL backend for maximum scalability.' },
      { q: 'Will the app be secure?', a: 'We follow OWASP guidelines, implement CSRF/XSS protection, and secure APIs.' }
    ]
  },
  'landing-page': {
    title: 'Landing Page',
    h1: 'Landing Page\nDesign & Development.',
    sub: 'High-performance, conversion-optimised landing pages built for paid traffic & SEO.',
    stats: '200+ Pages Built · 12% Avg CVR · <1s Load Time · A/B Testing Ready',
    heroImage: '/images/generic-1.png',
    meta: 'Landing Page Design & Development | DevBotics',
    features: [
      { name: 'Single-page Landing Page Development', desc: 'Focused entirely on one call-to-action.' },
      { name: 'Product / SaaS Landing Page', desc: 'Showcasing features and benefits.' },
      { name: 'Event / Webinar Registration Page', desc: 'High-converting signup forms.' },
      { name: 'Coming Soon / Pre-launch Page', desc: 'Capture leads before you launch.' },
      { name: 'A/B Test Ready Structure', desc: 'Easily swap components for testing.' },
      { name: 'Conversion Rate Optimisation (CRO)', desc: 'Data-driven layout decisions.' },
      { name: 'Analytics & Heatmap Integration', desc: 'Google Analytics, Pixel, and Hotjar.' },
      { name: 'Mobile-First Responsive Design', desc: 'Perfect rendering on all devices.' }
    ],
    keyFeatures: [
      { title: 'Sub-Second Load Times', desc: 'We build static pages with Next.js/Tailwind that load instantly, preventing ad bounce.', img: '/images/generic-2.png' },
      { title: 'Engaging Micro-interactions', desc: 'Subtle Framer Motion animations that guide the user\'s eye to the CTA.', img: '/images/generic-1.png' },
      { title: 'Pixel-Perfect Tracking', desc: 'We ensure Facebook Pixel, Google Tag Manager, and UTM tracking are flawless.', img: '/images/generic-2.png' }
    ],
    techStack: ['Next.js', 'Tailwind CSS', 'Framer Motion', 'Vercel', 'Google Analytics', 'Hotjar', 'Figma', 'GTM'],
    platforms: ['Landing Page', 'Thank You Page', 'Lead Capture API', 'Analytics Dashboard', 'Email Autoresponder'],
    pricing: [
      { name: 'Starter', price: '₹18,000', desc: 'Single landing page, 1 week' },
      { name: 'Professional', price: '₹40,000', desc: 'Multi-section, animated, A/B ready' },
      { name: 'Enterprise', price: '₹75,000+', desc: 'Full funnel, multiple variants' }
    ],
    faq: [
      { q: 'How long does a landing page take?', a: 'Typically 1 week from design to live deployment.' },
      { q: 'Do you write the copy?', a: 'We provide structural wireframes. We can assist with copywriting for an extra fee.' },
      { q: 'Can you integrate Mailchimp/Hubspot?', a: 'Yes, we integrate with any CRM or email marketing tool.' },
      { q: 'Do you use Unbounce or Webflow?', a: 'We prefer custom code (Next.js/React) for speed, but can use Webflow if requested.' },
      { q: 'Will it be mobile friendly?', a: 'Always. Over 70% of ad traffic is mobile, so we design mobile-first.' },
      { q: 'Do you set up tracking pixels?', a: 'Yes, we implement GTM, Meta Pixel, and Google Analytics.' },
      { q: 'What is a good conversion rate?', a: 'Depending on the industry, 3-5% is average, but we aim for 10%+. ' },
      { q: 'Can I edit the page later?', a: 'Yes, we can connect it to a lightweight headless CMS like Sanity.' }
    ]
  },
  'custom-web': {
    title: 'Custom Web Development',
    h1: 'Custom Web\nDevelopment Company.',
    sub: 'Fully bespoke web solutions — no templates, no limits, built exactly to your vision.',
    stats: '150+ Projects · Limitless Scale · 100% Bespoke · Complex Logic',
    heroImage: '/images/generic-1.png',
    meta: 'Custom Web Development Company | DevBotics',
    features: [
      { name: 'Custom Web Application Development', desc: 'Solving unique business problems.' },
      { name: 'Web Portal / Directory Development', desc: 'Complex data structures and search.' },
      { name: 'Booking & Scheduling Platform', desc: 'Custom calendars and availability logic.' },
      { name: 'Real-time Web Application', desc: 'WebSockets for live updates.' },
      { name: 'Custom CMS Development', desc: 'When WordPress isn\'t enough.' },
      { name: 'Progressive Web App (PWA)', desc: 'Installable web experiences.' },
      { name: 'Chrome Extension Development', desc: 'Browser-based utilities.' },
      { name: 'API-first Architecture Design', desc: 'Headless from day one.' }
    ],
    keyFeatures: [
      { title: 'Complex Business Logic', desc: 'We specialize in translating convoluted spreadsheet workflows into elegant web software.', img: '/images/generic-2.png' },
      { title: 'Modern Tech Stacks', desc: 'We don\'t force you into one language. We pick the right tool (Node, Go, Python) for the job.', img: '/images/generic-1.png' },
      { title: 'Scalable Cloud Infrastructure', desc: 'AWS/GCP architectures designed to scale from 10 to 100,000 concurrent users seamlessly.', img: '/images/generic-2.png' }
    ],
    techStack: ['React', 'Vue', 'Angular', 'Node.js', 'Python', 'Go', 'AWS', 'Docker', 'Kubernetes'],
    platforms: ['Web Client', 'Admin Dashboard', 'Microservices', 'GraphQL API', 'Data Pipelines'],
    pricing: [
      { name: 'Discovery Phase', price: '₹25,000', desc: 'Architecture & UI/UX wireframes' },
      { name: 'MVP Build', price: 'From ₹1.5L', desc: 'Core feature set for launch' },
      { name: 'Full Scale', price: 'Custom', desc: 'Complete enterprise application' }
    ],
    faq: [
      { q: 'What is "Custom" web development?', a: 'Building a system completely from scratch with code, rather than piecing together plugins on WordPress or Shopify.' },
      { q: 'How do you price custom projects?', a: 'Based on the estimated hours to build the required features. We do a thorough discovery phase first.' },
      { q: 'Do you sign NDAs?', a: 'Yes, your intellectual property and ideas are strictly protected.' },
      { q: 'Will I own the source code?', a: 'Yes, 100% intellectual property rights are transferred to you upon final payment.' },
      { q: 'How do we track progress?', a: 'Through Jira/Trello boards, weekly sprint demos, and staging environment links.' },
      { q: 'Can you work with my existing in-house team?', a: 'Yes, we frequently augment existing teams or handle specific microservices.' },
      { q: 'What happens if we need to scale?', a: 'Our architectures are designed for horizontal scaling on AWS/GCP from the start.' },
      { q: 'Do you write documentation?', a: 'Yes, thorough API and codebase documentation is provided.' }
    ]
  }
};

