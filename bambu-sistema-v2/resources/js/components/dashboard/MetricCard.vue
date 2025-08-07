<template>
  <div 
    class="rounded-lg bg-slate-800 border border-slate-700 p-5 relative transition-all duration-200 hover:border-slate-600 hover:shadow-lg hover:shadow-slate-900/20 min-h-[120px]"
  >
    <div class="flex justify-between items-start mb-3">
      <h3 class="text-sm font-medium text-slate-400">{{ title }}</h3>
      <span 
        v-if="trendValue"
        :class="[
          'text-xs font-semibold px-2 py-1 rounded inline-flex items-center gap-1',
          trend === 'up' ? 
            'bg-green-900/20 text-green-400 border border-green-800/30' : 
            'bg-red-900/20 text-red-400 border border-red-800/30'
        ]"
      >
        <span v-if="trend === 'up'">↗</span>
        <span v-else>↘</span>
        {{ trendValue }}
      </span>
    </div>
    
    <div class="text-xl font-bold mb-2 text-white font-mono">
      <template v-if="!loading">
        {{ value }}
      </template>
      <template v-else>
        <div class="h-6 w-20 rounded animate-pulse bg-slate-700"></div>
      </template>
    </div>
    
    <div class="flex items-center justify-between mt-3">
      <component :is="icon" class="w-4 h-4 text-slate-500" />
      <!-- Trend indicator simple -->
      <div v-if="trend" class="text-xs text-slate-500">
        {{ trend === 'up' ? '📈' : '📉' }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { type Component } from 'vue'

defineProps<{
  title: string
  value: string
  trend?: 'up' | 'down'
  trendValue?: string
  icon: Component
  loading?: boolean
}>()
</script>