/**
 * Floating WhatsApp chat widget (links are generated server-side).
 */
export default () => ({
    open: false,
    hinted: false,

    init() {
        // Show the greeting tooltip once per session after a short delay.
        try {
            if (!sessionStorage.getItem('softec-wa-hint')) {
                window.setTimeout(() => {
                    if (!this.open) {
                        this.hinted = true;
                    }
                }, 6000);
            }
        } catch {
            /* storage unavailable */
        }
    },

    toggle() {
        this.open = !this.open;
        this.dismissHint();

        if (this.open) {
            this.$nextTick(() => this.$root.querySelector('[data-wa-first]')?.focus());
        }
    },

    close() {
        if (!this.open) {
            return;
        }

        this.open = false;
        this.$nextTick(() => this.$root.querySelector('[aria-controls="whatsapp-panel"]')?.focus());
    },

    dismissHint() {
        this.hinted = false;

        try {
            sessionStorage.setItem('softec-wa-hint', '1');
        } catch {
            /* storage unavailable */
        }
    },
});
