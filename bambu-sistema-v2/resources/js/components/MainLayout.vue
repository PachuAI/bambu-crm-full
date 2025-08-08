<template>
  <!-- =======================================
       BAMBU MainLayout - Mobile-First Layout Principal
       
       Versión: 1.0.0
       Fecha: 2025-08-08
       
       💼 Built for chemical industry by ÍTERA
       https://iteraestudio.com
       
       LAYOUT RESPONSIVO:
       - Mobile (0-767px): Sidebar overlay + hamburger
       - Tablet (768-1023px): Sidebar overlay 280px + header search  
       - Desktop (1024px+): Sidebar fijo + layout completo
       ======================================= -->

  <!-- Sidebar Overlay (Mobile/Tablet) -->
  <div 
    v-if="sidebarOpen && sidebarMode === 'overlay'"
    class="sidebar-overlay"
    @click="closeSidebar"
    role="presentation"
  />

  <!-- Sidebar Principal -->
  <nav 
    :class="[
      'sidebar',
      sidebarMode === 'overlay' ? 'sidebar-overlay-mode' : 'sidebar-fixed-mode',
      sidebarOpen ? 'sidebar-open' : ''
    ]"
    data-sidebar
    role="navigation"
    aria-label="Navegación principal"
  >
    <!-- Logo y Branding -->
    <div class="sidebar-logo">
      <div class="logo-icon">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
          <rect width="32" height="32" rx="6" fill="currentColor" fill-opacity="0.1"/>
          <path 
            d="M8 12h16v2H8v-2zm0 4h16v2H8v-2zm0 4h12v2H8v-2z" 
            fill="var(--primary)"
          />
        </svg>
      </div>
      <div class="logo-text">
        <h1 class="logo-title">BAMBU</h1>
        <p class="logo-subtitle">Sistema CRM</p>
      </div>
    </div>

    <!-- Navegación Principal -->
    <div class="sidebar-nav">
      <ul class="nav-list" role="list">
        <li role="listitem">
          <router-link 
            to="/dashboard" 
            class="sidebar-link"
            :class="{ active: $route.name === 'dashboard' }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001 1V10a1 1 0 00-1-1h-2z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Dashboard</span>
          </router-link>
        </li>
        
        <li role="listitem">
          <router-link 
            to="/productos" 
            class="sidebar-link"
            :class="{ active: $route.name?.includes('productos') }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V4zM5 10a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Productos</span>
          </router-link>
        </li>

        <li role="listitem">
          <router-link 
            to="/clientes" 
            class="sidebar-link"
            :class="{ active: $route.name?.includes('clientes') }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.725 13.025a3.994 3.994 0 00-5.45 0A2.99 2.99 0 006 15.75V17a1 1 0 001 1h6a1 1 0 001-1v-1.25c0-.68-.27-1.34-.725-1.925zM18 15.75V17a1 1 0 01-1 1h-2v-1.25c0-1.68-.69-3.2-1.8-4.3a3.994 3.994 0 013.8.075z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Clientes</span>
          </router-link>
        </li>

        <li role="listitem">
          <router-link 
            to="/pedidos" 
            class="sidebar-link"
            :class="{ active: $route.name?.includes('pedidos') }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Pedidos</span>
          </router-link>
        </li>

        <li role="listitem">
          <router-link 
            to="/cotizaciones" 
            class="sidebar-link"
            :class="{ active: $route.name?.includes('cotizaciones') }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm8 2.5a.5.5 0 01.5-.5h3a.5.5 0 010 1h-3a.5.5 0 01-.5-.5zm.5 2.5a.5.5 0 000 1h3a.5.5 0 000-1h-3zm-8-2.5A1.5 1.5 0 015.5 5h3A1.5 1.5 0 0110 6.5v3A1.5 1.5 0 018.5 11h-3A1.5 1.5 0 014 9.5v-3z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Cotizaciones</span>
          </router-link>
        </li>

        <li role="listitem">
          <router-link 
            to="/reportes" 
            class="sidebar-link"
            :class="{ active: $route.name?.includes('reportes') }"
            @click="isMobile && closeSidebar()"
          >
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" fill="currentColor"/>
            </svg>
            <span class="nav-text">Reportes</span>
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Footer Sidebar -->
    <div class="sidebar-footer">
      <button 
        @click="toggleTheme"
        class="sidebar-link sidebar-theme-toggle"
        :aria-label="`Cambiar a modo ${isDark ? 'claro' : 'oscuro'}`"
      >
        <svg v-if="isDark" class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 17.77L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z" fill="currentColor"/>
        </svg>
        <svg v-else class="nav-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" fill="currentColor"/>
        </svg>
        <span class="nav-text">{{ isDark ? 'Modo Claro' : 'Modo Oscuro' }}</span>
      </button>

      <!-- Usuario Info (Solo Desktop) -->
      <div v-if="isDesktop" class="user-info">
        <div class="user-avatar">
          <span class="user-initials">{{ userInitials }}</span>
        </div>
        <div class="user-details">
          <p class="user-name">{{ userName }}</p>
          <p class="user-role">{{ userRole }}</p>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Wrapper -->
  <div 
    :class="[
      'main-wrapper',
      sidebarMode === 'fixed' ? 'main-wrapper-with-sidebar' : '',
      sidebarOpen && sidebarMode === 'overlay' ? 'main-wrapper-sidebar-open' : ''
    ]"
  >
    <!-- Header Principal -->
    <header class="main-header">
      <!-- Hamburger Menu (Mobile/Tablet) -->
      <button 
        v-if="showHamburger"
        @click="toggleSidebar"
        class="hamburger-btn"
        :class="{ active: sidebarOpen }"
        :aria-label="sidebarOpen ? 'Cerrar menú' : 'Abrir menú'"
        :aria-expanded="sidebarOpen"
      >
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>

      <!-- Header Title -->
      <div class="header-title">
        <h1 class="page-title">{{ pageTitle || 'Dashboard' }}</h1>
        <p v-if="pageSubtitle" class="page-subtitle">{{ pageSubtitle }}</p>
      </div>

      <!-- Header Search (Tablet+) -->
      <div v-if="showHeaderSearch" class="header-search">
        <div class="search-input-wrapper">
          <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <input 
            type="text" 
            placeholder="Buscar productos, clientes, pedidos..."
            class="search-input"
            v-model="searchQuery"
            @input="handleSearch"
          >
        </div>
      </div>

      <!-- Header Actions -->
      <div class="header-actions">
        <!-- Notificaciones -->
        <button class="header-action-btn" aria-label="Notificaciones">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M10 2a6 6 0 016 6c0 1.887.454 3.665 1.257 5.234a.75.75 0 01-.515 1.09L3.258 15.324a.75.75 0 01-.515-1.09C3.546 11.665 4 9.887 4 8a6 6 0 016-6zM7 17h6a3 3 0 01-6 0z" fill="currentColor"/>
          </svg>
          <span v-if="notificationsCount" class="notification-badge">{{ notificationsCount }}</span>
        </button>

        <!-- Usuario (Mobile/Tablet) -->
        <button v-if="!isDesktop" class="header-action-btn user-btn">
          <div class="user-avatar-small">
            <span class="user-initials">{{ userInitials }}</span>
          </div>
        </button>
      </div>
    </header>

    <!-- Content Area -->
    <main 
      ref="mainContent"
      class="content-area"
      :class="containerClass"
      role="main"
    >
      <!-- Slot para contenido de páginas -->
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTheme } from '@/composables/useTheme'
import { useResponsive } from '@/composables/useResponsive'

// ========================================
// PROPS & EMITS
// ========================================

const props = defineProps({
  pageTitle: {
    type: String,
    default: null
  },
  pageSubtitle: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['search'])

// ========================================
// COMPOSABLES
// ========================================

const route = useRoute()
const { isDark, toggleTheme } = useTheme()
const { 
  sidebarOpen, 
  sidebarMode, 
  showHamburger, 
  showHeaderSearch,
  containerClass,
  isMobile,
  isDesktop,
  toggleSidebar,
  closeSidebar
} = useResponsive()

// ========================================
// STATE
// ========================================

const searchQuery = ref('')
const mainContent = ref(null)
const notificationsCount = ref(3) // Mock data

// Mock user data - normalmente vendría del auth store
const userName = ref('Juan Pérez')
const userRole = ref('Administrador')
const userInitials = computed(() => {
  return userName.value
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// ========================================
// METHODS
// ========================================

const handleSearch = () => {
  emit('search', searchQuery.value)
}

// ========================================
// LIFECYCLE
// ========================================

onMounted(() => {
  // Setup inicial si es necesario
})

onUnmounted(() => {
  // Cleanup si es necesario
})
</script>

<style scoped>
/* =======================================
   SIDEBAR STYLES
   ======================================= */

.sidebar {
  /* Base styles ya definidos en components.css */
  /* Aquí solo overrides específicos si necesario */
}

.sidebar-overlay-mode {
  transform: translateX(-100%);
  transition: transform var(--transition-normal);
}

.sidebar-overlay-mode.sidebar-open {
  transform: translateX(0);
}

.sidebar-fixed-mode {
  transform: translateX(0);
}

/* Logo Styles */
.sidebar-logo {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-lg);
  border-bottom: 1px solid var(--border);
  min-height: var(--header-height);
}

.logo-icon {
  color: var(--primary);
  flex-shrink: 0;
}

.logo-text {
  min-width: 0; /* Permite text truncation */
}

.logo-title {
  font-size: var(--font-lg);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: var(--font-xs);
  color: var(--text-secondary);
  margin: 0;
  line-height: 1;
}

/* Navigation Styles */
.sidebar-nav {
  flex: 1;
  padding: var(--space-md) 0;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.nav-list {
  margin: 0;
  padding: 0;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm) var(--space-lg);
  color: var(--text-secondary);
  transition: var(--transition-fast);
  min-height: 48px; /* Touch target */
  text-decoration: none;
  border: none;
  background: transparent;
  width: 100%;
  cursor: pointer;
}

.sidebar-link:hover {
  background: var(--bg-elevated);
  color: var(--text-primary);
}

.sidebar-link.active {
  background: var(--primary-bg);
  color: var(--primary);
  border-left: 3px solid var(--primary);
}

.nav-icon {
  flex-shrink: 0;
}

.nav-text {
  font-weight: 500;
  min-width: 0;
}

/* Footer Styles */
.sidebar-footer {
  border-top: 1px solid var(--border);
  padding: var(--space-md);
}

.sidebar-theme-toggle {
  justify-content: flex-start;
  margin-bottom: var(--space-md);
}

.user-info {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm);
  background: var(--bg-elevated);
  border-radius: var(--radius-base);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-initials {
  color: var(--on-primary);
  font-weight: 600;
  font-size: var(--font-sm);
}

.user-details {
  min-width: 0;
}

.user-name {
  font-weight: 500;
  color: var(--text-primary);
  margin: 0;
  font-size: var(--font-sm);
  line-height: 1.2;
}

.user-role {
  color: var(--text-secondary);
  margin: 0;
  font-size: var(--font-xs);
  line-height: 1;
}

/* =======================================
   MAIN WRAPPER STYLES
   ======================================= */

.main-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: margin-left var(--transition-normal);
}

.main-wrapper-with-sidebar {
  margin-left: var(--sidebar-width);
}

.main-wrapper-sidebar-open {
  /* Estilos cuando sidebar overlay está abierto */
}

/* =======================================
   HEADER STYLES
   ======================================= */

.main-header {
  /* Base styles ya definidos en components.css y responsive.css */
  background: var(--bg-base);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: var(--space-lg);
  position: sticky;
  top: 0;
  z-index: var(--z-sticky);
  padding: 0 var(--space-md);
  min-height: var(--header-height);
}

/* Hamburger Styles */
.hamburger-btn {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  background: transparent;
  border: none;
  cursor: pointer;
  gap: 4px;
  padding: 0;
  border-radius: var(--radius-base);
  transition: background-color var(--transition-fast);
}

.hamburger-btn:hover {
  background: var(--bg-elevated);
}

.hamburger-line {
  width: 20px;
  height: 2px;
  background: var(--text-primary);
  transition: var(--transition-fast);
  display: block;
}

/* X Animation cuando está activo */
.hamburger-btn.active .hamburger-line:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.hamburger-btn.active .hamburger-line:nth-child(2) {
  opacity: 0;
}

.hamburger-btn.active .hamburger-line:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

/* Header Title */
.header-title {
  flex: 1;
}

.page-title {
  font-size: var(--font-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
  line-height: 1.2;
}

.page-subtitle {
  font-size: var(--font-sm);
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.2;
}

/* Header Search */
.header-search {
  flex: 1;
  max-width: 400px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: var(--space-sm);
  color: var(--text-muted);
  pointer-events: none;
}

.search-input {
  /* Extends from .form-input in components.css */
  padding-left: calc(var(--space-lg) + 20px); /* Space for icon */
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-base);
}

.search-input::placeholder {
  color: var(--text-muted);
}

.search-input:focus {
  outline: none;
  border-color: var(--border-focus);
  box-shadow: var(--focus-ring);
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.header-action-btn {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-base);
  background: transparent;
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
  transition: var(--transition-fast);
  cursor: pointer;
}

.header-action-btn:hover {
  background: var(--bg-elevated);
  border-color: var(--border-hover);
  color: var(--text-primary);
}

.notification-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: var(--error);
  color: var(--on-error);
  border-radius: 50%;
  min-width: 16px;
  height: 16px;
  font-size: 10px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
}

.user-avatar-small {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-avatar-small .user-initials {
  font-size: 10px;
  font-weight: 600;
}

/* =======================================
   CONTENT AREA STYLES
   ======================================= */

.content-area {
  flex: 1;
  padding: var(--space-lg) var(--space-md);
}

/* =======================================
   OVERLAY STYLES
   ======================================= */

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: var(--z-modal-backdrop);
  animation: fadeIn var(--transition-fast);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* =======================================
   RESPONSIVE OVERRIDES
   ======================================= */

/* Tablet Styles */
@media (min-width: 768px) {
  .main-header {
    padding: 0 var(--space-lg);
  }
  
  .content-area {
    padding: var(--space-xl) var(--space-lg);
  }
  
  .sidebar-overlay-mode {
    width: 320px; /* Más ancho en tablet */
  }
}

/* Desktop Styles */  
@media (min-width: 1024px) {
  .main-header {
    padding: 0 var(--space-xl);
  }
  
  .content-area {
    padding: var(--space-xl);
  }
  
  .header-search {
    max-width: 500px;
  }
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {
  .sidebar-link,
  .hamburger-btn,
  .header-action-btn {
    min-height: 48px;
    min-width: 48px;
  }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  .sidebar-overlay-mode,
  .hamburger-line {
    transition: none;
  }
  
  .hamburger-btn.active .hamburger-line {
    transition: none;
  }
}
</style>