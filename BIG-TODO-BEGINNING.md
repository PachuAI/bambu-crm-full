# BAMBU CRM - PLAN MAESTRO MOBILE-FIRST IMPLEMENTATION

**Fecha Inicio**: 2025-08-08  
**Desarrollado por**: ÍTERA (https://iteraestudio.com)  
**Stack**: Laravel 11 + Vue 3 + PostgreSQL + Tailwind 4 + Sanctum  

## 🎯 CONTEXTO Y OBJETIVO

Este documento contiene el plan maestro completo para implementar el sistema frontend BAMBU CRM siguiendo metodología mobile-first rigurosa. El plan consta de **11 fases** con **55 tareas** específicas, cada una con criterios de validación y testing.

### Stack Tecnológico
- **Backend**: Laravel 11, PostgreSQL, 113 tests pasando ✅
- **Frontend**: Vue 3, Tailwind 4.0, CSS Variables
- **Autenticación**: Laravel Sanctum  
- **Testing**: Playwright (E2E), Chromatic/Percy (Visual)
- **Tema**: Dark mode por defecto (industria profesional)
- **Responsive**: Mobile-first (320px → 1440px+)

### Estado Actual del Proyecto
- **Backend**: 100% completo con 113 tests pasando
- **Frontend**: Tokens CSS y composables base completados
- **Documentación**: Sistema completo definido
- **Siguiente paso**: Implementar MainLayout.vue mobile-first

---

## 📋 PLAN COMPLETO - 11 FASES

### ✅ FASE 0: Preparación del entorno y tokens CSS unificados [COMPLETADA]
**Estado**: ✅ COMPLETADA  

#### ✅ 1.1 Crear archivo tokens.css con todas las variables del sistema
**Archivo**: `/resources/css/tokens.css`
- ✅ Variables de color completas (dark/light mode)
- ✅ Spacing system (--space-xs a --space-3xl)
- ✅ Typography scale fluida mobile-first
- ✅ Shadows, transitions, z-index layers
- ✅ Variables específicas dominio químico (stock, prioridades, productos)
- ✅ Modos de densidad (alta densidad admin, modo guantes logística)

#### ✅ 1.2 Estructura CSS definitiva implementada
**Archivos creados**:
- ✅ `app.css` - Entry point con imports ordenados
- ✅ `tokens.css` - Única fuente de verdad para variables  
- ✅ `components.css` - Estilos componentes base
- ✅ `responsive.css` - Media queries mobile-first completo

#### ✅ 1.3 Composables base creados
**Archivos**:
- ✅ `useTheme.ts` - Dark/light theme + FOUC prevention
- ✅ `useResponsive.ts` - Breakpoints + sidebar + focus-trap + accessibility
- ✅ `useBambuApi.js` - API client con cache + error handling + batch ops

#### ⏳ 1.4 Configurar herramientas de testing visual (Storybook/Chromatic)
**Pendiente** - Configurar Storybook + Chromatic para regresión visual

#### ⏳ 1.5 Setup de testing E2E con Playwright 
**Pendiente** - Playwright config para testing responsive 5 viewports

---

### ✅ FASE 1: Dashboard Mobile (320px-767px) - Desarrollo Base [COMPLETADA]
**Estado**: ✅ COMPLETADA

#### ✅ 2.1 Implementar MainLayout.vue con sidebar overlay mobile-first [COMPLETADA]
**Archivo**: `/resources/js/components/MainLayout.vue`
**Requerimientos**:
- Sidebar overlay (translateX animation) 
- Hamburger menu responsive
- Focus-trap automático en mobile
- `body overflow: hidden` cuando sidebar abierto
- `main` con `inert` attribute durante overlay
- Escape key listener
- Auto-close en resize a desktop

#### ✅ 2.2 Crear MetricCard.vue responsive - 1 columna mobile [COMPLETADA]
**Archivo**: `/resources/js/components/MetricCard.vue`
**Requerimientos**:
- Grid 1 columna en mobile (320px-767px)
- Cards con padding responsive
- Íconos + valores grandes legibles
- Loading states animados
- Touch targets 48px mínimo

#### ✅ 2.3 Implementar hamburger menu funcional con animación [COMPLETADA]
**Integrado en MainLayout.vue**:
- Animación X cuando abierto (CSS transforms)
- 3 líneas → X transition suave
- Touch target 48px
- Estados hover/active accesibles

#### ✅ 2.4 TEST: Validar en iPhone SE (375px) y Android (360px) [APROBADO]
**Validaciones**:
- Todo el contenido visible sin scroll horizontal
- Touch targets accesibles
- Texto legible sin zoom
- Sidebar overlay funcionando

#### ✅ 2.5 TEST: Touch targets mínimo 48px para operarios con guantes [APROBADO]
**Componentes a validar**:
- Botones principales
- Links de sidebar  
- Hamburger menu
- Form inputs
- Cards clickables

#### ✅ 2.6 TEST: Sidebar focus-trap + tecla Esc + overflow control [APROBADO]
**Funcionalidades**:
- Focus se mueve a primer elemento sidebar
- Tab cycle solo dentro del sidebar
- Esc cierra sidebar
- Main content con `inert`
- Body sin scroll cuando abierto

---

### ⏳ FASE 2: Métricas y KPIs Mobile - Integración Backend
**Estado**: ⏳ PENDIENTE

#### ⏳ 3.1 Conectar MetricCards con API real (/api/v1/dashboard/metrics)
**Endpoint**: `GET /api/v1/dashboard/metrics`
**Data esperada**:
```javascript
{
  ventas_mes: { valor: 150000, variacion: 12 },
  pedidos_pendientes: { valor: 23, urgentes: 5 },
  stock_critico: { valor: 8, productos: [...] },
  clientes_activos: { valor: 45, nuevos: 3 }
}
```

#### ⏳ 3.2 Implementar StockIndicator.vue con estados no-solo-color
**Archivo**: `/resources/js/components/StockIndicator.vue`
**Estados**: Alto (✅), Medio (⚠️), Bajo (🔴), Agotado (⚫)
**Requerimientos WCAG**:
- Ícono + texto + color
- Contraste AA (4.5:1 minimum)
- Screen reader friendly

#### ⏳ 3.3 Crear sistema de alertas críticas
**Tipos de alertas**:
- Stock bajo productos críticos
- Productos próximos a vencer
- Pedidos vencidos
- Clientes morosos

#### ⏳ 3.4-3.6 Tests de estados, contraste y pictogramas GHS/ADR
**Validaciones específicas industria química**

---

### ⏳ FASE 3: Charts y Gráficos Mobile - Adaptación Visual
**Estado**: ⏳ PENDIENTE

#### ⏳ 4.1 Implementar ChartContainer.vue con Chart.js responsive
**Archivo**: `/resources/js/components/ChartContainer.vue`
**Chart.js config mobile-optimized**:
- Responsive: true
- maintainAspectRatio: false  
- Touch gestures enabled
- Simplified tooltips
- Larger touch targets

#### ⏳ 4.2 Adaptar gráfico de ventas para viewport mobile
**Simplificaciones**:
- Menos puntos de datos
- Labels rotados/abreviados  
- Legend posición optimizada
- Colores accesibles

#### ⏳ 4.3 Gráfico productos top (horizontal bars para mobile)
**Mejor para mobile**:
- Barras horizontales (más espacio para labels)
- Max 5-8 productos mostrados
- Scroll vertical si necesario

#### ⏳ 4.4-4.5 Tests touch gestures y performance

---

### ⏳ FASE 4: Tablas Mobile - Transformación Responsive
**Estado**: ⏳ PENDIENTE

#### ⏳ 5.1 ProductosTable.vue con scroll horizontal
**Fallback para tablas complejas**:
- Scroll horizontal suave
- Sticky primera columna
- Touch scroll optimizado
- Indicador de más contenido

#### ⏳ 5.2 Modo cards para tablas en mobile
**CSS transformation**:
- `display: block` en mobile
- Cada row → card individual
- Labels antes de cada dato
- CSS `::before` con `attr(data-label)`

#### ⏳ 5.3 Acciones rápidas por swipe
**Interacciones táctiles**:
- Swipe left: acciones secundarias
- Swipe right: acción primaria  
- Visual feedback
- Fallback para no-touch

---

### ⏳ FASE 5: Tablet (768px-1023px) - Expansión Layout
**Estado**: ⏳ PENDIENTE

#### ⏳ 6.1 Grid 2 columnas para métricas
**Media query**: `@media (min-width: 768px)`
- MetricCard grid: 2 columnas
- Gap aumentado a `--space-lg`
- Cards más altos (mejor ratio)

#### ⏳ 6.2 Sidebar overlay 280px width
**Tablet optimizations**:
- Ancho sidebar: 280px (más contenido)
- Overlay backdrop más sutil
- Transición más rápida

#### ⏳ 6.3 Búsqueda visible en header
**Header expandido**:
- Search input visible
- Flex layout optimizado
- Max-width 400px

---

### ⏳ FASE 6: Desktop (1024px+) - Layout Completo
**Estado**: ⏳ PENDIENTE

#### ⏳ 7.1 Sidebar fijo permanente
**Media query**: `@media (min-width: 1024px)`
- `position: fixed` sidebar
- `margin-left: var(--sidebar-width)` en main
- No overlay, siempre visible
- Hamburger hidden

#### ⏳ 7.2 Grid 4 columnas métricas
**Desktop grid**:
- 4 columnas responsive grid
- Cards más compactos pero informativos
- Mejor uso espacio horizontal

#### ⏳ 7.3 Hover states activados
**Solo desktop**:
- Card lift on hover (`translateY(-2px)`)
- Button hover effects
- Transition smooth animations
- Focus-visible mejorado

---

### ⏳ FASE 7: Sistema Dark/Light Theme
**Estado**: ⏳ PENDIENTE

#### ⏳ 8.1 Toggle tema con localStorage
**Implementation**:
- Button en sidebar/header
- Persist preferencia usuario
- Sync entre tabs
- Smooth transition

#### ⏳ 8.2-8.5 Tests contraste, transiciones, FOUC prevention

---

### ⏳ FASE 8: Testing Visual Completo
**Estado**: ⏳ PENDIENTE

#### ⏳ 9.1 Regresión visual con Chromatic
**Setup**:
- All components en Storybook
- Visual regression tests
- Cross-browser validation
- Mobile/desktop comparisons

#### ⏳ 9.2 E2E testing 5 viewports
**Playwright tests**:
- 320px (iPhone SE)
- 375px (iPhone standard)  
- 768px (iPad portrait)
- 1024px (Desktop small)
- 1440px (Desktop large)

#### ⏳ 9.3-9.5 Accessibility, Performance, Real devices

---

### ⏳ FASE 9: Optimización Production
**Estado**: ⏳ PENDIENTE

#### ⏳ 10.1-10.5 Lazy loading, Bundle optimization, PWA, Caching

---

### ⏳ FASE 10: Documentación y Handoff  
**Estado**: ⏳ PENDIENTE

#### ⏳ 11.1-11.5 Storybook docs, Guías dev, Reports, Video demo

---

## 🧪 ESPECIFICACIONES TÉCNICAS CRÍTICAS

### Breakpoints Sistema BAMBU
```css
/* Mobile first approach */
mobile: 0-767px     /* 1 columna, overlay sidebar */
tablet: 768-1023px  /* 2 columnas, overlay sidebar 280px */
desktop: 1024px+    /* 4 columnas, fixed sidebar */
wide: 1440px+       /* Container max-width constrained */
```

### CSS Variables Core
```css
/* Colores principales */
--brand-hue: 238 (violeta BAMBU)
--primary: hsl(238, 84%, 67%)
--bg-base: hsl(238, 15%, 5%)     /* Dark mode default */
--text-primary: hsl(238, 5%, 95%)

/* Spacing system */
--space-xs: 4px
--space-sm: 8px  
--space-md: 16px
--space-lg: 24px
--space-xl: 32px

/* Touch targets críticos */
--touch-target-min: 48px  /* Operarios con guantes */
```

### API Endpoints Principales
```javascript
// Dashboard
GET /api/v1/dashboard/metrics
GET /api/v1/dashboard/stock-status  
GET /api/v1/dashboard/alertas
GET /api/v1/dashboard/ventas?periodo=30d
GET /api/v1/dashboard/productos-top?limit=10

// Entidades principales  
GET /api/v1/productos
GET /api/v1/clientes  
GET /api/v1/pedidos
GET /api/v1/cotizaciones
```

### Testing Checklist Per Fase
- [ ] 5 viewports validation (320px, 375px, 768px, 1024px, 1440px)
- [ ] Touch targets ≥48px validation  
- [ ] Focus-trap and keyboard navigation
- [ ] WCAG AA contrast (4.5:1 minimum)
- [ ] Dark/light theme both working
- [ ] Loading states and error handling
- [ ] No horizontal scroll on any breakpoint
- [ ] Performance < 200ms API responses

---

## 📝 NOTAS IMPORTANTES

### Reglas Imperativas Activas
1. **Mobile-first SIEMPRE**: Diseñar desde 320px hacia arriba
2. **No hardcoding colors**: Solo CSS variables del tokens.css
3. **Touch targets 48px**: Especialmente crítico para operarios 
4. **Focus-trap obligatorio**: Sidebar overlay debe bloquear navegación
5. **Dark theme default**: Industria profesional prefiere dark
6. **Testing riguroso**: Cada componente testeado en 5 viewports
7. **Accesibilidad WCAG AA**: No negociable para aplicación profesional

### Archivos Base Completados ✅
- `tokens.css` - Única fuente de verdad variables
- `app.css` - Entry point CSS  
- `components.css` - Componentes base
- `responsive.css` - Media queries completas
- `useTheme.ts` - Composable tema dark/light
- `useResponsive.ts` - Composable breakpoints + sidebar
- `useBambuApi.js` - Client API con cache + error handling

### Próximo Paso Inmediato
**Implementar MainLayout.vue mobile-first** con:
1. Sidebar overlay funcional
2. Hamburger menu animado
3. Focus-trap accesibilidad 
4. Responsive grid preparado
5. Testing en iPhone SE (375px)

---

**Última actualización**: 2025-08-08  
**Progreso**: FASE 0 completada ✅, FASE 1 completada ✅, FASE 2 iniciando 🚧  
**Siguiente milestone**: Integración backend API + StockIndicator.vue