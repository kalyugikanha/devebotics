<x-layout :seo="$seo">
    <x-site.page-hero :seo="$seo" illustration="strategy" eyebrow="AI Scope & Cost Architect" title="Calculate your [project cost & scope]"
        intro="Instant architectural breakdown, realistic timeline and transparent pricing in ₹ INR and $ USD. Starter business websites start from ₹5,000 to ₹10,000." />

    <section class="section pt-10" id="calculator-section" x-data="projectCalculator" x-cloak>
        <div class="container-site max-w-6xl">

            {{-- Main Calculator Grid --}}
            <div class="grid gap-8 lg:grid-cols-12">

                {{-- Left Column: Input Panel --}}
                <div class="lg:col-span-7 space-y-6">

                    <div class="rounded-panel border border-border bg-background p-6 shadow-sm sm:p-8">
                        <div class="border-b border-border/70 pb-4">
                            <h2 class="font-display text-lg font-bold text-foreground">How would you like to estimate?</h2>
                            <p class="text-xs text-muted mt-0.5">Describe your idea in plain Hindi/English or configure technical requirements</p>
                        </div>

                        {{-- Mode Selector Tabs --}}
                        <div class="mt-5 grid grid-cols-2 gap-2 rounded-xl bg-surface/70 p-1.5 border border-border/50">
                            <button type="button" x-on:click="setTab('idea')" 
                                    :class="{ 'bg-background text-foreground shadow-sm font-semibold': isTab('idea'), 'text-muted hover:text-foreground': !isTab('idea') }"
                                    class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-xs sm:text-sm transition-all duration-200">
                                <x-heroicon-m-sparkles class="size-4 text-brand-violet" />
                                <span>Describe Idea (AI Scope)</span>
                            </button>
                            <button type="button" x-on:click="setTab('builder')" 
                                    :class="{ 'bg-background text-foreground shadow-sm font-semibold': isTab('builder'), 'text-muted hover:text-foreground': !isTab('builder') }"
                                    class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-xs sm:text-sm transition-all duration-200">
                                <x-heroicon-m-adjustments-horizontal class="size-4 text-brand-cyan" />
                                <span>Interactive Scope Builder</span>
                            </button>
                        </div>

                        {{-- Tab 1: Describe Idea --}}
                        <div x-show="isTab('idea')" class="mt-6 space-y-4">
                            <div>
                                <label for="idea_prompt" class="block text-sm font-semibold text-foreground">
                                    Describe what you want to build:
                                </label>
                                <span class="block text-xs text-muted mt-0.5">Type in Hindi, English, or Hinglish (e.g., "Mujhe gym ke liye membership and slot booking system chahiye")</span>
                            </div>
                            
                            <textarea id="idea_prompt" x-model="ideaPrompt" rows="4" 
                                      placeholder="e.g. I need a modern responsive 5-page website for my business in Jaipur with appointment request, customer reviews, and direct WhatsApp inquiry..."
                                      class="form-control w-full resize-y text-sm"></textarea>

                            {{-- Inspiration Pills --}}
                            <div>
                                <span class="text-xs font-medium text-muted block mb-2">Or click an example idea:</span>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" x-on:click="useExample('Modern responsive website for clinic with appointment booking, doctor profiles, and WhatsApp connect.', 'website', 'starter')" 
                                            class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                        🦷 Doctor / Clinic Site
                                    </button>
                                    <button type="button" x-on:click="useExample('Gym membership landing page with class schedule, trainer profiles, and membership inquiry.', 'website', 'starter')" 
                                            class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                        🏋️ Gym & Fitness Portal
                                    </button>
                                    <button type="button" x-on:click="useExample('Property showcase portal with search filters, high-res photo gallery, and WhatsApp lead form.', 'saas_portal', 'growth')" 
                                            class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                        🏢 Real Estate Listings
                                    </button>
                                    <button type="button" x-on:click="useExample('IT agency internal automation for developer scheduling, project SLA tracking, and milestone billing.', 'enterprise_it', 'growth')" 
                                            class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                        ⚙️ Agency OS (Legzen-Class)
                                    </button>
                                    <button type="button" x-on:click="useExample('AI customer support chatbot trained on company FAQ with live chat and WhatsApp integration.', 'ai_agent', 'starter')" 
                                            class="rounded-full border border-border/80 bg-surface/60 px-3 py-1 text-xs text-foreground/80 hover:border-brand-violet hover:bg-brand-violet/10 transition-all">
                                        🤖 AI Agent / Chatbot
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Interactive Builder --}}
                        <div x-show="isTab('builder')" class="mt-6 space-y-6">
                            {{-- Project Category --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">1. Project Category</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    <button type="button" x-on:click="setProjectType('website')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('website'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('website') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Starter Website</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹5k – ₹10k base</div>
                                    </button>
                                    <button type="button" x-on:click="setProjectType('growth_web')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('growth_web'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('growth_web') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Business CMS</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹12k – ₹25k base</div>
                                    </button>
                                    <button type="button" x-on:click="setProjectType('ecommerce')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('ecommerce'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('ecommerce') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">E-Commerce</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹18k – ₹40k base</div>
                                    </button>
                                    <button type="button" x-on:click="setProjectType('saas_portal')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('saas_portal'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('saas_portal') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Web App / Portal</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹30k – ₹65k base</div>
                                    </button>
                                    <button type="button" x-on:click="setProjectType('enterprise_it')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('enterprise_it'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('enterprise_it') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">IT Automation</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹60k – ₹140k base</div>
                                    </button>
                                    <button type="button" x-on:click="setProjectType('ai_agent')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isProjectType('ai_agent'), 'border-border bg-surface/40 text-foreground/80': !isProjectType('ai_agent') }"
                                            class="rounded-xl border p-3 text-left transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">AI Agent / Observability</div>
                                        <div class="text-[0.6875rem] text-muted mt-0.5">₹75k – ₹180k base</div>
                                    </button>
                                </div>
                            </div>

                            {{-- Scale --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">2. Scale & Page Volume</label>
                                <div class="grid grid-cols-3 gap-2.5">
                                    <button type="button" x-on:click="setScale('starter')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isScale('starter'), 'border-border bg-surface/40 text-foreground/80': !isScale('starter') }"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Starter</div>
                                        <div class="text-[0.6875rem] text-muted">1 - 5 Pages / MVP</div>
                                    </button>
                                    <button type="button" x-on:click="setScale('growth')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isScale('growth'), 'border-border bg-surface/40 text-foreground/80': !isScale('growth') }"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Growth</div>
                                        <div class="text-[0.6875rem] text-muted">5 - 15 Pages / CMS</div>
                                    </button>
                                    <button type="button" x-on:click="setScale('enterprise')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 ring-1 ring-brand-violet text-foreground': isScale('enterprise'), 'border-border bg-surface/40 text-foreground/80': !isScale('enterprise') }"
                                            class="rounded-xl border p-3 text-center transition-all">
                                        <div class="font-semibold text-xs sm:text-sm">Enterprise</div>
                                        <div class="text-[0.6875rem] text-muted">Multi-Module Platform</div>
                                    </button>
                                </div>
                            </div>

                            {{-- Feature Addons --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">3. Essential Feature Add-ons</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" x-on:click="toggleFeature('whatsapp')"
                                            :class="{ 'border-brand-violet/80 bg-brand-violet/10 text-foreground': hasFeature('whatsapp'), 'border-border bg-surface/30 text-muted': !hasFeature('whatsapp') }"
                                            class="flex items-center gap-2 rounded-lg border p-2.5 text-left text-xs transition-all font-medium">
                                        <span x-text="hasFeature('whatsapp') ? '✓' : '+'" class="font-bold"></span>
                                        <span>WhatsApp Direct Chat</span>
                                    </button>
                                    <button type="button" x-on:click="toggleFeature('auth')"
                                            :class="{ 'border-brand-violet/80 bg-brand-violet/10 text-foreground': hasFeature('auth'), 'border-border bg-surface/30 text-muted': !hasFeature('auth') }"
                                            class="flex items-center gap-2 rounded-lg border p-2.5 text-left text-xs transition-all font-medium">
                                        <span x-text="hasFeature('auth') ? '✓' : '+'" class="font-bold"></span>
                                        <span>User Login & Auth</span>
                                    </button>
                                    <button type="button" x-on:click="toggleFeature('payments')"
                                            :class="{ 'border-brand-violet/80 bg-brand-violet/10 text-foreground': hasFeature('payments'), 'border-border bg-surface/30 text-muted': !hasFeature('payments') }"
                                            class="flex items-center gap-2 rounded-lg border p-2.5 text-left text-xs transition-all font-medium">
                                        <span x-text="hasFeature('payments') ? '✓' : '+'" class="font-bold"></span>
                                        <span>Razorpay / Stripe Payments</span>
                                    </button>
                                    <button type="button" x-on:click="toggleFeature('dashboard')"
                                            :class="{ 'border-brand-violet/80 bg-brand-violet/10 text-foreground': hasFeature('dashboard'), 'border-border bg-surface/30 text-muted': !hasFeature('dashboard') }"
                                            class="flex items-center gap-2 rounded-lg border p-2.5 text-left text-xs transition-all font-medium">
                                        <span x-text="hasFeature('dashboard') ? '✓' : '+'" class="font-bold"></span>
                                        <span>Admin Management Portal</span>
                                    </button>
                                    <button type="button" x-on:click="toggleFeature('ai_chatbot')"
                                            :class="{ 'border-brand-violet/80 bg-brand-violet/10 text-foreground': hasFeature('ai_chatbot'), 'border-border bg-surface/30 text-muted': !hasFeature('ai_chatbot') }"
                                            class="flex items-center gap-2 rounded-lg border p-2.5 text-left text-xs transition-all font-medium">
                                        <span x-text="hasFeature('ai_chatbot') ? '✓' : '+'" class="font-bold"></span>
                                        <span>AI Chatbot Integration</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Urgency --}}
                            <div>
                                <label class="block text-xs font-semibold tracking-wider uppercase text-muted mb-2.5">4. Delivery Timeline</label>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <button type="button" x-on:click="setUrgency('standard')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 text-foreground font-semibold': isUrgency('standard'), 'border-border bg-surface/40 text-muted': !isUrgency('standard') }"
                                            class="rounded-xl border p-2.5 text-center text-xs transition-all">
                                        Standard Turnaround
                                    </button>
                                    <button type="button" x-on:click="setUrgency('rush')"
                                            :class="{ 'border-brand-violet bg-brand-violet/10 text-foreground font-semibold': isUrgency('rush'), 'border-border bg-surface/40 text-muted': !isUrgency('rush') }"
                                            class="rounded-xl border p-2.5 text-center text-xs transition-all">
                                        ⚡ Fast-Track / Rush Delivery
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Trigger Calculate Button --}}
                        <div class="mt-8 pt-5 border-t border-border/70 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <span class="text-xs text-muted text-center sm:text-left">
                                <strong class="text-foreground">100% Free:</strong> No email or credit card required
                            </span>
                            <button type="button" x-on:click="calculate" :disabled="loading"
                                    class="btn btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 font-bold shadow-lg shadow-brand-violet/20 hover:shadow-brand-violet/30 transition-all text-sm">
                                <span x-show="!loading" class="flex items-center gap-2">
                                    <x-heroicon-m-sparkles class="size-4" />
                                    <span>Calculate Cost & Scope</span>
                                </span>
                                <span x-show="loading" class="flex items-center gap-2">
                                    <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Analyzing Architecture...</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Price Guarantee Notice --}}
                    <div class="rounded-xl border border-border/80 bg-surface/40 p-4 text-xs text-muted flex items-start gap-3">
                        <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-500 shrink-0">
                            <x-heroicon-m-check-badge class="size-5" />
                        </div>
                        <div>
                            <span class="font-semibold text-foreground">Softec AI Price Assurance:</span> Simple business landing pages and starter websites start from <strong class="text-foreground">₹5,000 to ₹10,000</strong> with responsive design, WhatsApp direct button, and Google Search indexing included.
                        </div>
                    </div>
                </div>

                {{-- Right Column: Output Card --}}
                <div class="lg:col-span-5 space-y-6" id="calculator-output">

                    {{-- Empty State (Before Calculation) --}}
                    <div x-show="!result && !loading" class="rounded-panel border border-dashed border-border bg-surface/20 p-8 text-center flex flex-col items-center justify-center min-h-[460px]">
                        <div class="size-16 rounded-2xl bg-brand-violet/10 text-brand-violet grid place-items-center mb-4">
                            <x-heroicon-o-calculator class="size-8" />
                        </div>
                        <h3 class="font-display text-lg font-bold text-foreground">Your Tailored Scope Will Appear Here</h3>
                        <p class="text-xs text-muted max-w-sm mt-2 leading-relaxed">
                            Describe your idea or configure your project preferences on the left. Our AI Solutions Architect will generate the full MVP deliverables, timeline, and calibrated price range.
                        </p>
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">₹5k - ₹10k Starter Base</span>
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">Direct WhatsApp Quote</span>
                            <span class="rounded-full bg-surface px-3 py-1 text-[0.6875rem] font-medium border border-border">Zero Hidden Costs</span>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="loading" class="rounded-panel border border-border bg-background p-6 space-y-4 animate-pulse min-h-[460px]">
                        <div class="h-6 bg-surface rounded w-2/3"></div>
                        <div class="h-16 bg-surface rounded"></div>
                        <div class="space-y-2 pt-4">
                            <div class="h-4 bg-surface rounded w-full"></div>
                            <div class="h-4 bg-surface rounded w-5/6"></div>
                            <div class="h-4 bg-surface rounded w-4/6"></div>
                        </div>
                        <div class="h-28 bg-surface rounded mt-6"></div>
                    </div>

                    {{-- Calculated Results Card --}}
                    <template x-if="result && !loading">
                        <div class="rounded-panel border-2 border-brand-violet/30 bg-background p-6 shadow-xl space-y-5">
                            
                            {{-- Header --}}
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-full bg-brand-violet/10 px-3 py-0.5 text-[0.6875rem] font-semibold text-brand-violet uppercase tracking-wider" x-text="result.project_type"></span>
                                    <span class="text-xs font-semibold text-muted flex items-center gap-1">
                                        <x-heroicon-m-clock class="size-3.5" />
                                        <span x-text="result.timeline_weeks"></span>
                                    </span>
                                </div>
                                <h3 class="font-display text-xl font-bold text-foreground mt-2" x-text="result.project_title"></h3>
                            </div>

                            {{-- Pricing Display --}}
                            <div class="rounded-xl bg-gradient-to-br from-brand-violet/15 via-surface to-brand-cyan/10 p-5 border border-brand-violet/20 text-center">
                                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Estimated Project Investment</span>
                                <div class="mt-1 flex items-baseline justify-center gap-2">
                                    <span class="font-display text-3xl sm:text-4xl font-extrabold text-foreground tracking-tight">
                                        ₹<span x-text="formatNumber(result.cost_min_inr)"></span> – ₹<span x-text="formatNumber(result.cost_max_inr)"></span>
                                    </span>
                                </div>
                                <div class="text-xs text-muted mt-1 font-medium">
                                    Approx. $<span x-text="result.cost_min_usd"></span> – $<span x-text="result.cost_max_usd"></span> USD
                                </div>
                            </div>

                            {{-- Executive Summary --}}
                            <div>
                                <p class="text-xs text-foreground/85 leading-relaxed italic border-l-2 border-brand-violet pl-3" x-text="result.executive_summary"></p>
                            </div>

                            {{-- Recommended Tech Stack --}}
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-muted block mb-2">Recommended Tech Stack</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="tech in (result.recommended_stack || [])" :key="tech">
                                        <span class="rounded-md bg-surface px-2.5 py-1 text-xs font-medium text-foreground border border-border" x-text="tech"></span>
                                    </template>
                                </div>
                            </div>

                            {{-- Phase 1 MVP Deliverables --}}
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-muted block mb-2">Phase 1 (MVP) Deliverables</span>
                                <ul class="space-y-1.5 text-xs text-foreground">
                                    <template x-for="(item, idx) in (result.mvp_scope_items || [])" :key="idx">
                                        <li class="flex items-start gap-2">
                                            <x-heroicon-m-check class="size-4 text-emerald-500 shrink-0 mt-0.5" />
                                            <span x-text="item"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            {{-- Cost Saving Advice --}}
                            <template x-if="result.cost_saving_tip">
                                <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 p-3 text-xs text-amber-700 dark:text-amber-300">
                                    <strong class="font-semibold block mb-0.5">💡 Solutions Architect Tip:</strong>
                                    <span x-text="result.cost_saving_tip"></span>
                                </div>
                            </template>

                            {{-- High-Converting Actions --}}
                            <div class="space-y-2.5 pt-2">
                                <a x-bind:href="getWhatsAppUrl()" target="_blank" rel="noopener"
                                   class="btn w-full justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 gap-2 shadow-md shadow-emerald-600/20">
                                    <x-heroicon-m-chat-bubble-bottom-center-text class="size-4" />
                                    <span>Get Official WhatsApp Proposal (+91 63970 13739)</span>
                                </a>

                                <a x-bind:href="getStartProjectUrl()"
                                   class="btn btn-secondary w-full justify-center font-semibold py-2.5 text-xs gap-1.5">
                                    <span>Lock This Estimate & Start Project</span>
                                    <x-heroicon-m-arrow-right class="size-3.5" />
                                </a>

                                <button type="button" x-on:click="copyScope"
                                        class="w-full text-center py-1.5 text-xs text-muted hover:text-foreground transition-colors">
                                    <span x-text="copied ? '✓ Scope Copied to Clipboard!' : '📋 Copy Complete Scope to Clipboard'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            {{-- FAQ Grid --}}
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
</x-layout>
