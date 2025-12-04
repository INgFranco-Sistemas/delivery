<template>
  <div class="space-y-4">
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h2 class="font-display text-lg md:text-xl font-semibold text-slate-800">
          Pedidos recibidos
        </h2>
        <p class="text-xs text-slate-500">
          Revisa el estado de cada pedido y actualízalo según el avance.
        </p>
      </div>
      <div class="flex items-center gap-2 text-[11px] text-slate-600">
        <span class="px-2 py-1 rounded-full bg-slate-100 border border-slate-200">
          {{ orders.length }} pedido(s)
        </span>
      </div>
    </header>

    <div v-if="loading" class="text-sm text-slate-500">
      Cargando pedidos...
    </div>

    <div
      v-else-if="error"
      class="text-sm text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2"
    >
      {{ error }}
    </div>

    <div v-else-if="!orders.length" class="text-sm text-slate-500">
      Aún no hay pedidos registrados.
    </div>

    <section v-else class="space-y-3">
      <article
        v-for="order in orders"
        :key="order.id"
        class="bg-white/90 border border-pastel-pink/30 rounded-2xl p-4 space-y-2 shadow-sm"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div>
            <p class="text-xs text-slate-500">
              Pedido #{{ order.id }} ·
              {{ new Date(order.created_at).toLocaleString() }}
            </p>
            <p class="text-sm font-semibold text-slate-800">
              {{ order.user?.name }} ·
              <span class="text-pastel-rose">
                S/ {{ Number(order.total_amount).toFixed(2) }}
              </span>
            </p>
          </div>
          <div class="flex items-center gap-2 text-xs">
            <select
              v-model="order.localStatus"
              @change="updateStatus(order)"
              class="rounded-xl border border-slate-200 px-2 py-1 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose"
            >
              <option value="pending">Pendiente</option>
              <option value="confirmed">Confirmado</option>
              <option value="in_delivery">En reparto</option>
              <option value="delivered">Entregado</option>
              <option value="cancelled">Cancelado</option>
            </select>

            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
              :class="statusClass(order.status)"
            >
              {{ statusLabel(order.status) }}
            </span>
          </div>
        </div>

        <div class="border-t border-dashed border-slate-200 pt-2 text-xs text-slate-600 space-y-1">
          <p><strong>Entrega:</strong> {{ order.delivery_address }}</p>
          <p><strong>Teléfono:</strong> {{ order.delivery_phone }}</p>
          <p v-if="order.notes"><strong>Notas:</strong> {{ order.notes }}</p>
        </div>

        <div class="pt-2">
          <p class="text-[11px] text-slate-500 mb-1">Tortas:</p>
          <ul class="text-xs text-slate-700 space-y-0.5">
            <li v-for="item in order.items" :key="item.id">
              • {{ item.quantity }} x {{ item.product?.name }} —
              S/ {{ Number(item.subtotal).toFixed(2) }}
            </li>
          </ul>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import http from '@/api/http';

const orders = ref([]);
const loading = ref(false);
const error = ref('');

const fetchOrders = async () => {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await http.get('/orders');
    // agregamos campo localStatus para binding del select
    orders.value = data.map((o) => ({
      ...o,
      localStatus: o.status,
    }));
  } catch (e) {
    console.error(e);
    error.value = 'No se pudieron cargar los pedidos.';
  } finally {
    loading.value = false;
  }
};

const statusLabel = (status) => {
  const map = {
    pending: 'Pendiente',
    confirmed: 'Confirmado',
    in_delivery: 'En reparto',
    delivered: 'Entregado',
    cancelled: 'Cancelado',
  };
  return map[status] || status;
};

const statusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'bg-yellow-50 text-yellow-700 border border-yellow-100';
    case 'confirmed':
      return 'bg-blue-50 text-blue-700 border border-blue-100';
    case 'in_delivery':
      return 'bg-indigo-50 text-indigo-700 border border-indigo-100';
    case 'delivered':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-100';
    case 'cancelled':
      return 'bg-red-50 text-red-600 border border-red-100';
    default:
      return 'bg-slate-100 text-slate-700';
  }
};

const updateStatus = async (order) => {
  const newStatus = order.localStatus;
  const previousStatus = order.status;

  try {
    await http.put(`/orders/${order.id}/status`, { status: newStatus });
    order.status = newStatus;
  } catch (e) {
    console.error(e);
    order.localStatus = previousStatus; // revertir
    alert('No se pudo actualizar el estado del pedido.');
  }
};

onMounted(fetchOrders);
</script>
