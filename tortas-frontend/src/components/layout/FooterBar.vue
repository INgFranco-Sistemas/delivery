<template>
  <footer class="border-t border-pastel-pink/30 bg-white/70">
    <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
      <span>© {{ year }} Dulce Delivery · Sistema de delivery de tortas</span>
      <span class="flex items-center gap-1">
        Hecho con
        <span class="text-pastel-rose">♥</span>
        y mucho azúcar
      </span>
    </div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear();
</script>
