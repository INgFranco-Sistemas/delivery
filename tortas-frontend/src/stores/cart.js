// src/stores/cart.js
import { defineStore } from 'pinia';

const STORAGE_KEY = 'cart_items';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'),
  }),
  getters: {
    itemCount: (state) =>
      state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalAmount: (state) =>
      state.items.reduce(
        (sum, item) => sum + item.quantity * Number(item.product.price),
        0
      ),
  },
  actions: {
    save() {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(this.items));
    },
    addItem(product) {
      const existing = this.items.find(
        (item) => item.product.id === product.id
      );

      if (existing) {
        existing.quantity += 1;
      } else {
        this.items.push({
          product,
          quantity: 1,
        });
      }

      this.save();
    },
    removeItem(productId) {
      this.items = this.items.filter((i) => i.product.id !== productId);
      this.save();
    },
    updateQuantity(productId, quantity) {
      const item = this.items.find((i) => i.product.id === productId);
      if (!item) return;

      item.quantity = quantity < 1 ? 1 : quantity;
      this.save();
    },
    clear() {
      this.items = [];
      this.save();
    },
  },
});
