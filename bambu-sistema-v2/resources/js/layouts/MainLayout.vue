<template>
  <div class="min-h-screen bg-slate-900">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-[280px] bg-slate-900 border-r border-slate-700 z-50">
      <!-- Brand -->
      <div class="h-16 flex items-center justify-center border-b border-slate-700">
        <h1 class="text-2xl font-bold text-indigo-400">BAMBU</h1>
      </div>
      
      <!-- Navigation -->
      <nav class="p-4 space-y-1">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-150 cursor-pointer"
          :class="isActiveRoute(item.path) ? 
            'bg-indigo-600 text-white' : 
            'text-slate-400 hover:bg-slate-800 hover:text-white'"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span>{{ item.label }}</span>
        </router-link>
      </nav>
      
      <!-- User Section at Bottom -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
              {{ userInitials }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-white truncate">
                {{ authStore.userName }}
              </p>
              <p class="text-xs text-slate-400 truncate">
                {{ authStore.userEmail }}
              </p>
            </div>
          </div>
        </div>
        
        <button
          @click="handleLogout"
          class="w-full px-3 py-1.5 text-xs rounded-md font-medium inline-flex items-center gap-2 transition-all duration-150 bg-transparent text-slate-400 hover:bg-slate-800 hover:text-white border-none"
        >
          <ArrowRightOnRectangleIcon class="w-4 h-4" />
          <span>Cerrar Sesión</span>
        </button>
      </div>
    </aside>
    
    <!-- Main Content Area -->
    <div class="ml-[280px]">
      <!-- Header -->
      <header class="fixed top-0 left-[280px] right-0 h-16 bg-slate-900 border-b border-slate-700 z-40">
        <div class="h-full px-6 flex items-center justify-between">
          <!-- Search Bar -->
          <div class="flex-1 max-w-xl">
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
              <input
                type="text"
                placeholder="Buscar productos, clientes, pedidos..."
                class="w-full pl-10 pr-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
              />
            </div>
          </div>
          
          <!-- Header Actions -->
          <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="p-2 rounded-lg text-slate-400 hover:bg-slate-800 transition-colors"
              :title="isDark ? 'Modo claro' : 'Modo oscuro'"
            >
              <SunIcon v-if="isDark" class="w-5 h-5" />
              <MoonIcon v-else class="w-5 h-5" />
            </button>
            
            <!-- Notifications -->
            <button class="p-2 rounded-lg text-slate-400 hover:bg-slate-800 transition-colors relative">
              <BellIcon class="w-5 h-5" />
              <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            
            <!-- Settings -->
            <router-link
              to="/configuracion"
              class="p-2 rounded-lg text-slate-400 hover:bg-slate-800 transition-colors"
            >
              <Cog6ToothIcon class="w-5 h-5" />
            </router-link>
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <main class="pt-16 min-h-screen">
        <div class="p-6">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTheme } from '@/composables/useTheme'
import {
  HomeIcon,
  CubeIcon,
  UserGroupIcon,
  DocumentTextIcon,
  TruckIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  MagnifyingGlassIcon,
  BellIcon,
  SunIcon,
  MoonIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const { isDark, toggleTheme } = useTheme()

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: HomeIcon },
  { path: '/productos', label: 'Productos', icon: CubeIcon },
  { path: '/clientes', label: 'Clientes', icon: UserGroupIcon },
  { path: '/pedidos', label: 'Pedidos', icon: DocumentTextIcon },
  { path: '/stock', label: 'Stock', icon: TruckIcon },
  { path: '/reportes', label: 'Reportes', icon: ChartBarIcon },
  { path: '/configuracion', label: 'Configuración', icon: Cog6ToothIcon },
]

const userInitials = computed(() => {
  const name = authStore.userName || 'A'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActiveRoute = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const handleLogout = async () => {
  await authStore.logout()
}

onMounted(() => {
  // Ensure dark theme is applied
  document.body.className = 'bg-slate-900 text-white'
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>