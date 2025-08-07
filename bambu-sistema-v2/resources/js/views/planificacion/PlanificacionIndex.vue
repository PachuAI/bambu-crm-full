<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Encabezado -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Planificación de Entregas</h1>
          <p class="text-gray-600 mt-1">Asigna pedidos a vehículos y programa entregas</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="programarEntrega"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
          >
            + Programar Entrega
          </button>
        </div>
      </div>

      <!-- Controles de navegación semanal -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center">
          <button
            @click="semanaAnterior"
            class="flex items-center text-gray-600 hover:text-gray-900"
          >
            ← Semana Anterior
          </button>
          
          <div class="text-center">
            <h2 class="text-lg font-semibold">{{ fechaSemanaTexto }}</h2>
            <p class="text-sm text-gray-600">
              {{ fechaInicio.toLocaleDateString() }} - {{ fechaFin.toLocaleDateString() }}
            </p>
          </div>
          
          <button
            @click="semanaSiguiente"
            class="flex items-center text-gray-600 hover:text-gray-900"
          >
            Siguiente Semana →
          </button>
        </div>

        <div class="mt-4 flex justify-center">
          <button
            @click="irHoy"
            class="text-blue-600 hover:text-blue-800 text-sm"
          >
            Ir a esta semana
          </button>
        </div>
      </div>

      <!-- Grid semanal de planificación -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-7 border-b border-gray-200">
          <div
            v-for="dia in diasSemana"
            :key="dia.fecha"
            class="p-4 border-r border-gray-200 last:border-r-0"
          >
            <div class="text-center">
              <div class="font-medium text-gray-900">{{ dia.dia_semana }}</div>
              <div class="text-sm text-gray-600">{{ formatearFecha(dia.fecha) }}</div>
              <div class="text-xs text-blue-600 mt-1">
                {{ dia.repartos.length }} entregas
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-7 min-h-96">
          <div
            v-for="dia in diasSemana"
            :key="dia.fecha"
            class="border-r border-gray-200 last:border-r-0 p-2"
          >
            <!-- Repartos del día -->
            <div class="space-y-2">
              <div
                v-for="reparto in dia.repartos"
                :key="reparto.id"
                :class="getColorReparto(reparto.estado)"
                class="p-2 rounded text-xs cursor-pointer"
                @click="verDetalleReparto(reparto)"
              >
                <div class="font-medium">
                  {{ reparto.pedido.cliente.nombre_comercial }}
                </div>
                <div class="text-xs opacity-75">
                  {{ reparto.vehiculo?.patente || 'Sin vehículo' }}
                </div>
                <div class="text-xs opacity-75">
                  {{ reparto.hora_salida || 'Sin hora' }}
                </div>
              </div>

              <!-- Botón para agregar reparto -->
              <button
                @click="programarEntregaFecha(dia.fecha)"
                class="w-full p-2 border-2 border-dashed border-gray-300 rounded text-xs text-gray-600 hover:border-blue-300 hover:text-blue-600"
              >
                + Programar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección de vehículos disponibles -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Vehículos Disponibles</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="vehiculo in vehiculosDisponibles"
            :key="vehiculo.id"
            class="border rounded-lg p-4"
          >
            <div class="flex justify-between items-start">
              <div>
                <div class="font-medium">{{ vehiculo.patente }}</div>
                <div class="text-sm text-gray-600">{{ vehiculo.nombre_completo }}</div>
                <div class="text-xs text-gray-500 mt-1">
                  Capacidad: {{ vehiculo.capacidad_kg }}kg / {{ vehiculo.capacidad_bultos }} bultos
                </div>
              </div>
              <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-600">
                Disponible
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal para programar entrega -->
      <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold">Programar Entrega</h3>
              <button @click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                ✕
              </button>
            </div>

            <form @submit.prevent="guardarProgramacion">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Pedido</label>
                  <select v-model="formulario.pedido_id" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Seleccionar pedido...</option>
                    <option v-for="pedido in pedidosDisponibles" :key="pedido.id" :value="pedido.id">
                      {{ pedido.cliente.nombre_comercial }} - #{{ pedido.id }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Vehículo</label>
                  <select v-model="formulario.vehiculo_id" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">Seleccionar vehículo...</option>
                    <option v-for="vehiculo in vehiculosDisponibles" :key="vehiculo.id" :value="vehiculo.id">
                      {{ vehiculo.patente }} - {{ vehiculo.marca }} {{ vehiculo.modelo }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Fecha Programada</label>
                  <input
                    v-model="formulario.fecha_programada"
                    type="date"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Hora de Salida (opcional)</label>
                  <input
                    v-model="formulario.hora_salida"
                    type="time"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                  <textarea
                    v-model="formulario.observaciones"
                    rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  ></textarea>
                </div>
              </div>

              <div class="flex justify-end space-x-3 mt-6">
                <button
                  type="button"
                  @click="cerrarModal"
                  class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="guardando"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ guardando ? 'Guardando...' : 'Programar' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'

interface Reparto {
  id: number
  pedido_id: number
  vehiculo_id: number
  fecha_programada: string
  hora_salida: string | null
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

interface DiaPlanificacion {
  fecha: string
  dia_semana: string
  repartos: Reparto[]
}

interface Vehiculo {
  id: number
  patente: string
  marca: string
  modelo: string
  capacidad_kg: number
  capacidad_bultos: number
  nombre_completo: string
}

interface Pedido {
  id: number
  cliente: {
    nombre_comercial: string
  }
}

const diasSemana = ref<DiaPlanificacion[]>([])
const vehiculosDisponibles = ref<Vehiculo[]>([])
const pedidosDisponibles = ref<Pedido[]>([])
const fechaActual = ref(new Date())
const cargando = ref(true)
const mostrarModal = ref(false)
const guardando = ref(false)

const formulario = ref({
  pedido_id: '',
  vehiculo_id: '',
  fecha_programada: '',
  hora_salida: '',
  observaciones: ''
})

const fechaInicio = computed(() => {
  const fecha = new Date(fechaActual.value)
  fecha.setDate(fecha.getDate() - fecha.getDay())
  return fecha
})

const fechaFin = computed(() => {
  const fecha = new Date(fechaInicio.value)
  fecha.setDate(fecha.getDate() + 6)
  return fecha
})

const fechaSemanaTexto = computed(() => {
  const opciones: Intl.DateTimeFormatOptions = { 
    month: 'long', 
    year: 'numeric' 
  }
  return fechaActual.value.toLocaleDateString('es-ES', opciones)
})

const cargarPlanificacion = async () => {
  try {
    cargando.value = true
    
    const [planificacionRes, vehiculosRes, pedidosRes] = await Promise.all([
      fetch(`/api/v1/planificacion-semanal?fecha=${fechaActual.value.toISOString().split('T')[0]}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      }),
      fetch('/api/v1/vehiculos-disponibles', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      }),
      fetch('/api/v1/pedidos?estado=confirmado', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      })
    ])

    const planificacionData = await planificacionRes.json()
    const vehiculosData = await vehiculosRes.json()
    const pedidosData = await pedidosRes.json()

    diasSemana.value = Object.values(planificacionData.planificacion)
    vehiculosDisponibles.value = vehiculosData.vehiculos
    pedidosDisponibles.value = pedidosData.pedidos || []

  } catch (error) {
    console.error('Error al cargar planificación:', error)
  } finally {
    cargando.value = false
  }
}

const semanaAnterior = () => {
  fechaActual.value = new Date(fechaActual.value.setDate(fechaActual.value.getDate() - 7))
  cargarPlanificacion()
}

const semanaSiguiente = () => {
  fechaActual.value = new Date(fechaActual.value.setDate(fechaActual.value.getDate() + 7))
  cargarPlanificacion()
}

const irHoy = () => {
  fechaActual.value = new Date()
  cargarPlanificacion()
}

const formatearFecha = (fecha: string) => {
  const date = new Date(fecha + 'T00:00:00')
  return date.getDate()
}

const getColorReparto = (estado: string): string => {
  const colores = {
    'programado': 'bg-blue-100 text-blue-800 border border-blue-200',
    'en_ruta': 'bg-orange-100 text-orange-800 border border-orange-200',
    'entregado': 'bg-green-100 text-green-800 border border-green-200',
    'fallido': 'bg-red-100 text-red-800 border border-red-200'
  }
  return colores[estado as keyof typeof colores] || colores.programado
}

const programarEntrega = () => {
  formulario.value.fecha_programada = new Date().toISOString().split('T')[0]
  mostrarModal.value = true
}

const programarEntregaFecha = (fecha: string) => {
  formulario.value.fecha_programada = fecha
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
  formulario.value = {
    pedido_id: '',
    vehiculo_id: '',
    fecha_programada: '',
    hora_salida: '',
    observaciones: ''
  }
}

const guardarProgramacion = async () => {
  try {
    guardando.value = true
    
    const response = await fetch('/api/v1/repartos', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formulario.value)
    })

    if (response.ok) {
      alert('Entrega programada exitosamente')
      cerrarModal()
      cargarPlanificacion()
    } else {
      const data = await response.json()
      alert(data.message || 'Error al programar entrega')
    }
  } catch (error) {
    console.error('Error al programar entrega:', error)
    alert('Error al programar entrega')
  } finally {
    guardando.value = false
  }
}

const verDetalleReparto = (reparto: Reparto) => {
  // Implementar navegación al detalle del reparto
  console.log('Ver detalle reparto:', reparto)
}

onMounted(() => {
  cargarPlanificacion()
})
</script>