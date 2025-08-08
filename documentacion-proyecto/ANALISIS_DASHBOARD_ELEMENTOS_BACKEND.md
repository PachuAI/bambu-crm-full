# 📊 ANÁLISIS DASHBOARD - ELEMENTOS Y CONEXIONES BACKEND

> **Propósito**: Documentar todos los elementos del dashboard actual y sus conexiones con backend para replicar más tarde con responsive design correcto.

---

## 🗂️ ESTRUCTURA PRINCIPAL DEL DASHBOARD

### **Header Section**
```vue
<!-- Page Header con User Info -->
<div class="flex items-center justify-between">
  <div>
    <h1>Dashboard</h1>
    <p>Control operativo BAMBU - Alto Valle</p>
    <p>Última actualización: {{ lastUpdated }}</p>
  </div>
  <div>
    <!-- Date Range Selector (desktop only) -->
    <button>Últimos 30 días</button>
    <!-- User Avatar + Name -->
    <div>{{ userInitials }} - {{ userName }}</div>
  </div>
</div>
```

### **Metrics Grid (5 Cards)**
```vue
<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
  <MetricCard /> <!-- x5 cards -->
</div>
```

### **Main Content (2 columnas en desktop)**
```vue
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
  <!-- Facturación Chart (2/3 width) -->
  <div class="xl:col-span-2">
    <!-- Chart + Quick Stats -->
  </div>
  
  <!-- Top Products (1/3 width) -->
  <div>
    <!-- Lista de productos destacados -->
  </div>
</div>
```

### **Recent Orders Table**
```vue
<table>
  <!-- Headers: #, Cliente, Producto, Total, Estado, Fecha -->
  <!-- Rows dinámicas desde recentOrders -->
</table>
```

---

## 📡 CONEXIONES CON BACKEND

### **API Endpoint Principal**
```typescript
// Endpoint que debe existir
GET /api/v1/reportes/dashboard

// Response esperado:
{
  ventas_totales: number,
  crecimiento_ventas: number,
  pedidos_mes: number,
  pedidos_hoy: number,
  clientes_activos: number,
  clientes_nuevos: number,
  entregas_hoy: number,
  entregas_completadas: number,
  vehiculos_disponibles: number,
  vehiculos_totales: number,
  pedidos_recientes: [
    {
      id: string,
      cliente: string,
      total: number,
      estado: string,
      fecha: date
    }
  ],
  productos_destacados: [
    {
      id: number,
      nombre: string,
      ventas: number,
      porcentaje: number
    }
  ]
}
```

### **Mapeo de Estados**
```typescript
const mapStatus = (estado: string) => {
  const statusMap: Record<string, string> = {
    'confirmado': 'En proceso',
    'listo_envio': 'Listo envío', 
    'en_transito': 'En tránsito',
    'entregado': 'Completado',
    'cancelado': 'Cancelado'
  }
  return statusMap[estado] || estado
}
```

### **Navegación**
```typescript
// Click en pedido → router.push(`/pedidos/${orderId}`)
// Link "Ver todos" → router-link to="/pedidos"
```

---

## 🎯 ELEMENTOS PRINCIPALES A REPLICAR

### **1. Metrics Cards (5 elementos)**

**Datos actuales (hardcodeados):**
```typescript
const metrics = ref([
  {
    id: 1,
    title: 'Ventas Totales',
    value: '$2,847,650',
    trend: 'up',
    trendValue: '+18.3%',
    icon: CurrencyDollarIcon
  },
  {
    id: 2, 
    title: 'Pedidos',
    value: '284',
    trend: 'up',
    trendValue: '+12.7%',
    icon: ShoppingCartIcon
  },
  {
    id: 3,
    title: 'Clientes', 
    value: '147',
    trend: 'up',
    trendValue: '+5.8%',
    icon: UserGroupIcon
  },
  {
    id: 4,
    title: 'Entregas Hoy',
    value: '12', 
    trend: 'up',
    trendValue: '8 completadas',
    icon: TruckIcon
  },
  {
    id: 5,
    title: 'Pendientes Entrega',
    value: '7',
    trend: 'down', 
    trendValue: '-15.2%',
    icon: ClockIcon
  }
])
```

**Backend Connection:**
- Actualiza desde `fetchDashboardData()`
- Mapping: `ventas_totales` → Card 1, `pedidos_mes` → Card 2, etc.

### **2. Chart de Facturación**

**Elementos:**
- Título: "Facturación del Mes"
- Botones: Mensual/Semanal/Diario
- Mock chart bars con datos: `[45, 65, 35, 78, 52, 89, 45, 67, 73, 82, 69, 91]`
- Overlay central: "$2,847,650 - Total este mes"
- Quick stats (3 elementos):
  - "+18.3% vs mes anterior" 
  - "$94,922 promedio diario"
  - "284 pedidos totales"

### **3. Productos Destacados**

**Datos actuales:**
```typescript
const topProducts = ref([
  { id: 1, name: 'Detergente BAMBU (Bidón 5L)', quantity: 145, percentage: 32, color: '#10b981' },
  { id: 2, name: 'Desinfectante BAMBU (Bidón 5L)', quantity: 128, percentage: 28, color: '#6366f1' },
  { id: 3, name: 'Limpiador de pisos BAMBU (Bidón 5L)', quantity: 96, percentage: 21, color: '#06b6d4' },
  { id: 4, name: 'Jabón líquido BAMBU (Bidón 5L)', quantity: 86, percentage: 19, color: '#f59e0b' },
  { id: 5, name: 'Suavizante BAMBU (Bidón 5L)', quantity: 62, percentage: 14, color: '#ec4899' }
])
```

**Layout:**
- Lista con color dot + nombre + cantidad
- Progress bar derecha con porcentaje
- Summary: "Total vendido: 517 unidades"

### **4. Tabla Pedidos Recientes**

**Headers:** #, Cliente, Producto, Total, Estado, Fecha

**Datos típicos:**
```typescript
{
  id: '2847',
  customer: 'Supermercado Don Juan - General Roca',
  product: 'Detergente BAMBU (Bidón 5L) + Limpiador', 
  items: 24,
  total: '18,720.00',
  status: 'En camino',
  date: 'Hoy, 14:30'
}
```

**Estados y colores:**
```typescript
const getStatusClasses = (status: string) => {
  const classes = {
    'Entregado': 'bg-green-500/20 text-green-400 border-green-500/30',
    'En camino': 'bg-blue-500/20 text-blue-400 border-blue-500/30', 
    'Listo para enviar': 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    'Preparando': 'bg-orange-500/20 text-orange-400 border-orange-500/30',
    'Pendiente de armado': 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    'Cancelado': 'bg-red-500/20 text-red-400 border-red-500/30'
  }
}
```

---

## 🔧 LÓGICA JAVASCRIPT A MANTENER

### **Imports necesarios:**
```typescript
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { 
  CalendarIcon, 
  ChevronDownIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon, 
  UserGroupIcon,
  TruckIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import MetricCard from '@/components/dashboard/MetricCard.vue'
```

### **Reactive data:**
```typescript
const router = useRouter()
const loading = ref(false)
const lastUpdated = ref('')
const userName = ref('Juan Pérez')
const userInitials = ref('JP')
```

### **Métodos a mantener:**
```typescript
const navigateToOrder = (orderId: string) => {
  router.push(`/pedidos/${orderId}`)
}

const updateLastUpdated = () => {
  const now = new Date()
  lastUpdated.value = now.toLocaleString('es-AR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const fetchDashboardData = async () => {
  // Lógica completa de conexión API
}

const mapStatus = (estado: string) => {
  // Mapeo de estados backend → frontend
}
```

### **Lifecycle:**
```typescript
onMounted(() => {
  updateLastUpdated()
  fetchDashboardData()
  
  // Auto-refresh cada 5 minutos
  setInterval(() => {
    updateLastUpdated()
    fetchDashboardData()
  }, 5 * 60 * 1000)
})
```

---

## 🎨 SISTEMA DE COLORES USADO

### **Backgrounds:**
- `bg-slate-900` - Fondo principal
- `bg-slate-800` - Cards principales
- `bg-slate-900/30` - Hover states
- `bg-slate-700` - Bordes y separadores

### **Chart Colors:**
- Indigo: `#6366f1` (principal)
- Green: `#10b981` 
- Blue: `#06b6d4`
- Amber: `#f59e0b`
- Pink: `#ec4899`

### **Status Colors:**
- Green: Entregado
- Blue: En camino  
- Purple: Listo para enviar
- Orange: Preparando
- Amber: Pendiente
- Red: Cancelado

---

## ⚠️ PROBLEMAS IDENTIFICADOS (NO REPLICAR)

### **CSS Issues:**
- `min-w-[120px]`, `min-w-[150px]` en headers tabla
- `w-12` fijo en progress bars
- Grids inconsistentes: `grid-cols-2 sm:grid-cols-2`
- Overlays múltiples: `bg-slate-900/80` sobre `bg-slate-900/50`
- Truncate con anchos fijos: `max-w-[120px]`

### **Mobile Issues:**
- Tabla no se convierte a cards
- Headers con `min-w` fijos rompen viewport
- Progress bars no responsive
- Chart sin adaptación mobile

---

## ✅ COMPONENTE MetricCard.vue

**Ubicación:** `@/components/dashboard/MetricCard.vue`

**Props esperadas:**
```typescript
{
  title: string,
  value: string,
  trend: 'up' | 'down',
  trendValue: string,  
  icon: Component,
  loading?: boolean
}
```

**Uso:**
```vue
<MetricCard
  :title="metric.title"
  :value="metric.value" 
  :trend="metric.trend"
  :trendValue="metric.trendValue"
  :icon="metric.icon"
  :loading="loading"
/>
```

---

## 🔄 PLAN DE REPLICACIÓN

### **Orden sugerido para replicar:**
1. **Header section** (simple, pocas dependencias)
2. **Metrics grid** (usando MetricCard existente) 
3. **Chart section** (mock data primero)
4. **Products list** (lista simple)
5. **Orders table → cards** (más complejo, requiere responsive)
6. **Backend integration** (último paso)

### **Responsive Strategy:**
- **Mobile (320px):** Stack vertical, cards en lugar de tabla
- **Tablet (768px):** 2 columnas, tabla simplificada
- **Desktop (1024px+):** Layout original 3 columnas

---

**Documento creado:** 2025-08-08  
**Estado:** Dashboard actual analizado, listo para vaciado y rebuild mobile-first