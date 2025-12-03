<template>
  <header class="bg-white/80 backdrop-blur border-b border-pastel-pink/40 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
      <!-- Logo -->
      <div class="flex items-center gap-2">
        <div
          class="h-10 w-10 rounded-full bg-gradient-to-tr from-pastel-rose to-pastel-pink flex items-center justify-center shadow-soft"
        >
          <span class="text-white text-xl font-bold">🍰</span>
        </div>
        <div class="flex flex-col leading-tight">
          <span class="font-display font-semibold text-slate-800">Dulce Delivery</span>
          <span class="text-xs text-slate-500">Tortas & Detalles</span>
        </div>
      </div>

      <!-- Desktop nav -->
      <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
        <RouterLink
          v-if="isAuthenticated"
          :to="{ name: 'home' }"
          :class="linkClass('home')"
        >
          Inicio
        </RouterLink>

        <RouterLink
          v-if="isAuthenticated && !isAdmin"
          :to="{ name: 'cart' }"
          :class="linkClass('cart') + ' flex items-center gap-1'"
        >
          <span>Mi carrito</span>
          <span
            v-if="cartCount > 0"
            class="text-xs bg-pastel-rose/10 text-pastel-rose px-2 py-0.5 rounded-full"
          >
            {{ cartCount }}
          </span>
        </RouterLink>

        <RouterLink
          v-if="isAuthenticated && !isAdmin"
          :to="{ name: 'orders' }"
          :class="linkClass('orders')"
        >
          Mis pedidos
        </RouterLink>

        <RouterLink
          v-if="isAdmin"
          :to="{ name: 'admin-products' }"
          :class="linkClass('admin-products')"
        >
          Admin productos
        </RouterLink>

        <RouterLink
          v-if="isAdmin"
          :to="{ name: 'admin-orders' }"
          :class="linkClass('admin-orders')"
        >
          Admin pedidos
        </RouterLink>
      </nav>

      <!-- Auth + mobile button -->
      <div class="flex items-center gap-2">
        <!-- Desktop botones auth -->
        <div class="hidden sm:flex items-center gap-2">
          <button
            v-if="!isAuthenticated"
            @click="goLogin"
            class="px-4 py-2 rounded-full border border-pastel-rose/60 text-pastel-rose text-sm font-medium hover:bg-pastel-rose/10 transition-all"
          >
            Ingresar
          </button>
          <button
            v-if="!isAuthenticated"
            @click="goRegister"
            class="px-4 py-2 rounded-full bg-gradient-to-r from-pastel-rose to-pastel-pink text-white text-sm font-semibold shadow-soft hover:translate-y-0.5 transition-transform"
          >
            Registrarme
          </button>

          <div v-if="isAuthenticated" class="flex items-center gap-3">
            <span class="hidden sm:block text-xs text-slate-600 max-w-[140px] truncate">
              Hola, <strong>{{ auth.user?.name }}</strong>
            </span>
            <button
              @click="handleLogout"
              class="px-3 py-1.5 rounded-full bg-white text-xs font-medium border border-slate-200 hover:bg-pastel-pink/10 transition-colors"
            >
              Cerrar sesión
            </button>
          </div>
        </div>

        <!-- Botón móvil -->
        <button
          class="md:hidden h-9 w-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 bg-white/80"
          @click="toggleMobile"
        >
          <span v-if="!showMobileMenu">☰</span>
          <span v-else>✕</span>
        </button>
      </div>
    </div>

    <!-- Menú móvil -->
    <transition name="fade">
      <div
        v-if="showMobileMenu"
        class="md:hidden border-t border-pastel-pink/30 bg-white/95 backdrop-blur"
      >
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-3 text-sm">
          <div v-if="isAuthenticated" class="text-xs text-slate-600">
            Sesión iniciada como <strong>{{ auth.user?.name }}</strong>
          </div>

          <RouterLink
            v-if="isAuthenticated"
            :to="{ name: 'home' }"
            :class="mobileLinkClass('home')"
            @click="closeMobile"
          >
            Inicio
          </RouterLink>

          <RouterLink
            v-if="isAuthenticated && !isAdmin"
            :to="{ name: 'cart' }"
            :class="mobileLinkClass('cart')"
            @click="closeMobile"
          >
            Mi carrito
            <span
              v-if="cartCount > 0"
              class="ml-2 text-[11px] bg-pastel-rose/10 text-pastel-rose px-2 py-0.5 rounded-full"
            >
              {{ cartCount }}
            </span>
          </RouterLink>

          <RouterLink
            v-if="isAuthenticated && !isAdmin"
            :to="{ name: 'orders' }"
            :class="mobileLinkClass('orders')"
            @click="closeMobile"
          >
            Mis pedidos
          </RouterLink>

          <RouterLink
            v-if="isAdmin"
            :to="{ name: 'admin-products' }"
            :class="mobileLinkClass('admin-products')"
            @click="closeMobile"
          >
            Admin productos
          </RouterLink>

          <RouterLink
            v-if="isAdmin"
            :to="{ name: 'admin-orders' }"
            :class="mobileLinkClass('admin-orders')"
            @click="closeMobile"
          >
            Admin pedidos
          </RouterLink>

          <div class="border-t border-slate-100 pt-3 flex flex-col gap-2">
            <button
              v-if="!isAuthenticated"
              @click="() => { closeMobile(); goLogin(); }"
              class="w-full px-4 py-2 rounded-xl border border-pastel-rose/60 text-pastel-rose text-sm font-medium hover:bg-pastel-rose/10 transition-all"
            >
              Ingresar
            </button>
            <button
              v-if="!isAuthenticated"
              @click="() => { closeMobile(); goRegister(); }"
              class="w-full px-4 py-2 rounded-xl bg-gradient-to-r from-pastel-rose to-pastel-pink text-white text-sm font-semibold shadow-soft"
            >
              Registrarme
            </button>

            <button
              v-if="isAuthenticated"
              @click="() => { closeMobile(); handleLogout(); }"
              class="w-full px-4 py-2 rounded-xl bg-white text-xs font-medium border border-slate-200 hover:bg-pastel-pink/10 transition-colors"
            >
              Cerrar sesión
            </button>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue';
import { RouterLink, useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';

const auth = useAuthStore();
const cart = useCartStore();
const router = useRouter();
const route = useRoute();

const showMobileMenu = ref(false);

const cartCount = computed(() => cart.itemCount);
const isAuthenticated = computed(() => auth.isAuthenticated);
const isAdmin = computed(() => auth.isAdmin);

const toggleMobile = () => {
  showMobileMenu.value = !showMobileMenu.value;
};
const closeMobile = () => {
  showMobileMenu.value = false;
};

const goLogin = () => router.push({ name: 'login' });
const goRegister = () => router.push({ name: 'register' });

const handleLogout = async () => {
  await auth.logout();
  router.push({ name: 'login' });
};

const linkClass = (name) =>
  [
    'transition-colors',
    route.name === name ? 'text-pastel-rose font-semibold' : 'text-slate-700 hover:text-pastel-rose',
  ].join(' ');

const mobileLinkClass = (name) =>
  [
    'flex items-center justify-between px-2 py-2 rounded-lg',
    route.name === name
      ? 'bg-pastel-rose/10 text-pastel-rose font-semibold'
      : 'text-slate-700 hover:bg-slate-50',
  ].join(' ');
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.18s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
