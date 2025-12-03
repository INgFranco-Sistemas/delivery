// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Vistas
import LoginView from '@/views/auth/LoginView.vue';
import RegisterView from '@/views/auth/RegisterView.vue';
import HomeView from '@/views/client/HomeView.vue';
import CartView from '../views/client/CartView.vue';
import MyOrdersView from '../views/client/MyOrdersView.vue';

import AdminDashboardView from '../views/admin/AdminDashboardView.vue';
import AdminProductsView from '../views/admin/AdminProductsView.vue';
import AdminOrdersView from '../views/admin/AdminOrdersView.vue';

const routes = [
  { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
  { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },

  { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },
  { path: '/cart', name: 'cart', component: CartView, meta: { requiresAuth: true } },
  { path: '/orders', name: 'orders', component: MyOrdersView, meta: { requiresAuth: true } },

  {
    path: '/admin',
    component: AdminDashboardView,
    meta: { requiresAuth: true, admin: true },
    children: [
      { path: 'products', name: 'admin-products', component: AdminProductsView },
      { path: 'orders', name: 'admin-orders', component: AdminOrdersView },
    ],
  },

  { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Guardas de navegación
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  if (!auth.user && auth.token) {
    await auth.fetchMe();
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return next({ name: 'home' });
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login' });
  }

  if (to.meta.admin && !auth.isAdmin) {
    return next({ name: 'home' });
  }

  return next();
});

export default router;
