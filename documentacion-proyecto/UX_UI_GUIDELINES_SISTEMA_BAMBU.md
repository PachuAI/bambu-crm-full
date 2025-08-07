# Guidelines UX/UI - Sistema BAMBU v2.0
## Inspiración: Dashboard Trezo (Análisis de Screenshots)

---

## 🎨 ANÁLISIS VISUAL DE REFERENCIAS

### Screenshots Analizadas:
- **Modo Oscuro**: `chrome_Oj0mMUUDqf.png` - Dashboard Trezo tema dark
- **Modo Claro**: `chrome_F6YWN8Pb6M.png` - Dashboard Trezo tema light

### Elementos Identificados y Medidas Específicas:
1. **Sidebar oscura fija** (≈240px) con navegación jerárquica, íconos outline style
2. **Header superior** (≈64px) con búsqueda prominente centrada y user controls
3. **Grid de métricas** (4 columnas) con mini-charts integrados y badges de tendencia
4. **Gráfico principal** área chart con gradientes violeta-azul y tooltip dark
5. **Donut chart** con centro destacado (868 total) y leyenda color-coded
6. **Tablas compactas** con headers sticky y row hover states
7. **Color branding** violeta primario (#6366f1) con variantes para data viz
8. **Micro-interacciones**: Dropdowns con chevrons, badges redondeados, botones con estado

---

## 1. PRINCIPIOS FUNDAMENTALES

### 1.1 Filosofía de Diseño
- **Profesionalismo corporativo**: Estilo sobrio, clean, orientado a productividad
- **Densidad de información**: Máximo datos útiles, mínimo ruido visual  
- **Consistencia visual**: Patrones repetibles, elementos predecibles
- **Accesibilidad**: Contraste alto, navegación clara, responsive

### 1.2 Geometría
- **Bordes**: Radius mínimo (2-4px) solo para suavizar, nunca "soft UI"
- **Espaciado**: Sistema consistente basado en múltiplos de 4px o 8px
- **Alineación**: Grid estricto, elementos perfectamente alineados
- **Jerarquía**: Tamaños tipográficos y espacios que guíen la vista

---

## 2. PALETA DE COLORES

### 2.1 Modo Oscuro (Predeterminado)
```css
/* Backgrounds - Basado en análisis Trezo */
--bg-primary: #0a0e1a;     /* Fondo principal (más oscuro que Trezo) */
--bg-secondary: #141b2d;   /* Cards y elementos elevados */
--bg-sidebar: #0a0e1a;     /* Sidebar igual al fondo principal */
--bg-tertiary: #1a2332;    /* Elementos menos prominentes */

/* Surfaces */
--surface-1: #1e2936;      /* Bordes y separadores sutiles */
--surface-2: #2a3441;      /* Hover states */
--surface-3: #364153;      /* Active/pressed states */

/* Text */
--text-primary: #ffffff;   /* Texto principal (blanco puro para contraste) */
--text-secondary: #c1c9d2; /* Texto secundario */
--text-muted: #8b96a5;     /* Labels y texto terciario */
--text-placeholder: #6b7785; /* Placeholder text */

/* Accent Colors - Esquema de Trezo */
--primary: #6366f1;        /* Violeta principal (branding) */
--primary-hover: #5855eb;  /* Violeta hover */
--primary-light: #818cf8;  /* Violeta claro */
--primary-alpha: rgba(99, 102, 241, 0.1); /* Violeta transparente */

/* Status Colors */
--success: #22c55e;        /* Verde para positivos */
--success-light: #86efac;  /* Verde claro */
--warning: #f59e0b;        /* Amarillo para advertencias */
--warning-light: #fde68a;  /* Amarillo claro */
--danger: #ef4444;         /* Rojo para errores/negativos */
--danger-light: #fca5a5;   /* Rojo claro */

/* Data Visualization Colors */
--chart-1: #6366f1;        /* Violeta principal */
--chart-2: #8b5cf6;        /* Púrpura */
--chart-3: #06b6d4;        /* Cyan */
--chart-4: #10b981;        /* Esmeralda */
--chart-5: #f59e0b;        /* Ámbar */
--chart-6: #ef4444;        /* Rojo */
```

### 2.2 Modo Claro
```css
/* Backgrounds */
--bg-primary: #f8fafc;     /* Fondo principal claro pero no blanco */
--bg-secondary: #ffffff;   /* Cards con blanco real */
--bg-sidebar: #0f172a;     /* Sidebar SIEMPRE oscura */

/* Surfaces */
--surface-1: #e2e8f0;      /* Elementos interactivos */
--surface-2: #cbd5e1;      /* Hover states */

/* Text */
--text-primary: #1e293b;   /* Texto principal */
--text-secondary: #475569; /* Texto secundario */
--text-muted: #64748b;     /* Labels y texto terciario */

/* Accent Colors (iguales) */
--primary: #6366f1;
--success: #059669;
--warning: #d97706;
--danger: #dc2626;
```

---

## 3. LAYOUT Y ESTRUCTURA

### 3.1 Sidebar (Navegación Principal)
```scss
.sidebar {
  width: 280px; // Fijo, no colapsible inicialmente
  background: var(--bg-sidebar);
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  z-index: 100;
  
  // Logo area
  .brand {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
  }
  
  // Navigation items
  .nav-item {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin: 0 0.5rem;
    
    &.active {
      background: var(--primary);
      color: white;
    }
    
    &:hover:not(.active) {
      background: rgba(255,255,255,0.05);
    }
  }
}
```

### 3.2 Header
```scss
.header {
  height: 64px;
  background: var(--bg-primary); // En claro también puede ser oscuro
  border-bottom: 1px solid var(--surface-1);
  position: fixed;
  top: 0;
  left: 280px; // Offset por sidebar
  right: 0;
  z-index: 90;
  
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  
  .search-bar {
    width: 100%;
    max-width: 400px;
    background: var(--bg-secondary);
    border: 1px solid var(--surface-1);
    border-radius: 8px;
  }
  
  .header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
}
```

### 3.3 Main Content
```scss
.main-content {
  margin-left: 280px; // Offset por sidebar
  margin-top: 64px;    // Offset por header
  padding: 1.5rem;
  min-height: calc(100vh - 64px);
  background: var(--bg-primary);
}
```

---

## 4. COMPONENTES PRINCIPALES

### 4.1 Metric Cards (KPI Cards)
**Basado en las 4 cards superiores del dashboard Trezo**
```scss
.metric-card {
  background: var(--bg-secondary);
  border: 1px solid var(--surface-1);
  border-radius: 8px;
  padding: 1.5rem;
  position: relative;
  transition: var(--transition-fast);
  
  &:hover {
    border-color: var(--surface-2);
    background: var(--bg-tertiary);
  }
  
  .metric-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    
    .metric-title {
      font-size: 0.875rem;
      color: var(--text-secondary);
      font-weight: 500;
      text-transform: none; /* Como en Trezo */
    }
    
    .metric-trend {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.5rem;
      border-radius: 12px; /* Más redondeado como Trezo */
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      
      &.positive {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success-light);
        
        &::before {
          content: "+";
        }
      }
      
      &.negative {
        background: rgba(239, 68, 68, 0.15);
        color: var(--danger-light);
        
        &::before {
          content: "-";
        }
      }
    }
  }
  
  .metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-variant-numeric: tabular-nums; /* Números tabulares */
    line-height: 1.1;
  }
  
  .metric-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
  }
  
  // Mini chart integration - Sparkline style como Trezo
  .metric-chart {
    height: 40px;
    margin-top: 1rem;
    position: relative;
    
    // Gradiente sutil para las líneas
    .chart-line {
      stroke: var(--primary);
      stroke-width: 2;
      fill: none;
    }
    
    .chart-area {
      fill: url(#areaGradient);
      opacity: 0.3;
    }
  }
  
  // Indicador de métrica en la esquina (como Dashboard icon)
  &::after {
    content: "";
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 6px;
    height: 6px;
    background: var(--primary);
    border-radius: 50%;
    opacity: 0.4;
  }
}
```

### 4.2 Charts Container
```scss
.chart-container {
  background: var(--bg-secondary);
  border: 1px solid var(--surface-1);
  border-radius: 8px;
  padding: 1.5rem;
  
  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    
    .chart-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    
    .chart-controls {
      display: flex;
      gap: 0.5rem;
    }
  }
  
  .chart-content {
    height: 300px; // Fixed height for consistency
  }
  
  .chart-legend {
    display: flex;
    gap: 1.5rem;
    margin-top: 1rem;
    font-size: 0.875rem;
  }
}
```

### 4.3 Data Tables
```scss
.data-table {
  background: var(--bg-secondary);
  border: 1px solid var(--surface-1);
  border-radius: 8px;
  overflow: hidden;
  
  .table-header {
    background: var(--bg-primary);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--surface-1);
    
    display: flex;
    justify-content: space-between;
    align-items: center;
    
    .table-title {
      font-weight: 600;
      color: var(--text-primary);
    }
  }
  
  table {
    width: 100%;
    
    th {
      background: var(--bg-primary);
      padding: 0.75rem 1rem;
      text-align: left;
      font-weight: 500;
      color: var(--text-secondary);
      font-size: 0.875rem;
      border-bottom: 1px solid var(--surface-1);
    }
    
    td {
      padding: 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      color: var(--text-primary);
      
      &:last-child {
        text-align: right;
      }
    }
    
    tr:hover {
      background: var(--surface-1);
    }
  }
}
```

---

## 5. SISTEMA TIPOGRÁFICO

### 5.1 Font Stack
```css
:root {
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
}
```

### 5.2 Scale Tipográfica
```css
:root {
  --text-xs: 0.75rem;    /* 12px - Labels pequeños */
  --text-sm: 0.875rem;   /* 14px - Texto secundario */
  --text-base: 1rem;     /* 16px - Texto base */
  --text-lg: 1.125rem;   /* 18px - Títulos secundarios */
  --text-xl: 1.25rem;    /* 20px - Títulos principales */
  --text-2xl: 1.5rem;    /* 24px - Títulos grandes */
  --text-3xl: 1.875rem;  /* 30px - Métricas */
  --text-4xl: 2.25rem;   /* 36px - Valores KPI */
}
```

---

## 6. COMPONENTES DE INTERACCIÓN

### 6.1 Botones (Estilo Trezo)
```scss
.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid transparent;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;
  
  &:focus {
    outline: none;
    box-shadow: 0 0 0 3px var(--primary-alpha);
  }
  
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
  
  &.btn-primary {
    background: var(--primary);
    color: white;
    
    &:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    }
    
    &:active {
      transform: translateY(0);
      box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }
  }
  
  &.btn-secondary {
    background: transparent;
    color: var(--text-secondary);
    border-color: var(--surface-1);
    
    &:hover {
      background: var(--surface-1);
      color: var(--text-primary);
      border-color: var(--surface-2);
    }
  }
  
  &.btn-ghost {
    background: transparent;
    color: var(--text-secondary);
    
    &:hover {
      background: var(--surface-1);
      color: var(--text-primary);
    }
  }
  
  // Como el botón "Buy Now" de Trezo
  &.btn-accent {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    font-weight: 600;
    
    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(99, 102, 241, 0.4);
    }
  }
  
  &.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    gap: 0.375rem;
  }
  
  &.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    gap: 0.75rem;
  }
  
  // Loading state
  &.loading {
    position: relative;
    color: transparent;
    
    &::after {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 16px;
      height: 16px;
      border: 2px solid currentColor;
      border-radius: 50%;
      border-top-color: transparent;
      animation: spin 1s linear infinite;
    }
  }
}

@keyframes spin {
  to { transform: translate(-50%, -50%) rotate(360deg); }
}
```

### 6.2 Form Controls
```scss
.form-control {
  background: var(--bg-secondary);
  border: 1px solid var(--surface-1);
  border-radius: 6px;
  padding: 0.625rem 0.75rem;
  color: var(--text-primary);
  font-size: 0.875rem;
  
  &:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }
  
  &::placeholder {
    color: var(--text-muted);
  }
}
```

---

## 7. SISTEMA DE SPACING

```css
:root {
  --spacing-0: 0;
  --spacing-1: 0.25rem;  /* 4px */
  --spacing-2: 0.5rem;   /* 8px */
  --spacing-3: 0.75rem;  /* 12px */
  --spacing-4: 1rem;     /* 16px */
  --spacing-5: 1.25rem;  /* 20px */
  --spacing-6: 1.5rem;   /* 24px */
  --spacing-8: 2rem;     /* 32px */
  --spacing-10: 2.5rem;  /* 40px */
  --spacing-12: 3rem;    /* 48px */
}
```

---

## 8. RESPONSIVE DESIGN

### 8.1 Breakpoints
```css
:root {
  --screen-sm: 640px;
  --screen-md: 768px;
  --screen-lg: 1024px;
  --screen-xl: 1280px;
  --screen-2xl: 1536px;
}
```

### 8.2 Mobile Adaptations
```scss
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 280px; /* Mantiene el mismo ancho */
    box-shadow: 2xl; /* Sombra cuando está abierto */
    
    &.open {
      transform: translateX(0);
    }
  }
  
  .main-content {
    margin-left: 0;
  }
  
  .header {
    left: 0;
    
    .hamburger {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      
      .hamburger-lines {
        width: 18px;
        height: 2px;
        background: var(--text-primary);
        position: relative;
        transition: var(--transition-fast);
        
        &::before,
        &::after {
          content: "";
          position: absolute;
          width: 100%;
          height: 2px;
          background: var(--text-primary);
          transition: var(--transition-fast);
        }
        
        &::before { top: -6px; }
        &::after { bottom: -6px; }
      }
    }
  }
  
  // Grid de métricas responsive
  .metrics-grid {
    grid-template-columns: 1fr 1fr; /* 2 columnas en mobile */
    gap: 1rem;
  }
  
  // Charts en mobile
  .chart-container {
    .chart-content {
      height: 240px; /* Altura reducida */
    }
  }
}

@media (max-width: 480px) {
  .metrics-grid {
    grid-template-columns: 1fr; /* 1 columna en móviles pequeños */
  }
  
  .metric-card {
    padding: 1rem; /* Padding reducido */
    
    .metric-value {
      font-size: 1.5rem; /* Tamaño reducido */
    }
  }
}
```

---

## 9. ANIMACIONES Y TRANSICIONES

### 9.1 Tiempos de Transición
```css
:root {
  --transition-fast: 0.15s ease;
  --transition-normal: 0.3s ease;
  --transition-slow: 0.5s ease;
}
```

### 9.2 Efectos Comunes
```css
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

.slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
```

---

## 10. IMPLEMENTACIÓN EN VUE.JS

### 10.1 Estructura de Componentes
```
resources/js/design/
├── components/           # Componentes base reutilizables
│   ├── base/
│   │   ├── BaseButton.vue
│   │   ├── BaseCard.vue
│   │   ├── BaseTable.vue
│   │   └── BaseInput.vue
│   ├── charts/
│   │   ├── LineChart.vue
│   │   ├── BarChart.vue
│   │   └── PieChart.vue
│   └── layout/
│       ├── Sidebar.vue
│       ├── Header.vue
│       └── MainContent.vue
├── composables/          # Lógica reutilizable
│   ├── useTheme.js
│   ├── useBreakpoints.js
│   └── useCharts.js
└── tokens/              # Variables de diseño
    ├── colors.css
    ├── typography.css
    └── spacing.css
```

### 10.2 Composable de Tema
```javascript
// composables/useTheme.js
import { ref, computed } from 'vue'

const isDark = ref(true) // Dark mode por defecto

export function useTheme() {
  const toggleTheme = () => {
    isDark.value = !isDark.value
    updateCSSVariables()
  }
  
  const updateCSSVariables = () => {
    const root = document.documentElement
    if (isDark.value) {
      root.setAttribute('data-theme', 'dark')
    } else {
      root.setAttribute('data-theme', 'light')
    }
  }
  
  const themeClasses = computed(() => ({
    'theme-dark': isDark.value,
    'theme-light': !isDark.value
  }))
  
  return {
    isDark,
    toggleTheme,
    themeClasses
  }
}
```

---

## 11. PATRONES DE INTERACCIÓN ESPECÍFICOS

### 11.1 Hover States (Basado en Trezo)
```scss
// Table rows
.table-row:hover {
  background: rgba(99, 102, 241, 0.05);
  transition: var(--transition-fast);
}

// Sidebar navigation
.nav-item:hover:not(.active) {
  background: rgba(255, 255, 255, 0.08);
  transform: translateX(2px);
}

// Cards
.card:hover {
  border-color: var(--surface-2);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
```

### 11.2 Loading States
```scss
.skeleton {
  background: linear-gradient(
    90deg,
    var(--surface-1) 25%,
    var(--surface-2) 50%,
    var(--surface-1) 75%
  );
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
```

### 11.3 Micro-interacciones
```scss
.dropdown-trigger {
  &::after {
    content: "▼";
    font-size: 0.75rem;
    margin-left: 0.5rem;
    transition: var(--transition-fast);
  }
  
  &.open::after {
    transform: rotate(180deg);
  }
}

.button {
  &:active {
    transform: scale(0.98);
  }
}
```

---

## 12. IMPLEMENTACIÓN CON VUE 3 + COMPOSITION API

### 12.1 Composable para Métricas
```javascript
// composables/useMetrics.js
import { ref, computed } from 'vue'

export function useMetrics() {
  const metrics = ref([])
  const loading = ref(false)
  
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-AR', {
      style: 'currency',
      currency: 'ARS',
      minimumFractionDigits: 0
    }).format(value)
  }
  
  const formatPercentage = (value) => {
    return `${value > 0 ? '+' : ''}${value.toFixed(1)}%`
  }
  
  const getTrendClass = (value) => {
    return value > 0 ? 'positive' : value < 0 ? 'negative' : 'neutral'
  }
  
  return {
    metrics,
    loading,
    formatCurrency,
    formatPercentage,
    getTrendClass
  }
}
```

### 12.2 Componente MetricCard
```vue
<template>
  <div class="metric-card" :class="{ loading }">
    <div class="metric-header">
      <h3 class="metric-title">{{ title }}</h3>
      <span 
        v-if="trend" 
        class="metric-trend"
        :class="getTrendClass(trend)"
      >
        {{ formatPercentage(trend) }}
      </span>
    </div>
    
    <div class="metric-value">
      {{ formatValue(value) }}
    </div>
    
    <p class="metric-subtitle">{{ subtitle }}</p>
    
    <div v-if="chartData" class="metric-chart">
      <svg class="w-full h-full" viewBox="0 0 200 40">
        <defs>
          <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" :stop-color="primaryColor" stop-opacity="0.4"/>
            <stop offset="100%" :stop-color="primaryColor" stop-opacity="0"/>
          </linearGradient>
        </defs>
        <path 
          :d="chartPath" 
          class="chart-area" 
        />
        <path 
          :d="chartPath" 
          class="chart-line" 
        />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useMetrics } from '@/composables/useMetrics'

const props = defineProps({
  title: String,
  value: Number,
  subtitle: String,
  trend: Number,
  chartData: Array,
  type: { type: String, default: 'number' },
  loading: { type: Boolean, default: false }
})

const { formatCurrency, formatPercentage, getTrendClass } = useMetrics()

const formatValue = (value) => {
  if (props.type === 'currency') return formatCurrency(value)
  if (props.type === 'percentage') return `${value}%`
  return value.toLocaleString()
}

const primaryColor = 'rgb(99, 102, 241)'

const chartPath = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return ''
  
  const data = props.chartData
  const max = Math.max(...data)
  const min = Math.min(...data)
  const range = max - min || 1
  
  const points = data.map((value, index) => {
    const x = (index / (data.length - 1)) * 200
    const y = 40 - ((value - min) / range) * 30 - 5
    return `${x},${y}`
  })
  
  return `M ${points.join(' L ')}`
})
</script>
```

---

---

## 13. CONTEXTO ESPECÍFICO SISTEMA BAMBU

### 13.1 Pantallas Principales Requeridas
```scss
// Dashboard Principal - Métricas de negocio
.dashboard-home {
  .metrics-overview {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
    
    // Métricas específicas BAMBU
    .metric-revenue { --accent: var(--success); }
    .metric-orders { --accent: var(--primary); }
    .metric-clients { --accent: var(--chart-3); }
    .metric-products { --accent: var(--chart-2); }
  }
}

// Gestión de Productos
.products-view {
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
  }
  
  .product-card {
    .stock-indicator {
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      
      &.low-stock { color: var(--warning); }
      &.out-stock { color: var(--danger); }
      &.good-stock { color: var(--success); }
    }
  }
}

// Gestión de Pedidos  
.orders-view {
  .order-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
    
    &.borrador { background: rgba(156, 163, 175, 0.15); color: #9ca3af; }
    &.confirmado { background: var(--primary-alpha); color: var(--primary-light); }
    &.en_reparto { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
    &.entregado { background: rgba(34, 197, 94, 0.15); color: var(--success); }
    &.cancelado { background: rgba(239, 68, 68, 0.15); color: var(--danger); }
    
    &::before {
      content: "";
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
    }
  }
}
```

### 13.2 Plan de Implementación
**FASE 1: Estructura Base**
1. Crear tokens CSS (colors, typography, spacing)
2. Implementar layout components (Sidebar, Header, MainContent)
3. Desarrollar componentes base (Button, Input, Card, Table)

**FASE 2: Componentes Business**
1. MetricCard con mini-charts
2. ProductCard con stock indicators
3. OrderStatus con estados visuales
4. Cliente cards con tier system

**FASE 3: Pantallas Principales**
1. Dashboard con métricas BAMBU
2. Vista productos con grid responsivo  
3. Gestión pedidos con estados
4. Panel configuración Filament

---

🎨 **DOCUMENTO FINALIZADO - ITERACIÓN 3/3**  
🚀 **Sistema de diseño completo para BAMBU v2.0**  
📋 **Listo para implementación con Vue 3 + Tailwind**