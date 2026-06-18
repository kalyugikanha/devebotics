/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          bg:           "#FFFFFF",
          bgSurface:    "#F9FAFB",
          card:         "#F3F4F6",
          border:       "#E5E7EB",
          accent:       "#1F8844",
          textPrimary:  "#111827",
          textSecondary:"#4B5563",
          textTertiary: "#9CA3AF",
        }
      },
      fontFamily: {
        montserrat: ['Montserrat', 'sans-serif'],
        lexend:     ['Lexend', 'sans-serif'],
      },
      animation: {
        'marquee':     'marquee 30s linear infinite',
        'marquee-rev': 'marquee-rev 30s linear infinite',
        'pulse-line':  'pulse-line 2s ease-in-out infinite',
        'fade-up':     'fade-up 0.6s ease forwards',
      },
      keyframes: {
        marquee: {
          '0%':   { transform: 'translateX(0)' },
          '100%': { transform: 'translateX(-50%)' },
        },
        'marquee-rev': {
          '0%':   { transform: 'translateX(-50%)' },
          '100%': { transform: 'translateX(0)' },
        },
        'pulse-line': {
          '0%, 100%': { opacity: '0.2' },
          '50%':      { opacity: '0.8' },
        },
        'fade-up': {
          '0%':   { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      }
    },
  },
  plugins: [],
}
