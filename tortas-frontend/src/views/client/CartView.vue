<template>
  <div class="space-y-6">
    <header>
      <h1 class="font-display text-2xl md:text-3xl font-semibold text-slate-800">
        Tu carrito de tortas
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Revisa tu pedido y completa los datos de entrega para finalizar la compra.
      </p>
    </header>

    <div v-if="items.length === 0" class="text-sm text-slate-500">
      Tu carrito está vacío. Ve al
      <button
        class="text-pastel-rose font-medium hover:underline"
        @click="$router.push({ name: 'home' })"
      >
        catálogo de tortas
      </button>
      para añadir productos.
    </div>

    <div v-else class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)] items-start">
      <!-- Lista de items -->
      <section class="space-y-3">
        <article
          v-for="item in items"
          :key="item.product.id"
          class="bg-white/90 border border-pastel-pink/20 rounded-2xl p-3 flex gap-3 items-center"
        >
          <div
            class="h-14 w-14 rounded-2xl bg-gradient-to-br from-pastel-pink via-pastel-cream to-pastel-mint flex items-center justify-center"
          >
            <span class="text-2xl">🎂</span>
          </div>
          <div class="flex-1">
            <h2 class="font-display text-sm font-semibold text-slate-800">
              {{ item.product.name }}
            </h2>
            <p class="text-[11px] text-slate-500">
              S/ {{ Number(item.product.price).toFixed(2) }} c/u
            </p>
          </div>
          <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-2">
              <button
                class="h-7 w-7 rounded-full border border-slate-200 flex items-center justify-center text-xs"
                @click="decrement(item.product.id, item.quantity)"
              >
                -
              </button>
              <input
                type="number"
                min="1"
                v-model.number="quantities[item.product.id]"
                @change="onQuantityInput(item.product.id)"
                class="w-12 text-center text-xs border border-slate-200 rounded-lg px-1 py-1"
              />
              <button
                class="h-7 w-7 rounded-full border border-slate-200 flex items-center justify-center text-xs"
                @click="increment(item.product.id)"
              >
                +
              </button>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-slate-700">
                S/ {{ (item.quantity * Number(item.product.price)).toFixed(2) }}
              </span>
              <button
                class="text-[11px] text-red-500 hover:underline"
                @click="remove(item.product.id)"
              >
                Quitar
              </button>
            </div>
          </div>
        </article>
      </section>

      <!-- Resumen y formulario -->
      <aside class="bg-white/90 border border-pastel-pink/30 rounded-2xl p-4 space-y-4">
        <h2 class="font-display text-lg font-semibold text-slate-800">
          Resumen del pedido
        </h2>

        <div class="space-y-1 text-sm text-slate-600">
          <div class="flex justify-between">
            <span>Productos ({{ itemCount }})</span>
            <span>S/ {{ totalAmount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xs text-slate-500">
            <span>Envío</span>
            <span>Por coordinar</span>
          </div>
        </div>

        <div class="border-t border-dashed border-slate-200 pt-2 flex justify-between items-center">
          <span class="text-sm font-semibold text-slate-800">Total a pagar</span>
          <span class="text-lg font-bold text-pastel-rose">S/ {{ totalAmount.toFixed(2) }}</span>
        </div>

        <form @submit.prevent="submitOrder" class="space-y-3 pt-2">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Dirección de entrega
            </label>
            <input
              v-model="form.delivery_address"
              type="text"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
              placeholder="Ej. Jr. Pasteles 456, Urb. Dulce Hogar"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Teléfono de contacto
            </label>
            <input
              v-model="form.delivery_phone"
              type="text"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
              placeholder="Ej. 987654321"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">
              Notas adicionales (opcional)
            </label>
            <textarea
              v-model="form.notes"
              rows="2"
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
              placeholder="Ej. Entregar entre 4 y 5 pm, timbrar una vez."
            ></textarea>
          </div>

          <p v-if="error" class="text-xs text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2">
            {{ error }}
          </p>
          <p v-if="success" class="text-xs text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
            {{ success }}
          </p>

          <button
            type="submit"
            :disabled="loading || items.length === 0"
            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-semibold text-white bg-gradient-to-r from-pastel-rose to-pastel-pink shadow-soft disabled:opacity-60 disabled:cursor-not-allowed hover:translate-y-0.5 transition-transform"
          >
            <span v-if="!loading">Confirmar pedido</span>
            <span v-else>Enviando pedido...</span>
          </button>
        </form>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cart';
import http from '@/api/http';
import { useAuthStore } from '@/stores/auth';

const cart = useCartStore();
const auth = useAuthStore();
const router = useRouter();

const items = computed(() => cart.items);
const itemCount = computed(() => cart.itemCount);
const totalAmount = computed(() => cart.totalAmount);

const quantities = reactive({});

// inicializar cantidades según el carrito
watch(
  items,
  (newItems) => {
    newItems.forEach((item) => {
      quantities[item.product.id] = item.quantity;
    });
  },
  { immediate: true }
);

const loading = ref(false);
const error = ref('');
const success = ref('');

const form = reactive({
  delivery_address: auth.user?.address || '',
  delivery_phone: auth.user?.phone || '',
  notes: '',
});

const increment = (productId) => {
  const current = quantities[productId] || 1;
  const value = current + 1;
  quantities[productId] = value;
  cart.updateQuantity(productId, value);
};

const decrement = (productId, currentQty) => {
  const value = currentQty - 1;
  if (value < 1) return;
  quantities[productId] = value;
  cart.updateQuantity(productId, value);
};

const onQuantityInput = (productId) => {
  const value = Number(quantities[productId] || 1);
  cart.updateQuantity(productId, value);
};

const remove = (productId) => {
  cart.removeItem(productId);
};

const submitOrder = async () => {
  if (!items.value.length) return;

  loading.value = true;
  error.value = '';
  success.value = '';

  try {
    const payload = {
      delivery_address: form.delivery_address,
      delivery_phone: form.delivery_phone,
      notes: form.notes,
      items: items.value.map((item) => ({
        product_id: item.product.id,
        quantity: item.quantity,
      })),
    };

    const { data } = await http.post('/orders', payload);

    success.value = 'Pedido registrado correctamente. Gracias por tu compra 🎉';
    cart.clear();

    // Opcional: ir a Mis pedidos después de unos segundos
    setTimeout(() => {
      router.push({ name: 'orders' });
    }, 1200);
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message ||
      'No se pudo registrar el pedido. Intenta nuevamente.';
  } finally {
    loading.value = false;
  }
};
</script>
