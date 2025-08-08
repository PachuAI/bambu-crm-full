<template>
  <div class="main-wrapper">
    <!-- Mobile Overlay -->
    <div 
      v-show="mobileMenuOpen" 
      class="sidebar-overlay" 
      @click="closeMobileMenu"
    ></div>
    
    <!-- Sidebar -->
    <aside :class="[
      'sidebar',
      { 'sidebar-open': mobileMenuOpen }
    ]" @click.stop>
      <!-- Brand -->
      <div class="sidebar-logo">
        <h1 class="sidebar-brand">BAMBU</h1>
        <!-- Mobile close button -->
        <button 
          @click="closeMobileMenu"
          class="btn-icon sidebar-close"
        >
          <XMarkIcon class="icon-base" />
        </button>
      </div>
      
      <!-- Navigation -->
      <nav class="sidebar-nav">
        <template v-for="item in menuItems" :key="item.label">
          <!-- Dropdown Item (Repartos) -->
          <div v-if="item.isDropdown">
            <button
              @click="toggleRepartos"
              class="sidebar-link sidebar-dropdown"
              :class="{ 'active': isRepartosActive() }"
            >
              <component :is="item.icon" class="icon-base" />
              <span class="sidebar-text">{{ item.label }}</span>
              <ChevronDownIcon 
                class="icon-sm dropdown-arrow"
                :class="{ 'rotate-180': repartosExpanded }"
              />
            </button>
            
            <!-- Subitems -->
            <div v-if="repartosExpanded" class="sidebar-subitems">
              <router-link
                v-for="subItem in item.subItems"
                :key="subItem.path"
                :to="subItem.path"
                class="sidebar-link sidebar-sublink"
                :class="{ 'active': isActiveRoute(subItem.path) }"
              >
                <component :is="subItem.icon" class="icon-sm" />
                <span class="sidebar-text">{{ subItem.label }}</span>
              </router-link>
            </div>
          </div>
          
          <!-- Regular Item -->
          <router-link
            v-else
            :to="item.path"
            class="sidebar-link"
            :class="{ 'active': isActiveRoute(item.path) }"
          >
            <component :is="item.icon" class="icon-base" />
            <span class="sidebar-text">{{ item.label }}</span>
          </router-link>
        </template>
        
        <!-- Cotizador Button -->
        <router-link
          to="/cotizador"
          class="sidebar-link sidebar-special"
        >
          <svg class="icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          <span class="sidebar-text">Cotizador</span>
        </router-link>
      </nav>
      
      <!-- User Section at Bottom -->
      <div class="sidebar-user">
        <div class="user-info">
          <div class="user-avatar">
            {{ userInitials }}
          </div>
          <div class="user-details">
            <p class="user-name">
              {{ authStore.userName }}
            </p>
            <p class="user-email">
              {{ authStore.userEmail }}
            </p>
          </div>
        </div>
        
        <button
          @click="handleLogout"
          class="btn-secondary logout-btn"
        >
          <ArrowRightOnRectangleIcon class="icon-sm" />
          <span class="sidebar-text">Cerrar Sesión</span>
        </button>
      </div>
    </aside>
    
    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Header -->
      <header class="main-header">
        <div class="header-content">
          <!-- Mobile Menu Button -->
          <button 
            @click="toggleMobileMenu"
            class="hamburger-btn"
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
          
          <!-- Search Bar -->
          <div class="header-search">
            <div class="search-container">
              <MagnifyingGlassIcon class="search-icon" />
              <input
                type="text"
                placeholder="Buscar productos, clientes, pedidos..."
                class="form-input search-input"
              />
            </div>
          </div>
          
          <!-- Header Actions -->
          <div class="header-actions">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="btn-icon theme-toggle"
              :title="isDark ? 'Modo claro' : 'Modo oscuro'"
            >
              <SunIcon v-if="isDark" class="icon-base" />
              <MoonIcon v-else class="icon-base" />
            </button>
            
            <!-- Notifications -->
            <button class="btn-icon notification-btn">
              <BellIcon class="icon-base" />
              <span class="notification-dot"></span>
            </button>
            
            <!-- Settings -->
            <router-link
              to="/configuracion"
              class="btn-icon settings-btn"
            >
              <Cog6ToothIcon class="icon-base" />
            </router-link>
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <main class="main-content-area">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
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
  XMarkIcon,
  CalendarDaysIcon,
  MapPinIcon,
  ClipboardDocumentListIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const { isDark, toggleTheme } = useTheme()

// Sidebar collapse state
const sidebarCollapsed = ref(false)
const mobileMenuOpen = ref(false)
const repartosExpanded = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

const toggleRepartos = () => {
  repartosExpanded.value = !repartosExpanded.value
}

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: HomeIcon },
  { path: '/productos', label: 'Productos', icon: CubeIcon },
  { path: '/clientes', label: 'Clientes', icon: UserGroupIcon },
  { path: '/pedidos', label: 'Pedidos', icon: DocumentTextIcon },
  { 
    path: null, 
    label: 'Repartos', 
    icon: TruckIcon,
    isDropdown: true,
    expanded: false,
    subItems: [
      { path: '/vehiculos', label: 'Vehículos', icon: TruckIcon },
      { path: '/planificacion', label: 'Planificación', icon: CalendarDaysIcon },
      { path: '/seguimiento', label: 'Seguimiento', icon: MapPinIcon }
    ]
  },
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

const isRepartosActive = () => {
  const repartosRoutes = ['/vehiculos', '/planificacion', '/seguimiento']
  return repartosRoutes.some(routePath => 
    route.path === routePath || route.path.startsWith(routePath + '/')
  )
}

const handleLogout = async () => {
  await authStore.logout()
}

onMounted(() => {
  // Theme is handled by our CSS system
  // No manual body class manipulation needed
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