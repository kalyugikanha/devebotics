<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectEstimatorService
{
    /**
     * Calibrate and estimate project scope and cost.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function estimate(array $input): array
    {
        $prompt = trim((string) ($input['idea_prompt'] ?? ''));
        $projectType = (string) ($input['project_type'] ?? 'website');
        $scale = (string) ($input['scale'] ?? 'starter');
        $features = (array) ($input['features'] ?? []);
        $urgency = (string) ($input['urgency'] ?? 'standard');

        $apiKey = config('services.gemini.key') 
            ?: env('GEMINI_API_KEY') 
            ?: env('GOOGLE_AI_KEY');

        if ($apiKey) {
            try {
                $aiEstimate = $this->estimateWithGemini($apiKey, $prompt, $projectType, $scale, $features, $urgency);
                if ($aiEstimate && !empty($aiEstimate['cost_min_inr'])) {
                    return $aiEstimate;
                }
            } catch (\Throwable $e) {
                Log::warning('[ProjectEstimator] Gemini API fallback triggered: ' . $e->getMessage());
            }
        }

        // Resilient Heuristic Fallback Engine
        return $this->estimateWithHeuristics($prompt, $projectType, $scale, $features, $urgency);
    }

    /**
     * Call Google Gemini API to analyze scope and provide calibrated pricing.
     */
    protected function estimateWithGemini(
        string $apiKey,
        string $prompt,
        string $projectType,
        string $scale,
        array $features,
        string $urgency
    ): ?array {
        $systemInstructions = <<<EOT
You are an expert Chief Solutions Architect at Softec AI Solution (softecai.com), an enterprise software, web engineering, and AI consultancy based in Jaipur, India.
Your job is to analyze client requirements (which may be in plain English, Hindi, or Hinglish) and provide an accurate, honest, realistic project scope and cost breakdown.

PRICING CALIBRATION BENCHMARKS:
- Simple Starter Website / 1-5 Page Landing Page: ₹5,000 to ₹10,000 INR ($60 - $120 USD).
- Growth Business Website / Dynamic CMS (5-12 pages, SEO, blog): ₹12,000 to ₹25,000 INR ($150 - $300 USD).
- E-Commerce Catalog / Store (Payment gateway, cart, inventory): ₹20,000 to ₹45,000 INR ($250 - $550 USD).
- Custom Web App / SaaS / Client Portal / Booking Engine: ₹35,000 to ₹75,000 INR ($420 - $900 USD).
- Enterprise IT Automation / Agency OS (Legzen-Class workflows, SLA monitoring): ₹65,000 to ₹1,60,000 INR ($780 - $1,900 USD).
- AI Agents / Telemetry / Observability (ThirdEye-Class RAG bots, real-time monitoring): ₹80,000 to ₹2,20,000+ INR ($950 - $2,600+ USD).

If the user has provided a vague or loose idea with no technical scope, acts as a Senior Architect:
- Break the idea into a concrete Phase 1 (MVP) scope.
- Suggest a modern, battle-tested tech stack (e.g., Laravel, Vue/React/Next.js, Tailwind, MySQL, Docker).
- Highlight 1-2 cost-saving tips on how they can launch their MVP quickly on a sensible budget.

Output MUST be strictly valid JSON matching this schema:
{
  "project_title": "Concise professional project title",
  "project_type": "Starter Website | Business CMS | E-Commerce | Web Application | Enterprise Automation | AI Solution",
  "cost_min_inr": integer (minimum INR price, e.g. 5000),
  "cost_max_inr": integer (maximum INR price, e.g. 10000),
  "cost_min_usd": integer (approx USD),
  "cost_max_usd": integer (approx USD),
  "timeline_weeks": "Estimated delivery timeline, e.g. 3-7 days or 2-3 weeks",
  "recommended_stack": ["Stack1", "Stack2", "Stack3"],
  "executive_summary": "2-3 sentences explaining the architecture and vision",
  "mvp_scope_items": [
    "Deliverable 1 (e.g. Responsive Mobile-First Landing UI)",
    "Deliverable 2 (e.g. WhatsApp Direct Inquiry & Contact Form)",
    "Deliverable 3",
    "Deliverable 4",
    "Deliverable 5"
  ],
  "phase_2_recommendations": [
    "Future enhancement 1",
    "Future enhancement 2"
  ],
  "cost_saving_tip": "Specific practical advice to keep launch costs minimal",
  "suggested_softec_capability": "Relevant Softec AI service capability name"
}
EOT;

        $userContent = "CLIENT PROJECT INPUT:\n";
        if (!empty($prompt)) {
            $userContent .= "Idea / Problem Statement: {$prompt}\n";
        }
        $userContent .= "Selected Type: {$projectType}\n";
        $userContent .= "Scale: {$scale}\n";
        $userContent .= "Selected Features: " . (empty($features) ? 'None specified' : implode(', ', $features)) . "\n";
        $userContent .= "Timeline Urgency: {$urgency}\n";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(12)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemInstructions . "\n\n" . $userContent],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.3,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $decoded = json_decode($rawText, true);
            if (is_array($decoded) && isset($decoded['cost_min_inr'])) {
                // Ensure baseline minimum floor of ₹5,000
                $decoded['cost_min_inr'] = max(5000, (int) $decoded['cost_min_inr']);
                $decoded['cost_max_inr'] = max($decoded['cost_min_inr'], (int) $decoded['cost_max_inr']);
                $decoded['cost_min_usd'] = max(60, (int) ($decoded['cost_min_usd'] ?? round($decoded['cost_min_inr'] / 84)));
                $decoded['cost_max_usd'] = max($decoded['cost_min_usd'], (int) ($decoded['cost_max_usd'] ?? round($decoded['cost_max_inr'] / 84)));
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Resilient Heuristic Scope and Pricing Engine.
     */
    protected function estimateWithHeuristics(
        string $prompt,
        string $projectType,
        string $scale,
        array $features,
        string $urgency
    ): array {
        $baseMinInr = 5000;
        $baseMaxInr = 10000;
        $timeline = '3 - 7 days';
        $title = 'Starter Business Website';
        $typeLabel = 'Starter Website';
        $stack = ['Laravel / HTML5', 'Tailwind CSS', 'Alpine.js', 'MySQL'];
        $capability = 'Custom Web Development';

        $promptLower = mb_strtolower($prompt);

        if (str_contains($promptLower, 'ai') || str_contains($promptLower, 'agent') || str_contains($promptLower, 'bot') || str_contains($promptLower, 'monitoring') || $projectType === 'ai_agent') {
            $baseMinInr = 75000;
            $baseMaxInr = 180000;
            $timeline = '3 - 6 weeks';
            $title = 'AI-Powered Agent & Automation Engine';
            $typeLabel = 'AI Solution';
            $stack = ['Python FastAPI', 'Next.js 15', 'LangChain / Gemini API', 'PostgreSQL', 'Redis'];
            $capability = 'AI Agent Development';
        } elseif (str_contains($promptLower, 'app') || str_contains($promptLower, 'mobile') || str_contains($promptLower, 'android') || str_contains($promptLower, 'ios') || $projectType === 'mobile_app') {
            $baseMinInr = 40000;
            $baseMaxInr = 95000;
            $timeline = '3 - 5 weeks';
            $title = 'Cross-Platform Mobile Application';
            $typeLabel = 'Mobile Application';
            $stack = ['Flutter', 'Node.js / Laravel API', 'Firebase', 'PostgreSQL'];
            $capability = 'Mobile App Development';
        } elseif (str_contains($promptLower, 'crm') || str_contains($promptLower, 'erp') || str_contains($promptLower, 'agency') || str_contains($promptLower, 'automation') || $projectType === 'enterprise_it') {
            $baseMinInr = 60000;
            $baseMaxInr = 140000;
            $timeline = '4 - 7 weeks';
            $title = 'Enterprise IT Workflow Automation (Legzen-Class)';
            $typeLabel = 'Enterprise Automation';
            $stack = ['Laravel 11', 'Vue.js / React', 'Redis Queues', 'MySQL', 'Docker'];
            $capability = 'Enterprise IT Automation';
        } elseif (str_contains($promptLower, 'portal') || str_contains($promptLower, 'saas') || str_contains($promptLower, 'dashboard') || str_contains($promptLower, 'booking') || $projectType === 'saas_portal') {
            $baseMinInr = 30000;
            $baseMaxInr = 65000;
            $timeline = '2 - 4 weeks';
            $title = 'Custom Web Application & Portal';
            $typeLabel = 'Web Application';
            $stack = ['Laravel / Next.js', 'Tailwind CSS', 'PostgreSQL', 'Razorpay'];
            $capability = 'SaaS & Custom Application';
        } elseif (str_contains($promptLower, 'ecommerce') || str_contains($promptLower, 'shop') || str_contains($promptLower, 'store') || $projectType === 'ecommerce') {
            $baseMinInr = 18000;
            $baseMaxInr = 40000;
            $timeline = '2 - 3 weeks';
            $title = 'High-Conversion E-Commerce Store';
            $typeLabel = 'E-Commerce';
            $stack = ['Laravel / WooCommerce', 'Tailwind CSS', 'Razorpay / Stripe', 'MySQL'];
            $capability = 'E-Commerce Development';
        } elseif ($scale === 'growth') {
            $baseMinInr = 12000;
            $baseMaxInr = 22000;
            $timeline = '1 - 2 weeks';
            $title = 'Growth Business Website & CMS';
            $typeLabel = 'Business CMS';
            $stack = ['Laravel', 'Tailwind CSS', 'Alpine.js', 'MySQL'];
            $capability = 'Custom Web Development';
        }

        // Adjust for scale
        if ($scale === 'enterprise') {
            $baseMinInr = (int) round($baseMinInr * 1.5);
            $baseMaxInr = (int) round($baseMaxInr * 1.6);
        }

        // Feature add-ons
        $featureAddonCost = 0;
        if (in_array('payments', $features)) $featureAddonCost += 4000;
        if (in_array('auth', $features)) $featureAddonCost += 3500;
        if (in_array('dashboard', $features)) $featureAddonCost += 6000;
        if (in_array('ai_chatbot', $features)) $featureAddonCost += 8000;
        if (in_array('whatsapp', $features)) $featureAddonCost += 2500;

        $finalMinInr = $baseMinInr + $featureAddonCost;
        $finalMaxInr = $baseMaxInr + (int) round($featureAddonCost * 1.3);

        if ($urgency === 'rush') {
            $finalMinInr = (int) round($finalMinInr * 1.25);
            $finalMaxInr = (int) round($finalMaxInr * 1.25);
            $timeline .= ' (Fast-Track Rush)';
        }

        $minUsd = (int) round($finalMinInr / 84);
        $maxUsd = (int) round($finalMaxInr / 84);

        $summary = !empty($prompt)
            ? "Based on your idea ('{$prompt}'), we have structured an agile MVP roadmap that focuses on core high-impact features first, keeping initial capital lean and time-to-market rapid."
            : "A robust, scalable architectural blueprint engineered with clean code, responsive performance, and built-in search engine optimization.";

        return [
            'project_title' => $title,
            'project_type' => $typeLabel,
            'cost_min_inr' => $finalMinInr,
            'cost_max_inr' => $finalMaxInr,
            'cost_min_usd' => $minUsd,
            'cost_max_usd' => $maxUsd,
            'timeline_weeks' => $timeline,
            'recommended_stack' => $stack,
            'executive_summary' => $summary,
            'mvp_scope_items' => [
                'Responsive UI/UX design optimized for all screen sizes',
                'Core business workflows and responsive interactive components',
                'WhatsApp quick-connect & automated lead capture integration',
                'On-page SEO optimization with schema markup and Google Search indexing',
                'Production cloud deployment with SSL security and domain setup',
            ],
            'phase_2_recommendations' => [
                'Automated customer analytics and behavior heatmaps',
                'Custom AI diagnostic assistant or automated WhatsApp bot notifications',
            ],
            'cost_saving_tip' => 'Launch Phase 1 with the essential MVP deliverables first. Validate real customer interest before investing in complex secondary features.',
            'suggested_softec_capability' => $capability,
        ];
    }
}
