<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Encabezado -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Seguimiento en Tiempo Real</h1>
          <p class="text-gray-600 mt-1">Monitorea las entregas del día</p>
        </div>
        <div class="text-sm text-gray-600">
          Última actualización: {{ ultimaActualizacion }}
        </div>
      </div>

      <!-- Métricas del día -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <MetricCard
          titulo="Total Programados"
          :valor="estadisticas.total_programados"
          icono="📋"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="En Ruta"
          :valor="estadisticas.en_ruta"
          icono="🚛"
          color="orange"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="Entregados"
          :valor="estadisticas.entregados"
          icono="✅"
          color="green"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="Fallidos"
          :valor="estadisticas.fallidos"
          icono="❌"
          color="red"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="Pendientes"
          :valor="estadisticas.pendientes"
          icono="⏳"
          color="blue"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
      </div>

      <!-- Panel de control -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center">
          <div class="flex space-x-4">
            <select v-model="filtroEstado" class="border rounded px-3 py-2">
              <option value="">Todos los estados</option>
              <option value="programado">Programados</option>
              <option value="en_ruta">En Ruta</option>
              <option value="entregado">Entregados</option>
              <option value="fallido">Fallidos</option>
            </select>
            <select v-model="filtroVehiculo" class="border rounded px-3 py-2">
              <option value="">Todos los vehículos</option>
              <option v-for="vehiculo in vehiculosActivos" :key="vehiculo.id" :value="vehiculo.id">
                {{ vehiculo.patente }}
              </option>
            </select>
          </div>
          
          <div class="flex space-x-2">
            <button
              @click="toggleAutoRefresh"
              :class="autoRefresh ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="px-4 py-2 rounded-lg font-medium transition-colors"
            >
              {{ autoRefresh ? '🔄 Auto-refresh ON' : '⏸️ Auto-refresh OFF' }}
            </button>
            <button
              @click="actualizarDatos"
              :disabled="cargando"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
            >
              {{ cargando ? 'Cargando...' : 'Actualizar' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Lista de repartos -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente / Pedido
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vehículo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Horarios
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Progreso
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="reparto in repartosFiltrados" :key="reparto.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="font-medium text-gray-900">
                      {{ reparto.pedido.cliente.nombre_comercial }}
                    </div>
                    <div class="text-sm text-gray-500">
                      Pedido #{{ reparto.pedido.id }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div v-if="reparto.vehiculo" class="text-sm">
                    <div class="font-medium text-gray-900">{{ reparto.vehiculo.patente }}</div>
                    <div class="text-gray-500">{{ reparto.vehiculo.marca }} {{ reparto.vehiculo.modelo }}</div>
                  </div>
                  <span v-else class="text-gray-400">Sin asignar</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getEstadoBadgeClass(reparto.estado)">
                    {{ getEstadoTexto(reparto.estado) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>
                    <div v-if="reparto.hora_salida">Salida: {{ reparto.hora_salida }}</div>
                    <div v-if="reparto.hora_entrega">Entrega: {{ reparto.hora_entrega }}</div>
                    <div v-if="!reparto.hora_salida && !reparto.hora_entrega" class="text-gray-400">
                      Sin horarios
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        :class="getProgresoColor(reparto.estado)"
                        class="h-2 rounded-full transition-all duration-300"
                        :style="{ width: getProgresoWidth(reparto.estado) + '%' }"
                      ></div>
                    </div>
                    <span class="ml-2 text-xs text-gray-600">{{ getProgresoWidth(reparto.estado) }}%</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <button
                      v-if="reparto.estado === 'programado'"
                      @click="iniciarReparto(reparto)"
                      class="text-blue-600 hover:text-blue-900 px-2 py-1 text-xs bg-blue-50 rounded"
                    >
                      Iniciar
                    </button>
                    <button
                      v-if="reparto.estado === 'en_ruta'"
                      @click="completarReparto(reparto)"
                      class="text-green-600 hover:text-green-900 px-2 py-1 text-xs bg-green-50 rounded"
                    >
                      Entregar
                    </button>
                    <button
                      v-if="reparto.estado === 'en_ruta'"
                      @click="marcarFallido(reparto)"
                      class="text-red-600 hover:text-red-900 px-2 py-1 text-xs bg-red-50 rounded"
                    >
                      Falló
                    </button>
                    <button
                      @click="verDetalle(reparto)"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      Ver
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="repartosFiltrados.length === 0" class="text-center py-12">
          <div class="text-gray-500">No hay repartos programados para hoy</div>
        </div>
      </div>

      <!-- Vehículos activos -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Estado de Vehículos</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="vehiculo in vehiculosActivos"
            :key="vehiculo.id"
            class="border rounded-lg p-4"
          >
            <div class="flex justify-between items-center">
              <div>
                <div class="font-medium">{{ vehiculo.patente }}</div>
                <div class="text-sm text-gray-600">{{ vehiculo.marca }} {{ vehiculo.modelo }}</div>
              </div>
              <span :class="getVehiculoEstadoClass(vehiculo.repartos)">
                {{ getVehiculoEstadoTexto(vehiculo.repartos) }}
              </span>
            </div>
            <div class="mt-2 text-sm text-gray-500">
              Repartos hoy: {{ vehiculo.repartos?.length || 0 }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import MetricCard from '@/components/dashboard/MetricCard.vue'

interface Reparto {
  id: number
  pedido_id: number
  vehiculo_id: number | null
  fecha_programada: string
  hora_salida: string | null
  hora_entrega: string | null
  estado: string
  pedido: {
    id: number
    cliente: {
      nombre_comercial: string
    }
  }
  vehiculo: {
    id: number
    patente: string
    marca: string
    modelo: string
  } | null
}

interface VehiculoActivo {
  id: number
  patente: string
  marca: string
  modelo: string
  repartos?: Reparto[]
}

interface Estadisticas {
  total_programados: number
  en_ruta: number
  entregados: number
  fallidos: number
  pendientes: number
}

const repartos = ref<Reparto[]>([])
const vehiculosActivos = ref<VehiculoActivo[]>([])
const estadisticas = ref<Estadisticas>({
  total_programados: 0,
  en_ruta: 0,
  entregados: 0,
  fallidos: 0,
  pendientes: 0
})

const cargando = ref(true)
const autoRefresh = ref(true)
const filtroEstado = ref('')
const filtroVehiculo = ref('')
const ultimaActualizacion = ref('')

let intervalId: NodeJS.Timeout | null = null

const repartosFiltrados = computed(() => {
  let resultado = [...repartos.value]

  if (filtroEstado.value) {
    resultado = resultado.filter(r => r.estado === filtroEstado.value)
  }

  if (filtroVehiculo.value) {
    resultado = resultado.filter(r => r.vehiculo_id === parseInt(filtroVehiculo.value))
  }

  return resultado.sort((a, b) => {
    const prioridad = { 'en_ruta': 0, 'programado': 1, 'fallido': 2, 'entregado': 3 }
    return (prioridad[a.estado as keyof typeof prioridad] || 4) - (prioridad[b.estado as keyof typeof prioridad] || 4)
  })
})

const actualizarDatos = async () => {
  try {
    cargando.value = true
    
    const response = await fetch('/api/v1/seguimiento-tiempo-real', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    })

    const data = await response.json()
    
    repartos.value = data.repartos
    vehiculosActivos.value = data.vehiculos_activos
    estadisticas.value = data.estadisticas
    ultimaActualizacion.value = new Date().toLocaleTimeString()

  } catch (error) {
    console.error('Error al cargar seguimiento:', error)
  } finally {
    cargando.value = false
  }
}

const toggleAutoRefresh = () => {
  autoRefresh.value = !autoRefresh.value
  
  if (autoRefresh.value) {
    intervalId = setInterval(actualizarDatos, 30000) // 30 segundos
  } else if (intervalId) {
    clearInterval(intervalId)
    intervalId = null
  }
}

const iniciarReparto = async (reparto: Reparto) => {
  try {
    const response = await fetch(`/api/v1/repartos/${reparto.id}/estado`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        estado: 'en_ruta',
        hora_salida: new Date().toTimeString().slice(0, 5)
      })
    })

    if (response.ok) {
      actualizarDatos()
    } else {
      const data = await response.json()
      alert(data.message || 'Error al iniciar reparto')
    }
  } catch (error) {
    console.error('Error al iniciar reparto:', error)
  }
}

const completarReparto = async (reparto: Reparto) => {
  const kmRecorridos = prompt('Ingrese los kilómetros recorridos (opcional):')
  
  try {
    const response = await fetch(`/api/v1/repartos/${reparto.id}/estado`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        estado: 'entregado',
        hora_entrega: new Date().toTimeString().slice(0, 5),
        ...(kmRecorridos && { km_recorridos: parseFloat(kmRecorridos) })
      })
    })

    if (response.ok) {
      actualizarDatos()
      alert('Entrega completada exitosamente')
    } else {
      const data = await response.json()
      alert(data.message || 'Error al completar entrega')
    }
  } catch (error) {
    console.error('Error al completar reparto:', error)
  }
}

const marcarFallido = async (reparto: Reparto) => {
  const motivo = prompt('Motivo del fallo (opcional):')
  
  try {
    const response = await fetch(`/api/v1/repartos/${reparto.id}/estado`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        estado: 'fallido',
        hora_entrega: new Date().toTimeString().slice(0, 5),
        observaciones: motivo || ''
      })
    })

    if (response.ok) {
      actualizarDatos()
      alert('Reparto marcado como fallido')
    } else {
      const data = await response.json()
      alert(data.message || 'Error al marcar como fallido')
    }
  } catch (error) {
    console.error('Error al marcar reparto como fallido:', error)
  }
}

const verDetalle = (reparto: Reparto) => {
  // Implementar navegación al detalle
  console.log('Ver detalle:', reparto)
}

const getEstadoBadgeClass = (estado: string): string => {
  const clases = {
    'programado': 'px-2 py-1 text-xs font-medium rounded-full text-blue-600 bg-blue-100',
    'en_ruta': 'px-2 py-1 text-xs font-medium rounded-full text-orange-600 bg-orange-100',
    'entregado': 'px-2 py-1 text-xs font-medium rounded-full text-green-600 bg-green-100',
    'fallido': 'px-2 py-1 text-xs font-medium rounded-full text-red-600 bg-red-100'
  }
  return clases[estado as keyof typeof clases] || clases.programado
}

const getEstadoTexto = (estado: string): string => {
  const textos = {
    'programado': 'Programado',
    'en_ruta': 'En Ruta',
    'entregado': 'Entregado',
    'fallido': 'Fallido'
  }
  return textos[estado as keyof typeof textos] || 'Desconocido'
}

const getProgresoWidth = (estado: string): number => {
  const progreso = {
    'programado': 25,
    'en_ruta': 75,
    'entregado': 100,
    'fallido': 50
  }
  return progreso[estado as keyof typeof progreso] || 0
}

const getProgresoColor = (estado: string): string => {
  const colores = {
    'programado': 'bg-blue-500',
    'en_ruta': 'bg-orange-500',
    'entregado': 'bg-green-500',
    'fallido': 'bg-red-500'
  }
  return colores[estado as keyof typeof colores] || 'bg-gray-500'
}

const getVehiculoEstadoClass = (repartos: Reparto[] = []): string => {
  if (repartos.some(r => r.estado === 'en_ruta')) {
    return 'px-2 py-1 text-xs font-medium rounded-full text-orange-600 bg-orange-100'
  }
  if (repartos.some(r => r.estado === 'programado')) {
    return 'px-2 py-1 text-xs font-medium rounded-full text-blue-600 bg-blue-100'
  }
  return 'px-2 py-1 text-xs font-medium rounded-full text-green-600 bg-green-100'
}

const getVehiculoEstadoTexto = (repartos: Reparto[] = []): string => {
  if (repartos.some(r => r.estado === 'en_ruta')) return 'En Ruta'
  if (repartos.some(r => r.estado === 'programado')) return 'Programado'
  return 'Disponible'
}

onMounted(() => {
  actualizarDatos()
  if (autoRefresh.value) {
    intervalId = setInterval(actualizarDatos, 30000)
  }
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>