<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Encabezado -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Reportes y Análisis</h1>
          <p class="text-gray-600 mt-1">Información detallada sobre entregas y operaciones</p>
        </div>
        <div class="flex space-x-3">
          <select v-model="tipoReporte" @change="cargarReporte" class="border rounded px-3 py-2">
            <option value="dashboard">Dashboard General</option>
            <option value="vehiculos">Reporte de Vehículos</option>
            <option value="entregas">Reporte de Entregas</option>
            <option value="operativo">Reporte Operativo</option>
          </select>
          <button
            @click="exportarReporte"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
          >
            📊 Exportar
          </button>
        </div>
      </div>

      <!-- Filtros de fecha -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-wrap items-center gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Desde</label>
            <input
              v-model="filtros.desde"
              type="date"
              class="mt-1 border rounded px-3 py-2"
              @change="cargarReporte"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Hasta</label>
            <input
              v-model="filtros.hasta"
              type="date"
              class="mt-1 border rounded px-3 py-2"
              @change="cargarReporte"
            />
          </div>
          <div class="flex space-x-2 mt-6">
            <button @click="setFiltroRapido('hoy')" class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
              Hoy
            </button>
            <button @click="setFiltroRapido('semana')" class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
              Esta semana
            </button>
            <button @click="setFiltroRapido('mes')" class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
              Este mes
            </button>
          </div>
        </div>
      </div>

      <!-- Dashboard General -->
      <div v-if="tipoReporte === 'dashboard'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <MetricCard
            titulo="Entregas Hoy"
            :valor="dashboardData.metricas?.entregas_hoy || 0"
            icono="✅"
            color="green"
          />
          <MetricCard
            titulo="Pendientes Entrega"
            :valor="dashboardData.metricas?.pendientes_entrega || 0"
            icono="⏳"
            color="blue"
          />
          <MetricCard
            titulo="Vehículos Activos"
            :valor="dashboardData.metricas?.vehiculos_activos || 0"
            icono="🚛"
          />
          <MetricCard
            titulo="Efectividad"
            :valor="dashboardData.metricas?.efectividad_entregas || 0"
            icono="🎯"
            sufijo="%"
            color="purple"
          />
          <MetricCard
            titulo="KM Recorridos"
            :valor="Math.round(dashboardData.metricas?.km_recorridos_hoy || 0)"
            icono="🛣️"
            sufijo=" km"
          />
        </div>
      </div>

      <!-- Reporte de Vehículos -->
      <div v-if="tipoReporte === 'vehiculos'" class="space-y-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Rendimiento por Vehículo</h3>
            <p class="text-sm text-gray-600">{{ formatearPeriodo(filtros.desde, filtros.hasta) }}</p>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vehículo
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total Repartos
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Entregados
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Efectividad
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    KM Totales
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Días Trabajados
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="vehiculo in vehiculosData" :key="vehiculo.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">{{ vehiculo.nombre_completo }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ vehiculo.total_repartos }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ vehiculo.repartos_entregados }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                        <div 
                          class="h-2 bg-green-500 rounded-full"
                          :style="{ width: vehiculo.efectividad + '%' }"
                        ></div>
                      </div>
                      <span class="text-sm text-gray-900">{{ vehiculo.efectividad }}%</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ vehiculo.km_totales }} km
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ vehiculo.dias_trabajados }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Reporte de Entregas -->
      <div v-if="tipoReporte === 'entregas'" class="space-y-6">
        <!-- Resumen general -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <MetricCard
            titulo="Total Repartos"
            :valor="entregasData.resumen?.total_repartos || 0"
            icono="📦"
          />
          <MetricCard
            titulo="Entregados"
            :valor="entregasData.resumen?.total_entregados || 0"
            icono="✅"
            color="green"
          />
          <MetricCard
            titulo="Fallidos"
            :valor="entregasData.resumen?.total_fallidos || 0"
            icono="❌"
            color="red"
          />
          <MetricCard
            titulo="Efectividad"
            :valor="entregasData.resumen?.efectividad_general || 0"
            icono="🎯"
            sufijo="%"
            color="purple"
          />
        </div>

        <!-- Entregas por cliente -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Top Clientes por Entregas</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total Repartos
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Entregados
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fallidos
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="cliente in entregasData.por_cliente?.slice(0, 10)" :key="cliente.cliente_id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">{{ cliente.nombre_comercial }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ cliente.total_repartos }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                    {{ cliente.entregados }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                    {{ cliente.fallidos }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Reporte Operativo -->
      <div v-if="tipoReporte === 'operativo'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Tiempo promedio -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Tiempo Promedio de Entrega</h3>
            <div class="text-3xl font-bold text-blue-600">
              {{ Math.round(operativoData.tiempo_promedio_entrega || 0) }} min
            </div>
            <p class="text-sm text-gray-600 mt-2">Desde salida hasta entrega</p>
          </div>

          <!-- Utilización de capacidad -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Utilización Promedio</h3>
            <div class="space-y-3">
              <div v-for="vehiculo in operativoData.capacidad_vehiculos?.slice(0, 3)" :key="vehiculo.id" class="flex justify-between">
                <span class="text-sm">{{ vehiculo.patente }}</span>
                <span class="text-sm font-medium">{{ Math.round(vehiculo.porcentaje_utilizacion || 0) }}%</span>
              </div>
            </div>
          </div>

          <!-- Distribución por horas -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Entregas por Hora</h3>
            <div class="space-y-2">
              <div v-for="hora in operativoData.repartos_por_hora?.slice(0, 5)" :key="hora.hora" class="flex justify-between">
                <span class="text-sm">{{ hora.hora }}:00</span>
                <div class="flex items-center space-x-2">
                  <div class="w-16 bg-gray-200 rounded-full h-2">
                    <div 
                      class="h-2 bg-blue-500 rounded-full"
                      :style="{ width: (hora.entregados / Math.max(...(operativoData.repartos_por_hora?.map(h => h.entregados) || [1]))) * 100 + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs text-gray-600">{{ hora.entregados }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="cargando" class="text-center py-12">
        <div class="text-gray-500">Cargando reporte...</div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import MetricCard from '@/components/dashboard/MetricCard.vue'

const tipoReporte = ref('dashboard')
const cargando = ref(false)

const filtros = ref({
  desde: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 días atrás
  hasta: new Date().toISOString().split('T')[0]
})

const dashboardData = ref<any>({})
const vehiculosData = ref<any[]>([])
const entregasData = ref<any>({})
const operativoData = ref<any>({})

const cargarReporte = async () => {
  cargando.value = true
  
  try {
    let endpoint = ''
    switch (tipoReporte.value) {
      case 'dashboard':
        endpoint = '/api/v1/reportes/dashboard'
        break
      case 'vehiculos':
        endpoint = `/api/v1/reportes/vehiculos?desde=${filtros.value.desde}&hasta=${filtros.value.hasta}`
        break
      case 'entregas':
        endpoint = `/api/v1/reportes/entregas?desde=${filtros.value.desde}&hasta=${filtros.value.hasta}`
        break
      case 'operativo':
        endpoint = `/api/v1/reportes/operativo?desde=${filtros.value.desde}&hasta=${filtros.value.hasta}`
        break
    }

    const response = await fetch(endpoint, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    })

    const data = await response.json()

    switch (tipoReporte.value) {
      case 'dashboard':
        dashboardData.value = data
        break
      case 'vehiculos':
        vehiculosData.value = data.vehiculos
        break
      case 'entregas':
        entregasData.value = data
        break
      case 'operativo':
        operativoData.value = data
        break
    }

  } catch (error) {
    console.error('Error al cargar reporte:', error)
  } finally {
    cargando.value = false
  }
}

const setFiltroRapido = (periodo: string) => {
  const hoy = new Date()
  
  switch (periodo) {
    case 'hoy':
      filtros.value.desde = hoy.toISOString().split('T')[0]
      filtros.value.hasta = hoy.toISOString().split('T')[0]
      break
    case 'semana':
      const inicioSemana = new Date(hoy)
      inicioSemana.setDate(hoy.getDate() - hoy.getDay())
      filtros.value.desde = inicioSemana.toISOString().split('T')[0]
      filtros.value.hasta = hoy.toISOString().split('T')[0]
      break
    case 'mes':
      const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1)
      filtros.value.desde = inicioMes.toISOString().split('T')[0]
      filtros.value.hasta = hoy.toISOString().split('T')[0]
      break
  }
  
  cargarReporte()
}

const formatearPeriodo = (desde: string, hasta: string): string => {
  const fechaDesde = new Date(desde)
  const fechaHasta = new Date(hasta)
  return `${fechaDesde.toLocaleDateString()} - ${fechaHasta.toLocaleDateString()}`
}

const exportarReporte = () => {
  // Implementar exportación
  alert('Función de exportación pendiente de implementar')
}

onMounted(() => {
  cargarReporte()
})
</script>