import { storage } from './storage';

/**
 * Dismissible announcement bar; the dismissal is remembered per message.
 */
export default () => ({
    visible: true,

    init() {
        const key = this.$root.dataset.dismissKey;
        this.visible = !key || storage.get(key) !== '1';
    },

    dismiss() {
        const key = this.$root.dataset.dismissKey;

        if (key) {
            storage.set(key, '1');
        }

        this.visible = false;
    },
});
