// src/stores/products.js
import { defineStore } from 'pinia';
import http from '@/api/http';

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchProducts() {
      this.loading = true;
      this.error = null;

      try {
        const { data } = await http.get('/products');
        this.products = data;
      } catch (e) {
        console.error(e);
        this.error =
          e.response?.data?.message ||
          'No se pudieron cargar las tortas. Intenta nuevamente.';
      } finally {
        this.loading = false;
      }
    },
  },
});
