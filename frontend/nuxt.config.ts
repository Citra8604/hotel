import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  future: {
    compatibilityVersion: 4,
  },
  vite: {
    plugins: [
      tailwindcss(),
    ],
    server: {
      hmr: {
        overlay: false,
      },
    },
  },
  nitro: {
    experimental: {
      wasm: false,
    },
    routeRules: {
      '/api/**': { proxy: 'http://127.0.0.1:8000/api/**' },
      '/sanctum/**': { proxy: 'http://127.0.0.1:8000/sanctum/**' },
      '/storage/**': { proxy: 'http://127.0.0.1:8000/storage/**' },
    }
  },
  css: ['~/assets/css/main.css'],
})