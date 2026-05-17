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
          bg:           "#08090A",
          bgSurface:    "#111214",
          card:         "#0E1012",
          border:       "#1C1E21",
          accent:       "#E8FF47",
          textPrimary:  "#F0F0F0",
          textSecondary:"#6B6F76",
          textTertiary: "#3A3D42",
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
