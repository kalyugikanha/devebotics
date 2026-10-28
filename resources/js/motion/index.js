/**
 * Motion system.
 *
 * - Reads whitelisted presets from <script id="motion-config"> (admin settings).
 * - Honours prefers-reduced-motion, the visitor's own toggle and the global switch.
 * - Scroll reveals and counters use IntersectionObserver (tiny, no library).
 * - GSAP + ScrollTrigger are imported lazily, only when a page contains
 *   parallax/scrubbed elements and motion is allowed.
 */
import { storage } from '../components/storage';
import { initEffects } from '../effects';

const defaults = {
    enabled: true,
    intensity: 'standard',
    durationScale: 1,
    hero: 'rise',
    reveal: 'fade-up',
    stagger: true,
    parallax: true,
    counters: true,
    ambient: true,
    marquee: true,
    decorativeOnMobile: false,
};

export function readConfig() {
    const node = document.getElementById('motion-config');

    try {
        return { ...defaults, ...(node ? JSON.parse(node.textContent || '{}') : {}) };
    } catch {
        return defaults;
    }
}

export function prefersReducedMotion(config) {
    return (
        !config.enabled ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        storage.get('softec-reduce-motion') === '1'
    );
}

export function initMotion() {
    const config = readConfig();
    const root = document.documentElement;
    const reduced = prefersReducedMotion(config);

    root.style.setProperty('--motion-scale', String(config.durationScale));
    root.classList.toggle('no-ambient', !config.ambient || reduced);
    root.classList.toggle('no-stagger', !config.stagger);
    root.classList.toggle('decorative-mobile', !!config.decorativeOnMobile);

    initCounters(config, reduced);
    initEffects({ reduced });

    if (reduced) {
        root.classList.add('motion-reduced');
        revealAll();

        return;
    }

    if (config.reveal !== 'none' && 'IntersectionObserver' in window) {
        root.dataset.revealPreset = config.reveal;
        root.classList.add('motion-ready');
        initReveals();
    } else {
        revealAll();
    }

    const isSmallScreen = window.matchMedia('(max-width: 767px)').matches;
    const wantsScroll = !!document.querySelector('[data-scrub-words], [data-progress-line]');
    const wantsParallax = config.parallax && !!document.querySelector('[data-parallax], [data-image-reveal]') && (!isSmallScreen || config.decorativeOnMobile);

    if (wantsScroll || wantsParallax) {
        const load = () =>
            import('./gsap-effects')
                .then((module) => module.init(config, { parallax: wantsParallax }))
                .catch(() => root.classList.add('motion-reduced'));

        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(load, { timeout: 1500 });
        } else {
            window.setTimeout(load, 400);
        }
    }
}

function revealAll() {
    document.querySelectorAll('[data-reveal]').forEach((element) => element.classList.add('is-revealed'));
}

function initReveals() {
    const elements = document.querySelectorAll('[data-reveal]');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    elements.forEach((element) => {
        // Elements already in view at load are revealed immediately (no delay for above-the-fold content).
        const rect = element.getBoundingClientRect();

        if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
            requestAnimationFrame(() => element.classList.add('is-revealed'));
        } else {
            observer.observe(element);
        }
    });
}

/**
 * Animated counters. The final value is rendered server-side (accessible and
 * crawlable); the count-up only runs when motion is allowed.
 */
function initCounters(config, reduced) {
    const counters = document.querySelectorAll('[data-counter]');

    if (!counters.length || reduced || !config.counters || !('IntersectionObserver' in window)) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                observer.unobserve(entry.target);
                animateCounter(entry.target, config);
            });
        },
        { threshold: 0.4 },
    );

    counters.forEach((counter) => observer.observe(counter));
}

function animateCounter(element, config) {
    const target = Number(element.dataset.counter);
    const decimals = Number(element.dataset.decimals || 0);
    const prefix = element.dataset.prefix || '';
    const suffix = element.dataset.suffix || '';
    const duration = 1600 * config.durationScale;
    const formatter = new Intl.NumberFormat(document.documentElement.lang || 'en', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });

    if (!Number.isFinite(target)) {
        return;
    }

    const start = performance.now();

    const frame = (now) => {
        const progress = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - progress, 4);
        element.textContent = prefix + formatter.format(target * eased) + suffix;

        if (progress < 1) {
            requestAnimationFrame(frame);
        }
    };

    requestAnimationFrame(frame);
}
