/**
 * AI-Powered Project Cost & Scope Calculator (Alpine CSP Component)
 */
export default () => ({
    tab: 'idea',
    ideaPrompt: '',
    projectType: 'website',
    scale: 'starter',
    features: ['whatsapp'],
    urgency: 'standard',
    loading: false,
    result: null,
    copied: false,
    error: null,

    init() {
        // Ready
    },

    setTab(t) {
        this.tab = t;
    },

    isTab(t) {
        return this.tab === t;
    },

    setProjectType(pt) {
        this.projectType = pt;
    },

    isProjectType(pt) {
        return this.projectType === pt;
    },

    setScale(s) {
        this.scale = s;
    },

    isScale(s) {
        return this.scale === s;
    },

    toggleFeature(f) {
        if (this.features.includes(f)) {
            this.features = this.features.filter(item => item !== f);
        } else {
            this.features.push(f);
        }
    },

    hasFeature(f) {
        return this.features.includes(f);
    },

    setUrgency(u) {
        this.urgency = u;
    },

    isUrgency(u) {
        return this.urgency === u;
    },

    useExample(prompt, type, scale) {
        this.ideaPrompt = prompt;
        this.projectType = type;
        this.scale = scale;
    },

    formatNumber(val) {
        if (!val) return '0';
        return new Intl.NumberFormat('en-IN').format(val);
    },

    async calculate() {
        this.loading = true;
        this.copied = false;
        this.error = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const payload = {
                idea_prompt: this.ideaPrompt,
                project_type: this.projectType,
                scale: this.scale,
                features: this.features,
                urgency: this.urgency
            };

            const response = await fetch('/api/calculate-project-cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error('Server response failed with status ' + response.status);
            }

            const res = await response.json();
            if (res.success && res.data) {
                this.result = res.data;
                const outEl = document.getElementById('calculator-output');
                if (outEl) {
                    outEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else {
                throw new Error('Invalid calculation response');
            }
        } catch (e) {
            console.error('Calculation error', e);
            this.error = 'Unable to complete estimate right now. Please message directly on WhatsApp (+91 63970 13739).';
        } finally {
            this.loading = false;
        }
    },

    getWhatsAppUrl() {
        const phone = '916397013739';
        if (!this.result) {
            return `https://wa.me/${phone}?text=${encodeURIComponent('Namaste Softec AI! I want to discuss a project estimate.')}`;
        }
        const text = `Namaste Softec AI! I just used your Project Calculator:\n\n*Project:* ${this.result.project_title}\n*Estimated Investment:* ₹${this.formatNumber(this.result.cost_min_inr)} - ₹${this.formatNumber(this.result.cost_max_inr)} ($${this.result.cost_min_usd} - $${this.result.cost_max_usd} USD)\n*Timeline:* ${this.result.timeline_weeks}\n\nCan we discuss the detailed architecture and start this project?`;
        return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
    },

    getStartProjectUrl() {
        const base = '/start-a-project';
        if (!this.result) return base;
        const params = new URLSearchParams({
            subject: this.result.project_title || '',
            budget_range: `₹${this.formatNumber(this.result.cost_min_inr)} - ₹${this.formatNumber(this.result.cost_max_inr)}`,
            timeline: this.result.timeline_weeks || '',
            scope: this.result.executive_summary || ''
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
});
