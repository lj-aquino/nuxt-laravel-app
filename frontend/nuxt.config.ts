// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  pages: true,
  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api',
      mainApiKey: process.env.NUXT_PUBLIC_MAIN_API_KEY,
      enrollmentApiKey: process.env.NUXT_PUBLIC_ENROLLMENT_API_KEY,
      apiUrl: process.env.NUXT_PUBLIC_API_URL || 'https://sp-j16t.onrender.com/api',
      amisUrl: process.env.NUXT_PUBLIC_AMIS_URL,
      // Add this line to properly expose the backend URL
      apiBackendUrl: process.env.NUXT_PUBLIC_API_BACKEND_URL
    },
  },

  // Link TailwindCSS CSS file
  css: [
    '~/assets/css/tailwind.css',  // Path to your TailwindCSS file
    '@fortawesome/fontawesome-svg-core/styles.css', // Add Font Awesome styles
  ],

  // Add plugins configuration
  plugins: [
    '~/plugins/fontawesome.js'
  ],

  build: {
    postcss: {
      postcssOptions: {
        plugins: {
          tailwindcss: {},
          autoprefixer: {},
        },
      },
    },
    transpile: [
      '@fortawesome/vue-fontawesome',
      '@fortawesome/fontawesome-svg-core',
      '@fortawesome/free-solid-svg-icons',
    ],
  },
});