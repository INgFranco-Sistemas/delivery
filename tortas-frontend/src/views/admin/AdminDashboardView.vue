<template>
  <div class="space-y-4">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h1 class="font-display text-2xl md:text-3xl font-semibold text-slate-800">
          Panel de administración
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Gestiona tus productos y pedidos desde un solo lugar.
        </p>
      </div>
      <div class="flex gap-2 text-xs">
        <span class="px-3 py-1 rounded-full bg-pastel-rose/10 text-pastel-rose border border-pastel-rose/30">
          Modo administrador
        </span>
      </div>
    </header>

    <!-- Tabs admin -->
    <nav class="flex gap-2 text-xs md:text-sm border-b border-slate-200 pb-1">
      <button
        :class="tabClass('admin-products')"
        @click="go('admin-products')"
      >
        Productos
      </button>
      <button
        :class="tabClass('admin-orders')"
        @click="go('admin-orders')"
      >
        Pedidos
      </button>
    </nav>

    <!-- Contenido hijo (products / orders) -->
    <section class="pt-2">
      <RouterView />
    </section>
  </div>
</template>

<script setup>
import { useRoute, useRouter, RouterView } from 'vue-router';

const route = useRoute();
const router = useRouter();

const go = (name) => router.push({ name });

const tabClass = (name) =>
  [
    'px-3 py-1.5 rounded-xl border text-xs md:text-sm',
    route.name === name
      ? 'bg-pastel-rose text-white border-pastel-rose shadow-soft'
      : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50',
  ].join(' ');
</script>
