/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'brand-blue': '#E5F1FC',
        'brand-blue-dark': '#B5D5F5',
        'brand-text': '#1F2937',
      }
    },
  },
  plugins: [],
}
