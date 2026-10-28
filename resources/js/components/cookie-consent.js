/**
 * Cookie consent banner + preferences dialog. Optional analytics are only
 * loaded after explicit consent; the choice is stored by the server in a
 * first-party cookie and recorded anonymously.
 */
export default () => ({
    open: false,
    customizing: false,
    analytics: false,
    marketing: false,
    saving: false,

    init() {
        const consent = this.readCookie();

        if (consent && consent.v === this.$root.dataset.version) {
            this.analytics = !!consent.analytics;
            this.marketing = !!consent.marketing;
        } else {
            this.open = true;
        }

        window.addEventListener('open-cookie-preferences', () => {
            this.open = true;
            this.customizing = true;
        });
    },

    readCookie() {
        const match = document.cookie.split('; ').find((row) => row.startsWith('softec_consent='));

        if (!match) {
            return null;
        }

        try {
            return JSON.parse(decodeURIComponent(match.split('=').slice(1).join('=')));
        } catch {
            return null;
        }
    },

    customize() {
        this.customizing = true;
    },

    acceptAll() {
        this.analytics = true;
        this.marketing = true;
        this.save();
    },

    rejectOptional() {
        this.analytics = false;
        this.marketing = false;
        this.save();
    },

    toggleAnalytics() {
        this.analytics = !this.analytics;
    },

    toggleMarketing() {
        this.marketing = !this.marketing;
    },

    async save() {
        this.saving = true;

        try {
            await fetch(this.$root.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({ analytics: this.analytics, marketing: this.marketing }),
            });
        } catch {
            /* The banner closes regardless; nothing optional loads without consent. */
        }

        this.saving = false;
        this.open = false;
        this.customizing = false;

        if (this.analytics) {
            this.loadAnalytics();
        }
    },

    loadAnalytics() {
        const id = document.body.dataset.ga4;
        const gtm = document.body.dataset.gtm;

        if (window.__softecAnalyticsLoaded || (!id && !gtm)) {
            return;
        }

        window.__softecAnalyticsLoaded = true;
        window.dataLayer = window.dataLayer || [];

        if (gtm) {
            window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
            this.inject(`https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtm)}`);
        }

        if (id) {
            window.gtag = function gtag() {
                window.dataLayer.push(arguments);
            };
            window.gtag('js', new Date());
            window.gtag('config', id, { anonymize_ip: true });
            this.inject(`https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`);
        }
    },

    inject(src) {
        const script = document.createElement('script');
        script.async = true;
        script.src = src;
        document.head.appendChild(script);
    },
});
