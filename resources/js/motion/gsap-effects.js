/**
 * Lazily loaded GSAP effects (only on pages that need them).
 * - [data-scrub-words]: statement words light up as the section scrolls by.
 * - [data-progress-line]: a line fills with scroll; [data-step] items activate as it passes.
 * - [data-parallax="0.2"]: gentle vertical parallax on decorative elements (desktop).
 * - [data-image-reveal]: clip-path reveal for images as they enter (desktop).
 */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const intensity = { subtle: 0.6, standard: 1, expressive: 1.4 };

export function init(config, { parallax = true } = {}) {
    gsap.registerPlugin(ScrollTrigger);

    const strength = intensity[config.intensity] ?? 1;
    const triggers = [];

    gsap.utils.toArray('[data-scrub-words]').forEach((element) => {
        const words = element.querySelectorAll('.scrub-word');

        triggers.push(
            gsap.fromTo(words, { opacity: 0.5 }, {
                opacity: 1,
                ease: 'none',
                stagger: 0.08,
                scrollTrigger: { trigger: element, start: 'top 80%', end: 'bottom 45%', scrub: 0.5 },
            }).scrollTrigger,
        );
    });

    gsap.utils.toArray('[data-progress-line]').forEach((line) => {
        const scope = line.closest('[data-progress-scope]') || line.parentElement;
        const steps = [...scope.querySelectorAll('[data-step]')];
        const vertical = line.dataset.progressLine === 'y';

        triggers.push(
            gsap.fromTo(line, vertical ? { scaleY: 0 } : { scaleX: 0 }, {
                ...(vertical ? { scaleY: 1 } : { scaleX: 1 }),
                ease: 'none',
                scrollTrigger: {
                    trigger: scope,
                    start: 'top 75%',
                    end: 'bottom 60%',
                    scrub: 0.6,
                    onUpdate: (self) => {
                        steps.forEach((step, index) => {
                            step.classList.toggle('is-active', self.progress >= index / Math.max(1, steps.length - 1) - 0.02);
                        });
                    },
                },
            }).scrollTrigger,
        );
    });

    if (parallax) {
        gsap.utils.toArray('[data-parallax]').forEach((element) => {
            const speed = Math.max(-1, Math.min(1, Number(element.dataset.parallax || 0.15))) * strength;

            triggers.push(
                gsap.to(element, {
                    yPercent: speed * -30,
                    ease: 'none',
                    scrollTrigger: { trigger: element.closest('section') || element, start: 'top bottom', end: 'bottom top', scrub: 0.6 },
                }).scrollTrigger,
            );
        });

        gsap.utils.toArray('[data-image-reveal]').forEach((element) => {
            gsap.set(element, { clipPath: 'inset(10% 10% 10% 10% round 28px)', opacity: 0.5 });

            triggers.push(
                gsap.to(element, {
                    clipPath: 'inset(0% 0% 0% 0% round 28px)',
                    opacity: 1,
                    duration: 1.1 * config.durationScale,
                    ease: 'expo.out',
                    scrollTrigger: { trigger: element, start: 'top 85%', once: true },
                }).scrollTrigger,
            );
        });
    }

    // Clean up when the page is hidden for bfcache restores.
    window.addEventListener('pagehide', () => triggers.forEach((trigger) => trigger?.kill()), { once: true });
}
