/**
 * Subtle "AI network" constellation for dark hero sections.
 * ~50 drifting points connected by faint lines. Pauses off-screen and when
 * the tab is hidden; resolution-aware; no allocations in the frame loop.
 */
export function initConstellation(canvas) {
    const context = canvas.getContext('2d');

    if (!context) {
        return;
    }

    const colours = ['139, 92, 246', '217, 70, 239', '34, 211, 238', '52, 211, 153'];
    let width = 0;
    let height = 0;
    let points = [];
    let running = false;
    let frame = null;
    const pointer = { x: -9999, y: -9999 };

    const resize = () => {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        width = canvas.clientWidth;
        height = canvas.clientHeight;
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);

        const count = Math.round(Math.min(64, Math.max(24, (width * height) / 22000)));
        points = Array.from({ length: count }, (_, i) => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.25,
            vy: (Math.random() - 0.5) * 0.25,
            r: Math.random() * 1.6 + 0.6,
            c: colours[i % colours.length],
        }));
    };

    const draw = () => {
        context.clearRect(0, 0, width, height);
        const maxDistance = 130;

        for (let i = 0; i < points.length; i++) {
            const a = points[i];
            a.x += a.vx;
            a.y += a.vy;

            if (a.x < 0 || a.x > width) a.vx *= -1;
            if (a.y < 0 || a.y > height) a.vy *= -1;

            for (let j = i + 1; j < points.length; j++) {
                const b = points[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < maxDistance) {
                    context.strokeStyle = `rgba(${a.c}, ${0.16 * (1 - distance / maxDistance)})`;
                    context.lineWidth = 1;
                    context.beginPath();
                    context.moveTo(a.x, a.y);
                    context.lineTo(b.x, b.y);
                    context.stroke();
                }
            }

            const px = a.x - pointer.x;
            const py = a.y - pointer.y;
            const near = px * px + py * py < 140 * 140;

            context.fillStyle = `rgba(${a.c}, ${near ? 0.95 : 0.55})`;
            context.beginPath();
            context.arc(a.x, a.y, near ? a.r + 1.2 : a.r, 0, Math.PI * 2);
            context.fill();
        }

        frame = running ? requestAnimationFrame(draw) : null;
    };

    const start = () => {
        if (!running) {
            running = true;
            frame = requestAnimationFrame(draw);
        }
    };

    const stop = () => {
        running = false;

        if (frame) {
            cancelAnimationFrame(frame);
            frame = null;
        }
    };

    resize();
    window.addEventListener('resize', () => resize(), { passive: true });
    canvas.parentElement?.addEventListener(
        'pointermove',
        (event) => {
            const rect = canvas.getBoundingClientRect();
            pointer.x = event.clientX - rect.left;
            pointer.y = event.clientY - rect.top;
        },
        { passive: true },
    );

    new IntersectionObserver(([entry]) => (entry.isIntersecting ? start() : stop())).observe(canvas);
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
}
