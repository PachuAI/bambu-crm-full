<template>
  <!-- =======================================
       BAMBU MetricCard - Tarjeta de Métricas Responsive
       
       Versión: 1.0.0
       Fecha: 2025-08-08
       
       📊 Designed for chemical industry by ÍTERA
       https://iteraestudio.com
       
       RESPONSIVE:
       - Mobile (0-767px): 1 columna, padding compacto
       - Tablet (768-1023px): 2 columnas grid
       - Desktop (1024px+): 4 columnas grid
       ======================================= -->

  <div 
    :class="[
      'metric-card',
      `metric-card--${variant}`,
      { 
        'metric-card--loading': loading,
        'metric-card--clickable': clickable,
        'metric-card--error': hasError 
      }
    ]"
    :role="clickable ? 'button' : null"
    :tabindex="clickable ? '0' : null"
    :aria-label="clickable ? `Ver detalles de ${title}` : null"
    @click="handleClick"
    @keydown.enter="handleClick"
    @keydown.space.prevent="handleClick"
  >
    <!-- Loading State -->
    <div v-if="loading" class="metric-card__loading">
      <div class="loading-spinner" aria-label="Cargando datos"></div>
      <div class="loading-content">
        <div class="loading-title"></div>
        <div class="loading-value"></div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="hasError" class="metric-card__error">
      <div class="error-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path 
            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" 
            stroke="currentColor" 
            stroke-width="1.5" 
            stroke-linecap="round" 
            stroke-linejoin="round"
          />
        </svg>
      </div>
      <div class="error-content">
        <p class="error-title">Error al cargar</p>
        <p class="error-message">{{ errorMessage || 'No se pudieron cargar los datos' }}</p>
      </div>
    </div>

    <!-- Content State -->
    <div v-else class="metric-card__content">
      <!-- Header -->
      <div class="metric-card__header">
        <div class="metric-icon" :class="`metric-icon--${iconColor}`">
          <!-- Íconos por tipo de métrica -->
          <svg v-if="icon === 'ventas'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2l3.09 6.26L22 9l-5 4.74L18.18 21L12 17.27L5.82 21L7 13.74L2 9l6.91-.74L12 2z" fill="currentColor"/>
          </svg>
          
          <svg v-else-if="icon === 'pedidos'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" fill="currentColor"/>
          </svg>

          <svg v-else-if="icon === 'stock'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>

          <svg v-else-if="icon === 'clientes'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M13 7a4 4 0 11-8 0 4 4 0 018 0zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>

          <svg v-else-if="icon === 'productos'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>

          <svg v-else-if="icon === 'alertas'" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>

          <!-- Default icon -->
          <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <div class="metric-title">
          <h3 class="title-text">{{ title }}</h3>
          <p v-if="subtitle" class="subtitle-text">{{ subtitle }}</p>
        </div>
      </div>

      <!-- Main Value -->
      <div class="metric-card__value">
        <span class="value-number">{{ formattedValue }}</span>
        <span v-if="unit" class="value-unit">{{ unit }}</span>
      </div>

      <!-- Trend/Change -->
      <div v-if="change !== null" class="metric-card__change">
        <div 
          :class="[
            'change-indicator',
            {
              'change-indicator--positive': change > 0,
              'change-indicator--negative': change < 0,
              'change-indicator--neutral': change === 0
            }
          ]"
        >
          <!-- Trend Arrow -->
          <svg v-if="change > 0" class="trend-icon trend-icon--up" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="m4.5 10.5 3-3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          
          <svg v-else-if="change < 0" class="trend-icon trend-icon--down" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="m10.5 6.5-3 3-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          
          <svg v-else class="trend-icon trend-icon--neutral" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M4 8h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>

          <span class="change-value">{{ formattedChange }}</span>
        </div>
        
        <p v-if="changeLabel" class="change-label">{{ changeLabel }}</p>
      </div>

      <!-- Footer -->
      <div v-if="description || actionLabel" class="metric-card__footer">
        <p v-if="description" class="metric-description">{{ description }}</p>
        
        <div v-if="actionLabel && clickable" class="metric-action">
          <span class="action-text">{{ actionLabel }}</span>
          <svg class="action-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="m6 4 4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>

      <!-- Status Badge (Solo para métricas específicas) -->
      <div v-if="statusBadge" class="metric-card__status">
        <div 
          :class="[
            'status-badge',
            `status-badge--${statusBadge.type}`
          ]"
        >
          <span class="status-icon">{{ statusBadge.icon }}</span>
          <span class="status-text">{{ statusBadge.text }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// ========================================
// PROPS
// ========================================

const props = defineProps({
  // Content
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: null
  },
  value: {
    type: [Number, String],
    required: true
  },
  unit: {
    type: String,
    default: null
  },
  description: {
    type: String,
    default: null
  },
  
  // Appearance
  icon: {
    type: String,
    default: 'default',
    validator: (value) => ['ventas', 'pedidos', 'stock', 'clientes', 'productos', 'alertas', 'default'].includes(value)
  },
  iconColor: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'error', 'info'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'highlighted', 'minimal'].includes(value)
  },
  
  // Data
  change: {
    type: Number,
    default: null
  },
  changeLabel: {
    type: String,
    default: null
  },
  statusBadge: {
    type: Object,
    default: null
    // { type: 'success|warning|error', icon: '✅', text: 'En Stock' }
  },
  
  // State
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  },
  
  // Interaction
  clickable: {
    type: Boolean,
    default: false
  },
  actionLabel: {
    type: String,
    default: null
  }
})

// ========================================
// EMITS
// ========================================

const emit = defineEmits(['click'])

// ========================================
// COMPUTED
// ========================================

const hasError = computed(() => props.error !== null)

const formattedValue = computed(() => {
  if (typeof props.value === 'string') return props.value
  
  // Format numbers with appropriate separators
  if (typeof props.value === 'number') {
    // Para números grandes, usar formato con separadores
    if (props.value >= 1000) {
      return new Intl.NumberFormat('es-ES').format(props.value)
    }
    return props.value.toString()
  }
  
  return props.value
})

const formattedChange = computed(() => {
  if (props.change === null) return ''
  
  const absChange = Math.abs(props.change)
  const sign = props.change > 0 ? '+' : ''
  
  // Si es porcentaje (números pequeños como decimales)
  if (absChange < 1 && absChange > 0) {
    return `${sign}${(props.change * 100).toFixed(1)}%`
  }
  
  // Si es número entero
  return `${sign}${props.change.toFixed(1)}%`
})

// ========================================
// METHODS
// ========================================

const handleClick = () => {
  if (props.clickable && !props.loading && !hasError.value) {
    emit('click')
  }
}
</script>

<style scoped>
/* =======================================
   METRIC CARD BASE STYLES
   ======================================= */

.metric-card {
  /* Extend base card styles from components.css */
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-base);
  padding: var(--space-md);
  transition: var(--transition-base);
  box-shadow: var(--shadow-sm);
  position: relative;
  min-height: 120px;
  display: flex;
  flex-direction: column;
}

.metric-card--clickable {
  cursor: pointer;
}

.metric-card--clickable:hover {
  border-color: var(--border-hover);
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

.metric-card--clickable:focus-visible {
  outline: 2px solid var(--primary);
  outline-offset: 2px;
}

.metric-card--loading {
  pointer-events: none;
}

.metric-card--error {
  border-color: var(--error);
  background: var(--error-bg);
}

/* Variants */
.metric-card--highlighted {
  border-color: var(--primary);
  background: var(--primary-bg);
}

.metric-card--minimal {
  border: none;
  box-shadow: none;
  background: transparent;
}

/* =======================================
   LOADING STATE
   ======================================= */

.metric-card__loading {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  flex: 1;
}

.loading-spinner {
  width: 24px;
  height: 24px;
  border: 2px solid var(--border);
  border-top: 2px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.loading-title,
.loading-value {
  height: 16px;
  background: var(--bg-elevated);
  border-radius: var(--radius-sm);
  animation: pulse 1.5s ease-in-out infinite;
}

.loading-title {
  width: 70%;
}

.loading-value {
  width: 50%;
  height: 24px;
}

@keyframes pulse {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 0.8; }
}

/* =======================================
   ERROR STATE
   ======================================= */

.metric-card__error {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  flex: 1;
}

.error-icon {
  color: var(--error);
  flex-shrink: 0;
}

.error-content {
  flex: 1;
}

.error-title {
  font-weight: 600;
  color: var(--error);
  margin: 0 0 var(--space-xs) 0;
  font-size: var(--font-sm);
}

.error-message {
  color: var(--text-secondary);
  margin: 0;
  font-size: var(--font-xs);
  line-height: 1.4;
}

/* =======================================
   CONTENT LAYOUT
   ======================================= */

.metric-card__content {
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: var(--space-sm);
}

/* Header */
.metric-card__header {
  display: flex;
  align-items: flex-start;
  gap: var(--space-sm);
}

.metric-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-base);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.metric-icon--primary {
  background: var(--primary-bg);
  color: var(--primary);
}

.metric-icon--success {
  background: var(--success-bg);
  color: var(--success);
}

.metric-icon--warning {
  background: var(--warning-bg);
  color: var(--warning);
}

.metric-icon--error {
  background: var(--error-bg);
  color: var(--error);
}

.metric-icon--info {
  background: var(--info-bg);
  color: var(--info);
}

.metric-title {
  flex: 1;
  min-width: 0;
}

.title-text {
  font-size: var(--font-sm);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.3;
}

.subtitle-text {
  font-size: var(--font-xs);
  color: var(--text-secondary);
  margin: var(--space-xs) 0 0 0;
  line-height: 1.3;
}

/* Value */
.metric-card__value {
  display: flex;
  align-items: baseline;
  gap: var(--space-xs);
  margin: var(--space-sm) 0;
}

.value-number {
  font-size: var(--font-2xl);
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.value-unit {
  font-size: var(--font-sm);
  font-weight: 500;
  color: var(--text-secondary);
}

/* Change/Trend */
.metric-card__change {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-xs);
}

.change-indicator {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-sm);
  font-size: var(--font-xs);
  font-weight: 500;
}

.change-indicator--positive {
  background: var(--success-bg);
  color: var(--success);
}

.change-indicator--negative {
  background: var(--error-bg);
  color: var(--error);
}

.change-indicator--neutral {
  background: var(--bg-elevated);
  color: var(--text-secondary);
}

.trend-icon {
  flex-shrink: 0;
}

.change-value {
  font-weight: 600;
  white-space: nowrap;
}

.change-label {
  font-size: var(--font-xs);
  color: var(--text-muted);
  margin: 0;
  text-align: right;
}

/* Footer */
.metric-card__footer {
  margin-top: auto;
  padding-top: var(--space-sm);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-sm);
}

.metric-description {
  font-size: var(--font-xs);
  color: var(--text-secondary);
  margin: 0;
  flex: 1;
  line-height: 1.3;
}

.metric-action {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  color: var(--primary);
  font-size: var(--font-xs);
  font-weight: 500;
  flex-shrink: 0;
}

.action-icon {
  transition: var(--transition-fast);
}

.metric-card--clickable:hover .action-icon {
  transform: translateX(2px);
}

/* Status Badge */
.metric-card__status {
  position: absolute;
  top: var(--space-sm);
  right: var(--space-sm);
}

.status-badge {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-base);
  font-size: var(--font-xs);
  font-weight: 500;
  border: 1px solid;
}

.status-badge--success {
  background: var(--success-bg);
  color: var(--success);
  border-color: var(--success);
}

.status-badge--warning {
  background: var(--warning-bg);
  color: var(--warning);
  border-color: var(--warning);
}

.status-badge--error {
  background: var(--error-bg);
  color: var(--error);
  border-color: var(--error);
}

/* =======================================
   RESPONSIVE OVERRIDES
   ======================================= */

/* Mobile optimizations */
@media (max-width: 767px) {
  .metric-card {
    padding: var(--space-md);
    min-height: 100px;
  }
  
  .value-number {
    font-size: var(--font-xl);
  }
  
  .metric-card__footer {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-xs);
  }
  
  .change-label {
    text-align: left;
  }
}

/* Tablet optimizations */
@media (min-width: 768px) {
  .metric-card {
    padding: var(--space-lg);
    min-height: 120px;
  }
  
  .value-number {
    font-size: var(--font-2xl);
  }
}

/* Desktop optimizations */
@media (min-width: 1024px) {
  .metric-card {
    padding: var(--space-xl);
    min-height: 140px;
  }
  
  .value-number {
    font-size: var(--font-3xl);
  }
  
  /* Hover effects only on desktop */
  .metric-card:hover {
    transform: translateY(-2px);
  }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
  .metric-card {
    min-height: 120px;
    padding: var(--space-md) var(--space-lg);
  }
  
  .metric-card:hover {
    transform: none;
    box-shadow: var(--shadow-sm);
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .metric-card {
    border-width: 2px;
  }
  
  .change-indicator {
    border: 1px solid currentColor;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .metric-card,
  .action-icon,
  .loading-spinner {
    transition: none;
    animation: none;
  }
  
  .metric-card:hover {
    transform: none;
  }
}
</style>