// src/stores/auth.js
import { defineStore } from 'pinia';
import http from '../api/http';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    setAuth(data) {
      this.user = data.user;
      this.token = data.token;
      localStorage.setItem('token', data.token);
    },
    clearAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
    },
    async login(credentials) {
      const { data } = await http.post('/auth/login', credentials);
      this.setAuth(data);
      return data;
    },
    async register(payload) {
      const { data } = await http.post('/auth/register', payload);
      this.setAuth(data);
      return data;
    },
    async fetchMe() {
      if (!this.token) return;
      try {
        const { data } = await http.get('/auth/me');
        this.user = data;
      } catch (e) {
        this.clearAuth();
      }
    },
    async logout() {
      try {
        await http.post('/auth/logout');
      } catch (e) {
        // ignorar
      }
      this.clearAuth();
    },
  },
});
