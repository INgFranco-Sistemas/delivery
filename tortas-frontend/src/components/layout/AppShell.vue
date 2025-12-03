<template>
  <div class="max-w-6xl mx-auto px-4 py-6">
    <div class="grid gap-6 md:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)] items-start">
      <!-- Contenido principal -->
      <section class="bg-white/80 rounded-3xl shadow-soft p-5 md:p-7 border border-pastel-pink/20">
        <slot />
      </section>

      <!-- Panel decorativo / promo -->
      <aside class="hidden md:block">
        <div class="bg-gradient-to-br from-pastel-pink via-pastel-cream to-pastel-mint rounded-3xl h-full p-6 flex flex-col justify-between shadow-soft border border-white/60">
          <div>
            <h2 class="font-display text-2xl font-semibold text-slate-800 mb-2">
              Endulza tu día
            </h2>
            <p class="text-sm text-slate-700 mb-4">
              Tortas artesanales, decoradas con cariño y listas para llegar a tu puerta.
            </p>
            <ul class="text-xs text-slate-700 space-y-1">
              <li>• Entrega rápida en la ciudad</li>
              <li>• Opciones personalizadas para eventos</li>
              <li>• Ingredientes frescos y de calidad</li>
            </ul>
          </div>
          <div class="flex justify-end mt-6">
            <div class="relative">
              <div class="h-24 w-24 rounded-full bg-white/90 flex items-center justify-center">
                <span class="text-4xl">🧁</span>
              </div>
              <div class="absolute -top-3 -left-3 h-10 w-10 rounded-full bg-white/70 flex items-center justify-center text-lg">
                🎉
              </div>
              <div class="absolute -bottom-3 -right-2 h-8 w-16 rounded-full bg-white/80 text-[10px] flex items-center justify-center font-semibold text-pastel-rose shadow">
                -15% hoy
              </div>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
</script>
