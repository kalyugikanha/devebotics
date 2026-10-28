/**
 * Accessible tabs (WAI-ARIA tabs pattern with roving tabindex and arrow keys).
 */
export default () => ({
    active: 0,

    init() {
        this.active = Number(this.$el.dataset.initial || 0);
    },

    select(index) {
        this.active = Number(index);
    },

    isActive(index) {
        return this.active === Number(index);
    },

    tabIndex(index) {
        return this.isActive(index) ? 0 : -1;
    },

    onKeydown(event) {
        const tabs = Array.from(this.$el.querySelectorAll('[role="tab"]'));
        const current = tabs.indexOf(document.activeElement);

        if (current === -1) {
            return;
        }

        let next = null;

        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
            next = (current + 1) % tabs.length;
        } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
            next = (current - 1 + tabs.length) % tabs.length;
        } else if (event.key === 'Home') {
            next = 0;
        } else if (event.key === 'End') {
            next = tabs.length - 1;
        }

        if (next !== null) {
            event.preventDefault();
            this.active = next;
            tabs[next].focus();
        }
    },
});
