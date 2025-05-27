import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 3000, // можно изменить
    proxy: {
      '/api': 'http://localhost:8000', // проксируем API Laravel
    },
  },
});
