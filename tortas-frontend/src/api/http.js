// src/api/http.js
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const http = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // backend Laravel
});

// Agregar automáticamente el token JWT si existe
http.interceptors.request.use((config) => {
  const authStore = useAuthStore();
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`;
  }
  return config;
});

export default http;
