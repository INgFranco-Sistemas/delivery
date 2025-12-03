<template>
  <div class="space-y-6">
    <header>
      <h1 class="font-display text-2xl md:text-3xl font-semibold text-slate-800">
        Crea tu cuenta
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Regístrate para empezar a pedir tus tortas favoritas a domicilio.
      </p>
    </header>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-slate-600 mb-1">Nombre completo</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="Ej. Ana Torres"
          />
        </div>

        <div class="md:col-span-2">
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
          <label class="block text-xs font-medium text-slate-600 mb-1">Teléfono</label>
          <input
            v-model="form.phone"
            type="text"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="Ej. 987654321"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Dirección</label>
          <input
            v-model="form.address"
            type="text"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="Ej. Jr. Pasteles 123"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Contraseña</label>
          <input
            v-model="form.password"
            type="password"
            required
            minlength="6"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="Mínimo 6 caracteres"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Repetir contraseña</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="6"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:border-pastel-rose bg-white/80"
            placeholder="Confirma tu contraseña"
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
        <span v-if="!loading">Crear cuenta</span>
        <span v-else>Creando cuenta...</span>
      </button>
    </form>

    <p class="text-xs text-slate-500">
      ¿Ya tienes cuenta?
      <button
        class="text-pastel-rose font-medium hover:underline"
        @click="$router.push({ name: 'login' })"
      >
        Inicia sesión aquí
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
  name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);
const error = ref('');

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  if (form.password !== form.password_confirmation) {
    error.value = 'Las contraseñas no coinciden.';
    loading.value = false;
    return;
  }

  try {
    await auth.register(form);
    // Después de registrarse, lo mandamos a home (cliente) o admin
    if (auth.isAdmin) {
      router.push({ name: 'admin-products' });
    } else {
      router.push({ name: 'home' });
    }
  } catch (e) {
    // Errores de validación del backend
    const data = e.response?.data;
    if (data?.errors) {
      // mostramos el primer mensaje de error
      const firstKey = Object.keys(data.errors)[0];
      error.value = data.errors[firstKey][0];
    } else {
      error.value = data?.message || 'Error al registrar.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
