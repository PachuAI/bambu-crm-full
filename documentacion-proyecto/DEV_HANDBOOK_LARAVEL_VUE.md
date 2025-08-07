# 📚 DEV HANDBOOK - Laravel + Vue SPA Projects

**Biblia de lecciones aprendidas para proyectos SaaS con Laravel + Vue**

---

## 🚨 CSS & FRONTEND GOTCHAS

### ⚡ CSS Reset Global Disaster
```css
❌ JAMÁS HACER ESTO:
* { margin: 0; padding: 0; }
```
**💥 IMPACTO**: Bloqueaba TODAS las clases Tailwind (space-y-*, mb-*, gap-*, etc.)  
**🐛 SÍNTOMA**: Componentes sin espaciado, layout roto sin razón aparente  
**✅ SOLUCIÓN**: Reset específico solo html/body
```css
✅ HACER ESTO:
html, body {
  margin: 0;
  padding: 0;
}
```
**📝 LECCIÓN**: Nunca usar reset universal en proyectos Tailwind

### 🎨 Tailwind 4 + CSS Variables - Arquitectura Híbrida que FUNCIONA
```yaml
✅ ESTRATEGIA GANADORA:
  Tailwind 4:
    - Estructura, spacing, layout, responsive
    - Utilities (flex, grid, text-*, etc.)
    - Mejor DX con autocomplete
    
  CSS Variables:  
    - SOLO colores para theming (dark/light mode)
    - Control centralizado de paleta
    - Cambio dinámico sin rebuild
```

**📝 LECCIÓN**: No elegir uno u otro, usar ambos para lo que son mejores

**🔍 BENEFICIOS COMPROBADOS**:
- Escalabilidad a largo plazo ✅
- Mejor DX que CSS puro ✅  
- Flexibilidad de theming que Tailwind dark: no ofrece ✅
- Mantenimiento más sencillo ✅

---

## 🔧 LARAVEL + VUE SPA SETUP

### 🛡️ Sanctum SPA Authentication - Configuración que FUNCIONA

```php
// .env - CRÍTICO
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000,localhost:3000
SESSION_DRIVER=database
```

```javascript
// axios config - ESENCIAL
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://127.0.0.1:8000';
```

```javascript
// Vue Router Guards - PATRÓN
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});
```

**📝 LECCIÓN**: Session-based auth mejor que tokens para SPA  
**⚠️ CUIDADO**: CSRF token necesario en formularios

### 🗄️ PostgreSQL vs MySQL - Decisión Estratégica

```yaml
✅ POSTGRESQL 15+ ELEGIDO:
  Razones:
    - JSON fields nativos (sin stringify/parse)
    - Performance superior en consultas complejas  
    - Tipos avanzados (arrays, enums reales)
    - Mejor para analytics y reportes
  
  Requerimientos:
    - Extensions PHP: pdo_pgsql, pgsql (OBLIGATORIAS)
    - createdb bambu_sistema_v2 (comando inicial)
```

**⚠️ GOTCHA**: Extensions PHP no instaladas por defecto en algunos ambientes  
**📝 LECCIÓN**: Verificar extensions antes de migrar

---

## 🎨 UI/UX PATTERNS QUE FUNCIONAN

### 🎯 Design System Híbrido - No Reinventar la Rueda

```yaml
✅ INSPIRACIÓN TREZO THEME:
  Paleta Primary: #6366f1 (indigo-600)
  Background: slate-900 (#0f172a) 
  Cards: slate-800 (#1e293b)
  Borders: slate-700 (#334155)
  
  Tema Default: Oscuro (más profesional)
  Tema Opcional: Claro (UX específica)
```

**📝 LECCIÓN**: Inspirarse en temas probados, no crear desde cero  
**🎨 PATRÓN**: Modo oscuro por defecto para apps profesionales

### 🏗️ Componentes Vue 3 - Patrones Exitosos

```vue
✅ PATRÓN COMPOSITION API:
<script setup lang="ts">
// Imports tipados
import { reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

// Props tipadas
defineProps<{
  title: string
  loading?: boolean
}>()

// Store pattern
const authStore = useAuthStore()

// Estado reactivo
const form = reactive({
  email: '',
  password: ''
})
</script>
```

**📝 LECCIÓN**: Composition API + TypeScript = Mejor DX que Options API

---

## 🧪 TESTING STRATEGY

### 📊 Test Coverage que IMPORTA

```yaml
✅ PRIORIDADES TESTING (orden de importancia):
  1. API Endpoints - CRUD completo (ProductoApiTest.php)
  2. Modelos Eloquent - Relaciones, scopes (ProductoModelTest.php)  
  3. Base de Datos - Estructura, constraints (DatabaseTest.php)
  4. Authentication - Login/logout flows
  
  TARGET: 70%+ coverage, NO MÁS
  RAZÓN: ROI decreciente después del 70%
```

**🎯 RESULTADO COMPROBADO**: 72/72 tests pasando (491 assertions)  
**📝 LECCIÓN**: Calidad > Cantidad en tests

### 🧬 Tests que VALEN LA PENA

```php
✅ TESTS CRÍTICOS:
// API completo
public function test_can_create_producto_with_valid_data()
public function test_can_update_producto() 
public function test_can_delete_producto()
public function test_validates_required_fields()

// Modelos relationships  
public function test_producto_has_many_pedido_items()
public function test_cliente_has_many_pedidos()

// Database constraints
public function test_foreign_keys_work()
public function test_unique_constraints()
```

---

## 📦 STACK DECISIONS - Por Qué Elegimos Qué

### 🚀 Backend Framework - Laravel 11

```yaml
✅ RAZONES LARAVEL 11:
  - Ecosystem maduro y estable
  - Filament admin panel (GAME CHANGER)
  - Sanctum auth built-in
  - Eloquent ORM robusto
  - Testing suite excelente
  - Deploy simple (no microservicios)
  
❌ ALTERNATIVAS DESCARTADAS:
  - Symfony: Curva aprendizaje alta
  - CodeIgniter: Ecosystem limitado
  - Raw PHP: Reinventar rueda
```

### 🎨 Frontend Framework - Vue 3 + TypeScript

```yaml
✅ RAZONES VUE 3:
  - Mejor DX que React para equipos pequeños
  - Composition API potente + flexible
  - Pinia state management simple
  - Ecosystem Laravel-friendly
  - Menos boilerplate que React
  - SSR opcional (Nuxt si necesario)
  
❌ REACT DESCARTADO:
  - Overhead innecesario proyectos pequeños
  - Ecosystem más fragmentado
  - Más verboso (JSX, props drilling)
```

### 🛠️ Admin Panel - Filament v3

```yaml  
✅ RAZONES FILAMENT:
  - Integración nativa Laravel
  - Resources automáticos desde modelos
  - Sistema permisos built-in  
  - UI profesional out-of-the-box
  - Extensible y customizable
  
❌ ALTERNATIVAS DESCARTADAS:
  - Laravel Nova: Paid, menos flexible
  - Custom admin: Tiempo desarrollo alto
  - WordPress admin: No fit para SaaS
```

---

## 🚀 DEPLOYMENT & DevOps

### 💻 Local Development

```yaml
✅ LARAGON + POSTGRESQL:
  - Setup rápido (< 30 min)
  - PHP 8.1+, Node 18+, PostgreSQL 15+
  - SSL automático
  - Multiple projects fácil
  
❌ DOCKER DESCARTADO:
  - Overhead innecesario proyectos pequeños
  - Consume recursos
  - Complejidad setup inicial
  - Debug más lento
```

### 🌐 Production Deployment

```yaml
✅ VPS MANUAL DEPLOYMENT:
  - Control total sobre ambiente
  - Costos menores vs PaaS
  - Performance predecible
  - Backup strategy customizable
  
⚠️ REQUERIMIENTOS VPS:
  - Ubuntu 22.04 LTS mínimo
  - 2GB RAM mínimo (4GB recomendado)
  - PHP 8.1+, PostgreSQL 15+, Nginx
  - SSL automático (Certbot)
```

---

## 🔮 WHEN TO CONSIDER DIFFERENT STACK

### 🤔 Cuándo NO usar este stack

```yaml
CONSIDERA REACT + Next.js SI:
  - SEO crítico (ecommerce, blog, landing)
  - Rendering híbrido (SSG + SSR)
  - Equipo grande (5+ devs frontend)
  - Ecosystem JS puro requerido

CONSIDERA MySQL SI:  
  - Hosting compartido limitado
  - Proyecto muy simple (< 10 tablas)
  - Team sin experiencia PostgreSQL

CONSIDERA DOCKER SI:
  - Microservicios architecture
  - Equipo grande (10+ devs)
  - Multi-ambiente complejo (dev/staging/prod)
  - CI/CD avanzado requerido
```

---

## 🐛 BUGS MEMORABLES & FIXES

### 🎯 Tailwind Classes No Funcionan
```css
❌ PROBLEMA: space-y-2, mb-4, gap-4 no aplicaban
🔍 ROOT CAUSE: * { margin: 0; padding: 0; } en globals.css
✅ FIX: Reset específico html/body únicamente
📅 FECHA: Commit 462534a (Agosto 2025)
```

### 🔐 Sanctum 419 CSRF Token Mismatch  
```javascript
❌ PROBLEMA: Login form retorna 419 error
🔍 ROOT CAUSE: Missing XSRF-TOKEN en SPA
✅ FIX: axios.get('/sanctum/csrf-cookie') antes de login
📅 SOLUCIÓN: Session-based auth en lugar de tokens
```

### 🎨 Vue Router Guards Loop Infinito
```javascript  
❌ PROBLEMA: Redirect loop en rutas protegidas
🔍 ROOT CAUSE: Guard redirige a login que redirige a guard
✅ FIX: Verificar to.path !== '/login' antes de redirect
```

---

## 📚 RESOURCES QUE VALEN LA PENA

### 📖 Documentation MUST-READ
- [Laravel 11 docs](https://laravel.com/docs/11.x) - Obviamente
- [Vue 3 Composition API](https://vuejs.org/api/composition-api-setup.html) - Core patterns  
- [Tailwind 4.0 docs](https://tailwindcss.com/docs) - Todas las utilities
- [Filament v3 docs](https://filamentphp.com/docs/3.x) - Admin panel
- [Pinia docs](https://pinia.vuejs.org/) - State management

### 🎨 UI Inspiration GOLD
- [Trezo Admin Template](https://trezo-template.vercel.app/) - Theme base usado
- [Tailwind UI Components](https://tailwindui.com/components) - Patterns probados  
- [Shadcn/ui](https://ui.shadcn.com/) - Concepts adaptables a Vue

### 🛠️ Tools que ACELERAN desarrollo
- [Laravel Tinker](https://laravel.com/docs/artisan#tinker) - Debug rápido BD
- [Vue DevTools](https://devtools.vuejs.org/) - Debug state Vue  
- [Tailwind IntelliSense](https://marketplace.visualstudio.com/items?itemName=bradlc.vscode-tailwindcss) - Autocomplete CSS

---

## 🎯 PHILOSOPHY & MANTRAS

### 💡 Mantras de Desarrollo

1. **"Base sólida antes de escalar"** - Perfeccionar Login + Dashboard antes de nuevos módulos
2. **"Hybrid > Pure"** - Mejor de ambos mundos (Tailwind + CSS vars)  
3. **"Tests que importan"** - 70% coverage en lo crítico, no 100% en trivial
4. **"Inspirarse, no copiar"** - Adaptar patterns probados, no reinventar
5. **"Simple > Complex"** - Laravel + Vue > microservicios para equipos pequeños

### 🚀 Success Metrics REALES

```yaml
📊 MÉTRICAS QUE IMPORTAN:
  - Developer Experience: ¿Setup < 30 min?
  - Performance: ¿Response time < 200ms?
  - Maintainability: ¿Onboarding nuevo dev < 1 día?
  - User Experience: ¿UI inspire confianza?
  
📈 RESULTADO BAMBU CRM:
  - Setup: ✅ < 30 min (Laragon + clone + migrate)
  - Tests: ✅ 72/72 passing (491 assertions)
  - Frontend: ✅ Vue SPA operativo (login + dashboard)
  - Performance: ⏳ Pending measurement
```

---

**📝 ÚLTIMA ACTUALIZACIÓN**: Agosto 2025 (Commit 462534a)  
**🔄 PRÓXIMA REVISIÓN**: Después de UI refinement completado  
**📧 AUTOR**: Equipo BAMBU CRM v2.0

---

**💡 RECUERDA**: Este documento se actualiza con cada lesson learned importante. Si algo te hizo renegar 2+ horas, documéntalo aquí para el próximo proyecto.