import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: RouteRecordRaw[] = [
  // Public routes
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: { requiresGuest: true }
  },
  
  // Protected routes with layout
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/views/DashboardView.vue')
      },
      
      // Productos
      {
        path: 'productos',
        name: 'productos',
        component: () => import('@/views/productos/ProductosIndex.vue')
      },
      {
        path: 'productos/crear',
        name: 'productos.create',
        component: () => import('@/views/productos/ProductosCreate.vue')
      },
      {
        path: 'productos/:id/editar',
        name: 'productos.edit',
        component: () => import('@/views/productos/ProductosEdit.vue')
      },
      
      // Clientes
      {
        path: 'clientes',
        name: 'clientes',
        component: () => import('@/views/clientes/ClientesIndex.vue')
      },
      {
        path: 'clientes/crear',
        name: 'clientes.create',
        component: () => import('@/views/clientes/ClientesCreate.vue')
      },
      {
        path: 'clientes/:id',
        name: 'clientes.show',
        component: () => import('@/views/clientes/ClientesShow.vue')
      },
      {
        path: 'clientes/:id/editar',
        name: 'clientes.edit',
        component: () => import('@/views/clientes/ClientesEdit.vue')
      },
      
      // Pedidos
      {
        path: 'pedidos',
        name: 'pedidos',
        component: () => import('@/views/pedidos/PedidosIndex.vue')
      },
      {
        path: 'pedidos/crear',
        name: 'pedidos.create',
        component: () => import('@/views/pedidos/PedidosCreate.vue')
      },
      {
        path: 'pedidos/:id',
        name: 'pedidos.show',
        component: () => import('@/views/pedidos/PedidosShow.vue')
      },
      
      // Stock
      {
        path: 'stock',
        name: 'stock',
        component: () => import('@/views/stock/StockIndex.vue')
      },
      {
        path: 'stock/movimientos',
        name: 'stock.movimientos',
        component: () => import('@/views/stock/MovimientosIndex.vue')
      },
      
      // Cotizador
      {
        path: 'cotizador',
        name: 'cotizador',
        component: () => import('@/views/CotizadorView.vue')
      },
      
      // Configuración
      {
        path: 'configuracion',
        name: 'configuracion',
        component: () => import('@/views/configuracion/ConfiguracionIndex.vue')
      },
      
      // Vehículos
      {
        path: 'vehiculos',
        name: 'vehiculos',
        component: () => import('@/views/vehiculos/VehiculosIndex.vue')
      },
      
      // Planificación
      {
        path: 'planificacion',
        name: 'planificacion',
        component: () => import('@/views/planificacion/PlanificacionIndex.vue')
      },
      
      // Seguimiento
      {
        path: 'seguimiento',
        name: 'seguimiento',
        component: () => import('@/views/seguimiento/SeguimientoIndex.vue')
      },
      
      // Reportes
      {
        path: 'reportes',
        name: 'reportes',
        component: () => import('@/views/reportes/ReportesIndex.vue')
      }
    ]
  },
  
  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // Redirect to login
      return next({
        name: 'login',
        query: { redirect: to.fullPath }
      })
    }
    
    // If authenticated but no user data, fetch it
    if (!authStore.user) {
      const success = await authStore.fetchUser()
      if (!success) {
        return next({ name: 'login' })
      }
    }
  }
  
  // Check if route requires guest (login/register pages)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return next({ name: 'dashboard' })
  }
  
  next()
})

export default router