import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';  // импорт path

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),  // добавляем алиас '@' на папку src
    },
  },
  server: {
    port: 3000, // можно изменить
    proxy: {
      '/api': 'http://localhost:8000', // проксируем API Laravel
    },
  },
});
