<template>
  <div 
    class="rounded-lg bg-slate-800 border border-slate-700 p-6 relative transition-all duration-200 hover:border-slate-600 hover:shadow-lg hover:shadow-slate-900/20 min-h-[140px]"
  >
    <div class="flex justify-between items-start mb-4">
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
    
    <div class="text-2xl font-bold mb-3 text-white font-mono">
      <template v-if="!loading">
        {{ value }}
      </template>
      <template v-else>
        <div class="h-8 w-24 rounded animate-pulse bg-slate-700"></div>
      </template>
    </div>
    
    <div class="flex items-end justify-between mt-4">
      <component :is="icon" class="w-5 h-5 text-slate-500" />
      <div class="h-8 flex-1 ml-4">
        <!-- Mini sparkline chart -->
        <svg class="w-full h-full" viewBox="0 0 100 32">
          <defs>
            <linearGradient id="sparklineGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" class="text-indigo-500" stop-opacity="0.4" />
              <stop offset="100%" class="text-indigo-500" stop-opacity="0" />
            </linearGradient>
          </defs>
          <path
            d="M 0,24 L 20,20 L 40,22 L 60,16 L 80,12 L 100,14"
            stroke="rgb(99, 102, 241)"
            stroke-width="2"
            fill="none"
          />
          <path
            d="M 0,24 L 20,20 L 40,22 L 60,16 L 80,12 L 100,14 L 100,32 L 0,32 Z"
            fill="url(#sparklineGradient)"
          />
        </svg>
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