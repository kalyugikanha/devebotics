/**
 * Copy-link button with an accessible status message.
 */
export default () => ({
    copied: false,

    async copy() {
        try {
            await navigator.clipboard.writeText(this.$root.dataset.url || window.location.href);
            this.copied = true;
            window.setTimeout(() => {
                this.copied = false;
            }, 2500);
        } catch {
            this.copied = false;
        }
    },
});
