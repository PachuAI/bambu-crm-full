# 📱 Sistema Responsive BAMBU - Guía Definitiva

## Índice
- [Estructura del Layout](#estructura-del-layout)
- [Breakpoints y Comportamiento](#breakpoints-y-comportamiento)
- [Sidebar Responsive](#sidebar-responsive)
- [Dashboard Mobile-First](#dashboard-mobile-first)
- [Componentes Adaptativos](#componentes-adaptativos)
- [JavaScript Necesario](#javascript-necesario)
- [Patrones de Implementación](#patrones-de-implementación)
- [Testing Responsive](#testing-responsive)

---

## Estructura del Layout

### Árbol de Cajas Principal

```
app-container (100vw x 100vh)
├── sidebar (280px fijo / overlay mobile)
│   ├── logo-area
│   ├── nav-menu
│   └── user-section
├── main-wrapper (flex-1)
│   ├── header (h-64px)
│   │   ├── hamburger-btn (solo mobile)
│   │   ├── search-bar
│   │   └── header-actions
│   └── content-area
│       ├── metrics-grid
│       ├── charts-row
│       └── tables-section
```

### HTML Base Semántico

```html
<div id="app" class="app-container">
  <!-- Sidebar -->
  <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
    <div class="sidebar-logo">
      <img src="logo.svg" alt="BAMBU">
      <span class="sidebar-logo-text">BAMBU</span>
    </div>
    
    <nav class="sidebar-nav">
      <a href="#" class="sidebar-link active">
        <i class="icon-dashboard"></i>
        <span>Dashboard</span>
      </a>
      <!-- más links -->
    </nav>
    
    <div class="sidebar-user">
      <!-- usuario info -->
    </div>
  </aside>
  
  <!-- Main Content -->
  <div class="main-wrapper">
    <!-- Header -->
    <header class="main-header">
      <button class="hamburger-btn" @click="toggleSidebar">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <div class="header-search">
        <input type="search" placeholder="Buscar...">
      </div>
      
      <div class="header-actions">
        <button class="theme-toggle">🌙</button>
        <button class="notifications">🔔</button>
        <div class="user-menu">👤</div>
      </div>
    </header>
    
    <!-- Content -->
    <main class="content-area">
      <router-view />
    </main>
  </div>
  
  <!-- Overlay para mobile -->
  <div class="sidebar-overlay" 
       v-if="sidebarOpen && isMobile" 
       @click="closeSidebar"></div>
</div>
```

---

## Breakpoints y Comportamiento

### Breakpoints Definitivos

```css
/* Mobile First - Definición clara */
:root {
  --mobile: 0px;      /* 0-767px */
  --tablet: 768px;    /* 768-1023px */
  --desktop: 1024px;  /* 1024-1439px */
  --wide: 1440px;     /* 1440px+ */
}
```

### Comportamiento por Breakpoint

| Breakpoint | Sidebar | Header | Grid | Spacing |
|------------|---------|--------|------|---------|
| **Mobile** (0-767) | Overlay 100% | Hamburger visible | 1 columna | 1rem |
| **Tablet** (768-1023) | Overlay 280px | Hamburger visible | 2 columnas | 1.5rem |
| **Desktop** (1024+) | Fijo 280px | Sin hamburger | 3-4 columnas | 2rem |

---

## Sidebar Responsive

### CSS Completo para Sidebar

```css
/* === SIDEBAR BASE === */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: var(--bg-surface);
  border-right: 1px solid var(--border);
  z-index: 1000;
  display: flex;
  flex-direction: column;
  transition: transform var(--transition-normal);
}

/* === MOBILE: Sidebar como Overlay === */
@media (max-width: 1023px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar-open {
    transform: translateX(0);
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
  }
  
  /* Overlay oscuro */
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    animation: fadeIn var(--transition-fast);
  }
}

/* === DESKTOP: Sidebar Fijo === */
@media (min-width: 1024px) {
  .sidebar {
    transform: translateX(0);
  }
  
  .main-wrapper {
    margin-left: 280px;
  }
  
  .hamburger-btn {
    display: none;
  }
}

/* === SIDEBAR INTERNO === */
.sidebar-logo {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: var(--text-secondary);
  transition: var(--transition-fast);
  
  &:hover {
    background: var(--bg-elevated);
    color: var(--text-primary);
  }
  
  &.active {
    background: var(--primary-bg);
    color: var(--primary);
    border-left: 3px solid var(--primary);
  }
}

/* === HAMBURGER BUTTON === */
.hamburger-btn {
  display: none;
  width: 40px;
  height: 40px;
  padding: 0;
  background: transparent;
  border: none;
  cursor: pointer;
  position: relative;
}

@media (max-width: 1023px) {
  .hamburger-btn {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 4px;
  }
  
  .hamburger-btn span {
    width: 20px;
    height: 2px;
    background: var(--text-primary);
    transition: var(--transition-fast);
    display: block;
  }
  
  /* Animación X cuando está abierto */
  .sidebar-open ~ .main-wrapper .hamburger-btn span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
  }
  
  .sidebar-open ~ .main-wrapper .hamburger-btn span:nth-child(2) {
    opacity: 0;
  }
  
  .sidebar-open ~ .main-wrapper .hamburger-btn span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
  }
}
```

---

## Dashboard Mobile-First

### Grid System Responsive

```css
/* === METRICS GRID === */
.metrics-grid {
  display: grid;
  gap: 1rem;
  margin-bottom: 2rem;
}

/* Mobile: 1 columna */
.metrics-grid {
  grid-template-columns: 1fr;
}

/* Tablet: 2 columnas */
@media (min-width: 768px) {
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
}

/* Desktop: 4 columnas */
@media (min-width: 1024px) {
  .metrics-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }
}

/* === METRIC CARDS === */
.metric-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 1rem;
  transition: var(--transition-fast);
}

@media (min-width: 768px) {
  .metric-card {
    padding: 1.5rem;
  }
}

.metric-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
}

@media (min-width: 768px) {
  .metric-value {
    font-size: 2rem;
  }
}

/* === CHARTS ROW === */
.charts-row {
  display: grid;
  gap: 1rem;
  margin-bottom: 2rem;
}

/* Mobile: Stack vertical */
.charts-row {
  grid-template-columns: 1fr;
}

/* Desktop: 2/3 + 1/3 */
@media (min-width: 1024px) {
  .charts-row {
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
  }
}

.chart-container {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 1rem;
  min-height: 250px;
}

@media (min-width: 768px) {
  .chart-container {
    padding: 1.5rem;
    min-height: 350px;
  }
}
```

### Tablas Responsive

```css
/* === TABLA WRAPPER === */
.table-wrapper {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  overflow: hidden;
}

/* Mobile: Scroll horizontal */
@media (max-width: 767px) {
  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  table {
    min-width: 600px;
  }
}

/* === TABLA RESPONSIVE ALTERNATIVA === */
@media (max-width: 767px) {
  .table-responsive {
    display: block;
  }
  
  .table-responsive thead {
    display: none;
  }
  
  .table-responsive tbody,
  .table-responsive tr,
  .table-responsive td {
    display: block;
  }
  
  .table-responsive tr {
    margin-bottom: 1rem;
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 1rem;
  }
  
  .table-responsive td {
    text-align: right;
    padding: 0.5rem 0;
    position: relative;
    padding-left: 50%;
  }
  
  .table-responsive td::before {
    content: attr(data-label);
    position: absolute;
    left: 0;
    width: 45%;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
  }
}
```

---

## Componentes Adaptativos

### Header Responsive

```css
.main-header {
  height: 64px;
  background: var(--bg-base);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  padding: 0 1rem;
  gap: 1rem;
  position: sticky;
  top: 0;
  z-index: 100;
}

/* Mobile: Búsqueda oculta por defecto */
.header-search {
  display: none;
  flex: 1;
  max-width: 400px;
}

@media (min-width: 768px) {
  .header-search {
    display: block;
  }
  
  .main-header {
    padding: 0 1.5rem;
  }
}

@media (min-width: 1024px) {
  .main-header {
    padding: 0 2rem;
  }
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: auto;
}

/* Mobile: Solo íconos */
@media (max-width: 767px) {
  .header-actions button span {
    display: none;
  }
}
```

### Formularios Responsive

```css
.form-grid {
  display: grid;
  gap: 1rem;
}

/* Mobile: 1 columna */
.form-grid {
  grid-template-columns: 1fr;
}

/* Desktop: 2 columnas */
@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-input {
  padding: 0.625rem;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  color: var(--text-primary);
  font-size: 1rem;
}

@media (max-width: 767px) {
  .form-input {
    font-size: 16px; /* Evita zoom en iOS */
  }
}
```

---

## JavaScript Necesario

### Vue Composable para Responsive

```javascript
// composables/useResponsive.js
import { ref, computed, onMounted, onUnmounted } from 'vue'

export function useResponsive() {
  const windowWidth = ref(window.innerWidth)
  const sidebarOpen = ref(false)
  
  // Breakpoints
  const isMobile = computed(() => windowWidth.value < 768)
  const isTablet = computed(() => windowWidth.value >= 768 && windowWidth.value < 1024)
  const isDesktop = computed(() => windowWidth.value >= 1024)
  
  // Sidebar control
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
  }
  
  const closeSidebar = () => {
    sidebarOpen.value = false
  }
  
  // Handle resize
  const handleResize = () => {
    windowWidth.value = window.innerWidth
    
    // Auto-cerrar sidebar en desktop
    if (isDesktop.value) {
      sidebarOpen.value = false
    }
  }
  
  // Lifecycle
  onMounted(() => {
    window.addEventListener('resize', handleResize)
  })
  
  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
  })
  
  return {
    windowWidth,
    sidebarOpen,
    isMobile,
    isTablet,
    isDesktop,
    toggleSidebar,
    closeSidebar
  }
}
```

### Layout Component Principal

```vue
<!-- MainLayout.vue -->
<template>
  <div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
      <div class="sidebar-logo">
        <img src="/logo.svg" alt="BAMBU" />
        <span v-if="!isMobile || sidebarOpen">BAMBU</span>
      </div>
      
      <nav class="sidebar-nav">
        <RouterLink 
          v-for="item in menuItems" 
          :key="item.path"
          :to="item.path"
          class="sidebar-link"
          @click="isMobile && closeSidebar()"
        >
          <i :class="item.icon"></i>
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>
    </aside>
    
    <!-- Main -->
    <div class="main-wrapper">
      <!-- Header -->
      <header class="main-header">
        <button 
          class="hamburger-btn" 
          @click="toggleSidebar"
          v-if="!isDesktop"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
        
        <div class="header-search" v-if="!isMobile">
          <input type="search" placeholder="Buscar..." />
        </div>
        
        <div class="header-actions">
          <button @click="toggleTheme" class="theme-toggle">
            {{ isDark ? '☀️' : '🌙' }}
          </button>
          <button class="notifications">
            🔔
            <span class="badge" v-if="notifications > 0">
              {{ notifications }}
            </span>
          </button>
        </div>
      </header>
      
      <!-- Content -->
      <main class="content-area">
        <slot />
      </main>
    </div>
    
    <!-- Mobile Overlay -->
    <div 
      v-if="sidebarOpen && !isDesktop" 
      class="sidebar-overlay"
      @click="closeSidebar"
    ></div>
  </div>
</template>

<script setup>
import { useResponsive } from '@/composables/useResponsive'
import { useTheme } from '@/composables/useTheme'

const { 
  sidebarOpen, 
  isMobile, 
  isTablet, 
  isDesktop, 
  toggleSidebar, 
  closeSidebar 
} = useResponsive()

const { isDark, toggleTheme } = useTheme()

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: 'icon-dashboard' },
  { path: '/productos', label: 'Productos', icon: 'icon-box' },
  { path: '/clientes', label: 'Clientes', icon: 'icon-users' },
  { path: '/pedidos', label: 'Pedidos', icon: 'icon-clipboard' },
  { path: '/stock', label: 'Stock', icon: 'icon-archive' },
]

const notifications = ref(3)
</script>
```

---

## Patrones de Implementación

### Mobile-First Development Flow

```css
/* 1. SIEMPRE empezar con mobile */
.component {
  /* Estilos base para mobile */
  padding: 1rem;
  font-size: 0.875rem;
}

/* 2. Tablet - ajustes medianos */
@media (min-width: 768px) {
  .component {
    padding: 1.5rem;
    font-size: 1rem;
  }
}

/* 3. Desktop - versión completa */
@media (min-width: 1024px) {
  .component {
    padding: 2rem;
    display: flex;
    gap: 2rem;
  }
}
```

### Contenedor Responsive Universal

```css
.container {
  width: 100%;
  padding: 0 1rem;
  margin: 0 auto;
}

@media (min-width: 768px) {
  .container {
    padding: 0 1.5rem;
  }
}

@media (min-width: 1024px) {
  .container {
    padding: 0 2rem;
    max-width: 1280px;
  }
}

@media (min-width: 1440px) {
  .container {
    max-width: 1440px;
  }
}
```

### Tipografía Fluida

```css
:root {
  /* Clamp: min, preferred, max */
  --text-xs: clamp(0.75rem, 2vw, 0.875rem);
  --text-sm: clamp(0.875rem, 2.5vw, 1rem);
  --text-base: clamp(1rem, 3vw, 1.125rem);
  --text-lg: clamp(1.125rem, 3.5vw, 1.25rem);
  --text-xl: clamp(1.25rem, 4vw, 1.5rem);
  --text-2xl: clamp(1.5rem, 5vw, 2rem);
  --text-3xl: clamp(2rem, 6vw, 3rem);
}
```

---

## Testing Responsive

### Checklist de Validación

#### Mobile (320-767px)
- [ ] Sidebar se abre como overlay
- [ ] Hamburger menu funciona
- [ ] Cards en 1 columna
- [ ] Tablas con scroll horizontal
- [ ] Touch targets mínimo 44px
- [ ] Sin hover states persistentes
- [ ] Fuentes mínimo 16px (evita zoom iOS)

#### Tablet (768-1023px)  
- [ ] Grid de 2 columnas
- [ ] Sidebar sigue como overlay
- [ ] Búsqueda visible en header
- [ ] Espaciado aumentado

#### Desktop (1024px+)
- [ ] Sidebar fijo visible
- [ ] Sin hamburger menu
- [ ] Grid de 3-4 columnas
- [ ] Hover states activos
- [ ] Layout máximo 1440px centrado

### Dispositivos de Prueba Críticos

```javascript
// Viewports a testear obligatoriamente
const criticalViewports = [
  { name: 'iPhone SE', width: 375, height: 667 },
  { name: 'iPhone 12', width: 390, height: 844 },
  { name: 'iPad', width: 768, height: 1024 },
  { name: 'Desktop', width: 1280, height: 800 },
  { name: 'Wide', width: 1920, height: 1080 }
]
```

### Debug Helper Component

```vue
<!-- DebugResponsive.vue -->
<template>
  <div v-if="showDebug" class="debug-responsive">
    <div>Width: {{ windowWidth }}px</div>
    <div>
      Mode: 
      <span v-if="isMobile">Mobile</span>
      <span v-else-if="isTablet">Tablet</span>
      <span v-else>Desktop</span>
    </div>
    <div>Sidebar: {{ sidebarOpen ? 'Open' : 'Closed' }}</div>
  </div>
</template>

<style scoped>
.debug-responsive {
  position: fixed;
  bottom: 1rem;
  right: 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--primary);
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-family: monospace;
  z-index: 9999;
  color: var(--text-primary);
}
</style>
```

---

## 📋 Resumen Ejecutivo

### Reglas de Oro del Responsive

1. **Mobile First SIEMPRE**: Escribir CSS para mobile, luego expandir
2. **Sidebar Overlay < 1024px**: Solo fijo en desktop real
3. **Grid Adaptativo**: 1 → 2 → 4 columnas según breakpoint
4. **Touch Targets**: Mínimo 44x44px en mobile
5. **Scroll Horizontal**: Para tablas complejas en mobile
6. **Sin Hover en Touch**: Estados hover solo en desktop
7. **Tipografía Fluida**: Usar clamp() para tamaños adaptativos

### Estructura de Archivos

```
resources/js/
├── composables/
│   ├── useResponsive.js    # Lógica responsive
│   └── useTheme.js         # Toggle dark/light
├── components/
│   ├── layout/
│   │   ├── MainLayout.vue  # Layout principal
│   │   ├── Sidebar.vue     # Sidebar component
│   │   └── Header.vue      # Header component
│   └── common/
│       ├── MetricCard.vue  # Cards responsive
│       └── DataTable.vue   # Tabla responsive
└── styles/
    ├── responsive.css       # Media queries
    └── components.css       # Estilos componentes
```

### Orden de Implementación

1. **Layout base** con sidebar y header
2. **Dashboard cards** responsive
3. **Tablas** con scroll o transformación
4. **Formularios** adaptativos
5. **Testing** en dispositivos reales

---

*Sistema Responsive definitivo BAMBU v1.0 - Seguir estrictamente el mobile-first approach*