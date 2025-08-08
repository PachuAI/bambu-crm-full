# 🏗️ BAMBÚ FRONTEND SYSTEM - Core de Desarrollo Frontend

**Versión**: 1.0.0  
**Stack**: Laravel 11 + Vue 3 + Tailwind 4.0 + CSS Variables  
**Actualizado**: 2025-08-08  

---

## 📖 Índice

1. [Introducción](#introducción)
2. [CSS Reset y Fundación](#css-reset-y-fundación)
3. [Variables Globales del Sistema](#variables-globales-del-sistema)
4. [Sistema de Utilidades CSS](#sistema-de-utilidades-css)
5. [Arquitectura de Archivos](#arquitectura-de-archivos)
6. [Componentes Base CRM](#componentes-base-crm)
7. [Patrones Específicos Bambú](#patrones-específicos-bambú)
8. [Checklist Frontend Profesional](#checklist-frontend-profesional)

---

## Introducción

Este documento define el **núcleo técnico del sistema frontend de Bambú**. Complementa y unifica los sistemas de diseño específicos:

- **[BAMBU_COLOR_SYSTEM.md](./BAMBU_COLOR_SYSTEM.md)** - Paleta de colores y temas
- **[BAMBU_RESPONSIVE_SYSTEM.md](./BAMBU_RESPONSIVE_SYSTEM.md)** - Breakpoints y responsive
- **[UX_UI_GUIDELINES_SISTEMA_BAMBU.md](./UX_UI_GUIDELINES_SISTEMA_BAMBU.md)** - Guías específicas de UX

### 🎯 Objetivo

Crear una **base técnica sólida** que permita desarrollo rápido, consistente y escalable de interfaces para el CRM de productos químicos Bambú.

### 🛠️ Stack Técnico Confirmado

```yaml
Backend: Laravel 11 + PostgreSQL 15 + Sanctum
Frontend: Vue 3 + TypeScript + Pinia
CSS: Tailwind 4.0 + CSS Variables (Híbrido)
Build: Vite + HMR
```

---

## CSS Reset y Fundación

### Reset Universal Obligatorio

```css
/* === BAMBU CSS RESET === */
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    line-height: 1.6;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-primary);
    background: var(--bg-base);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Elementos multimedia responsivos */
img, picture, video, canvas, svg {
    display: block;
    max-width: 100%;
    height: auto;
}

/* Formularios heredan tipografía */
input, button, textarea, select {
    font: inherit;
    color: inherit;
}

/* Botones sin estilos por defecto */
button {
    background: none;
    border: none;
    cursor: pointer;
}

/* Links sin decoración por defecto */
a {
    color: inherit;
    text-decoration: none;
}

/* Listas sin bullets por defecto */
ul, ol {
    list-style: none;
}

/* Tablas con bordes colapsados */
table {
    border-collapse: collapse;
    border-spacing: 0;
}
```

### Estilos Base del Sistema

```css
/* === BAMBU BASE STYLES === */
html {
    scroll-behavior: smooth;
}

body {
    min-height: 100vh;
    overflow-x: hidden;
}

/* Tipografía base */
h1, h2, h3, h4, h5, h6 {
    color: var(--text-primary);
    font-weight: 600;
    line-height: 1.2;
    margin-bottom: var(--space-md);
}

h1 { font-size: var(--font-4xl); }
h2 { font-size: var(--font-3xl); }  
h3 { font-size: var(--font-2xl); }
h4 { font-size: var(--font-xl); }
h5 { font-size: var(--font-lg); }
h6 { font-size: var(--font-base); }

p {
    color: var(--text-secondary);
    margin-bottom: var(--space-md);
    max-width: 65ch; /* Óptimo para legibilidad */
}

/* Links del sistema */
a {
    color: var(--primary);
    transition: color 0.2s ease;
}

a:hover {
    color: var(--primary-hover);
}

/* Focus states accesibles */
*:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}
```

---

## Variables Globales del Sistema

### Variables Específicas de Bambú

Estas variables **complementan** las definidas en `BAMBU_COLOR_SYSTEM.md`:

```css
/* === VARIABLES BAMBU SYSTEM === */
:root {
    /* Estados específicos del negocio químico */
    --status-stock-alto: hsl(120, 60%, 50%);
    --status-stock-medio: hsl(45, 85%, 60%);
    --status-stock-bajo: hsl(0, 70%, 55%);
    --status-stock-agotado: hsl(0, 0%, 50%);
    
    /* Prioridades de pedidos/producción */
    --priority-critica: hsl(0, 70%, 55%);
    --priority-alta: hsl(25, 85%, 60%);
    --priority-media: hsl(45, 85%, 60%);
    --priority-baja: hsl(120, 60%, 50%);
    
    /* Tipos de productos químicos */
    --producto-acido: hsl(0, 70%, 55%);
    --producto-base: hsl(210, 70%, 55%);
    --producto-neutro: hsl(45, 50%, 55%);
    --producto-especial: hsl(270, 60%, 55%);
    
    /* Espaciado específico dashboard */
    --sidebar-width: 280px;
    --sidebar-width-collapsed: 64px;
    --header-height: 64px;
    --card-padding: var(--space-lg);
    --table-padding: var(--space-sm);
    
    /* Elementos específicos del dominio */
    --bidon-5l-color: hsl(200, 60%, 50%);
    --bidon-20l-color: hsl(160, 60%, 50%);
    --contenedor-ibc-color: hsl(280, 60%, 50%);
    
    /* Transiciones del sistema */
    --transition-fast: 0.15s ease-out;
    --transition-base: 0.2s ease-out;
    --transition-slow: 0.3s ease-out;
    
    /* Z-index layers */
    --z-dropdown: 1000;
    --z-sticky: 1020;
    --z-fixed: 1030;
    --z-modal-backdrop: 1040;
    --z-modal: 1050;
    --z-popover: 1060;
    --z-tooltip: 1070;
    --z-toast: 1080;
}
```

---

## Sistema de Utilidades CSS

### Utilidades de Layout

```css
/* === LAYOUT UTILITIES === */
.container {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 var(--space-md);
}

.container-narrow { max-width: 800px; }
.container-wide { max-width: 1600px; }

/* Flexbox system */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-row { flex-direction: row; }
.flex-wrap { flex-wrap: wrap; }
.flex-nowrap { flex-wrap: nowrap; }

.items-start { align-items: flex-start; }
.items-center { align-items: center; }
.items-end { align-items: flex-end; }
.items-stretch { align-items: stretch; }

.justify-start { justify-content: flex-start; }
.justify-center { justify-content: center; }
.justify-end { justify-content: flex-end; }
.justify-between { justify-content: space-between; }
.justify-around { justify-content: space-around; }

.flex-1 { flex: 1; }
.flex-none { flex: none; }

/* Grid system */
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
.grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
.grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
.grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

.grid-auto { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
.grid-auto-sm { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
.grid-auto-lg { grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); }

.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }
.col-span-4 { grid-column: span 4; }

/* Gaps */
.gap-xs { gap: var(--space-xs); }
.gap-sm { gap: var(--space-sm); }
.gap-md { gap: var(--space-md); }
.gap-lg { gap: var(--space-lg); }
.gap-xl { gap: var(--space-xl); }
```

### Utilidades de Espaciado

```css
/* === SPACING UTILITIES === */
/* Padding */
.p-0 { padding: 0; }
.p-xs { padding: var(--space-xs); }
.p-sm { padding: var(--space-sm); }
.p-md { padding: var(--space-md); }
.p-lg { padding: var(--space-lg); }
.p-xl { padding: var(--space-xl); }

.px-sm { padding-left: var(--space-sm); padding-right: var(--space-sm); }
.px-md { padding-left: var(--space-md); padding-right: var(--space-md); }
.px-lg { padding-left: var(--space-lg); padding-right: var(--space-lg); }

.py-sm { padding-top: var(--space-sm); padding-bottom: var(--space-sm); }
.py-md { padding-top: var(--space-md); padding-bottom: var(--space-md); }
.py-lg { padding-top: var(--space-lg); padding-bottom: var(--space-lg); }

/* Margin */
.m-0 { margin: 0; }
.m-auto { margin: auto; }
.mx-auto { margin-left: auto; margin-right: auto; }
.my-auto { margin-top: auto; margin-bottom: auto; }

.mb-sm { margin-bottom: var(--space-sm); }
.mb-md { margin-bottom: var(--space-md); }
.mb-lg { margin-bottom: var(--space-lg); }
.mb-xl { margin-bottom: var(--space-xl); }
```

### Utilidades Específicas Bambú

```css
/* === BAMBU DOMAIN UTILITIES === */
.status-stock-alto { color: var(--status-stock-alto); }
.status-stock-medio { color: var(--status-stock-medio); }
.status-stock-bajo { color: var(--status-stock-bajo); }
.status-stock-agotado { color: var(--status-stock-agotado); }

.priority-critica { color: var(--priority-critica); }
.priority-alta { color: var(--priority-alta); }
.priority-media { color: var(--priority-media); }
.priority-baja { color: var(--priority-baja); }

.producto-acido { color: var(--producto-acido); }
.producto-base { color: var(--producto-base); }
.producto-neutro { color: var(--producto-neutro); }
.producto-especial { color: var(--producto-especial); }

/* Utilidades de contenedor */
.bidon-indicator::before {
    content: "🪣";
    margin-right: var(--space-xs);
}

.ibc-indicator::before {
    content: "📦";
    margin-right: var(--space-xs);
}
```

---

## Arquitectura de Archivos

### Estructura Obligatoria Laravel + Vue

```
bambu-sistema-v2/resources/
├── css/
│   ├── app.css                 # Entry point principal
│   ├── reset.css               # Reset del sistema
│   ├── variables.css           # Variables globales
│   ├── utilities.css           # Clases utilitarias
│   ├── components.css          # Estilos componentes base
│   └── vendor/
│       └── tailwind-override.css
├── js/
│   ├── app.js                  # Entry point Vue
│   ├── composables/
│   │   ├── useTheme.js         # Theme management
│   │   ├── useResponsive.js    # Responsive utilities
│   │   ├── useStock.js         # Stock domain logic
│   │   └── useBambuApi.js      # API layer
│   ├── components/
│   │   ├── ui/
│   │   │   ├── BambuCard.vue
│   │   │   ├── BambuButton.vue
│   │   │   ├── BambuInput.vue
│   │   │   └── BambuTable.vue
│   │   ├── domain/
│   │   │   ├── ProductoCard.vue
│   │   │   ├── ClienteCard.vue
│   │   │   ├── StockIndicator.vue
│   │   │   └── PedidoStatus.vue
│   │   └── layout/
│   │       ├── MainLayout.vue
│   │       ├── DashboardSidebar.vue
│   │       └── DashboardHeader.vue
│   ├── views/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── productos/
│   │   ├── clientes/
│   │   ├── stock/
│   │   └── pedidos/
│   └── utils/
│       ├── formatters.js       # Formateo de datos químicos
│       ├── validators.js       # Validaciones específicas
│       └── constants.js        # Constantes del dominio
```

### Entry Points Requeridos

**app.css:**
```css
/* === BAMBU APP.CSS === */
@import './reset.css';
@import './variables.css';
@import './utilities.css';
@import './components.css';

/* Tailwind imports */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Vendor overrides */
@import './vendor/tailwind-override.css';
```

---

## Componentes Base CRM

### Componente: BambuCard.vue

```vue
<template>
  <div 
    :class="[
      'bambu-card',
      variant && `bambu-card--${variant}`,
      clickable && 'bambu-card--clickable',
      loading && 'bambu-card--loading'
    ]"
    @click="handleClick"
  >
    <div v-if="loading" class="bambu-card__loading">
      <div class="spinner"></div>
    </div>
    
    <header v-if="$slots.header || title" class="bambu-card__header">
      <slot name="header">
        <h3 class="bambu-card__title">{{ title }}</h3>
        <p v-if="subtitle" class="bambu-card__subtitle">{{ subtitle }}</p>
      </slot>
    </header>
    
    <div class="bambu-card__content">
      <slot></slot>
    </div>
    
    <footer v-if="$slots.footer" class="bambu-card__footer">
      <slot name="footer"></slot>
    </footer>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  variant: {
    type: String,
    validator: (value) => ['default', 'elevated', 'outline', 'producto', 'stock'].includes(value)
  },
  clickable: Boolean,
  loading: Boolean
})

defineEmits(['click'])

function handleClick(event) {
  if (clickable) {
    emit('click', event)
  }
}
</script>

<style scoped>
.bambu-card {
  position: relative;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 4px; /* Max radius permitido */
  padding: var(--card-padding);
  transition: var(--transition-base);
}

.bambu-card--clickable {
  cursor: pointer;
}

.bambu-card--clickable:hover {
  border-color: var(--border-hover);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.bambu-card--elevated {
  box-shadow: var(--shadow-sm);
}

.bambu-card--outline {
  background: transparent;
  border: 2px solid var(--border);
}

.bambu-card--producto {
  border-left: 4px solid var(--primary);
}

.bambu-card--stock {
  border-left: 4px solid var(--status-stock-medio);
}

.bambu-card__header {
  margin-bottom: var(--space-md);
  padding-bottom: var(--space-sm);
  border-bottom: 1px solid var(--border);
}

.bambu-card__title {
  color: var(--text-primary);
  font-size: var(--font-lg);
  font-weight: 600;
  margin: 0;
}

.bambu-card__subtitle {
  color: var(--text-secondary);
  font-size: var(--font-sm);
  margin: var(--space-xs) 0 0 0;
}

.bambu-card__content {
  flex: 1;
}

.bambu-card__footer {
  margin-top: var(--space-md);
  padding-top: var(--space-sm);
  border-top: 1px solid var(--border);
}

.bambu-card--loading {
  position: relative;
  pointer-events: none;
}

.bambu-card__loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(var(--bg-surface), 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: inherit;
  z-index: 1;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 2px solid var(--border);
  border-top: 2px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
```

### Componente: StockIndicator.vue

```vue
<template>
  <div 
    :class="[
      'stock-indicator',
      `stock-indicator--${nivel}`
    ]"
    :title="`Stock: ${cantidad} ${unidad}`"
  >
    <div class="stock-indicator__icon">
      {{ getIcon(nivel) }}
    </div>
    <div class="stock-indicator__info">
      <span class="stock-indicator__cantidad">{{ formatCantidad }}</span>
      <span class="stock-indicator__nivel">{{ getNivelTexto(nivel) }}</span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  cantidad: {
    type: Number,
    required: true
  },
  unidad: {
    type: String,
    default: 'L'
  },
  minimo: {
    type: Number,
    default: 100
  },
  medio: {
    type: Number, 
    default: 500
  }
})

const nivel = computed(() => {
  if (props.cantidad === 0) return 'agotado'
  if (props.cantidad <= props.minimo) return 'bajo'
  if (props.cantidad <= props.medio) return 'medio'
  return 'alto'
})

const formatCantidad = computed(() => {
  return `${props.cantidad.toLocaleString()} ${props.unidad}`
})

function getIcon(nivel) {
  const icons = {
    alto: '🟢',
    medio: '🟡', 
    bajo: '🔴',
    agotado: '⚫'
  }
  return icons[nivel] || '⚫'
}

function getNivelTexto(nivel) {
  const textos = {
    alto: 'Stock Alto',
    medio: 'Stock Medio',
    bajo: 'Stock Bajo', 
    agotado: 'Agotado'
  }
  return textos[nivel] || 'Desconocido'
}
</script>

<style scoped>
.stock-indicator {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-xs) var(--space-sm);
  border-radius: 4px;
  font-size: var(--font-sm);
  transition: var(--transition-base);
}

.stock-indicator--alto {
  background: color-mix(in srgb, var(--status-stock-alto) 10%, transparent);
  color: var(--status-stock-alto);
  border: 1px solid color-mix(in srgb, var(--status-stock-alto) 30%, transparent);
}

.stock-indicator--medio {
  background: color-mix(in srgb, var(--status-stock-medio) 10%, transparent);
  color: var(--status-stock-medio);
  border: 1px solid color-mix(in srgb, var(--status-stock-medio) 30%, transparent);
}

.stock-indicator--bajo {
  background: color-mix(in srgb, var(--status-stock-bajo) 10%, transparent);
  color: var(--status-stock-bajo);
  border: 1px solid color-mix(in srgb, var(--status-stock-bajo) 30%, transparent);
}

.stock-indicator--agotado {
  background: color-mix(in srgb, var(--status-stock-agotado) 10%, transparent);
  color: var(--status-stock-agotado);
  border: 1px solid color-mix(in srgb, var(--status-stock-agotado) 30%, transparent);
}

.stock-indicator__icon {
  font-size: var(--font-base);
}

.stock-indicator__info {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.stock-indicator__cantidad {
  font-weight: 600;
}

.stock-indicator__nivel {
  font-size: var(--font-xs);
  opacity: 0.8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
</style>
```

---

## Patrones Específicos Bambú

### Layout Dashboard CRM

```vue
<!-- DashboardLayout.vue -->
<template>
  <div class="dashboard-layout">
    <DashboardSidebar 
      :collapsed="sidebarCollapsed"
      @toggle="sidebarCollapsed = !sidebarCollapsed"
    />
    
    <div class="dashboard-main">
      <DashboardHeader 
        :user="user"
        :sidebar-collapsed="sidebarCollapsed"
        @toggle-sidebar="sidebarCollapsed = !sidebarCollapsed"
      />
      
      <main class="dashboard-content">
        <div class="dashboard-container">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background: var(--bg-base);
}

.dashboard-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  margin-left: var(--sidebar-width);
  transition: margin-left var(--transition-base);
}

.dashboard-main--collapsed {
  margin-left: var(--sidebar-width-collapsed);
}

.dashboard-content {
  flex: 1;
  padding: var(--space-lg);
  overflow-x: auto;
}

.dashboard-container {
  max-width: 1600px;
  margin: 0 auto;
}

@media (max-width: 1024px) {
  .dashboard-main {
    margin-left: 0;
  }
}
</style>
```

### Patrón: Tablas de Productos Químicos

```vue
<!-- ProductosTable.vue -->
<template>
  <BambuCard title="Productos Químicos" variant="elevated">
    <template #header>
      <div class="productos-table__header">
        <h3>Productos Químicos</h3>
        <div class="productos-table__filters">
          <select v-model="filtroTipo" class="form-input">
            <option value="">Todos los tipos</option>
            <option value="acido">Ácidos</option>
            <option value="base">Bases</option>
            <option value="neutro">Neutros</option>
            <option value="especial">Especiales</option>
          </select>
        </div>
      </div>
    </template>
    
    <div class="productos-table">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Stock</th>
            <th>Precio/L</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in productosFiltrados" :key="producto.id">
            <td>
              <div class="producto-info">
                <strong>{{ producto.nombre }}</strong>
                <small class="text-secondary">{{ producto.codigo }}</small>
              </div>
            </td>
            <td>
              <span :class="`producto-${producto.tipo}`">
                {{ formatTipo(producto.tipo) }}
              </span>
            </td>
            <td>
              <StockIndicator 
                :cantidad="producto.stock_actual"
                :minimo="producto.stock_minimo"
                :medio="producto.stock_medio"
                :unidad="producto.unidad_medida"
              />
            </td>
            <td>
              <span class="precio">{{ formatPrice(producto.precio_por_litro) }}</span>
            </td>
            <td>
              <BambuBadge :variant="getEstadoVariant(producto.estado)">
                {{ producto.estado }}
              </BambuBadge>
            </td>
            <td>
              <div class="acciones">
                <button @click="editarProducto(producto)" class="btn-icon">
                  ✏️
                </button>
                <button @click="verStock(producto)" class="btn-icon">
                  📊
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </BambuCard>
</template>

<style scoped>
.productos-table__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-md);
}

.productos-table table {
  width: 100%;
  border-collapse: collapse;
}

.productos-table th,
.productos-table td {
  padding: var(--table-padding);
  text-align: left;
  border-bottom: 1px solid var(--border);
}

.productos-table th {
  background: var(--bg-elevated);
  font-weight: 600;
  color: var(--text-primary);
  font-size: var(--font-sm);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.producto-info strong {
  display: block;
  color: var(--text-primary);
}

.producto-info small {
  font-size: var(--font-xs);
}

.precio {
  font-weight: 600;
  color: var(--text-primary);
}

.acciones {
  display: flex;
  gap: var(--space-xs);
}

.btn-icon {
  padding: var(--space-xs);
  border: 1px solid var(--border);
  border-radius: 4px;
  background: transparent;
  cursor: pointer;
  transition: var(--transition-base);
}

.btn-icon:hover {
  background: var(--bg-elevated);
  border-color: var(--border-hover);
}
</style>
```

---

## Checklist Frontend Profesional

### ✅ Antes de Cada Commit

**🎨 Diseño y Consistencia:**
- [ ] Todos los colores usan variables CSS (no hardcodeados)
- [ ] Border-radius máximo 4px respetado
- [ ] Espaciado usa variables del sistema (--space-*)
- [ ] Tipografía usa escala definida (--font-*)

**📱 Responsive y Accesibilidad:**
- [ ] Mobile-first implementado correctamente
- [ ] Breakpoints definidos en BAMBU_RESPONSIVE_SYSTEM respetados
- [ ] Focus states visibles y accesibles
- [ ] Contraste de colores mínimo WCAG AA

**⚙️ Técnico:**
- [ ] Sin console.log() en producción
- [ ] Componentes siguen nomenclatura PascalCase
- [ ] Props tipadas correctamente
- [ ] Estilos scoped en componentes Vue
- [ ] No hay CSS duplicado o conflictos

**🧪 Funcionalidad:**
- [ ] Estados de loading implementados
- [ ] Estados de error manejados
- [ ] Validaciones de formularios funcionando
- [ ] Datos del dominio químico formateados correctamente

**🚀 Performance:**
- [ ] Imágenes optimizadas y lazy loading
- [ ] Componentes dinámicos con suspense cuando corresponde
- [ ] Sin re-renders innecesarios
- [ ] Bundle size razonable

### ✅ Checklist Específico Dominio Bambú

**🧪 Productos Químicos:**
- [ ] Tipos de productos correctamente categorizados (ácido/base/neutro/especial)
- [ ] Unidades de medida consistentes (L, mL, kg)
- [ ] Códigos de producto formateados correctamente
- [ ] Indicadores de peligrosidad visibles cuando corresponde

**📦 Stock y Logística:**
- [ ] Niveles de stock coloreados según umbrales
- [ ] Contenedores diferenciados (bidones 5L/20L, IBC)
- [ ] Fechas de vencimiento destacadas
- [ ] Lotes de producción trazables

**👥 Clientes y Pedidos:**
- [ ] Clientes institucionales vs consumidores diferenciados
- [ ] Estados de pedido claros y actualizados
- [ ] Rutas de entrega del Alto Valle identificadas
- [ ] Prioridades visuales según urgencia

---

## Integración con Sistema Bambú

### Archivos a Actualizar

1. **`bambu-sistema-v2/resources/css/app.css`**
   - Importar variables y utilidades de este sistema
   
2. **`bambu-sistema-v2/resources/js/app.js`**
   - Registrar componentes base globalmente
   
3. **Composables a crear:**
   - `useStock.js` - Lógica de stock
   - `useBambuApi.js` - API específica
   - `useProductos.js` - Domain logic productos

### Próximos Pasos

1. ✅ Implementar variables CSS en `app.css`
2. ✅ Crear composables base del sistema
3. ✅ Desarrollar componentes UI reutilizables
4. ✅ Implementar dashboard con nuevos componentes

---

**📚 Documentos Relacionados:**
- [BAMBU_COLOR_SYSTEM.md](./BAMBU_COLOR_SYSTEM.md)
- [BAMBU_RESPONSIVE_SYSTEM.md](./BAMBU_RESPONSIVE_SYSTEM.md)
- [UX_UI_GUIDELINES_SISTEMA_BAMBU.md](./UX_UI_GUIDELINES_SISTEMA_BAMBU.md)
- [DEV_HANDBOOK_LARAVEL_VUE.md](./DEV_HANDBOOK_LARAVEL_VUE.md)

---

## 🔍 REVISIÓN SENIOR FRONTEND - CAMBIOS PROPUESTOS

**Fecha revisión**: 2025-08-08  
**Estado**: Pendientes de implementación  

Luego de someter el sistema a una revisión exhaustiva por parte de un senior frontend developer, se propusieron los siguientes cambios críticos e importantes:

### 🚨 **CAMBIOS CRÍTICOS**

1. **Unificar estructura CSS**
   - **Problema**: Inconsistencia entre CLAUDE.md #8 y este documento
   - **Solución**: Estructura única `app.css + tokens.css + components.css + responsive.css`
   - **Impacto**: Elimina confusión para developers junior

2. **Corregir BambuCard.vue**
   - **Problema**: `emit('click')` usado sin declarar `defineEmits`
   - **Solución**: `const emit = defineEmits(['click'])`
   - **Impacto**: Fix de error de compilación

3. **Crear tokens.css unificado**
   - **Problema**: Tokens `--space-*`, `--font-*`, `--shadow-*` no están definidos
   - **Solución**: Archivo único `tokens.css` con todas las variables del sistema
   - **Impacto**: Fuente de verdad única para el design system

4. **Agregar tokens de sombras faltantes**
   - **Problema**: CSS usa `--shadow-sm/md` pero no existen
   - **Solución**: Definir `--shadow-sm/md/lg` con valores HSL
   - **Impacto**: Fix de estilos rotos

### ⚡ **CAMBIOS IMPORTANTES**

1. **Eliminar utilidades duplicadas de Tailwind**
   - **Problema**: Re-implementamos `flex`, `grid`, `spacing` que ya existen en Tailwind 4
   - **Solución**: Mantener SOLO utilidades específicas del dominio
   - **Impacto**: Reduce CSS bundle y elimina conflicts

2. **Consolidar transiciones**
   - **Problema**: Variables `--transition-*` duplicadas entre documentos
   - **Solución**: Centralizar en `tokens.css`
   - **Impacto**: Consistencia en todas las animaciones

3. **Agregar tokens de focus y radius**
   - **Problema**: Accesibilidad inconsistente
   - **Solución**: `--focus-ring` y `--radius-1..n` tokens
   - **Impacto**: Mejor experiencia para usuarios con teclado

### 💡 **CAMBIOS NICE-TO-HAVE**

1. **Mejorar arquitectura de componentes**
   - Separar componentes por responsabilidad (ui/ domain/ layout/)
   - Agregar composables específicos del negocio
   - Documentar patrones de reutilización

### 📋 **NUEVA ESTRUCTURA CSS PROPUESTA**

```
bambu-sistema-v2/resources/css/
├── app.css              # Entry point (imports todos)
├── tokens.css           # ÚNICA fuente de verdad (spacing, fonts, shadows, colors, transitions)  
├── components.css       # Estilos componentes base
└── responsive.css       # Media queries y breakpoints
```

### 🔧 **COMPONENTES A CORREGIR**

**BambuCard.vue actualizado:**
```vue
<script setup>
// ❌ Antes (error)
// defineEmits(['click'])

// ✅ Después (correcto)  
const emit = defineEmits(['click'])

function handleClick(event) {
  if (clickable) {
    emit('click', event)
  }
}
</script>
```

**Tokens de sombras a agregar:**
```css
/* tokens.css */
:root {
  --shadow-sm: 0 1px 2px hsl(0 0% 0% / 0.25);
  --shadow-md: 0 2px 6px hsl(0 0% 0% / 0.3);  
  --shadow-lg: 0 8px 16px hsl(0 0% 0% / 0.35);
}
```

### ✅ **PRÓXIMOS PASOS DE IMPLEMENTACIÓN**

1. **Crear `tokens.css`** con todas las variables del sistema
2. **Migrar variables** desde otros archivos a `tokens.css`
3. **Actualizar `app.css`** para importar nueva estructura
4. **Corregir componentes** con bugs identificados
5. **Eliminar utilidades duplicadas** manteniendo solo dominio-específicas
6. **Testing** de todos los componentes tras los cambios

---

**🎯 Este documento es OBLIGATORIO para todo desarrollo frontend en el sistema Bambú.**