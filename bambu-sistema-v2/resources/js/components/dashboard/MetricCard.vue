<template>
  <div 
    class="rounded-lg p-6 relative transition-all duration-150"
    style="background-color: var(--bg-secondary); 
           border: 1px solid var(--surface-1)"
    @mouseover="$event.target.style.borderColor = 'var(--surface-2)'; $event.target.style.backgroundColor = 'var(--bg-tertiary)'"
    @mouseleave="$event.target.style.borderColor = 'var(--surface-1)'; $event.target.style.backgroundColor = 'var(--bg-secondary)'"
  >
    <div class="flex justify-between items-start mb-4">
      <h3 class="text-sm font-medium" style="color: var(--text-secondary)">{{ title }}</h3>
      <span 
        v-if="trendValue"
        class="text-xs font-semibold px-2 py-1 rounded-full inline-flex items-center gap-1"
        :style="trend === 'up' ? 
          'background-color: rgba(34, 197, 94, 0.15); color: var(--success-light)' : 
          'background-color: rgba(239, 68, 68, 0.15); color: var(--danger-light)'"
      >
        <span v-if="trend === 'up'">+</span>
        <span v-else>-</span>
        {{ trendValue.replace(/^[+-]/, '') }}
      </span>
    </div>
    
    <div 
      class="text-3xl font-bold mb-1"
      style="color: var(--text-primary); font-variant-numeric: tabular-nums; line-height: 1.1"
    >
      <template v-if="!loading">
        {{ value }}
      </template>
      <template v-else>
        <div class="h-8 w-24 rounded animate-pulse" style="background-color: var(--surface-1)"></div>
      </template>
    </div>
    
    <div class="flex items-end justify-between mt-4">
      <component :is="icon" class="w-5 h-5" style="color: var(--text-muted)" />
      <div class="h-10 flex-1 ml-4">
        <!-- Mini chart integration - Sparkline style -->
        <svg class="w-full h-full" viewBox="0 0 100 40">
          <defs>
            <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" :style="`stop-color: var(--primary); stop-opacity: 0.3`" />
              <stop offset="100%" :style="`stop-color: var(--primary); stop-opacity: 0`" />
            </linearGradient>
          </defs>
          <path
            d="M 0,30 L 20,25 L 40,28 L 60,20 L 80,15 L 100,18"
            stroke="var(--primary)"
            stroke-width="2"
            fill="none"
          />
          <path
            d="M 0,30 L 20,25 L 40,28 L 60,20 L 80,15 L 100,18 L 100,40 L 0,40 Z"
            fill="url(#areaGradient)"
          />
        </svg>
      </div>
    </div>
    
    <!-- Status indicator -->
    <div 
      class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full opacity-40"
      style="background-color: var(--primary)"
    ></div>
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