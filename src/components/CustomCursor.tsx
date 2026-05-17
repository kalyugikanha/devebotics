import { useEffect, useRef } from 'react';

const CustomCursor = () => {
  const dotRef = useRef<HTMLDivElement>(null);
  const pos = useRef({ x: 0, y: 0 });
  const frame = useRef<number>(0);

  useEffect(() => {
    const onMove = (e: MouseEvent) => {
      pos.current = { x: e.clientX, y: e.clientY };
    };
    window.addEventListener('mousemove', onMove, { passive: true });

    const update = () => {
      if (dotRef.current) {
        dotRef.current.style.left = `${pos.current.x}px`;
        dotRef.current.style.top = `${pos.current.y}px`;
      }
      frame.current = requestAnimationFrame(update);
    };
    frame.current = requestAnimationFrame(update);

    return () => {
      window.removeEventListener('mousemove', onMove);
      cancelAnimationFrame(frame.current);
    };
  }, []);

  return (
    <div
      ref={dotRef}
      style={{
        position: 'fixed',
        width: 6,
        height: 6,
        background: '#F0F0F0',
        borderRadius: '50%',
        pointerEvents: 'none',
        zIndex: 9999,
        transform: 'translate(-50%, -50%)',
        transition: 'opacity 0.2s ease',
        mixBlendMode: 'difference',
      }}
    />
  );
};

export default CustomCursor;
