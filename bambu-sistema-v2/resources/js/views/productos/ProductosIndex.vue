<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Productos</h1>
        <p class="text-slate-400 mt-1">Gestión de catálogo y stock</p>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
          + Nuevo Producto
        </button>
      </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-4">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchTerm"
            @input="debouncedSearch"
            type="text"
            placeholder="Buscar productos por nombre, SKU o descripción..."
            class="w-full px-4 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>
        
        <div class="flex gap-2">
          <select
            v-model="filters.marca"
            @change="applyFilters"
            class="px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todas las marcas</option>
            <option value="BAMBU">BAMBU</option>
          </select>
          
          <select
            v-model="filters.stock"
            @change="applyFilters"
            class="px-3 py-2 bg-slate-900 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos</option>
            <option value="con_stock">Con stock</option>
            <option value="sin_stock">Sin stock</option>
            <option value="stock_bajo">Stock bajo</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Tabla de productos -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-700 bg-slate-900/50">
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Producto</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">SKU</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Precio</th>
              <th class="text-center px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Stock Actual</th>
              <th class="text-center px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Stock Mínimo</th>
              <th class="text-center px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado</th>
              <th class="text-center px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody v-if="!loading">
            <tr
              v-for="producto in productos"
              :key="producto.id"
              class="border-b border-slate-700/50 hover:bg-slate-900/30 transition-colors"
            >
              <!-- Producto Info -->
              <td class="px-6 py-4">
                <div>
                  <h3 class="font-medium text-white">{{ producto.nombre }}</h3>
                  <p class="text-sm text-slate-400 mt-1">{{ producto.descripcion || 'Sin descripción' }}</p>
                  <span v-if="producto.marca_producto" class="inline-block mt-1 px-2 py-1 bg-indigo-600/20 text-indigo-400 text-xs rounded-full">
                    {{ producto.marca_producto }}
                  </span>
                </div>
              </td>
              
              <!-- SKU -->
              <td class="px-6 py-4">
                <code class="px-2 py-1 bg-slate-900 rounded text-sm text-slate-300">{{ producto.sku }}</code>
              </td>
              
              <!-- Precio -->
              <td class="px-6 py-4">
                <span class="font-semibold text-white">${{ formatPrice(producto.precio_base_l1) }}</span>
              </td>
              
              <!-- Stock Actual -->
              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <span class="font-bold text-lg" :class="getStockColor(producto.stock_actual, producto.stock_minimo)">
                    {{ producto.stock_actual || 0 }}
                  </span>
                  <div class="flex flex-col gap-1">
                    <button 
                      @click="ajustarStock(producto.id, 'incrementar')"
                      class="w-6 h-6 bg-green-600/20 hover:bg-green-600/40 text-green-400 rounded flex items-center justify-center text-xs font-bold transition-colors"
                      title="Agregar stock"
                    >
                      +
                    </button>
                    <button 
                      @click="ajustarStock(producto.id, 'decrementar')"
                      class="w-6 h-6 bg-red-600/20 hover:bg-red-600/40 text-red-400 rounded flex items-center justify-center text-xs font-bold transition-colors"
                      title="Reducir stock"
                    >
                      -
                    </button>
                  </div>
                </div>
              </td>
              
              <!-- Stock Mínimo -->
              <td class="px-6 py-4 text-center">
                <span class="text-slate-400">{{ producto.stock_minimo || 'N/A' }}</span>
              </td>
              
              <!-- Estado -->
              <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="getStatusClasses(producto)">
                  {{ getStatus(producto) }}
                </span>
              </td>
              
              <!-- Acciones -->
              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    @click="editProduct(producto.id)"
                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded transition-colors"
                    title="Editar"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="viewProduct(producto.id)"
                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded transition-colors"
                    title="Ver detalles"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="showHistorialStock(producto.id)"
                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded transition-colors"
                    title="Historial de stock"
                  >
                    <ClockIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          
          <!-- Loading state -->
          <tbody v-else>
            <tr v-for="n in 5" :key="n" class="border-b border-slate-700/50">
              <td v-for="col in 7" :key="col" class="px-6 py-4">
                <div class="h-4 bg-slate-700 rounded animate-pulse"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación -->
      <div v-if="pagination.total > pagination.per_page" class="px-6 py-4 border-t border-slate-700 flex items-center justify-between">
        <div class="text-sm text-slate-400">
          Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total }} productos
        </div>
        
        <div class="flex gap-2">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-2 bg-slate-700 hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded transition-colors"
          >
            Anterior
          </button>
          
          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-2 bg-slate-700 hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded transition-colors"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { PencilIcon, EyeIcon, ClockIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(true)
const searchTerm = ref('')
const productos = ref([])

const filters = reactive({
  marca: '',
  stock: ''
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

// Fetch productos
const fetchProductos = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.current_page.toString(),
      per_page: pagination.per_page.toString()
    })
    
    if (searchTerm.value) params.append('search', searchTerm.value)
    if (filters.marca) params.append('marca', filters.marca)
    if (filters.stock === 'con_stock') params.append('en_stock', 'true')
    
    const response = await axios.get(`/api/v1/productos?${params}`)
    const data = response.data.data
    
    productos.value = data.data || []
    pagination.current_page = data.current_page || 1
    pagination.last_page = data.last_page || 1
    pagination.per_page = data.per_page || 15
    pagination.total = data.total || 0
    pagination.from = data.from || 0
    pagination.to = data.to || 0
    
  } catch (error) {
    console.error('Error fetching productos:', error)
  } finally {
    loading.value = false
  }
}

// Debounced search
let searchTimeout: any = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1
    fetchProductos()
  }, 300)
}

const applyFilters = () => {
  pagination.current_page = 1
  fetchProductos()
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page
    fetchProductos()
  }
}

// Utilidades
const formatPrice = (price: number | string) => {
  return new Intl.NumberFormat('es-AR').format(Number(price))
}

const getStockColor = (stockActual: number, stockMinimo: number) => {
  if (!stockActual || stockActual === 0) return 'text-red-400'
  if (stockMinimo && stockActual <= stockMinimo) return 'text-orange-400'
  return 'text-green-400'
}

const getStatus = (producto: any) => {
  if (!producto.stock_actual || producto.stock_actual === 0) return 'Sin stock'
  if (producto.stock_minimo && producto.stock_actual <= producto.stock_minimo) return 'Stock bajo'
  return 'Disponible'
}

const getStatusClasses = (producto: any) => {
  const status = getStatus(producto)
  switch (status) {
    case 'Sin stock':
      return 'bg-red-500/20 text-red-400'
    case 'Stock bajo':
      return 'bg-orange-500/20 text-orange-400'
    case 'Disponible':
      return 'bg-green-500/20 text-green-400'
    default:
      return 'bg-slate-500/20 text-slate-400'
  }
}

// Acciones
const ajustarStock = (productoId: string, accion: 'incrementar' | 'decrementar') => {
  // TODO: Implementar modal para ajuste de stock con justificación
  console.log(`Ajustar stock del producto ${productoId}: ${accion}`)
}

const editProduct = (id: string) => {
  router.push(`/productos/${id}/edit`)
}

const viewProduct = (id: string) => {
  router.push(`/productos/${id}`)
}

const showHistorialStock = (id: string) => {
  router.push(`/productos/${id}/historial`)
}

onMounted(() => {
  fetchProductos()
})
</script>