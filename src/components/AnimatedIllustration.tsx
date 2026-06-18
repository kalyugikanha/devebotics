import React from 'react';

export default function AnimatedIllustration({ className }: { className?: string }) {
  return (
    <div className={className} style={{ display: 'block', width: '100%', minHeight: 260 }}>
      <svg viewBox="0 0 600 360" width="100%" height="100%" preserveAspectRatio="xMidYMid slice" style={{ display: 'block' }}>
        <defs>
          <linearGradient id="g1" x1="0" x2="1">
            <stop offset="0%" stopColor="#1F8844" stopOpacity="0.12" />
            <stop offset="100%" stopColor="#1F8844" stopOpacity="0.03" />
          </linearGradient>
        </defs>

        <rect width="100%" height="100%" fill="#F9FAFB" />

        <g transform="translate(40,40)">
          <circle cx="80" cy="80" r="60" fill="url(#g1)">
            <animate attributeName="cy" dur="6s" values="80;60;90;80" repeatCount="indefinite" />
            <animate attributeName="cx" dur="8s" values="80;120;60;80" repeatCount="indefinite" />
          </circle>

          <rect x="220" y="40" rx="12" ry="12" width="240" height="140" fill="#FFFFFF" stroke="#E6F4EA" strokeWidth="1">
            <animate attributeName="x" dur="9s" values="220;200;240;220" repeatCount="indefinite" />
          </rect>

          <g transform="translate(260,80)">
            <rect x="0" y="0" width="120" height="14" rx="6" fill="#E6F4EA" />
            <rect x="0" y="28" width="80" height="10" rx="6" fill="#ECFDF3">
              <animate attributeName="width" dur="4s" values="80;120;60;80" repeatCount="indefinite" />
            </rect>
          </g>

          <g transform="translate(40,180)">
            <rect x="0" y="0" width="480" height="14" rx="6" fill="#FFFFFF" stroke="#E5E7EB" />
            <circle cx="36" cy="-8" r="18" fill="#1F8844" opacity="0.14">
              <animate attributeName="cy" dur="5s" values="-8;0;-12;-8" repeatCount="indefinite" />
            </circle>
          </g>
        </g>
      </svg>
    </div>
  );
}
