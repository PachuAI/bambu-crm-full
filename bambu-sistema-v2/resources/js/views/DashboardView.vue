<template>
  <div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <div class="mt-1 space-y-1">
          <p class="text-sm text-slate-400">Control operativo BAMBU - Alto Valle</p>
          <p class="text-xs text-slate-500 flex items-center gap-2">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
            </svg>
            Última actualización: {{ lastUpdated }}
          </p>
        </div>
      </div>
      
      <!-- Date Range Selector and User Avatar -->
      <div class="flex items-center gap-4">
        <button 
          class="px-4 py-2 rounded-lg font-medium text-sm inline-flex items-center gap-2 transition-colors duration-200 border border-slate-600 text-slate-400 hover:bg-slate-800 hover:text-white h-10"
        >
          <CalendarIcon class="w-4 h-4" />
          <span>Últimos 30 días</span>
          <ChevronDownIcon class="w-4 h-4" />
        </button>
        
        <!-- User Avatar -->
        <div class="flex items-center gap-3 pl-4 border-l border-slate-600">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold">
              {{ userInitials }}
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-white">{{ userName }}</p>
              <p class="text-xs text-slate-400">Administrador</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Metrics Grid con espaciado consistente -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
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
    
    <!-- Main Content Grid con proporciones coherentes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Facturación Chart (2/3 width) -->
      <div class="lg:col-span-2 bg-slate-800 rounded-lg border border-slate-700">
        <div class="p-4">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-lg font-semibold text-white">Facturación del Mes</h2>
              <p class="text-sm text-slate-400 mt-1">Evolución de ventas</p>
            </div>
            <div class="flex gap-1 bg-slate-900 rounded-lg p-1">
              <button class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-indigo-600 h-8">
                Mensual
              </button>
              <button class="px-3 py-1.5 text-xs font-medium rounded-md text-slate-400 hover:text-white transition-colors h-8">
                Semanal
              </button>
              <button class="px-3 py-1.5 text-xs font-medium rounded-md text-slate-400 hover:text-white transition-colors h-8">
                Diario
              </button>
            </div>
          </div>
          
          <!-- Chart con altura consistente -->
          <div class="h-64 rounded-lg bg-slate-900/50 border border-slate-700/50 relative overflow-hidden">
            <!-- Mock chart bars con mejor proporción -->
            <div class="absolute bottom-6 left-6 right-6 flex items-end justify-between h-40">
              <div v-for="(height, index) in chartData" :key="index" 
                   class="bg-indigo-500 rounded-t flex-1 mx-0.5 transition-all duration-300 hover:bg-indigo-400"
                   :style="{ height: height + '%', opacity: 0.8 }">
              </div>
            </div>
            <!-- Overlay con tipografía coherente -->
            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/80">
              <div class="text-center">
                <div class="text-3xl font-bold text-white tracking-tight">$2,847,650</div>
                <div class="text-sm text-slate-400 mt-1">Total este mes</div>
              </div>
            </div>
          </div>
          
          <!-- Quick stats con spacing sistemático -->
          <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="text-center p-3 bg-slate-900/30 rounded-lg">
              <div class="text-lg font-bold text-green-400">+18.3%</div>
              <div class="text-xs text-slate-500 mt-1">vs mes anterior</div>
            </div>
            <div class="text-center p-3 bg-slate-900/30 rounded-lg">
              <div class="text-lg font-bold text-white">$94,922</div>
              <div class="text-xs text-slate-500 mt-1">promedio diario</div>
            </div>
            <div class="text-center p-3 bg-slate-900/30 rounded-lg">
              <div class="text-lg font-bold text-blue-400">284</div>
              <div class="text-xs text-slate-500 mt-1">pedidos totales</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Top Products (1/3 width) con jerarquía clara -->
      <div class="bg-slate-800 rounded-lg border border-slate-700">
        <div class="p-4">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-lg font-semibold text-white">Productos Destacados</h2>
              <p class="text-sm text-slate-400 mt-1">Este mes</p>
            </div>
            <span class="text-xs text-slate-500 uppercase tracking-wider">Top 5</span>
          </div>
          
          <!-- Lista compacta con spacing consistente -->
          <div class="space-y-2">
            <div 
              v-for="(product, index) in topProducts" 
              :key="product.id" 
              class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-900/30 hover:bg-slate-900/50 transition-colors duration-200"
            >
              <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: product.color }"></div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-white truncate">{{ product.name }}</p>
                  <p class="text-xs text-slate-400">{{ product.quantity }} unidades</p>
                </div>
              </div>
              <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-lg font-bold text-white">{{ product.percentage }}%</span>
                <!-- Progress bar con proporciones coherentes -->
                <div class="w-12 h-2 bg-slate-700 rounded-full overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all duration-300" 
                    :style="{ 
                      width: `${product.percentage}%`, 
                      backgroundColor: product.color 
                    }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Summary con spacing coherente -->
          <div class="mt-4 pt-3 border-t border-slate-700/50">
            <div class="flex justify-between text-sm">
              <span class="text-slate-400">Total vendido:</span>
              <span class="text-white font-semibold">517 unidades</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Orders Table con tipografía sistemática -->
    <div class="bg-slate-800 rounded-lg border border-slate-700">
      <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700">
        <div>
          <h2 class="text-lg font-semibold text-white">Pedidos Recientes</h2>
          <p class="text-sm text-slate-400 mt-1">Últimas transacciones</p>
        </div>
        <router-link 
          to="/pedidos" 
          class="text-sm hover:text-indigo-300 text-indigo-400 transition-colors duration-200 font-medium"
        >
          Ver todos →
        </router-link>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-700/50 bg-slate-900/20">
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">#</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cliente</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Producto</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Total</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="order in recentOrders" 
              :key="order.id" 
              class="border-b border-slate-700/50 hover:bg-slate-900/30 transition-colors duration-200 cursor-pointer h-16"
              @click="navigateToOrder(order.id)"
            >
              <td class="px-6 py-4">
                <span class="text-sm font-semibold text-white">#{{ order.id }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-medium text-white">{{ order.customer }}</span>
              </td>
              <td class="px-6 py-4">
                <div>
                  <p class="text-sm font-medium text-white">{{ order.product }}</p>
                  <p class="text-xs text-slate-400">{{ order.items }} items</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-bold text-white">${{ order.total }}</span>
              </td>
              <td class="px-6 py-4">
                <span 
                  class="px-3 py-1.5 text-xs font-semibold rounded-full border"
                  :class="getStatusClasses(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4">
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
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { 
  CalendarIcon, 
  ChevronDownIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UserGroupIcon,
  TruckIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import MetricCard from '@/components/dashboard/MetricCard.vue'

const router = useRouter()
const loading = ref(false)
const lastUpdated = ref('')
const userName = ref('Juan Pérez')
const userInitials = ref('JP')

const navigateToOrder = (orderId: string) => {
  router.push(`/pedidos/${orderId}`)
}

const updateLastUpdated = () => {
  const now = new Date()
  lastUpdated.value = now.toLocaleString('es-AR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const metrics = ref([
  {
    id: 1,
    title: 'Ventas Totales',
    value: '$2,847,650',
    trend: 'up',
    trendValue: '+18.3%',
    icon: CurrencyDollarIcon
  },
  {
    id: 2,
    title: 'Pedidos',
    value: '284',
    trend: 'up',
    trendValue: '+12.7%',
    icon: ShoppingCartIcon
  },
  {
    id: 3,
    title: 'Clientes',
    value: '147',
    trend: 'up',
    trendValue: '+5.8%',
    icon: UserGroupIcon
  },
  {
    id: 4,
    title: 'Entregas Hoy',
    value: '12',
    trend: 'up',
    trendValue: '+25.0%',
    icon: TruckIcon
  },
  {
    id: 5,
    title: 'Pendientes Entrega',
    value: '7',
    trend: 'down',
    trendValue: '-15.2%',
    icon: ClockIcon
  }
])

const chartData = ref([45, 65, 35, 78, 52, 89, 45, 67, 73, 82, 69, 91])

const topProducts = ref([
  { id: 1, name: 'Detergente BAMBU (Bidón 5L)', quantity: 145, percentage: 32, color: '#10b981' },
  { id: 2, name: 'Desinfectante BAMBU (Bidón 5L)', quantity: 128, percentage: 28, color: '#6366f1' },
  { id: 3, name: 'Limpiador de pisos BAMBU (Bidón 5L)', quantity: 96, percentage: 21, color: '#06b6d4' },
  { id: 4, name: 'Jabón líquido BAMBU (Bidón 5L)', quantity: 86, percentage: 19, color: '#f59e0b' },
  { id: 5, name: 'Suavizante BAMBU (Bidón 5L)', quantity: 62, percentage: 14, color: '#ec4899' }
])

const recentOrders = ref([
  { 
    id: '2847', 
    customer: 'Supermercado Don Juan - General Roca', 
    product: 'Detergente BAMBU (Bidón 5L) + Limpiador',
    items: 24, 
    total: '18,720.00', 
    status: 'En camino', 
    date: 'Hoy, 14:30' 
  },
  { 
    id: '2846', 
    customer: 'Limpieza Pro S.A. - Neuquén', 
    product: 'Desinfectante BAMBU (Bidón 5L)',
    items: 16, 
    total: '12,480.00', 
    status: 'Entregado', 
    date: 'Hoy, 12:15' 
  },
  { 
    id: '2845', 
    customer: 'Municipalidad de Villa Regina', 
    product: 'Mix Productos BAMBU',
    items: 35, 
    total: '27,300.00', 
    status: 'Listo para enviar', 
    date: 'Hoy, 10:00' 
  },
  { 
    id: '2844', 
    customer: 'Minimercado Avenida - Cipolletti', 
    product: 'Jabón líquido BAMBU (Bidón 5L)',
    items: 12, 
    total: '9,360.00', 
    status: 'Listo para enviar', 
    date: 'Ayer, 18:45' 
  },
  { 
    id: '2843', 
    customer: 'María Beltrán - Fernández Oro', 
    product: 'Limpiador de pisos BAMBU',
    items: 8, 
    total: '6,240.00', 
    status: 'Pendiente de armado', 
    date: 'Ayer, 16:20' 
  }
])

const getStatusClasses = (status: string) => {
  const classes = {
    'Entregado': 'bg-green-500/20 text-green-400 border-green-500/30',
    'En camino': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    'Listo para enviar': 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    'Preparando': 'bg-orange-500/20 text-orange-400 border-orange-500/30',
    'Pendiente de armado': 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    'Cancelado': 'bg-red-500/20 text-red-400 border-red-500/30'
  }
  return classes[status] || 'bg-slate-500/20 text-slate-400 border-slate-500/30'
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/reportes/dashboard')
    const data = response.data
    
    // Actualizar métricas
    metrics.value[0].value = `$${data.ventas_totales?.toLocaleString() || 0}`
    metrics.value[0].trendValue = `${data.crecimiento_ventas || 0}%`
    metrics.value[0].trend = data.crecimiento_ventas > 0 ? 'up' : 'down'
    
    metrics.value[1].value = data.pedidos_mes?.toString() || '0'
    metrics.value[1].trendValue = `+${data.pedidos_hoy || 0} hoy`
    
    metrics.value[2].value = data.clientes_activos?.toString() || '0'
    metrics.value[2].trendValue = `+${data.clientes_nuevos || 0} nuevos`
    
    metrics.value[3].value = data.entregas_hoy?.toString() || '0'
    metrics.value[3].trendValue = `${data.efectividad || 0}% efectividad`
    
    metrics.value[4].value = data.vehiculos_disponibles?.toString() || '0'
    metrics.value[4].trendValue = `de ${data.vehiculos_totales || 0} total`
    
    // Actualizar pedidos recientes
    if (data.pedidos_recientes) {
      recentOrders.value = data.pedidos_recientes.map((pedido: any) => ({
        id: pedido.id,
        client: pedido.cliente,
        amount: `$${pedido.total?.toLocaleString() || 0}`,
        status: mapStatus(pedido.estado),
        date: new Date(pedido.fecha).toLocaleDateString('es-AR')
      }))
    }
    
    // Actualizar productos destacados
    if (data.productos_destacados) {
      topProducts.value = data.productos_destacados.map((producto: any) => ({
        id: producto.id,
        name: producto.nombre,
        sales: producto.ventas || 0,
        percentage: producto.porcentaje || 0
      }))
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const mapStatus = (estado: string) => {
  const statusMap: Record<string, string> = {
    'confirmado': 'En proceso',
    'listo_envio': 'Listo envío',
    'en_transito': 'En tránsito',
    'entregado': 'Completado',
    'cancelado': 'Cancelado'
  }
  return statusMap[estado] || estado
}

onMounted(() => {
  updateLastUpdated()
  fetchDashboardData()
  
  // Actualizar cada 5 minutos
  setInterval(() => {
    updateLastUpdated()
    fetchDashboardData()
  }, 5 * 60 * 1000)
})
</script>