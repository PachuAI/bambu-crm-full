<template>
  <div 
    class="bg-slate-800 rounded-lg border border-slate-700 p-3 sm:p-4 md:p-6 hover:border-slate-600 transition-colors duration-200 min-h-[100px] sm:min-h-[120px] flex flex-col justify-between w-full"
  >
    <!-- Header con jerarquía tipográfica correcta -->
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
        <div class="p-1.5 sm:p-2 rounded-md flex-shrink-0" :class="iconBgClass">
          <component :is="icon" class="w-3 h-3 sm:w-4 sm:h-4" :class="iconColorClass" />
        </div>
        <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wider truncate">{{ title }}</h3>
      </div>
      
      <!-- Badge de tendencia con tipografía consistente -->
      <span 
        v-if="trendValue"
        class="text-xs font-semibold px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-md flex items-center gap-0.5 sm:gap-1 whitespace-nowrap flex-shrink-0"
        :class="trendBadgeClass"
      >
        <svg v-if="trend === 'up'" class="w-3 h-3 fill-current" viewBox="0 0 12 12">
          <path d="M6 3l3 3h-2v3H5V6H3l3-3z"/>
        </svg>
        <svg v-else class="w-3 h-3 fill-current" viewBox="0 0 12 12">
          <path d="M6 9L3 6h2V3h2v3h2L6 9z"/>
        </svg>
        {{ trendValue }}
      </span>
    </div>
    
    <!-- Número principal con jerarquía clara - Mobile responsive -->
    <div class="mt-1 sm:mt-2">
      <template v-if="!loading">
        <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white leading-none tracking-tight">
          {{ value }}
        </div>
      </template>
      <template v-else>
        <div class="h-6 sm:h-7 md:h-9 w-16 sm:w-20 md:w-28 rounded bg-slate-700 animate-pulse"></div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { type Component, computed } from 'vue'

const props = defineProps<{
  title: string
  value: string
  trend?: 'up' | 'down'
  trendValue?: string
  icon: Component
  loading?: boolean
}>()

const iconBgClass = computed(() => {
  switch (props.title) {
    case 'Ventas Totales':
      return 'bg-green-500/20'
    case 'Pedidos':
      return 'bg-blue-500/20'
    case 'Clientes':
      return 'bg-purple-500/20'
    case 'Entregas Hoy':
      return 'bg-orange-500/20'
    case 'Pendientes Entrega':
      return 'bg-amber-500/20'
    default:
      return 'bg-slate-500/20'
  }
})

const iconColorClass = computed(() => {
  switch (props.title) {
    case 'Ventas Totales':
      return 'text-green-400'
    case 'Pedidos':
      return 'text-blue-400'
    case 'Clientes':
      return 'text-purple-400'
    case 'Entregas Hoy':
      return 'text-orange-400'
    case 'Pendientes Entrega':
      return 'text-amber-400'
    default:
      return 'text-slate-400'
  }
})

const trendBadgeClass = computed(() => {
  if (props.trend === 'up') {
    return 'bg-green-500/20 text-green-400'
  } else {
    return 'bg-red-500/20 text-red-400'
  }
})
</script>