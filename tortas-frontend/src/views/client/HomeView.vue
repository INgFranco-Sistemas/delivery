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
          class="relative h-32 bg-gradient-to-br from-pastel-pink via-pastel-cream to-pastel-mint flex items-center justify-center"
        >
          <span class="text-5xl">🎂</span>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useProductsStore } from '@/stores/products';
import { useCartStore } from '@/stores/cart';

const productsStore = useProductsStore();
const cartStore = useCartStore();

const search = ref('');

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

const addToCart = (cake) => {
  cartStore.addItem(cake);
};

onMounted(() => {
  if (!productsStore.products.length) {
    productsStore.fetchProducts();
  }
});
</script>

