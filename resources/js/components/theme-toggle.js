import { storage } from './storage';

const KEY = 'softec-theme';
const MODES = ['light', 'dark', 'system'];

/**
 * Light / dark / system switcher. The initial class is applied by an inline
 * script in <head> (no flash); this component only handles changes.
 */
export default () => ({
    mode: 'system',

    init() {
        const stored = storage.get(KEY);
        this.mode = MODES.includes(stored) ? stored : document.documentElement.dataset.themeDefault || 'system';

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.mode === 'system') {
                this.apply();
            }
        });
    },

    get label() {
        return { light: 'Light theme', dark: 'Dark theme', system: 'System theme' }[this.mode];
    },

    get isLight() {
        return this.mode === 'light';
    },

    get isDark() {
        return this.mode === 'dark';
    },

    get isSystem() {
        return this.mode === 'system';
    },

    cycle() {
        this.set(MODES[(MODES.indexOf(this.mode) + 1) % MODES.length]);
    },

    setLight() {
        this.set('light');
    },

    setDark() {
        this.set('dark');
    },

    setSystem() {
        this.set('system');
    },

    set(mode) {
        this.mode = mode;
        storage.set(KEY, mode);
        this.apply();
    },

    apply() {
        const root = document.documentElement;
        const dark = this.mode === 'dark' || (this.mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        root.classList.add('theme-transition');
        root.classList.toggle('dark', dark);
        root.style.colorScheme = dark ? 'dark' : 'light';

        window.setTimeout(() => root.classList.remove('theme-transition'), 400);
    },
});
