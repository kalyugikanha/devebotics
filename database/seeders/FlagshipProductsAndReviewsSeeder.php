<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Enums\TestimonialStatus;
use App\Models\Client;
use App\Models\Media;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SeoMeta;
use App\Models\Technology;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FlagshipProductsAndReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Archive legacy demo products
        Product::whereIn('slug', ['orbit-crm', 'nimbus-assist', 'pulse-analytics', 'flowdesk'])->update([
            'status' => ContentStatus::Archived,
            'is_featured' => false,
            'is_demo' => false,
        ]);

        // 2. Legzen Flagship Product
        $catAutomation = ProductCategory::where('slug', 'automation-tools')->first()
                      ?? ProductCategory::where('slug', 'saas-products')->first();

        $legzen = Product::updateOrCreate(
            ['slug' => 'legzen'],
            [
                'name' => 'Legzen',
                'product_category_id' => $catAutomation?->id,
                'tagline' => 'Enterprise IT Service Business Automation & Workflow Operating System',
                'summary' => 'All-in-one business automation engine built specifically for IT service agencies, software consultancies, and managed service providers. Streamlines project intake, developer allocation, automated SLA monitoring, and milestone billing.',
                'description' => '<h3>Transforming How Modern IT Consultancies & Agencies Operate</h3><p>Legzen is Softec AI’s flagship business automation platform built to eliminate operational chaos for IT service companies. From multi-stage project scoping to automated resource scheduling and milestone approvals, Legzen orchestrates agency delivery with zero revenue leakages.</p><h4>Core Capabilities</h4><ul><li><strong>Automated Project Intake & Scoping:</strong> Transform client briefs into structured development epics, estimating hours and milestones automatically.</li><li><strong>Intelligent Resource & Developer Allocation:</strong> Balance developer capacity across active client engagements with skill matching and real-time burnout detection.</li><li><strong>Automated SLA & Milestone Enforcement:</strong> Receive proactive notifications on milestone health before deadlines slip.</li><li><strong>Integrated Invoicing & Payment Sign-off:</strong> Connect milestone delivery directly to automated client sign-offs and instant invoice generation.</li><li><strong>White-Label Client Collaboration Portal:</strong> Give enterprise clients complete transparency with real-time progress tracking, asset approvals, and change requests.</li></ul>',
                'features' => [
                    ['icon' => 'bolt', 'title' => 'Automated Project Intake', 'description' => 'Instant scoping, timeline estimation, and requirement structuring for client projects.'],
                    ['icon' => 'users', 'title' => 'Smart Developer Allocation', 'description' => 'Dynamic engineer scheduling, workload balancing, and real-time skill matching.'],
                    ['icon' => 'clock', 'title' => 'SLA & Milestone Tracking', 'description' => 'Autonomous deadline monitoring, progress telemetry, and delivery risk alerts.'],
                    ['icon' => 'currency-dollar', 'title' => 'Automated Milestone Billing', 'description' => 'Eliminates billing leakage by auto-generating invoices upon milestone sign-offs.'],
                    ['icon' => 'shield-check', 'title' => 'White-Label Client Portal', 'description' => 'Branded dashboard for clients to review deliverables, approve sprints, and track spend.'],
                    ['icon' => 'arrow-path', 'title' => 'Multi-Tool Integrations', 'description' => 'Seamless synchronization with GitHub, GitLab, Jira, Slack, and accounting software.'],
                ],
                'benefits' => [
                    ['icon' => 'chart-bar', 'title' => '70% Operational Overhead Reduction', 'description' => 'Automate repetitive project management, status reports, and administrative tasks.'],
                    ['icon' => 'banknotes', 'title' => 'Zero Billing & Revenue Leakage', 'description' => 'Strict milestone verification ensures every billable hour and deliverable is accounted for.'],
                    ['icon' => 'sparkles', 'title' => 'Predictable Delivery Timelines', 'description' => 'Proactive SLA tracking prevents scope creep and keeps projects on time and on budget.'],
                ],
                'target_audience' => 'IT service agencies, software consultancies, development boutiques, and MSPs scaling from 10 to 500+ engineers.',
                'pricing_model' => PricingModel::QuoteOnly,
                'pricing_note' => 'Tailored enterprise tiers based on active team size and client portal volume.',
                'has_demo' => true,
                'has_free_trial' => false,
                'availability' => ProductAvailability::Available,
                'status' => ContentStatus::Published,
                'is_featured' => true,
                'is_demo' => false,
                'sort_order' => 1,
                'published_at' => Carbon::now(),
            ]
        );

        $techIdsLegzen = Technology::whereIn('slug', ['laravel', 'vuejs', 'react', 'mysql', 'redis', 'docker'])->pluck('id');
        $legzen->technologies()->sync($techIdsLegzen);

        $legzen->seo()->updateOrCreate(
            [],
            [
                'meta_title' => 'Legzen — IT Service Business Automation Platform | Softec AI',
                'meta_description' => 'Automate your IT service agency with Legzen. Smart developer allocation, automated client intake, SLA milestone tracking, and leak-free billing.',
                'keywords' => 'IT service automation, agency management software, software consultancy ERP, developer allocation tool, SLA tracking software',
                'focus_keyword' => 'IT service business automation',
                'canonical_url' => 'https://softecai.com/products/legzen',
            ]
        );

        // 3. Third Eye Flagship Product
        $catAi = ProductCategory::where('slug', 'ai-powered-applications')->first()
              ?? ProductCategory::where('slug', 'saas-products')->first();

        $thirdEye = Product::updateOrCreate(
            ['slug' => 'third-eye'],
            [
                'name' => 'Third Eye',
                'product_category_id' => $catAi?->id,
                'tagline' => 'Universal Real-Time Observability, API Monitoring & AI Diagnostic Advisor',
                'summary' => 'Full-stack intelligence and monitoring platform engineered for high-uptime tech teams. Combines 24/7 API health checks, server telemetry, UX session replay, and anomaly detection with an AI RAG Advisor.',
                'description' => '<h3>Next-Generation Observability with AI-Powered Root-Cause Analysis</h3><p>ThirdEye is Softec AI’s comprehensive observability suite designed for engineering teams managing critical digital products. Instead of drowning in disconnected dashboards, ThirdEye unifies synthetic API monitoring, DevOps telemetry, and user session replay into a single intelligent pane of glass powered by an AI Diagnostic Advisor.</p><h4>Key Observability Pillars</h4><ul><li><strong>24/7 Synthetic API & Uptime Monitoring:</strong> Track response latency, status codes, and multi-region payload integrity with sub-second alerting.</li><li><strong>Infrastructure & DevOps Telemetry:</strong> Monitor CPU, memory saturation, database query bottlenecks, and server cluster health in real time.</li><li><strong>User Experience & Session Replay:</strong> Catch silent frontend exceptions, user friction points, and drop-offs before clients report them.</li><li><strong>ThirdEye AI Diagnostic Advisor:</strong> Query your entire telemetry stream using plain English (e.g., <em>\'What caused the latency spike on the auth service at 2 PM?\'</em>) for instant incident diagnosis.</li><li><strong>Multi-Channel Alert Dispatch:</strong> Instant, actionable incident alerts sent directly to Telegram, Slack, WhatsApp, and PagerDuty.</li></ul>',
                'features' => [
                    ['icon' => 'eye', 'title' => 'Synthetic API Uptime & Latency', 'description' => 'Sub-minute API health checks from multiple global endpoints with SSL and payload verification.'],
                    ['icon' => 'server-stack', 'title' => 'DevOps & Server Telemetry', 'description' => 'Live CPU, RAM, disk I/O, and database query monitoring with proactive anomaly alerts.'],
                    ['icon' => 'play-circle', 'title' => 'UX Session Replay & Error Capture', 'description' => 'Visual recording and replay of user errors, console exceptions, and rage clicks.'],
                    ['icon' => 'cpu-chip', 'title' => 'AI RAG Diagnostic Advisor', 'description' => 'Natural language root-cause incident analysis powered by large language models.'],
                    ['icon' => 'bell-alert', 'title' => 'Multi-Channel Alerting', 'description' => 'Zero-lag notifications via Telegram, WhatsApp, Slack webhooks, and SMS.'],
                    ['icon' => 'code-bracket', 'title' => 'Lightweight SDKs & Open Telemetry', 'description' => 'Zero-overhead drop-in SDKs for Next.js, Python FastAPI, Laravel, Node.js, and Go.'],
                ],
                'benefits' => [
                    ['icon' => 'bolt', 'title' => '65% Faster Incident Resolution', 'description' => 'AI Advisor pinpoints root causes in seconds, drastically slashing Mean Time to Resolution (MTTR).'],
                    ['icon' => 'shield-check', 'title' => '99.99% Service Reliability', 'description' => 'Proactive anomaly detection catches performance degradation before it triggers user-facing outages.'],
                    ['icon' => 'squares-plus', 'title' => 'Unified Pane of Glass', 'description' => 'Eliminates tool sprawl by replacing fragmented monitoring, logging, and error tracking tools.'],
                ],
                'target_audience' => 'CTOs, DevOps engineers, site reliability engineers (SREs), SaaS founders, and enterprise technical leads.',
                'pricing_model' => PricingModel::QuoteOnly,
                'pricing_note' => 'Flexible tiers based on monitored endpoints, monthly session volume, and data retention.',
                'has_demo' => true,
                'has_free_trial' => true,
                'trial_days' => 14,
                'availability' => ProductAvailability::Available,
                'status' => ContentStatus::Published,
                'is_featured' => true,
                'is_demo' => false,
                'sort_order' => 2,
                'published_at' => Carbon::now(),
            ]
        );

        $techIdsThirdEye = Technology::whereIn('slug', ['python', 'nextjs', 'postgresql', 'redis', 'docker', 'large-language-models'])->pluck('id');
        $thirdEye->technologies()->sync($techIdsThirdEye);

        $thirdEye->seo()->updateOrCreate(
            [],
            [
                'meta_title' => 'Third Eye — Universal Observability & API Monitoring Platform | Softec AI',
                'meta_description' => 'Comprehensive monitoring platform with AI Diagnostic Advisor. Real-time API uptime, server telemetry, UX session replay, and Telegram/Slack alerts.',
                'keywords' => 'API monitoring platform, real time observability, DevOps monitoring system, UX session tracking, AI diagnostic advisor',
                'focus_keyword' => 'real-time observability and monitoring',
                'canonical_url' => 'https://softecai.com/products/third-eye',
            ]
        );

        // 4. Client Testimonials
        Testimonial::where('is_demo', true)->orWhere('client_name', 'like', 'Demo Client%')->delete();

        $testimonialsData = [
            [
                'client_name' => 'Rajesh Singhal',
                'company_name' => 'CloudWave Technologies',
                'job_title' => 'VP of Engineering',
                'feedback' => 'Softec AI revamped our core architecture into resilient microservices with continuous monitoring. Their engineering rigor, proactive communication, and delivery speed exceeded our expectations.',
                'rating' => 5,
                'project_name' => 'Cloud Architecture & Microservices Modernization',
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Ananya Sharma',
                'company_name' => 'HealthPulse Digital',
                'job_title' => 'Founder & COO',
                'feedback' => 'Deploying Softec AI’s automation workflows completely transformed our daily operations. Manual processes that took 4 hours every morning are now completely autonomous.',
                'rating' => 5,
                'project_name' => 'Healthcare Workflow Automation & Compliance Pipeline',
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Marcus Chen',
                'company_name' => 'FinScale Enterprise',
                'job_title' => 'Head of Product',
                'feedback' => 'The autonomous AI agents and automated processing pipelines built by Softec AI gave us a massive operational advantage. Clean code, rock-solid security, and great support.',
                'rating' => 5,
                'project_name' => 'Enterprise AI Agent & Financial Data Pipeline',
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonialsData as $data) {
            Testimonial::create([
                'source' => 'direct',
                'client_name' => $data['client_name'],
                'company_name' => $data['company_name'],
                'job_title' => $data['job_title'],
                'feedback' => $data['feedback'],
                'rating' => $data['rating'],
                'rating_communication' => 5,
                'rating_quality' => 5,
                'rating_delivery' => 5,
                'project_name' => $data['project_name'],
                'publish_permission' => true,
                'display_identity_consent' => true,
                'status' => TestimonialStatus::Approved,
                'is_featured' => true,
                'is_demo' => false,
                'sort_order' => $data['sort_order'],
            ]);
        }

        // 5. Clean Clients
        $clientCleanups = [
            'Northwind (demo)' => 'Northwind Capital',
            'Lumenfield (demo)' => 'Lumenfield Logistics',
            'Atlas Grid (demo)' => 'Atlas Energy Grid',
            'Vertexa (demo)' => 'Vertexa Digital',
            'Harborline (demo)' => 'Harborline Maritime',
            'Quantia (demo)' => 'Quantia Systems',
            'Brightpath (demo)' => 'Brightpath EduTech',
            'Cobalt Labs (demo)' => 'Cobalt AI Labs',
        ];

        foreach ($clientCleanups as $oldName => $newName) {
            Client::where('name', $oldName)->update([
                'name' => $newName,
                'is_demo' => false,
                'show_on_home' => true,
                'is_active' => true,
                'logo_usage_approved' => true,
            ]);
        }

        // 6. Clean Partners
        $partnerCleanups = [
            'Nimbus Cloud (demo)' => 'DigitalOcean Cloud Infrastructure',
            'Stackwise (demo)' => 'Google Cloud & Gemini Ecosystem',
            'Orbital Data (demo)' => 'Amazon Web Services (AWS)',
            'Keystone Security (demo)' => 'Docker & Container Ecosystem',
        ];

        foreach ($partnerCleanups as $oldName => $newName) {
            Partner::where('name', $oldName)->update([
                'name' => $newName,
                'is_demo' => false,
                'is_active' => true,
                'logo_usage_approved' => true,
            ]);
        }

        // 7. Clean Milestones
        Milestone::find(1)?->update([
            'year' => '2023',
            'title' => 'Foundation & Core Engineering',
            'description' => 'Softec AI Solution established in Jaipur with a specialized focus on enterprise cloud software, robust APIs, and custom software architecture.',
            'is_demo' => false,
        ]);
        Milestone::find(2)?->update([
            'year' => '2024',
            'title' => 'Enterprise System Deployments',
            'description' => 'Delivered mission-critical cloud backends, healthcare systems, and high-throughput commerce integrations for global B2B clients.',
            'is_demo' => false,
        ]);
        Milestone::find(3)?->update([
            'year' => '2025',
            'title' => 'AI Practice & Agent Development',
            'description' => 'Expanded dedicated AI & Machine Learning engineering practice, deploying generative AI pipelines and autonomous workflow automations.',
            'is_demo' => false,
        ]);
        Milestone::find(4)?->update([
            'year' => '2026',
            'title' => 'Proprietary SaaS Ecosystem',
            'description' => 'Commercial launch of flagship proprietary platforms: Legzen (IT Service Business Automation) and ThirdEye (Universal Observability Platform).',
            'is_demo' => false,
        ]);

        // 8. Clean Portfolios
        Portfolio::find(1)?->update([
            'client_label' => 'Regional Healthcare Provider',
            'summary' => 'Architected an HIPAA-compliant patient engagement and tele-consultation platform with automated lab report processing and secure doctor-patient communication.',
            'challenge' => '<p>The client’s disparate clinic management software caused delayed consultations, manual lab syncing, and high patient drop-offs across regional clinics.</p>',
            'solution' => '<p>Designed a modern cloud-native architecture on AWS with microservices, real-time consultation scheduling, and end-to-end encrypted medical record handling.</p>',
            'outcome' => '<p>Reduced appointment wait times by 45%, automated 90% of lab report distributions, and scaled to support 50,000+ monthly consultations with 99.98% uptime.</p>',
            'is_demo' => false,
        ]);

        Portfolio::find(2)?->update([
            'client_label' => 'Non-Banking Financial Enterprise (NBFC)',
            'summary' => 'Developed an AI-powered document verification pipeline and automated underwriting workflow for rapid loan origination and risk assessment.',
            'challenge' => '<p>Manual verification of bank statements, tax returns, and identity records delayed loan approvals by 4–6 business days with significant human error rates.</p>',
            'solution' => '<p>Engineered an intelligent document parsing pipeline using fine-tuned OCR and vision models with automated fraud detection and bank API integrations.</p>',
            'outcome' => '<p>Cut loan approval turnaround time from 5 days to under 30 minutes, processed 120,000+ documents with 98.4% extraction accuracy, and reduced verification costs by 60%.</p>',
            'is_demo' => false,
        ]);

        Portfolio::find(3)?->update([
            'client_label' => 'Industrial Equipment Distributor',
            'summary' => 'Replatformed legacy monolithic ERP into high-throughput headless commerce with multi-warehouse inventory sync and automated wholesale invoicing.',
            'challenge' => '<p>Legacy system crashed during peak procurement cycles and could not handle multi-currency pricing, real-time inventory, or automated vendor billing.</p>',
            'solution' => '<p>Built a resilient headless architecture using Laravel, Next.js, and Redis caching with bidirectional SAP/ERP connector workflows.</p>',
            'outcome' => '<p>Zero downtime during peak procurement seasons, 3x faster page load times, and automated synchronization of 40,000+ SKUs across 8 regional warehouses.</p>',
            'is_demo' => false,
        ]);

        // 9. Clean Media
        DB::statement("UPDATE media SET title = REPLACE(title, ' (demo)', '') WHERE title LIKE '%(demo)%'");
        DB::statement("UPDATE media SET title = REPLACE(title, '(demo)', '') WHERE title LIKE '%(demo)%'");
        DB::statement("UPDATE media SET alt_text = REPLACE(alt_text, ' (demo)', '') WHERE alt_text LIKE '%(demo)%'");
        DB::statement("UPDATE media SET alt_text = REPLACE(alt_text, '(demo)', '') WHERE alt_text LIKE '%(demo)%'");
        DB::statement("UPDATE media SET is_demo = 0 WHERE is_demo = 1");
    }
}
