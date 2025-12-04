<template>
  <div class="space-y-4">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h1 class="font-display text-2xl md:text-3xl font-semibold text-slate-800">
          Elige tu torta perfecta
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Descubre nuestras tortas artesanales y endulza cualquier momento.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar torta (chocolate, fresa...)"
          class="w-full md:w-64 rounded-2xl border border-slate-200 px-3 py-2 text-xs bg-white/80 focus:outline-none focus:ring-2 focus:ring-pastel-rose"
        />
      </div>
    </header>

    <!-- Mensajes de estado -->
    <div v-if="loading" class="text-sm text-slate-500">
      Cargando tortas...
    </div>

    <div v-else-if="error" class="text-sm text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2">
      {{ error }}
    </div>

    <div v-else-if="filteredCakes.length === 0" class="text-sm text-slate-500">
      No se encontraron tortas para tu búsqueda.
    </div>

    <!-- Lista de tortas -->
    <section v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="cake in filteredCakes"
        :key="cake.id"
        class="bg-white/90 border border-pastel-pink/20 rounded-2xl overflow-hidden flex flex-col shadow hover:shadow-soft hover:-translate-y-0.5 transition-all"
      >
        <div
          class="relative h-32 bg-gradient-to-br from-pastel-pink via-pastel-cream to-pastel-mint flex items-center justify-center overflow-hidden"
        >
          <template v-if="cake.image_url">
            <img
              :src="cake.image_url"
              :alt="cake.name"
              class="h-full w-full object-cover"
            />
          </template>
          <template v-else>
            <span class="text-5xl">🎂</span>
          </template>

          <div
            class="absolute top-2 left-2 bg-white/80 rounded-full px-2 py-0.5 text-[10px] font-semibold text-pastel-rose"
          >
            {{ cake.category?.name || 'Sin categoría' }}
          </div>
          <div
            v-if="cake.stock === 0"
            class="absolute bottom-2 right-2 bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full"
          >
            Agotado
          </div>
        </div>

        <div class="p-3 flex-1 flex flex-col">
          <h2 class="font-display text-sm font-semibold text-slate-800 truncate">
            {{ cake.name }}
          </h2>
          <p class="mt-1 text-xs text-slate-500 line-clamp-2">
            {{ cake.description || 'Deliciosa torta artesanal lista para tu ocasión especial.' }}
          </p>

          <div class="mt-3 flex items-center justify-between">
            <span class="text-sm font-semibold text-pastel-rose">
              S/ {{ Number(cake.price).toFixed(2) }}
            </span>

            <button
              :disabled="cake.stock === 0"
              @click="addToCart(cake)"
              class="text-xs px-3 py-1.5 rounded-full bg-pastel-rose text-white font-medium hover:bg-pastel-pink transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Añadir
          </button>
          </div>
        </div>
      </article>
    </section>
  </div>

  <div
    v-if="feedback"
    class="fixed bottom-4 right-4 z-40 bg-slate-900 text-white text-xs px-4 py-2 rounded-full shadow-soft flex items-center gap-2"
  >
    <span>🧁</span>
    <span>{{ feedback }}</span>
  </div>
  <!-- 👆👆 HASTA AQUÍ -->
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useProductsStore } from '@/stores/products';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';

const productsStore = useProductsStore();
const cartStore = useCartStore();
const auth = useAuthStore();
const router = useRouter();

const search = ref('');
const feedback = ref('');   // 👈 aquí guardamos el mensaje flotante

const loading = computed(() => productsStore.loading);
const error = computed(() => productsStore.error);
const cakes = computed(() => productsStore.products);

const filteredCakes = computed(() => {
  const term = search.value.toLowerCase();

  return cakes.value.filter((c) => {
    const name = c.name?.toLowerCase() || '';
    const description = c.description?.toLowerCase() || '';
    const categoryName = c.category?.name?.toLowerCase() || '';

    return (
      name.includes(term) ||
      description.includes(term) ||
      categoryName.includes(term)
    );
  });
});

// 👇 ESTA FUNCIÓN ES LA QUE FALTABA
const showFeedback = (msg) => {
  feedback.value = msg;
  setTimeout(() => {
    if (feedback.value === msg) {
      feedback.value = '';
    }
  }, 1800);
};

const addToCart = (cake) => {
  // si NO está logueado -> redirigir a login
  if (!auth.isAuthenticated) {
    showFeedback('Debes iniciar sesión para añadir al carrito');
    router.push({
      name: 'login',
      query: { redirect: '/' }, // o route.fullPath si quieres volver a esa página exacta
    });
    return;
  }

  // si está logueado -> añadir normal
  cartStore.addItem(cake);
  showFeedback('Añadido al carrito');
};

onMounted(() => {
  // Siempre recargamos productos al entrar al Home
  productsStore.fetchProducts();
});
</script>


