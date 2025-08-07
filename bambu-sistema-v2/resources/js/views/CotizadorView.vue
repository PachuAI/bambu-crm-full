<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Cotizador</h1>
        <p class="mt-1 text-slate-400">Genera cotizaciones rápidamente</p>
      </div>
      
      <div class="flex items-center gap-3">
        <button 
          @click="clearQuote"
          class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white border border-slate-600 rounded-lg hover:border-slate-500 transition-colors"
        >
          Limpiar
        </button>
        <button 
          @click="saveQuote"
          :disabled="quoteItems.length === 0"
          class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Generar PDF
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <!-- Product Search & Selection -->
      <div class="xl:col-span-2 space-y-6">
        <!-- Search Products -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
          <h2 class="text-lg font-semibold text-white mb-4">Buscar Productos</h2>
          
          <div class="relative mb-4">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar productos por nombre, código o categoría..."
              class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              @input="searchProducts"
            />
          </div>

          <!-- Product List -->
          <div class="space-y-2 max-h-96 overflow-y-auto">
            <div
              v-for="product in filteredProducts"
              :key="product.id"
              class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-slate-600 transition-colors group"
            >
              <div class="flex-1">
                <h3 class="font-medium text-white">{{ product.name }}</h3>
                <p class="text-sm text-slate-400">Stock: {{ product.stock }} | ${{ product.price }}</p>
              </div>
              <button
                @click="addToQuote(product)"
                class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 opacity-0 group-hover:opacity-100 transition-all"
              >
                Agregar
              </button>
            </div>
            
            <div v-if="filteredProducts.length === 0" class="text-center py-8 text-slate-400">
              {{ searchTerm ? 'No se encontraron productos' : 'Escribe para buscar productos' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Quote Summary -->
      <div class="space-y-6">
        <!-- Quote Items -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
          <h2 class="text-lg font-semibold text-white mb-4">Cotización</h2>
          
          <div v-if="quoteItems.length === 0" class="text-center py-8 text-slate-400">
            No hay productos agregados
          </div>
          
          <div v-else class="space-y-3">
            <div
              v-for="(item, index) in quoteItems"
              :key="index"
              class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg"
            >
              <div class="flex-1 min-w-0">
                <h4 class="font-medium text-white truncate">{{ item.name }}</h4>
                <p class="text-sm text-slate-400">${{ item.price }} c/u</p>
              </div>
              
              <div class="flex items-center gap-2 ml-3">
                <button
                  @click="updateQuantity(index, item.quantity - 1)"
                  class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 text-white text-sm flex items-center justify-center"
                >
                  -
                </button>
                <span class="w-8 text-center text-white text-sm">{{ item.quantity }}</span>
                <button
                  @click="updateQuantity(index, item.quantity + 1)"
                  class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 text-white text-sm flex items-center justify-center"
                >
                  +
                </button>
                <button
                  @click="removeFromQuote(index)"
                  class="ml-2 w-6 h-6 rounded bg-red-600 hover:bg-red-700 text-white text-sm flex items-center justify-center"
                >
                  ×
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Totals -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
          <h3 class="text-lg font-semibold text-white mb-4">Resumen</h3>
          
          <div class="space-y-2">
            <div class="flex justify-between text-slate-400">
              <span>Subtotal:</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>IVA (21%):</span>
              <span>${{ tax.toFixed(2) }}</span>
            </div>
            <div class="border-t border-slate-700 pt-2 mt-2">
              <div class="flex justify-between text-white font-bold text-lg">
                <span>Total:</span>
                <span>${{ total.toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <!-- Client Selection -->
          <div class="mt-6">
            <label class="block text-sm font-medium text-slate-400 mb-2">Cliente</label>
            <select
              v-model="selectedClient"
              class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Seleccionar cliente...</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

interface Product {
  id: number
  name: string
  price: number
  stock: number
  category: string
}

interface QuoteItem extends Product {
  quantity: number
}

interface Client {
  id: number
  name: string
  email: string
}

// Reactive data
const searchTerm = ref('')
const products = ref<Product[]>([])
const quoteItems = ref<QuoteItem[]>([])
const selectedClient = ref('')
const clients = ref<Client[]>([])

// Computed properties
const filteredProducts = computed(() => {
  if (!searchTerm.value) return []
  
  const term = searchTerm.value.toLowerCase()
  return products.value.filter(product =>
    product.name.toLowerCase().includes(term) ||
    product.category.toLowerCase().includes(term)
  )
})

const subtotal = computed(() => {
  return quoteItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const tax = computed(() => subtotal.value * 0.21)

const total = computed(() => subtotal.value + tax.value)

// Methods
const searchProducts = () => {
  // In real app, this would make an API call
  // For now, we'll just filter the existing products
}

const addToQuote = (product: Product) => {
  const existingItem = quoteItems.value.find(item => item.id === product.id)
  
  if (existingItem) {
    existingItem.quantity += 1
  } else {
    quoteItems.value.push({
      ...product,
      quantity: 1
    })
  }
}

const updateQuantity = (index: number, newQuantity: number) => {
  if (newQuantity <= 0) {
    removeFromQuote(index)
  } else {
    quoteItems.value[index].quantity = newQuantity
  }
}

const removeFromQuote = (index: number) => {
  quoteItems.value.splice(index, 1)
}

const clearQuote = () => {
  quoteItems.value = []
  selectedClient.value = ''
}

const saveQuote = () => {
  // In real app, this would generate a PDF and/or save to database
  alert(`Cotización generada para ${quoteItems.value.length} productos. Total: $${total.value.toFixed(2)}`)
}

// Load sample data
onMounted(() => {
  // Sample products - in real app, these would come from API
  products.value = [
    { id: 1, name: 'Cerveza Rubia 1L', price: 450, stock: 120, category: 'Bebidas' },
    { id: 2, name: 'Gaseosa Cola 2.25L', price: 320, stock: 85, category: 'Bebidas' },
    { id: 3, name: 'Agua Mineral 2L', price: 180, stock: 200, category: 'Bebidas' },
    { id: 4, name: 'Cerveza Negra 1L', price: 480, stock: 90, category: 'Bebidas' },
    { id: 5, name: 'Papas Fritas 150g', price: 220, stock: 150, category: 'Snacks' },
    { id: 6, name: 'Galletas Chocolate', price: 180, stock: 100, category: 'Snacks' },
    { id: 7, name: 'Leche Entera 1L', price: 280, stock: 75, category: 'Lácteos' },
    { id: 8, name: 'Queso Cremoso 200g', price: 420, stock: 45, category: 'Lácteos' },
  ]
  
  // Sample clients - in real app, these would come from API
  clients.value = [
    { id: 1, name: 'Kiosco San Martín', email: 'sanmartin@email.com' },
    { id: 2, name: 'Almacén Don José', email: 'donjose@email.com' },
    { id: 3, name: 'Supermercado Luna', email: 'luna@email.com' },
    { id: 4, name: 'Kiosco 24hs', email: '24hs@email.com' },
  ]
})
</script>