<template>
  <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1.4fr)] items-start">
    <!-- Formulario producto -->
    <section class="bg-white/90 border border-pastel-pink/30 rounded-2xl p-4 space-y-3 shadow-sm">
      <h2 class="font-display text-lg font-semibold text-slate-800">
        {{ form.id ? 'Editar torta' : 'Nueva torta' }}
      </h2>
      <p class="text-xs text-slate-500">
        Completa la información de la torta y asigna una categoría.
      </p>

      <form @submit.prevent="saveProduct" class="space-y-3 text-xs">
        <div class="space-y-1">
          <label class="font-medium text-slate-700">Nombre</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
            placeholder="Ej. Torta de Chocolate Clásica"
          />
        </div>

        <div class="space-y-1">
          <label class="font-medium text-slate-700">Descripción</label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
            placeholder="Describe brevemente la torta..."
          ></textarea>
        </div>

        <div class="space-y-1">
          <label class="font-medium text-slate-700">Imagen (opcional)</label>
          <div class="flex items-start gap-3">
            <input
              type="file"
              accept="image/*"
              @change="onImageChange"
              class="text-xs"
            />
            <div v-if="imagePreview" class="h-16 w-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
              <img :src="imagePreview" alt="Preview" class="h-full w-full object-cover" />
            </div>
          </div>
          <p class="text-[10px] text-slate-400">
            Formatos: JPG, PNG, WEBP. Máx. 2MB.
          </p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="font-medium text-slate-700">Precio (S/)</label>
            <input
              v-model.number="form.price"
              type="number"
              min="0"
              step="0.1"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
            />
          </div>

          <div class="space-y-1">
            <label class="font-medium text-slate-700">Stock</label>
            <input
              v-model.number="form.stock"
              type="number"
              min="0"
              step="1"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pastel-rose bg-white/80"
            />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="font-medium text-slate-700">Categoría</label>
            <select
              v-model.number="form.category_id"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white/80 focus:outline-none focus:ring-2 focus:ring-pastel-rose"
            >
              <option value="" disabled>Selecciona categoría</option>
              <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.name }}
              </option>
            </select>
          </div>

          <div class="space-y-1 flex items-end">
            <label class="flex items-center gap-2 text-xs text-slate-700">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-slate-300 text-pastel-rose focus:ring-pastel-rose"
              />
              <span>Visible para los clientes</span>
            </label>
          </div>
        </div>

        <p v-if="error" class="text-[11px] text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2">
          {{ error }}
        </p>
        <p v-if="success" class="text-[11px] text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
          {{ success }}
        </p>

        <div class="flex gap-2 pt-1">
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 rounded-2xl bg-gradient-to-r from-pastel-rose to-pastel-pink text-white text-xs font-semibold shadow-soft disabled:opacity-60 disabled:cursor-not-allowed hover:translate-y-0.5 transition-transform"
          >
            {{ form.id ? 'Actualizar' : 'Crear' }} producto
          </button>
          <button
            type="button"
            v-if="form.id"
            @click="resetForm"
            class="px-3 py-2 rounded-2xl border border-slate-200 text-xs text-slate-700 hover:bg-slate-50"
          >
            Cancelar edición
          </button>
        </div>
      </form>
    </section>

    <!-- Lista de productos -->
    <section class="bg-white/90 border border-pastel-pink/30 rounded-2xl p-4 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-display text-lg font-semibold text-slate-800">
          Lista de tortas
        </h2>
        <span class="text-[11px] text-slate-500">
          {{ products.length }} producto(s)
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-[11px] text-left">
          <thead>
            <tr class="border-b border-slate-200 text-slate-500">
              <th class="py-2 pr-3">Nombre</th>
              <th class="py-2 pr-3 hidden md:table-cell">Categoría</th>
              <th class="py-2 pr-3">Precio</th>
              <th class="py-2 pr-3">Stock</th>
              <th class="py-2 pr-3 hidden md:table-cell">Estado</th>
              <th class="py-2 pr-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="prod in products"
              :key="prod.id"
              class="border-b border-slate-100 hover:bg-pastel-cream/40"
            >
              <td class="py-2 pr-3">
                <div class="font-medium text-slate-800">
                  {{ prod.name }}
                </div>
                <div class="text-[10px] text-slate-500 line-clamp-1 md:line-clamp-none">
                  {{ prod.description }}
                </div>
              </td>
              <td class="py-2 pr-3 hidden md:table-cell">
                {{ prod.category?.name || '-' }}
              </td>
              <td class="py-2 pr-3">
                S/ {{ Number(prod.price).toFixed(2) }}
              </td>
              <td class="py-2 pr-3">
                <span
                  :class="prod.stock === 0 ? 'text-red-500' : 'text-slate-800'"
                >
                  {{ prod.stock }}
                </span>
              </td>
              <td class="py-2 pr-3 hidden md:table-cell">
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  :class="prod.is_active
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                    : 'bg-slate-100 text-slate-500 border border-slate-200'"
                >
                  {{ prod.is_active ? 'Activo' : 'Oculto' }}
                </span>
              </td>
              <td class="py-2 pr-0 text-right">
                <button
                  class="text-[11px] text-pastel-rose hover:underline mr-2"
                  @click="editProduct(prod)"
                >
                  Editar
                </button>
                <button
                  class="text-[11px] text-red-500 hover:underline"
                  @click="confirmDelete(prod)"
                >
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="!products.length">
              <td colspan="6" class="py-4 text-center text-slate-500">
                No hay productos registrados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Modal eliminación simple -->
    <div
      v-if="toDelete"
      class="fixed inset-0 bg-black/30 flex items-center justify-center z-40"
    >
      <div class="bg-white rounded-2xl p-4 w-full max-w-sm shadow-soft border border-slate-200 space-y-3 text-xs">
        <h3 class="font-display text-sm font-semibold text-slate-800">
          Eliminar producto
        </h3>
        <p class="text-slate-600">
          ¿Seguro que deseas eliminar la torta
          <strong>{{ toDelete.name }}</strong>? Esta acción no se puede deshacer.
        </p>
        <div class="flex justify-end gap-2 pt-1">
          <button
            class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50"
            @click="toDelete = null"
          >
            Cancelar
          </button>
          <button
            class="px-3 py-1.5 rounded-xl bg-red-500 text-white hover:bg-red-600"
            @click="deleteProduct"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import http from '@/api/http';

const products = ref([]);
const categories = ref([]);

const imageFile = ref(null);
const imagePreview = ref(null);

const loading = ref(false);
const error = ref('');
const success = ref('');
const toDelete = ref(null);

const emptyForm = () => ({
  id: null,
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category_id: '',
  is_active: true,
});

const form = reactive(emptyForm());

const fetchCategories = async () => {
  try {
    const { data } = await http.get('/categories');
    categories.value = data;
  } catch (e) {
    console.error(e);
  }
};

const onImageChange = (event) => {
  const file = event.target.files?.[0];
  if (!file) {
    imageFile.value = null;
    imagePreview.value = null;
    return;
  }
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const { data } = await http.get('/products');
    products.value = data;
  } catch (e) {
    console.error(e);
    error.value = 'No se pudieron cargar los productos.';
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  Object.assign(form, emptyForm());
  imageFile.value = null;
  imagePreview.value = null;
  error.value = '';
  success.value = '';
};

const editProduct = (prod) => {
  Object.assign(form, {
    id: prod.id,
    name: prod.name,
    description: prod.description,
    price: Number(prod.price),
    stock: prod.stock,
    category_id: prod.category_id,
    is_active: !!prod.is_active,
  });
  imageFile.value = null;
  imagePreview.value = prod.image_url || null;
  success.value = '';
  error.value = '';
};

const saveProduct = async () => {
  loading.value = true;
  error.value = '';
  success.value = '';

  const fd = new FormData();
  fd.append('name', form.name);
  fd.append('description', form.description || '');
  fd.append('price', form.price);
  fd.append('stock', form.stock);
  fd.append('category_id', form.category_id);
  fd.append('is_active', form.is_active ? '1' : '0');

  if (imageFile.value) {
    fd.append('image', imageFile.value);
  }

  try {
    if (form.id) {
      // Laravel acepta PUT con multipart, axios se encarga del header
      await http.post(`/products/${form.id}?_method=PUT`, fd);
      success.value = 'Producto actualizado correctamente.';
    } else {
      await http.post('/products', fd);
      success.value = 'Producto creado correctamente.';
    }

    await fetchProducts();
    if (!form.id) {
      resetForm();
    }
  } catch (e) {
    console.error(e);
    error.value =
      e.response?.data?.message || 'Ocurrió un error al guardar el producto.';
  } finally {
    loading.value = false;
  }
};

const confirmDelete = (prod) => {
  toDelete.value = prod;
};

const deleteProduct = async () => {
  if (!toDelete.value) return;

  try {
    await http.delete(`/products/${toDelete.value.id}`);
    toDelete.value = null;
    await fetchProducts();
  } catch (e) {
    console.error(e);
    error.value = 'No se pudo eliminar el producto.';
  }
};

const sortedProducts = computed(() =>
  [...products.value].sort((a, b) => a.name.localeCompare(b.name))
);

onMounted(async () => {
  await Promise.all([fetchCategories(), fetchProducts()]);
});

// Si quieres usar sortedProducts en vez de products en la tabla, cambia products por sortedProducts.
</script>
