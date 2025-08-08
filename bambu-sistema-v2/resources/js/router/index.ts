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
  
  // Protected routes - SIN LAYOUT por ahora
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/DashboardWithLayout.vue'),
    meta: { requiresAuth: true }
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