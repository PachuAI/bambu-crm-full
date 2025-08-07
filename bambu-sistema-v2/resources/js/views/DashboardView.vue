<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold" style="color: var(--text-primary)">Dashboard</h1>
        <p class="mt-1" style="color: var(--text-secondary)">Resumen de tu negocio en tiempo real</p>
      </div>
      
      <!-- Date Range Selector -->
      <div class="flex items-center gap-2">
        <button 
          class="px-4 py-2 rounded-md font-medium text-sm inline-flex items-center gap-2 transition-all duration-150"
          style="background-color: transparent; 
                 color: var(--text-secondary); 
                 border: 1px solid var(--surface-1)"
          @mouseover="$event.target.style.backgroundColor = 'var(--surface-1)'; $event.target.style.color = 'var(--text-primary)'"
          @mouseleave="$event.target.style.backgroundColor = 'transparent'; $event.target.style.color = 'var(--text-secondary)'"
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Sales Chart (2/3 width) -->
      <div class="lg:col-span-2 rounded-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--surface-1)">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold" style="color: var(--text-primary)">Ventas del Mes</h2>
            <div class="flex gap-2">
              <button 
                class="px-3 py-1 text-xs rounded-md text-white"
                style="background-color: var(--primary)"
              >
                Mensual
              </button>
              <button 
                class="px-3 py-1 text-xs rounded-md transition-colors"
                style="color: var(--text-secondary)"
                @mouseover="$event.target.style.backgroundColor = 'var(--surface-1)'"
                @mouseleave="$event.target.style.backgroundColor = 'transparent'"
              >
                Semanal
              </button>
              <button 
                class="px-3 py-1 text-xs rounded-md transition-colors"
                style="color: var(--text-secondary)"
                @mouseover="$event.target.style.backgroundColor = 'var(--surface-1)'"
                @mouseleave="$event.target.style.backgroundColor = 'transparent'"
              >
                Diario
              </button>
            </div>
          </div>
          
          <!-- Placeholder for chart -->
          <div 
            class="h-64 flex items-center justify-center rounded-lg"
            style="background-color: var(--surface-1)"
          >
            <p style="color: var(--text-muted)">Gráfico de ventas</p>
          </div>
        </div>
      </div>
      
      <!-- Products Distribution (1/3 width) -->
      <div class="rounded-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--surface-1)">
        <div class="p-6">
          <h2 class="text-lg font-semibold mb-4" style="color: var(--text-primary)">Productos Más Vendidos</h2>
          
          <!-- Product list -->
          <div class="space-y-3">
            <div v-for="product in topProducts" :key="product.id" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: product.color }"></div>
                <div>
                  <p class="text-sm font-medium" style="color: var(--text-primary)">{{ product.name }}</p>
                  <p class="text-xs" style="color: var(--text-muted)">{{ product.quantity }} unidades</p>
                </div>
              </div>
              <span class="text-sm font-semibold" style="color: var(--text-primary)">
                {{ product.percentage }}%
              </span>
            </div>
          </div>
          
          <!-- Placeholder for donut chart -->
          <div 
            class="mt-4 h-32 flex items-center justify-center rounded-lg"
            style="background-color: var(--surface-1)"
          >
            <p class="text-sm" style="color: var(--text-muted)">Gráfico circular</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Activity Table -->
    <div class="rounded-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--surface-1)">
      <div 
        class="px-6 py-4 flex items-center justify-between"
        style="border-bottom: 1px solid var(--surface-1)"
      >
        <h2 class="text-lg font-semibold" style="color: var(--text-primary)">Pedidos Recientes</h2>
        <router-link 
          to="/pedidos" 
          class="text-sm hover:opacity-80"
          style="color: var(--primary)"
        >
          Ver todos →
        </router-link>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="border-bottom: 1px solid var(--surface-1)">
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">#Pedido</th>
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">Cliente</th>
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">Productos</th>
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">Total</th>
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">Estado</th>
              <th class="text-left p-4 text-sm font-medium" style="color: var(--text-secondary)">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="order in recentOrders" 
              :key="order.id" 
              class="transition-colors"
              style="border-bottom: 1px solid var(--surface-1)"
              @mouseover="$event.target.style.backgroundColor = 'var(--surface-1)'"
              @mouseleave="$event.target.style.backgroundColor = 'transparent'"
            >
              <td class="p-4">
                <span class="text-sm font-medium" style="color: var(--text-primary)">#{{ order.id }}</span>
              </td>
              <td class="p-4">
                <span class="text-sm" style="color: var(--text-primary)">{{ order.customer }}</span>
              </td>
              <td class="p-4">
                <span class="text-sm" style="color: var(--text-secondary)">{{ order.items }} items</span>
              </td>
              <td class="p-4">
                <span class="text-sm font-semibold" style="color: var(--text-primary)">${{ order.total }}</span>
              </td>
              <td class="p-4">
                <span 
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :style="getStatusStyle(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="p-4">
                <span class="text-sm" style="color: var(--text-secondary)">{{ order.date }}</span>
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
    title: 'Ventas Totales',
    value: '$125,430',
    trend: 'up',
    trendValue: '+12.5%',
    icon: CurrencyDollarIcon
  },
  {
    id: 2,
    title: 'Pedidos',
    value: '356',
    trend: 'up',
    trendValue: '+8.2%',
    icon: ShoppingCartIcon
  },
  {
    id: 3,
    title: 'Clientes',
    value: '2,456',
    trend: 'down',
    trendValue: '-2.4%',
    icon: UserGroupIcon
  },
  {
    id: 4,
    title: 'Entregas Hoy',
    value: '28',
    trend: 'up',
    trendValue: '+15.3%',
    icon: TruckIcon
  }
])

const topProducts = ref([
  { id: 1, name: 'Cerveza Rubia 1L', quantity: 450, percentage: 35, color: '#6366f1' },
  { id: 2, name: 'Gaseosa Cola 2.25L', quantity: 320, percentage: 25, color: '#8b5cf6' },
  { id: 3, name: 'Agua Mineral 2L', quantity: 280, percentage: 22, color: '#06b6d4' },
  { id: 4, name: 'Cerveza Negra 1L', quantity: 230, percentage: 18, color: '#10b981' }
])

const recentOrders = ref([
  { id: '1234', customer: 'Kiosco San Martín', items: 12, total: '1,234.50', status: 'Entregado', date: 'Hoy, 14:30' },
  { id: '1233', customer: 'Almacén Don José', items: 8, total: '856.00', status: 'En camino', date: 'Hoy, 12:15' },
  { id: '1232', customer: 'Supermercado Luna', items: 25, total: '3,450.75', status: 'Preparando', date: 'Hoy, 10:00' },
  { id: '1231', customer: 'Kiosco 24hs', items: 15, total: '1,890.25', status: 'Entregado', date: 'Ayer, 18:45' },
  { id: '1230', customer: 'Minimercado Centro', items: 18, total: '2,156.50', status: 'Cancelado', date: 'Ayer, 16:20' }
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