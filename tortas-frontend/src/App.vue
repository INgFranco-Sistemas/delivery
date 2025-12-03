<!-- src/App.vue -->
<template>
  <div class="min-h-screen bg-pastel-cream flex flex-col">
    <NavBar />

    <main class="flex-1">
      <AppShell>
        <RouterView />
      </AppShell>
    </main>

    <FooterBar />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router';
import NavBar from './components/layout/NavBar.vue';
import AppShell from './components/layout/AppShell.vue';
import FooterBar from './components/layout/FooterBar.vue';
</script>
