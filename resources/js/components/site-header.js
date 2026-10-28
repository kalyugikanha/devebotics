/**
 * Sticky header: glass bar after scrolling, a highlight that glides to the
 * hovered nav item, hover-intent menus (mega menu with category rail) and the
 * mobile drawer (focus trapped via x-trap, Escape closes everything).
 */
const OPEN_DELAY = 70;
const CLOSE_DELAY = 180;

export default () => ({
    scrolled: false,
    overDark: false,
    mobileOpen: false,
    openMenu: null,
    activeGroup: 0,
    openTimer: null,
    closeTimer: null,

    init() {
        this.overDark = document.body.dataset.heroDark === '1';

        const update = () => {
            this.scrolled = window.scrollY > 16;
        };

        update();
        window.addEventListener('scroll', update, { passive: true });

        this.$watch('mobileOpen', (open) => {
            document.documentElement.classList.toggle('overflow-hidden', open);
        });

        // Rest the highlight on the current page's item once fonts have settled.
        this.$nextTick(() => {
            this.restIndicator();
            document.fonts?.ready.then(() => this.restIndicator());
            window.setTimeout(() => this.$refs.indicator?.classList.add('is-ready'), 50);
        });
        this.$watch('scrolled', () => window.setTimeout(() => this.restIndicator(), 520));
    },

    toggleMobile() {
        this.mobileOpen = !this.mobileOpen;
    },

    closeMobile() {
        this.mobileOpen = false;
    },

    isOpen(id) {
        return this.openMenu === id;
    },

    get anyOpen() {
        return this.openMenu !== null && this.openMenu.startsWith('menu-');
    },

    toggleMenu(id) {
        clearTimeout(this.openTimer);
        this.openMenu = this.openMenu === id ? null : id;
    },

    canHover() {
        return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    },

    /* Pointer enters a nav item: move the highlight, open its menu after a short intent delay. */
    enterItem(event) {
        const item = event.currentTarget;
        this.moveIndicator(item.querySelector('.nav-link'));

        if (!this.canHover()) {
            return;
        }

        clearTimeout(this.closeTimer);
        clearTimeout(this.openTimer);

        const id = item.dataset.menu || null;
        // Switching between open menus is instant; opening from closed waits briefly.
        const delay = this.openMenu === null ? OPEN_DELAY : 0;
        this.openTimer = setTimeout(() => {
            this.openMenu = id;
        }, delay);
    },

    focusItem(event) {
        this.moveIndicator(event.currentTarget);
    },

    keepOpen() {
        clearTimeout(this.closeTimer);
    },

    closeOnLeave() {
        if (!this.canHover()) {
            return;
        }

        clearTimeout(this.openTimer);
        clearTimeout(this.closeTimer);
        this.closeTimer = setTimeout(() => {
            this.openMenu = null;
            this.restIndicator();
        }, CLOSE_DELAY);
    },

    closeMenus() {
        clearTimeout(this.openTimer);
        this.openMenu = null;
        this.restIndicator();
    },

    closeAll() {
        this.closeMenus();
        this.mobileOpen = false;
    },

    showGroup(index) {
        this.activeGroup = index;
    },

    isGroup(index) {
        return this.activeGroup === index;
    },

    /* Position the gliding highlight over a nav link (relative to the header bar). */
    moveIndicator(link) {
        const indicator = this.$refs.indicator;
        const bar = this.$refs.bar;

        if (!indicator || !bar || !link || link.offsetWidth === 0) {
            return;
        }

        const linkBox = link.getBoundingClientRect();
        const barBox = bar.getBoundingClientRect();

        indicator.style.width = `${linkBox.width}px`;
        indicator.style.height = `${linkBox.height}px`;
        indicator.style.transform = `translate3d(${linkBox.left - barBox.left}px, ${linkBox.top - barBox.top}px, 0)`;
        indicator.classList.add('is-visible');
    },

    /* Back to the current page's item (or hidden) when nothing is hovered or open. */
    restIndicator() {
        if (this.anyOpen) {
            const open = this.$root.querySelector(`[data-menu="${this.openMenu}"] .nav-link`);

            if (open) {
                this.moveIndicator(open);

                return;
            }
        }

        const active = this.$root.querySelector('nav[aria-label="Primary"] [data-active]');

        if (active) {
            this.moveIndicator(active);
        } else {
            this.$refs.indicator?.classList.remove('is-visible');
        }
    },

    get onDark() {
        return this.overDark && !this.scrolled && !this.mobileOpen;
    },

    get barClasses() {
        return this.scrolled
            ? 'border-border/80 bg-background/80 shadow-[0_18px_50px_-28px_rgba(10,17,40,0.45)] backdrop-blur-xl'
            : 'border-transparent bg-transparent';
    },

    focusOut(event) {
        if (!this.$el.contains(event.relatedTarget)) {
            this.openMenu = null;
        }
    },
});
