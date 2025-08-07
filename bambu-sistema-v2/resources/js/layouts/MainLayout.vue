<template>
  <div class="min-h-screen bg-slate-900 flex">
    <!-- Sidebar -->
    <aside :class="[
      'fixed top-0 left-0 h-full bg-slate-900 border-r border-slate-700 z-50 transition-all duration-300',
      sidebarCollapsed ? 'w-16' : 'w-64'
    ]">
      <!-- Brand -->
      <div class="h-16 flex items-center justify-between px-4 border-b border-slate-700">
        <h1 v-if="!sidebarCollapsed" class="text-2xl font-bold text-indigo-400">BAMBU</h1>
        <h1 v-else class="text-xl font-bold text-indigo-400">B</h1>
        <button 
          @click="toggleSidebar"
          class="p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800"
        >
          <Bars3Icon v-if="!sidebarCollapsed" class="w-5 h-5" />
          <XMarkIcon v-else class="w-5 h-5" />
        </button>
      </div>
      
      <!-- Navigation -->
      <nav class="p-4 space-y-1">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-150 cursor-pointer group"
          :class="isActiveRoute(item.path) ? 
            'bg-indigo-600 text-white' : 
            'text-slate-400 hover:bg-slate-800 hover:text-white'"
          :title="sidebarCollapsed ? item.label : ''"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed" class="truncate">{{ item.label }}</span>
        </router-link>
        
        <!-- Cotizador Button -->
        <router-link
          to="/cotizador"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-150 cursor-pointer group bg-green-600 hover:bg-green-700 text-white mt-4"
          :title="sidebarCollapsed ? 'Cotizador' : ''"
        >
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          <span v-if="!sidebarCollapsed" class="truncate font-medium">Cotizador</span>
        </router-link>
      </nav>
      
      <!-- User Section at Bottom -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
        <div v-if="!sidebarCollapsed" class="mb-3">
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
          :class="[
            'flex items-center gap-2 px-3 py-2 text-sm rounded-md font-medium transition-all duration-150 bg-transparent text-slate-400 hover:bg-slate-800 hover:text-white',
            sidebarCollapsed ? 'w-8 h-8 justify-center' : 'w-full'
          ]"
          :title="sidebarCollapsed ? 'Cerrar Sesión' : ''"
        >
          <ArrowRightOnRectangleIcon class="w-4 h-4" />
          <span v-if="!sidebarCollapsed">Cerrar Sesión</span>
        </button>
      </div>
    </aside>
    
    <!-- Main Content Area -->
    <div :class="[
      'flex-1 flex flex-col transition-all duration-300',
      sidebarCollapsed ? 'ml-16' : 'ml-64'
    ]">
      <!-- Header -->
      <header class="h-16 bg-slate-900 border-b border-slate-700 flex-shrink-0">
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
      <main class="flex-1 overflow-auto">
        <div class="p-8">
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
import { computed, onMounted, ref } from 'vue'
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
  MoonIcon,
  Bars3Icon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const { isDark, toggleTheme } = useTheme()

// Sidebar collapse state
const sidebarCollapsed = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

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