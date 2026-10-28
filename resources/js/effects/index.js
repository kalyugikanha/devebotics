/**
 * Lightweight interaction layer (no dependencies):
 * spotlight cards, magnetic buttons, 3D tilt, mouse parallax, rotating words,
 * constellation canvas, scroll progress and back-to-top.
 * Decorative effects are skipped for reduced motion and on coarse pointers.
 */
import { initConstellation } from './constellation';

const finePointer = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches;

export function initEffects({ reduced }) {
    initScrollProgress();
    initBackToTop();
    initRotators(reduced);

    if (reduced) {
        return;
    }

    if (finePointer()) {
        initSpotlight();
        initMagnetic();
        initTilt();
        initParallaxScenes();
    }

    document.querySelectorAll('[data-constellation]').forEach((canvas) => initConstellation(canvas));
}

/* Cursor spotlight: sets --mx/--my on the hovered card (CSS draws the glow). */
function initSpotlight() {
    let frame = null;

    document.addEventListener(
        'pointermove',
        (event) => {
            const card = event.target.closest?.('.card-interactive, [data-spotlight]');

            if (!card || frame) {
                return;
            }

            frame = requestAnimationFrame(() => {
                const rect = card.getBoundingClientRect();
                card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
                card.style.setProperty('--my', `${event.clientY - rect.top}px`);
                frame = null;
            });
        },
        { passive: true },
    );
}

/* Buttons gently follow the cursor. */
function initMagnetic() {
    document.querySelectorAll('[data-magnetic]').forEach((element) => {
        const strength = Number(element.dataset.magnetic || 0.25);

        element.addEventListener('pointermove', (event) => {
            const rect = element.getBoundingClientRect();
            const x = event.clientX - rect.left - rect.width / 2;
            const y = event.clientY - rect.top - rect.height / 2;
            element.style.transform = `translate3d(${x * strength}px, ${y * strength}px, 0)`;
        });

        element.addEventListener('pointerleave', () => {
            element.style.transform = '';
        });
    });
}

/* 3D tilt for visuals and mockups. */
function initTilt() {
    document.querySelectorAll('[data-tilt]').forEach((element) => {
        const max = Number(element.dataset.tilt || 8);
        element.style.transformStyle = 'preserve-3d';
        element.style.transition = 'transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';

        element.addEventListener('pointermove', (event) => {
            const rect = element.getBoundingClientRect();
            const px = (event.clientX - rect.left) / rect.width - 0.5;
            const py = (event.clientY - rect.top) / rect.height - 0.5;
            element.style.transform = `perspective(1100px) rotateY(${px * max}deg) rotateX(${-py * max}deg)`;
        });

        element.addEventListener('pointerleave', () => {
            element.style.transform = 'perspective(1100px) rotateY(0deg) rotateX(0deg)';
        });
    });
}

/* Mouse parallax: children with data-depth move relative to the cursor. */
function initParallaxScenes() {
    document.querySelectorAll('[data-parallax-scene]').forEach((scene) => {
        const layers = [...scene.querySelectorAll('[data-depth]')];
        let frame = null;

        scene.addEventListener(
            'pointermove',
            (event) => {
                if (frame) {
                    return;
                }

                frame = requestAnimationFrame(() => {
                    const rect = scene.getBoundingClientRect();
                    const px = (event.clientX - rect.left) / rect.width - 0.5;
                    const py = (event.clientY - rect.top) / rect.height - 0.5;

                    layers.forEach((layer) => {
                        const depth = Number(layer.dataset.depth || 10);
                        layer.style.translate = `${px * depth}px ${py * depth}px`;
                    });

                    frame = null;
                });
            },
            { passive: true },
        );

        scene.addEventListener('pointerleave', () => {
            layers.forEach((layer) => (layer.style.translate = '0 0'));
        });
    });
}

/* Rotating words; the full list is always available to screen readers. */
function initRotators(reduced) {
    document.querySelectorAll('[data-rotator]').forEach((rotator) => {
        const words = [...rotator.querySelectorAll('[data-rotator-word]')];

        if (words.length < 2 || reduced) {
            return;
        }

        let index = 0;
        let timer = null;

        const tick = () => {
            words[index].classList.remove('is-active');
            index = (index + 1) % words.length;
            words[index].classList.add('is-active');
        };

        const start = () => {
            timer ??= window.setInterval(tick, Number(rotator.dataset.interval || 2600));
        };

        const stop = () => {
            window.clearInterval(timer);
            timer = null;
        };

        new IntersectionObserver(([entry]) => (entry.isIntersecting ? start() : stop())).observe(rotator);
        document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
    });
}

/* Gradient scroll-progress bar at the top of the viewport. */
function initScrollProgress() {
    const bar = document.querySelector('.scroll-progress');

    if (!bar) {
        return;
    }

    let frame = null;

    const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.setProperty('--scroll', max > 0 ? String(Math.min(1, window.scrollY / max)) : '0');
        frame = null;
    };

    window.addEventListener('scroll', () => (frame ??= requestAnimationFrame(update)), { passive: true });
    update();
}

function initBackToTop() {
    const button = document.querySelector('[data-back-to-top]');

    if (!button) {
        return;
    }

    const toggle = () => button.classList.toggle('is-visible', window.scrollY > window.innerHeight * 1.2);
    window.addEventListener('scroll', toggle, { passive: true });
    button.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: document.documentElement.classList.contains('motion-reduced') ? 'auto' : 'smooth' });
        document.getElementById('main')?.focus({ preventScroll: true });
    });
    toggle();
}
