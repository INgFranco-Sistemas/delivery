<template>
  <div class="space-y-6">
    <header>
      <h1 class="font-display text-2xl md:text-3xl font-semibold text-slate-800">
        Bienvenido de nuevo
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Ingresa para pedir tus tortas favoritas o gestionar la pastelería.
      </p>
    </header>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div class="grid gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Correo electrónico</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="tucorreo@ejemplo.com"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Contraseña</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="••••••••"
          />
        </div>
      </div>

      <p v-if="error" class="text-xs text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2">
        {{ error }}
      </p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-semibold text-white bg-gradient-to-r from-pastel-rose to-pastel-pink shadow-soft disabled:opacity-60 disabled:cursor-not-allowed hover:translate-y-0.5 transition-transform"
      >
        <span v-if="!loading">Ingresar</span>
        <span v-else>Entrando...</span>
      </button>
    </form>

    <p class="text-xs text-slate-500">
      ¿Aún no tienes cuenta?
      <button
        class="text-pastel-rose font-medium hover:underline"
        @click="$router.push({ name: 'register' })"
      >
        Regístrate aquí
      </button>
    </p>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
  email: '',
  password: '',
});

const loading = ref(false);
const error = ref('');

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    await auth.login(form);
    if (auth.isAdmin) {
      router.push({ name: 'admin-products' });
    } else {
      router.push({ name: 'home' });
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Error al iniciar sesión.';
  } finally {
    loading.value = false;
  }
};
</script>

