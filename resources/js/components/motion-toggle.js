import { storage } from './storage';

/**
 * Visitor-controlled "reduce motion" switch (in addition to the OS setting).
 */
export default () => ({
    reduced: false,

    init() {
        this.reduced = storage.get('softec-reduce-motion') === '1';
    },

    toggle() {
        this.reduced = !this.reduced;
        storage.set('softec-reduce-motion', this.reduced ? '1' : '0');
        document.documentElement.classList.toggle('reduce-motion', this.reduced);
        document.documentElement.classList.toggle('motion-reduced', this.reduced);
    },

    get label() {
        return this.reduced ? 'Enable animations' : 'Reduce motion';
    },
});
