/**
 * Highlights the table-of-contents entry for the heading currently in view.
 */
export default () => ({
    current: '',
    observer: null,

    init() {
        const headings = Array.from(document.querySelectorAll('[data-toc-content] h2[id], [data-toc-content] h3[id]'));

        if (!headings.length || !('IntersectionObserver' in window)) {
            return;
        }

        this.observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.current = entry.target.id;
                    }
                });
            },
            { rootMargin: '-20% 0px -70% 0px' },
        );

        headings.forEach((heading) => this.observer.observe(heading));
    },

    destroy() {
        this.observer?.disconnect();
    },

    isCurrent(id) {
        return this.current === id;
    },
});
