<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" illustration="strategy" eyebrow="AI Scope & Cost Architect" title="Calculate your [project cost & scope]"
        intro="Instant architectural breakdown, realistic timeline and transparent pricing in ₹ INR and $ USD. Starter business websites start from ₹5,000 to ₹10,000." />

    <section class="section pt-10" id="calculator-section" x-data="projectCalculator()" x-cloak>
        <div class="container-site max-w-6xl">

            {{-- Main Calculator Card --}}
            <div class="grid gap-8 lg:grid-cols-12">

                {{-- Left Column: Input Form --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Mode Selector Tabs --}}
                    <div class="rounded-panel border border-border bg-background p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-border/70 pb-4">
                            <div>
                                <h2 class="font-display text-lg font-bold text-foreground">How would you like to estimate?</h2>
                                <p class="text-xs text-muted mt-0.5">Choose between plain English/Hindi description or structured builder</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-surface/70 p-1.5 border border-border/50">
                            <button type="button" @click="tab = 'idea'" 
                                    :class="tab === 'idea' ? 'bg-background text-foreground shadow-sm font-semibold' : 'text-muted hover:text-foreground'"
                                    class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-xs sm:text-sm transition-all duration-200">
                                <x-heroicon-m-sparkles class="size-4 text-brand-violet" />
                                <span>Describe Idea (AI Defines Scope)</span>
                            </button>
                            <button type="button" @click="tab = 'builder'" 
                                    :class="tab === 'builder' ? 'bg-background text-foreground shadow-sm font-semibold' : 'text-muted hover:text-foreground'"
                                    class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-xs sm:text-sm transition-all duration-200">
                                <x-heroicon-m-adjustments-horizontal class="size-4 text-brand-cyan" />
                                <span>Interactive Scope Builder</span>
                            </button>
                        </div>

                        {{-- Tab 1: Describe Any Idea --}}
                        <div x-show="tab === 'idea'" x-transition class="mt-6 space-y-4">
                            <label for="idea_prompt" class="block text-sm font-semibold text-foreground">
                                Describe your project or problem statement:
                                <span class="block text-xs font-normal text-muted mt-0.5">Write in English, Hindi, or Hinglish (e.g., "Mujhe gym ke liye membership and slot booking system chahiye")</span>
                            </label>
                            
                            <textarea id="idea_prompt" x-model="ideaPrompt" rows="4" 
                                      placeholder="e.g. I need a modern 5-page responsive website for my dental clinic in Jaipur with online appointment booking and WhatsApp direct inquiry button..."
                                      class="form-control w-full resize-y text-sm"></textarea>

                            {{-- Quick Inspiration Pills --}}
                            <div>
                                <span class="text-xs font-medium text-muted block mb-2">Try quick examples:</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="example in examples" :key="example.label">
                                        <button type="button" @click="ideaPrompt = example.prompt; projectType = example.type; scale = example.scale;" 
                                                class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                            <span x-text="example.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Structured Scope Builder --}}
                        <div x-show="tab === 'builder'" x-transition class="mt-6 space-y-6">
                            {{-- Project Type --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">1. Project Category</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    <template x-for="pt in projectTypes" :key="pt.id">
                                        <button type="button" @click="projectType = pt.id"
                                                :class="projectType === pt.id ? 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground' : 'border-border bg-surface/40 hover:border-border/90 text-foreground/80'"
                                                class="rounded-xl border p-3 text-left transition-all">
                                            <div class="font-semibold text-xs sm:text-sm" x-text="pt.name"></div>
                                            <div class="text-[0.6875rem] text-muted mt-0.5" x-text="pt.baseline"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Scale & Pages --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">2. Scale / Page Count</label>
                                <div class="grid grid-cols-3 gap-2.5">
                                    <button type="button" @click="scale = 'starter'"
                                            :class="scale === 'starter' ? 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground' : 'border-border bg-surface/40 text-foreground/80'"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Starter</div>
                                        <div class="text-[0.6875rem] text-muted">1 - 5 Pages / MVP</div>
                                    </button>
                                    <button type="button" @click="scale = 'growth'"
                                            :class="scale === 'growth' ? 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground' : 'border-border bg-surface/40 text-foreground/80'"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Growth</div>
                                        <div class="text-[0.6875rem] text-muted">5 - 15 Pages / CMS</div>
                                    </button>
                                    <button type="button" @click="scale = 'enterprise'"
                                            :class="scale === 'enterprise' ? 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground' : 'border-border bg-surface/40 text-foreground/80'"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Enterprise</div>
                                        <div class="text-[0.6875rem] text-muted">Multi-Module Platform</div>
                                    </button>
                                </div>
                            </div>

                            {{-- Feature Addons --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">3. Essential Features</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="f in featureList" :key="f.id">
                                        <label :class="features.includes(f.id) ? 'border-brand-violet/80 bg-brand-violet/5' : 'border-border bg-surface/30'"
                                               class="flex items-center gap-2.5 rounded-lg border p-2.5 cursor-pointer text-xs transition-all">
                                            <input type="checkbox" :value="f.id" x-model="features" class="rounded border-border text-brand-violet focus:ring-brand-violet/20 size-4">
                                            <span class="font-medium text-foreground" x-text="f.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            {{-- Timeline Urgency --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">4. Delivery Timeline</label>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <button type="button" @click="urgency = 'standard'"
                                            :class="urgency === 'standard' ? 'border-brand-violet bg-brand-violet/10 text-foreground' : 'border-border bg-surface/40 text-muted'"
                                            class="rounded-xl border p-2.5 text-center text-xs font-medium transition-all">
                                        Standard Turnaround
                                    </button>
                                    <button type="button" @click="urgency = 'rush'"
                                            :class="urgency === 'rush' ? 'border-brand-violet bg-brand-violet/10 text-foreground' : 'border-border bg-surface/40 text-muted'"
                                            class="rounded-xl border p-2.5 text-center text-xs font-medium transition-all">
                                        ⚡ Fast-Track / Rush Delivery
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="mt-8 pt-4 border-t border-border/70 flex items-center justify-between">
                            <span class="text-xs text-muted">
                                <span class="font-semibold text-foreground">100% Free:</span> No registration or card needed
                            </span>
                            <button type="button" @click="calculate()" :disabled="loading"
                                    class="btn btn-primary inline-flex items-center gap-2 px-6 py-3 font-semibold shadow-lg shadow-brand-violet/20 hover:shadow-brand-violet/30 transition-all">
                                <template x-if="!loading">
                                    <span class="flex items-center gap-2">
                                        <x-heroicon-m-sparkles class="size-4" />
                                        <span>Calculate Cost & Scope</span>
                                    </span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                        <span>Analyzing Architecture...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>

                    {{-- Baseline Notice Card --}}
                    <div class="rounded-xl border border-border/80 bg-surface/40 p-4 text-xs text-muted flex items-start gap-3">
                        <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-500 shrink-0">
                            <x-heroicon-m-check-badge class="size-5" />
                        </div>
                        <div>
                            <span class="font-semibold text-foreground">Softec AI Price Assurance:</span> Simple business landing pages and starter websites start from <strong class="text-foreground">₹5,000 to ₹10,000</strong> with responsive design, WhatsApp integration, and search indexing included.
                        </div>
                    </div>
                </div>

                {{-- Right Column: Output / Scope Presentation --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Default State (Before Calculation) --}}
                    <div x-show="!result && !loading" class="rounded-panel border border-dashed border-border bg-surface/20 p-8 text-center flex flex-col items-center justify-center min-h-[460px]">
                        <div class="size-16 rounded-2xl bg-brand-violet/10 text-brand-violet grid place-items-center mb-4">
                            <x-heroicon-o-calculator class="size-8" />
                        </div>
                        <h3 class="font-display text-lg font-bold text-foreground">Your Tailored Scope Will Appear Here</h3>
                        <p class="text-xs text-muted max-w-sm mt-2 leading-relaxed">
                            Tell us what you want to build or select your preferences on the left. Our AI Solutions Architect will break down the deliverables, timeline and calibrated price.
                        </p>
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">₹5k - ₹10k Starter Base</span>
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">No Hidden Costs</span>
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">WhatsApp Quote</span>
                        </div>
                    </div>

                    {{-- Loading Skeleton --}}
                    <div x-show="loading" class="rounded-panel border border-border bg-background p-6 space-y-4 animate-pulse min-h-[460px]">
                        <div class="h-6 bg-surface rounded w-2/3"></div>
                        <div class="h-14 bg-surface rounded"></div>
                        <div class="space-y-2 pt-4">
                            <div class="h-4 bg-surface rounded w-full"></div>
                            <div class="h-4 bg-surface rounded w-5/6"></div>
                            <div class="h-4 bg-surface rounded w-4/6"></div>
                        </div>
                        <div class="h-28 bg-surface rounded mt-6"></div>
                    </div>

                    {{-- Calculated Result Card --}}
                    <div x-show="result && !loading" x-transition class="rounded-panel border-2 border-brand-violet/30 bg-background p-6 shadow-xl space-y-6">
                        
                        {{-- Header & Title --}}
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-full bg-brand-violet/10 px-3 py-0.5 text-[0.6875rem] font-semibold text-brand-violet uppercase tracking-wider" x-text="result?.project_type"></span>
                                <span class="text-xs font-semibold text-muted flex items-center gap-1">
                                    <x-heroicon-m-clock class="size-3.5" />
                                    <span x-text="result?.timeline_weeks"></span>
                                </span>
                            </div>
                            <h3 class="font-display text-xl font-bold text-foreground mt-2" x-text="result?.project_title"></h3>
                        </div>

                        {{-- Pricing Banner --}}
                        <div class="rounded-xl bg-gradient-to-br from-brand-violet/15 via-surface to-brand-cyan/10 p-5 border border-brand-violet/20 text-center">
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted">Estimated Project Investment</span>
                            <div class="mt-1 flex items-baseline justify-center gap-2">
                                <span class="font-display text-3xl sm:text-4xl font-extrabold text-foreground tracking-tight">
                                    ₹<span x-text="formatNumber(result?.cost_min_inr)"></span> – ₹<span x-text="formatNumber(result?.cost_max_inr)"></span>
                                </span>
                            </div>
                            <div class="text-xs text-muted mt-1 font-medium">
                                Approx. $<span x-text="result?.cost_min_usd"></span> – $<span x-text="result?.cost_max_usd"></span> USD
                            </div>
                        </div>

                        {{-- Executive Summary --}}
                        <div>
                            <p class="text-xs text-foreground/80 leading-relaxed italic border-l-2 border-brand-violet pl-3" x-text="result?.executive_summary"></p>
                        </div>

                        {{-- Recommended Stack --}}
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted block mb-2">Recommended Tech Stack</span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="tech in (result?.recommended_stack || [])" :key="tech">
                                    <span class="rounded-md bg-surface px-2.5 py-1 text-xs font-medium text-foreground border border-border" x-text="tech"></span>
                                </template>
                            </div>
                        </div>

                        {{-- MVP Deliverables --}}
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted block mb-2">Phase 1 (MVP) Deliverables</span>
                            <ul class="space-y-1.5 text-xs text-foreground">
                                <template x-for="(item, idx) in (result?.mvp_scope_items || [])" :key="idx">
                                    <li class="flex items-start gap-2">
                                        <x-heroicon-m-check class="size-4 text-emerald-500 shrink-0 mt-0.5" />
                                        <span x-text="item"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        {{-- Cost Saving Tip --}}
                        <template x-if="result?.cost_saving_tip">
                            <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 p-3 text-xs text-amber-700 dark:text-amber-300">
                                <strong class="font-semibold block mb-0.5">💡 Solutions Architect Tip:</strong>
                                <span x-text="result?.cost_saving_tip"></span>
                            </div>
                        </template>

                        {{-- Conversion CTA Buttons --}}
                        <div class="space-y-2.5 pt-2">
                            {{-- WhatsApp Direct Quote Button --}}
                            <a :href="getWhatsAppUrl()" target="_blank" rel="noopener"
                               class="btn w-full justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 gap-2 shadow-md shadow-emerald-600/20">
                                <x-heroicon-m-chat-bubble-bottom-center-text class="size-4" />
                                <span>Get Official WhatsApp Proposal (+91 63970 13739)</span>
                            </a>

                            {{-- Lock In & Start Project --}}
                            <a :href="getStartProjectUrl()"
                               class="btn btn-secondary w-full justify-center font-semibold py-2.5 text-xs gap-1.5">
                                <span>Lock This Estimate & Start Project</span>
                                <x-heroicon-m-arrow-right class="size-3.5" />
                            </a>

                            {{-- Copy Scope --}}
                            <button type="button" @click="copyScope()"
                                    class="w-full text-center py-1.5 text-xs text-muted hover:text-foreground transition-colors">
                                <span x-text="copied ? '✓ Scope Copied to Clipboard!' : '📋 Copy Complete Scope to Clipboard'"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FAQ Section for Calculator --}}
            <div class="mt-20 border-t border-border/70 pt-14">
                <div class="max-w-2xl mx-auto text-center mb-10">
                    <h2 class="font-display text-2xl font-bold text-foreground">Frequently Asked Pricing Questions</h2>
                    <p class="text-sm text-muted mt-2">Clear, upfront answers about our software and website development pricing.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 max-w-4xl mx-auto text-sm">
                    <div class="card p-5 border border-border">
                        <h4 class="font-semibold text-foreground">Is the ₹5,000 – ₹10,000 estimate accurate for simple websites?</h4>
                        <p class="text-xs text-muted mt-2 leading-relaxed">Yes! For starter businesses needing a professional 1 to 5-page responsive website with WhatsApp direct chat, contact forms, and Google Search indexing, our packages start at just ₹5,000 to ₹10,000 with quick delivery in 3 to 7 days.</p>
                    </div>
                    <div class="card p-5 border border-border">
                        <h4 class="font-semibold text-foreground">What if I only have an idea without a technical scope?</h4>
                        <p class="text-xs text-muted mt-2 leading-relaxed">That is exactly what our AI Estimator and Solutions Architects specialize in. Just describe what you want in plain words or message our WhatsApp team, and we will structure the MVP features and milestone roadmap for you.</p>
                    </div>
                    <div class="card p-5 border border-border">
                        <h4 class="font-semibold text-foreground">Are there any hidden costs?</h4>
                        <p class="text-xs text-muted mt-2 leading-relaxed">Never. We provide transparent, milestone-based quotes. Hosting domain registrations and third-party APIs (like payment gateways or SMS) are billed directly at actual provider costs without markups.</p>
                    </div>
                    <div class="card p-5 border border-border">
                        <h4 class="font-semibold text-foreground">Can I pay in milestones?</h4>
                        <p class="text-xs text-muted mt-2 leading-relaxed">Yes. All Softec AI custom projects follow structured milestone payments (e.g. 30% advance on scoping, 40% on design/dev demonstration, and 30% upon final production sign-off and deployment).</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script>
        function projectCalculator() {
            return {
                tab: 'idea',
                ideaPrompt: '',
                projectType: 'website',
                scale: 'starter',
                features: ['whatsapp'],
                urgency: 'standard',
                loading: false,
                result: null,
                copied: false,

                examples: [
                    { label: '🦷 Dental Clinic / Doctor Site', prompt: 'Modern responsive website for a medical clinic with appointment request, doctor profiles, and WhatsApp booking.', type: 'website', scale: 'starter' },
                    { label: '🏋️ Gym & Fitness Portal', prompt: 'Gym membership landing page with class schedule, trainer profiles, and membership inquiry.', type: 'website', scale: 'starter' },
                    { label: '🏢 Real Estate Listings', prompt: 'Property showcase portal with property filter, high-res photo gallery, and agent contact leads.', type: 'saas_portal', scale: 'growth' },
                    { label: '⚙️ IT Agency Workflow', prompt: 'Internal automation platform for IT consultancy to track developer allocations, project milestones, and client invoicing.', type: 'enterprise_it', scale: 'growth' },
                    { label: '🤖 AI Customer Chatbot', prompt: 'Autonomous customer support agent trained on company documents with WhatsApp and website live chat integration.', type: 'ai_agent', scale: 'starter' },
                ],

                projectTypes: [
                    { id: 'website', name: 'Starter Website', baseline: '₹5k – ₹10k base' },
                    { id: 'growth_web', name: 'Business CMS', baseline: '₹12k – ₹25k base' },
                    { id: 'ecommerce', name: 'E-Commerce Store', baseline: '₹18k – ₹40k base' },
                    { id: 'saas_portal', name: 'Web App / Portal', baseline: '₹30k – ₹65k base' },
                    { id: 'enterprise_it', name: 'IT Automation', baseline: '₹60k – ₹140k base' },
                    { id: 'ai_agent', name: 'AI Solution / Agent', baseline: '₹75k – ₹180k base' },
                ],

                featureList: [
                    { id: 'whatsapp', label: 'WhatsApp Direct Chat' },
                    { id: 'auth', label: 'User Login & Accounts' },
                    { id: 'payments', label: 'Payment Gateway (Razorpay/Stripe)' },
                    { id: 'dashboard', label: 'Admin Management Portal' },
                    { id: 'ai_chatbot', label: 'AI Chatbot Integration' },
                ],

                formatNumber(val) {
                    if (!val) return '0';
                    return new Intl.NumberFormat('en-IN').format(val);
                },

                async calculate() {
                    this.loading = true;
                    this.copied = false;

                    try {
                        const payload = {
                            idea_prompt: this.ideaPrompt,
                            project_type: this.projectType,
                            scale: this.scale,
                            features: this.features,
                            urgency: this.urgency,
                            _token: '{{ csrf_token() }}'
                        };

                        const response = await fetch('{{ route('calculator.calculate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const res = await response.json();
                        if (res.success && res.data) {
                            this.result = res.data;
                            // Smooth scroll to results on small screens
                            if (window.innerWidth < 1024) {
                                document.getElementById('calculator-section').scrollIntoView({ behavior: 'smooth' });
                            }
                        }
                    } catch (e) {
                        console.error('Calculation error', e);
                    } finally {
                        this.loading = false;
                    }
                },

                getWhatsAppUrl() {
                    const phone = '{{ $whatsappNumber }}';
                    if (!this.result) {
                        return `https://wa.me/${phone}?text=${encodeURIComponent('Namaste Softec AI! I want to discuss a project estimate.')}`;
                    }
                    const text = `Namaste Softec AI! I just used your Project Calculator:\n\n*Project:* ${this.result.project_title}\n*Estimated Investment:* ₹${this.formatNumber(this.result.cost_min_inr)} - ₹${this.formatNumber(this.result.cost_max_inr)} ($${this.result.cost_min_usd} - $${this.result.cost_max_usd} USD)\n*Timeline:* ${this.result.timeline_weeks}\n\nCan we discuss the detailed architecture and start this project?`;
                    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
                },

                getStartProjectUrl() {
                    const base = '{{ route('project.create') }}';
                    if (!this.result) return base;
                    const params = new URLSearchParams({
                        subject: this.result.project_title,
                        budget_range: `₹${this.formatNumber(this.result.cost_min_inr)} - ₹${this.formatNumber(this.result.cost_max_inr)}`,
                        timeline: this.result.timeline_weeks,
                        scope: this.result.executive_summary
                    });
                    return `${base}?${params.toString()}`;
                },

                copyScope() {
                    if (!this.result) return;
                    const items = (this.result.mvp_scope_items || []).map(i => `• ${i}`).join('\n');
                    const text = `=== SOFTEC AI PROJECT SCOPE & ESTIMATE ===\nProject: ${this.result.project_title} (${this.result.project_type})\nEstimated Cost: ₹${this.formatNumber(this.result.cost_min_inr)} - ₹${this.formatNumber(this.result.cost_max_inr)} ($${this.result.cost_min_usd} - $${this.result.cost_max_usd} USD)\nTimeline: ${this.result.timeline_weeks}\nTech Stack: ${(this.result.recommended_stack || []).join(', ')}\n\nDeliverables:\n${items}\n\nSummary:\n${this.result.executive_summary}\n\nContact: https://softecai.com`;

                    navigator.clipboard.writeText(text).then(() => {
                        this.copied = true;
                        setTimeout(() => { this.copied = false; }, 3500);
                    });
                }
            };
        }
    </script>
</x-layout>
