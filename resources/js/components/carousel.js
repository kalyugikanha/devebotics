/**
 * Scroll-snap carousel with previous/next controls. No autoplay (WCAG 2.2.2).
 */
export default () => ({
    atStart: true,
    atEnd: false,

    init() {
        const track = this.$refs.track;

        if (!track) {
            return;
        }

        const update = () => {
            this.atStart = track.scrollLeft <= 4;
            this.atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        };

        update();
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
    },

    step() {
        const track = this.$refs.track;
        const item = track.querySelector('[data-carousel-item]');

        return item ? item.getBoundingClientRect().width + 24 : track.clientWidth * 0.8;
    },

    prev() {
        this.$refs.track.scrollBy({ left: -this.step(), behavior: this.behavior() });
    },

    next() {
        this.$refs.track.scrollBy({ left: this.step(), behavior: this.behavior() });
    },

    behavior() {
        return document.documentElement.classList.contains('motion-reduced') ? 'auto' : 'smooth';
    },
});
