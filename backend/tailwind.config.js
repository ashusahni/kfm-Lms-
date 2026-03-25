/** @type {import('tailwindcss').Config} */
module.exports = {
  // JIT is default in Tailwind v3 – no need to set mode
  content: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
    './resources/js/**/*.js',
  ],
  // Run alongside Bootstrap: disable Preflight to avoid reset/conflict with Bootstrap's styles
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {},
  },
  plugins: [],
};
