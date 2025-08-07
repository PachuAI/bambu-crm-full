<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Encabezado -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-white">Gestión de Vehículos</h1>
          <p class="text-slate-400 mt-1">Administra la flota de vehículos para entregas</p>
        </div>
        <router-link
          to="/vehiculos/crear"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
        >
          + Agregar Vehículo
        </router-link>
      </div>

      <!-- Filtros y estadísticas rápidas -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <MetricCard
          titulo="Total Vehículos"
          :valor="estadisticas.total"
          icono="🚛"
        />
        <MetricCard
          titulo="Activos"
          :valor="estadisticas.activos"
          icono="✅"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="En Ruta"
          :valor="estadisticas.en_ruta"
          icono="🛣️"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
        <MetricCard
          titulo="Disponibles"
          :valor="estadisticas.disponibles"
          icono="🅿️"
          :tendencia="{ valor: 0, tipo: 'neutro' }"
        />
      </div>

      <!-- Filtros -->
      <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <div class="flex flex-wrap gap-4">
          <select v-model="filtros.estado" class="bg-slate-900 border border-slate-600 text-white rounded px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="activo">Solo activos</option>
            <option value="inactivo">Solo inactivos</option>
          </select>
          <input
            v-model="filtros.busqueda"
            type="text"
            placeholder="Buscar por patente o marca..."
            class="bg-slate-900 border border-slate-600 text-white placeholder-slate-400 rounded px-3 py-2 flex-1 min-w-0"
          />
          <button
            @click="limpiarFiltros"
            class="text-slate-400 hover:text-white transition-colors"
          >
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Tabla de vehículos -->
      <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Vehículo
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Capacidad
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Estado Hoy
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Repartos Hoy
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-slate-800 divide-y divide-slate-700">
              <tr v-for="vehiculo in vehiculosFiltrados" :key="vehiculo.id" class="hover:bg-slate-900/30 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-white">
                      {{ vehiculo.patente }}
                    </div>
                    <div class="text-xs text-slate-400">
                      {{ vehiculo.marca }} {{ vehiculo.modelo }} ({{ vehiculo.anio }})
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-white">{{ vehiculo.capacidad_kg }}kg</div>
                  <div class="text-xs text-slate-400">{{ vehiculo.capacidad_bultos }} bultos</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getEstadoBadgeClass(vehiculo.estado_hoy)">
                    {{ getEstadoTexto(vehiculo.estado_hoy) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                  {{ vehiculo.repartos_hoy }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="vehiculo.activo ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30'" 
                        class="px-3 py-1.5 text-xs font-semibold rounded-full border">
                    {{ vehiculo.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-3">
                    <router-link
                      :to="`/vehiculos/${vehiculo.id}`"
                      class="text-blue-400 hover:text-white transition-colors"
                    >
                      Ver
                    </router-link>
                    <router-link
                      :to="`/vehiculos/${vehiculo.id}/editar`"
                      class="text-slate-400 hover:text-white transition-colors"
                    >
                      Editar
                    </router-link>
                    <button
                      v-if="vehiculo.activo"
                      @click="desactivarVehiculo(vehiculo)"
                      class="text-red-400 hover:text-white transition-colors"
                    >
                      Desactivar
                    </button>
                    <button
                      v-else
                      @click="activarVehiculo(vehiculo)"
                      class="text-green-400 hover:text-white transition-colors"
                    >
                      Activar
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="vehiculosFiltrados.length === 0" class="text-center py-12">
          <div class="text-slate-400">No se encontraron vehículos</div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import MetricCard from '@/components/dashboard/MetricCard.vue'

interface Vehiculo {
  id: number
  patente: string
  marca: string
  modelo: string
  anio: number
  capacidad_kg: number
  capacidad_bultos: number
  activo: boolean
  observaciones: string
  nombre_completo: string
  estado_hoy: string
  repartos_hoy: number
  created_at: string
  updated_at: string
}

interface Estadisticas {
  total: number
  activos: number
  en_ruta: number
  disponibles: number
}

const vehiculos = ref<Vehiculo[]>([])
const cargando = ref(true)

const filtros = ref({
  estado: '',
  busqueda: ''
})

const estadisticas = computed((): Estadisticas => ({
  total: vehiculos.value.length,
  activos: vehiculos.value.filter(v => v.activo).length,
  en_ruta: vehiculos.value.filter(v => v.estado_hoy === 'en_ruta').length,
  disponibles: vehiculos.value.filter(v => v.estado_hoy === 'disponible').length
}))

const vehiculosFiltrados = computed(() => {
  let resultado = [...vehiculos.value]

  if (filtros.value.estado === 'activo') {
    resultado = resultado.filter(v => v.activo)
  } else if (filtros.value.estado === 'inactivo') {
    resultado = resultado.filter(v => !v.activo)
  }

  if (filtros.value.busqueda) {
    const busqueda = filtros.value.busqueda.toLowerCase()
    resultado = resultado.filter(v => 
      v.patente.toLowerCase().includes(busqueda) ||
      v.marca.toLowerCase().includes(busqueda) ||
      v.modelo.toLowerCase().includes(busqueda)
    )
  }

  return resultado
})

const cargarVehiculos = async () => {
  try {
    cargando.value = true
    const response = await fetch('/api/v1/vehiculos', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    })
    const data = await response.json()
    vehiculos.value = data.vehiculos
  } catch (error) {
    console.error('Error al cargar vehículos:', error)
  } finally {
    cargando.value = false
  }
}

const limpiarFiltros = () => {
  filtros.value = {
    estado: '',
    busqueda: ''
  }
}

const activarVehiculo = async (vehiculo: Vehiculo) => {
  try {
    const response = await fetch(`/api/v1/vehiculos/${vehiculo.id}/activar`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      vehiculo.activo = true
      alert('Vehículo activado exitosamente')
    }
  } catch (error) {
    console.error('Error al activar vehículo:', error)
  }
}

const desactivarVehiculo = async (vehiculo: Vehiculo) => {
  if (!confirm('¿Estás seguro de desactivar este vehículo?')) return
  
  try {
    const response = await fetch(`/api/v1/vehiculos/${vehiculo.id}/desactivar`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    })
    
    const data = await response.json()
    
    if (response.ok) {
      vehiculo.activo = false
      alert('Vehículo desactivado exitosamente')
    } else {
      alert(data.message || 'Error al desactivar vehículo')
    }
  } catch (error) {
    console.error('Error al desactivar vehículo:', error)
  }
}

const getEstadoBadgeClass = (estado: string): string => {
  const clases = {
    'disponible': 'px-3 py-1.5 text-xs font-semibold rounded-full border bg-green-500/20 text-green-400 border-green-500/30',
    'programado': 'px-3 py-1.5 text-xs font-semibold rounded-full border bg-blue-500/20 text-blue-400 border-blue-500/30',
    'en_ruta': 'px-3 py-1.5 text-xs font-semibold rounded-full border bg-orange-500/20 text-orange-400 border-orange-500/30',
    'libre': 'px-3 py-1.5 text-xs font-semibold rounded-full border bg-slate-500/20 text-slate-400 border-slate-500/30',
    'inactivo': 'px-3 py-1.5 text-xs font-semibold rounded-full border bg-red-500/20 text-red-400 border-red-500/30'
  }
  return clases[estado as keyof typeof clases] || clases.disponible
}

const getEstadoTexto = (estado: string): string => {
  const textos = {
    'disponible': 'Disponible',
    'programado': 'Programado',
    'en_ruta': 'En Ruta',
    'libre': 'Libre',
    'inactivo': 'Inactivo'
  }
  return textos[estado as keyof typeof textos] || 'Desconocido'
}

onMounted(() => {
  cargarVehiculos()
})
</script>