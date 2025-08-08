# 🏗️ PLAN CONCRETO DE DESARROLLO - PARTE 1: FOUNDATIONS Y FRONT-END

**Sistema**: BAMBU CRM - Productos Químicos Alto Valle  
**Fecha**: 2025-08-08  
**Estado**: CRÍTICO - Foundations del sistema  
**Método**: Patrón Canónico del Revisor Senior  

---

## 🚨 DIAGNÓSTICO DEL PROBLEMA ACTUAL

### ❌ BUG IDENTIFICADO EN NUESTRO CÓDIGO
```css
/* responsive.css línea 39 - ESTE ES EL PROBLEMA */
@media (min-width: 1024px) {
  .main-wrapper {
    margin-left: 280px;  /* ← ESTO CAUSA EL GAP MARRÓN */
  }
}
```

**¿Por qué falla?** Estamos aplicando `margin-left` cuando el sidebar debería ser una columna real del flex en desktop, NO un elemento fixed que necesita compensación.

### 📋 Análisis de Assets Actuales

✅ **LO QUE ESTÁ BIEN:**
- `tokens.css`: Excelente, variables completas y bien organizadas
- `app.css`: Reset CSS sólido, entry point limpio
- Font Inter cargada correctamente

❌ **LO QUE ESTÁ MAL:**
- `responsive.css`: Sidebar no es columna real en desktop
- `components.css`: No sigue el patrón grid del header
- Doble fuente de offset (sidebar position + margin-left)
- Z-index no sigue jerarquía correcta

---

## 🎯 INVARIANTES DEL SISTEMA (NUNCA ROMPER)

### 1️⃣ Una Sola Verdad del Ancho
```css
:root {
  --sidebar-w: 280px;  /* ÚNICA definición */
}
```

### 2️⃣ Modos Exclusivos por Breakpoint
- **< 1024px**: Sidebar es OVERLAY (position: fixed, fuera del flujo)
- **≥ 1024px**: Sidebar es COLUMNA REAL (flex: 0 0 var(--sidebar-w), dentro del flujo)

### 3️⃣ Regla de Oro del Layout
**Si sidebar es columna → main NO lleva margin-left**

### 4️⃣ Header Siempre Grid
```css
.header {
  display: grid;
  grid-template-columns: auto 1fr auto;  /* hamburguesa | búsqueda | acciones */
}
```

### 5️⃣ Contenedores con min-width: 0
Todo elemento que crece con flex necesita `min-width: 0` para evitar desborde

---

## 📐 ESTRUCTURA HTML CANÓNICA

```vue
<div class="app">                <!-- Contenedor principal flex -->
  <aside class="sidebar">        <!-- Columna real en desktop, overlay en mobile -->
    <!-- nav content -->
  </aside>
  
  <div class="shell">            <!-- Columna flexible que crece -->
    <header class="header">      <!-- Grid auto 1fr auto -->
      <button class="btn-icon">  <!-- Hamburguesa (solo mobile) -->
      <div class="search">       <!-- Búsqueda centrada -->
      <div class="actions">      <!-- Acciones derecha -->
    </header>
    
    <main class="content">       <!-- Área de contenido -->
      <router-view />
    </main>
  </div>
  
  <button class="scrim" />      <!-- Overlay (solo mobile cuando abierto) -->
</div>
```

---

## 🔧 PLAN DE IMPLEMENTACIÓN FASE POR FASE

### 📦 FASE 0: PREPARACIÓN [15 min]
**Objetivo**: Limpiar y preparar el terreno

1. **Backup del estado actual**
   ```bash
   git add -A
   git commit -m "backup: antes de implementar patrón canónico sidebar"
   ```

2. **Crear branch de trabajo**
   ```bash
   git checkout -b fix/patron-canonico-sidebar
   ```

3. **Identificar archivos a modificar**
   - `resources/css/components.css` - Reescribir layout
   - `resources/css/responsive.css` - Eliminar margin-left problemático
   - `resources/js/composables/useResponsive.js` - Crear/actualizar
   - `resources/js/layouts/AppShell.vue` - Crear nuevo

---

### 📦 FASE 1: VARIABLES CSS [30 min]
**Objetivo**: Establecer tokens mínimos necesarios

#### 1.1 Agregar a `tokens.css`:
```css
/* LAYOUT ESPECÍFICO */
--sidebar-w: 280px;        /* Ancho sidebar - ÚNICA definición */
--header-h: 64px;          /* Alto header fijo */
--btn-icon-size: 40px;     /* Tamaño botón ícono */
--icon-size: 16px;         /* Tamaño SVG interno */
```

#### 1.2 Ajustar z-index en `tokens.css`:
```css
/* Z-INDEX JERARQUÍA CORRECTA */
--z-base: 0;
--z-elevated: 10;
--z-header: 40;        /* Header sticky */
--z-scrim: 50;         /* Overlay del sidebar */
--z-sidebar: 60;       /* Sidebar sobre overlay */
--z-modal: 80;         /* Modales sobre todo */
--z-toast: 90;         /* Notificaciones máxima prioridad */
```

✅ **Checkpoint**: Variables definidas una sola vez

---

### 📦 FASE 2: CSS CANÓNICO [1 hora]
**Objetivo**: Implementar CSS exacto del revisor

#### 2.1 Nuevo `components-layout.css`:
```css
/* ============================================
   RESET ESENCIAL
   ============================================ */
* { box-sizing: border-box; }
html, body, #app { height: 100%; }
body { 
  margin: 0; 
  background: var(--bg-app); 
  color: var(--text-primary);
}

/* ============================================
   APP SHELL - PATRÓN CANÓNICO
   ============================================ */
.app {
  min-height: 100%;
  display: flex;
}

/* ============================================
   SIDEBAR - MOBILE FIRST (OVERLAY)
   ============================================ */
.sidebar {
  position: fixed;
  inset: 0 auto 0 0;  /* top right bottom left */
  width: var(--sidebar-w);
  max-width: 90vw;
  transform: translateX(-100%);
  transition: transform 0.2s ease;
  background: var(--bg-surface);
  border-right: 1px solid var(--border);
  z-index: var(--z-sidebar);
}

.sidebar.is-open {
  transform: translateX(0);
}

/* ============================================
   SCRIM (OVERLAY)
   ============================================ */
.scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  border: 0;
  padding: 0;
  margin: 0;
  z-index: var(--z-scrim);
}

/* ============================================
   SHELL (COLUMNA FLEXIBLE)
   ============================================ */
.shell {
  flex: 1 1 auto;
  min-width: 0;  /* CRÍTICO para evitar desborde */
  display: flex;
  flex-direction: column;
}

/* ============================================
   HEADER - GRID INFALIBLE
   ============================================ */
.header {
  position: sticky;
  top: 0;
  z-index: var(--z-header);
  height: var(--header-h);
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: var(--space-md);
  padding: 0 var(--space-md);
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
}

.header-search {
  max-width: 640px;
}

/* Mobile: ocultar búsqueda */
@media (max-width: 767px) {
  .header-search { display: none; }
}

/* ============================================
   BOTÓN ÍCONO - CONTRATO FIJO
   ============================================ */
.btn-icon {
  width: var(--btn-icon-size);
  height: var(--btn-icon-size);
  display: grid;
  place-items: center;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  background: transparent;
  cursor: pointer;
  transition: var(--transition-fast);
}

.btn-icon svg {
  width: var(--icon-size);
  height: var(--icon-size);
  display: block;
  flex-shrink: 0;
}

.btn-icon:hover {
  background: var(--bg-elevated);
  border-color: var(--border-hover);
}

/* ============================================
   CONTENT AREA
   ============================================ */
.content {
  padding: 24px;
  min-width: 0;
  flex: 1;
}

/* ============================================
   DESKTOP: SIDEBAR COMO COLUMNA REAL
   ============================================ */
@media (min-width: 1024px) {
  /* Sidebar entra al flujo como columna */
  .sidebar {
    position: relative;
    transform: none;
    z-index: auto;
    height: auto;
    inset: unset;
    flex: 0 0 var(--sidebar-w);
    width: var(--sidebar-w);
  }
  
  /* No hay overlay en desktop */
  .scrim { display: none; }
  
  /* SIN margin-left - sidebar es columna real */
  .shell { margin-left: 0; }
  
  /* Hamburguesa oculta en desktop */
  .hamburger { display: none; }
}
```

✅ **Checkpoint**: CSS implementado sin modificaciones

---

### 📦 FASE 3: COMPONENTE VUE [1.5 horas]
**Objetivo**: Crear AppShell.vue siguiendo estructura exacta

#### 3.1 Crear `useResponsive.js`:
```javascript
// composables/useResponsive.js
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'

export function useResponsive() {
  const sidebarOpen = ref(false)
  const isMobile = ref(true)
  
  // Media query para desktop
  const mq = window.matchMedia('(min-width: 1024px)')
  
  // Actualizar estado mobile/desktop
  const update = () => {
    isMobile.value = !mq.matches
    // Auto-cerrar sidebar al cambiar a desktop
    if (!isMobile.value) {
      sidebarOpen.value = false
    }
  }
  
  // Toggle sidebar
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
  }
  
  // Cerrar sidebar
  const closeSidebar = () => {
    sidebarOpen.value = false
  }
  
  // Manejar tecla Escape
  const handleEscape = (e) => {
    if (e.key === 'Escape' && sidebarOpen.value) {
      closeSidebar()
    }
  }
  
  // Control de scroll del body
  watch(sidebarOpen, (isOpen) => {
    if (isOpen && isMobile.value) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  })
  
  // Lifecycle
  onMounted(() => {
    update()
    mq.addEventListener('change', update)
    window.addEventListener('keydown', handleEscape)
  })
  
  onBeforeUnmount(() => {
    mq.removeEventListener('change', update)
    window.removeEventListener('keydown', handleEscape)
    document.body.style.overflow = ''
  })
  
  return {
    sidebarOpen,
    isMobile,
    isDesktop: computed(() => !isMobile.value),
    toggleSidebar,
    closeSidebar
  }
}
```

#### 3.2 Crear `AppShell.vue`:
```vue
<!-- layouts/AppShell.vue -->
<template>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'is-open': isMobile && sidebarOpen }">
      <div class="sidebar-header">
        <img src="/logo.svg" alt="BAMBU" class="logo">
        <span class="logo-text">BAMBU</span>
      </div>
      
      <nav class="sidebar-nav">
        <router-link 
          v-for="item in menuItems" 
          :key="item.path"
          :to="item.path" 
          class="nav-link"
          @click="isMobile && closeSidebar()"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span class="nav-text">{{ item.label }}</span>
        </router-link>
      </nav>
    </aside>
    
    <!-- Shell -->
    <div class="shell">
      <!-- Header -->
      <header class="header">
        <button 
          v-if="isMobile" 
          class="btn-icon hamburger"
          @click="toggleSidebar"
          aria-label="Abrir menú"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 12h18M3 6h18M3 18h18" stroke-width="2"/>
          </svg>
        </button>
        
        <div class="header-search">
          <input 
            type="search" 
            class="search-input"
            placeholder="Buscar productos, clientes..."
          >
        </div>
        
        <div class="header-actions">
          <button class="btn-icon" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M10 2v2a6 6 0 016 6c0 1.7.3 3.2.9 4.5l.6 1.5H2.5l.6-1.5C3.7 13.2 4 11.7 4 10a6 6 0 016-6V2a2 2 0 114 0zM9 20h4a2 2 0 11-4 0z"/>
            </svg>
          </button>
          
          <button class="btn-icon" aria-label="Configuración">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
              <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
          </button>
        </div>
      </header>
      
      <!-- Content -->
      <main class="content" role="main">
        <router-view />
      </main>
    </div>
    
    <!-- Scrim (overlay mobile) -->
    <button 
      v-if="isMobile && sidebarOpen"
      class="scrim"
      @click="closeSidebar"
      aria-label="Cerrar menú"
    />
  </div>
</template>

<script setup>
import { useResponsive } from '@/composables/useResponsive'

const { sidebarOpen, isMobile, toggleSidebar, closeSidebar } = useResponsive()

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: '📊' },
  { path: '/productos', label: 'Productos', icon: '🧪' },
  { path: '/clientes', label: 'Clientes', icon: '👥' },
  { path: '/pedidos', label: 'Pedidos', icon: '📋' },
  { path: '/stock', label: 'Stock', icon: '📦' },
]
</script>
```

✅ **Checkpoint**: Estructura Vue exacta implementada

---

### 📦 FASE 4: TESTING Y VALIDACIÓN [30 min]
**Objetivo**: Verificar que todo funciona sin bugs

#### 4.1 Checklist de Estructura
- [ ] Solo existe una definición de `--sidebar-w`
- [ ] En desktop, `.sidebar` no usa `position: fixed`
- [ ] `.shell` y `.content` tienen `min-width: 0`
- [ ] Header usa Grid `auto 1fr auto`

#### 4.2 Checklist Responsive
- [ ] En <1024px, sidebar abre con clase `is-open`
- [ ] En <1024px, hay `.scrim` visible
- [ ] En <1024px, body tiene `overflow: hidden` cuando abierto
- [ ] En ≥1024px, sidebar no tiene overlay
- [ ] En ≥1024px, NO hay `margin-left` en el main

#### 4.3 Checklist Accesibilidad
- [ ] Botón hamburguesa con `aria-label`
- [ ] Overlay clickeable para cerrar
- [ ] Tecla Esc cierra el sidebar
- [ ] Focus visible en elementos interactivos

#### 4.4 Checklist Render
- [ ] No hay reflow al cruzar 1024px
- [ ] No hay gap marrón entre sidebar y contenido
- [ ] Iconos perfectamente centrados
- [ ] Tablas con scroll horizontal en mobile

---

## 🚨 PITFALLS A EVITAR

### ❌ NO HACER:
1. **NO agregar margin-left al shell en desktop** - sidebar es columna real
2. **NO usar position: fixed en sidebar desktop** - debe ser relative
3. **NO definir sidebar-w en múltiples lugares** - solo en tokens
4. **NO mezclar flex y grid en el header** - siempre grid
5. **NO olvidar min-width: 0** - causa desborde

### ✅ SIEMPRE HACER:
1. **Mobile-first** - empezar con overlay, luego columna
2. **Una fuente de verdad** - todas las variables en tokens
3. **Contrato fijo botones** - 40×40px con SVG 16×16
4. **Body overflow control** - bloquear scroll cuando sidebar abierto
5. **Testing en breakpoint** - verificar transición 1023px → 1024px

---

## 📊 MÉTRICAS DE ÉXITO

✅ **Layout Perfecto**
- Sin gaps ni superposiciones
- Transiciones suaves entre breakpoints
- Sin reflows ni saltos visuales

✅ **Responsive Real**
- Funciona en iPhone SE (375px)
- Sidebar accesible con teclado
- Touch targets ≥44px

✅ **Performance**
- CSS < 50KB total
- Sin re-renders innecesarios
- Transiciones en 60fps

✅ **Mantenibilidad**
- Código siguiendo patrón único
- Sin hacks ni workarounds
- Documentado y testeable

---

## 🎯 RESULTADO ESPERADO

Al completar este plan tendremos:

1. **App Shell infalible** sin bugs de layout
2. **Sidebar responsive** que funciona en todos los dispositivos
3. **Header con grid** perfectamente alineado
4. **Base sólida** para construir el resto del CRM

**Tiempo estimado total**: 4 horas de implementación cuidadosa

---

**IMPORTANTE**: Este es el foundation del sistema. No avanzar hasta que esté 100% funcional y sin bugs. La solidez de todo el CRM depende de estas bases.