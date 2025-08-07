<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-white">Dashboard</h1>
        <p class="mt-2 text-slate-400">Control operativo BAMBU - Alto Valle</p>
      </div>
      
      <!-- Date Range Selector -->
      <div class="flex items-center gap-2">
        <button 
          class="px-4 py-2 rounded font-medium text-sm inline-flex items-center gap-2 transition-all duration-150 border border-slate-600 text-slate-400 hover:bg-slate-800 hover:text-white"
        >
          <CalendarIcon class="w-4 h-4" />
          <span>Últimos 30 días</span>
          <ChevronDownIcon class="w-4 h-4" />
        </button>
      </div>
    </div>
    
    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <MetricCard
        v-for="metric in metrics"
        :key="metric.id"
        :title="metric.title"
        :value="metric.value"
        :trend="metric.trend"
        :trendValue="metric.trendValue"
        :icon="metric.icon"
        :loading="loading"
      />
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <!-- Sales Chart (2/3 width) -->
      <div class="lg:col-span-2 rounded-lg bg-slate-800 border border-slate-700">
        <div class="p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-white">Facturación del Mes</h2>
            <div class="flex gap-2">
              <button class="px-3 py-1 text-xs rounded text-white bg-indigo-600">
                Mensual
              </button>
              <button class="px-3 py-1 text-xs rounded text-slate-400 hover:bg-slate-700 hover:text-white transition-colors">
                Semanal
              </button>
              <button class="px-3 py-1 text-xs rounded text-slate-400 hover:bg-slate-700 hover:text-white transition-colors">
                Diario
              </button>
            </div>
          </div>
          
          <!-- Placeholder for chart -->
          <div class="h-56 flex items-center justify-center rounded bg-slate-900">
            <p class="text-slate-500">Gráfico de facturación</p>
          </div>
        </div>
      </div>
      
      <!-- Products Distribution (1/3 width) -->
      <div class="rounded-lg bg-slate-800 border border-slate-700">
        <div class="p-5">
          <h2 class="text-lg font-semibold mb-3 text-white">Productos Más Vendidos</h2>
          
          <!-- Product list -->
          <div class="space-y-2">
            <div v-for="product in topProducts" :key="product.id" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: product.color }"></div>
                <div>
                  <p class="text-sm font-medium text-white">{{ product.name }}</p>
                  <p class="text-xs text-slate-400">{{ product.quantity }} unidades</p>
                </div>
              </div>
              <span class="text-sm font-semibold text-white">
                {{ product.percentage }}%
              </span>
            </div>
          </div>
          
          <!-- Placeholder for donut chart -->
          <div class="mt-3 h-28 flex items-center justify-center rounded bg-slate-900">
            <p class="text-sm text-slate-500">Gráfico circular</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Activity Table -->
    <div class="rounded-lg bg-slate-800 border border-slate-700">
      <div class="px-5 py-3 flex items-center justify-between border-b border-slate-700">
        <h2 class="text-lg font-semibold text-white">Pedidos Recientes</h2>
        <router-link 
          to="/pedidos" 
          class="text-sm hover:text-indigo-300 text-indigo-400 transition-colors duration-150"
        >
          Ver todos →
        </router-link>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-700">
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">#Pedido</th>
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">Cliente</th>
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">Productos</th>
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">Total</th>
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">Estado</th>
              <th class="text-left px-5 py-2 text-sm font-medium text-slate-400">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="order in recentOrders" 
              :key="order.id" 
              class="border-b border-slate-700 hover:bg-slate-750/30 transition-all duration-150 hover:scale-[1.005]"
            >
              <td class="px-5 py-2">
                <span class="text-sm font-medium text-white">#{{ order.id }}</span>
              </td>
              <td class="px-5 py-2">
                <span class="text-sm text-white">{{ order.customer }}</span>
              </td>
              <td class="px-5 py-2">
                <span class="text-sm text-slate-400">{{ order.items }} items</span>
              </td>
              <td class="px-5 py-2">
                <span class="text-sm font-semibold text-white">${{ order.total }}</span>
              </td>
              <td class="px-5 py-2">
                <span 
                  class="px-2 py-1 text-xs font-medium rounded"
                  :class="{
                    'bg-green-900/30 text-green-400': order.status === 'Entregado',
                    'bg-blue-900/30 text-blue-400': order.status === 'En flete',
                    'bg-yellow-900/30 text-yellow-400': order.status === 'Preparando',
                    'bg-red-900/30 text-red-400': order.status === 'Cancelado'
                  }"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-5 py-2">
                <span class="text-sm text-slate-400">{{ order.date }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { 
  CalendarIcon, 
  ChevronDownIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UserGroupIcon,
  TruckIcon
} from '@heroicons/vue/24/outline'
import MetricCard from '@/components/dashboard/MetricCard.vue'

const loading = ref(false)

const metrics = ref([
  {
    id: 1,
    title: 'Facturación Mensual',
    value: '$2,847,650',
    trend: 'up',
    trendValue: '+18.3%',
    icon: CurrencyDollarIcon
  },
  {
    id: 2,
    title: 'Pedidos del Mes',
    value: '284',
    trend: 'up',
    trendValue: '+12.7%',
    icon: ShoppingCartIcon
  },
  {
    id: 3,
    title: 'Clientes Activos',
    value: '147',
    trend: 'up',
    trendValue: '+5.8%',
    icon: UserGroupIcon
  },
  {
    id: 4,
    title: 'Fletes Hoy',
    value: '12',
    trend: 'up',
    trendValue: '+25.0%',
    icon: TruckIcon
  }
])

const topProducts = ref([
  { id: 1, name: 'Detergente BAMBU (Bidón 5L)', quantity: 145, percentage: 32, color: '#6366f1' },
  { id: 2, name: 'Desinfectante BAMBU (Bidón 5L)', quantity: 128, percentage: 28, color: '#8b5cf6' },
  { id: 3, name: 'Limpiador de pisos BAMBU (Bidón 5L)', quantity: 96, percentage: 21, color: '#06b6d4' },
  { id: 4, name: 'Jabón líquido BAMBU (Bidón 5L)', quantity: 86, percentage: 19, color: '#10b981' }
])

const recentOrders = ref([
  { id: '2847', customer: 'Supermercado Don Juan - General Roca', items: 24, total: '18,720.00', status: 'En flete', date: 'Hoy, 14:30' },
  { id: '2846', customer: 'Limpieza Pro S.A. - Neuquén', items: 16, total: '12,480.00', status: 'Entregado', date: 'Hoy, 12:15' },
  { id: '2845', customer: 'Municipalidad de Villa Regina', items: 35, total: '27,300.00', status: 'Preparando', date: 'Hoy, 10:00' },
  { id: '2844', customer: 'Minimercado Avenida - Cipolletti', items: 12, total: '9,360.00', status: 'Entregado', date: 'Ayer, 18:45' },
  { id: '2843', customer: 'María Beltrán - Fernández Oro', items: 8, total: '6,240.00', status: 'Cancelado', date: 'Ayer, 16:20' }
])

const getStatusStyle = (status: string) => {
  const styles: Record<string, string> = {
    'Entregado': 'background-color: rgba(34, 197, 94, 0.2); color: var(--success)',
    'En camino': 'background-color: rgba(99, 102, 241, 0.2); color: var(--primary)',
    'Preparando': 'background-color: rgba(245, 158, 11, 0.2); color: var(--warning)',
    'Cancelado': 'background-color: rgba(239, 68, 68, 0.2); color: var(--danger)'
  }
  return styles[status] || 'background-color: var(--surface-1); color: var(--text-secondary)'
}

onMounted(() => {
  // Load dashboard data
  // This would typically fetch from API
})
</script>