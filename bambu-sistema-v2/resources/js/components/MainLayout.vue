<template>
  <div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
      <div class="sidebar-content">
        <div class="sidebar-header">
          <div class="sidebar-logo">
            <div class="logo-img">🧪</div>
            <span class="logo-text">BAMBU</span>
          </div>
          <button v-if="isMobile" @click="closeSidebar" class="btn-icon sidebar-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M18 6L6 18M6 6L18 18" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        
        <nav class="sidebar-nav">
          <router-link to="/dashboard" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Dashboard</span>
          </router-link>
          
          <router-link to="/productos" class="nav-item">
            <span class="nav-icon">🧪</span>
            <span class="nav-text">Productos</span>
          </router-link>
          
          <router-link to="/clientes" class="nav-item">
            <span class="nav-icon">👥</span>
            <span class="nav-text">Clientes</span>
          </router-link>
          
          <router-link to="/stock" class="nav-item">
            <span class="nav-icon">📦</span>
            <span class="nav-text">Stock</span>
          </router-link>
          
          <router-link to="/pedidos" class="nav-item">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Pedidos</span>
          </router-link>
          
          <router-link to="/cotizador" class="nav-item">
            <span class="nav-icon">💰</span>
            <span class="nav-text">Cotizador</span>
          </router-link>
        </nav>
        
        <div class="sidebar-footer">
          <button @click="toggleTheme" class="btn-icon theme-toggle" title="Cambiar tema">
            <svg v-if="isDark" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main wrapper -->
    <div class="main-wrapper">
      <!-- Header con CSS Grid INFALIBLE -->
      <header class="main-header">
        <button v-if="!isDesktop" @click="toggleSidebar" class="btn-icon hamburger-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 12h18M3 6h18M3 18h18" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
        
        <div class="header-search">
          <input 
            type="search" 
            placeholder="Buscar productos, clientes..."
            class="search-input"
            v-model="searchQuery"
          >
        </div>
        
        <div class="header-actions">
          <button class="btn-icon" title="Notificaciones">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </button>
          
          <button class="btn-icon" title="Perfil de usuario">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </button>
        </div>
      </header>

      <!-- Content area -->
      <main class="content-area">
        <div class="content-container">
          <slot></slot>
        </div>
      </main>
    </div>

    <!-- Sidebar overlay solo mobile -->
    <div 
      v-if="sidebarOpen && !isDesktop" 
      class="sidebar-overlay" 
      @click="closeSidebar"
    ></div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useResponsive } from '@/composables/useResponsive'
import { useTheme } from '@/composables/useTheme'

// Composables
const {
  sidebarOpen,
  isMobile,
  isDesktop,
  toggleSidebar,
  closeSidebar
} = useResponsive()

const { isDark, toggleTheme } = useTheme()

// Search functionality
const searchQuery = ref('')

// Watcher para search (futuro)
// watch(searchQuery, (newValue) => {
//   // Implementar búsqueda global
// })
</script>

<style scoped>
/* ============================================
   🎨 SIDEBAR FOOTER
   ============================================ */

.sidebar-footer {
  padding: var(--space-md);
  border-top: 1px solid var(--border);
}

.theme-toggle {
  width: 100%;
  justify-content: flex-start;
  gap: var(--space-sm);
  padding: var(--space-sm) var(--space-md);
  height: 44px;
  border-radius: var(--radius-md);
}

.theme-toggle:hover {
  background: var(--bg-elevated);
}

/* ============================================
   🎨 LOGO PERSONALIZADO
   ============================================ */

.logo-img {
  width: 32px;
  height: 32px;
  display: grid;
  place-items: center;
  background: var(--primary-bg);
  border-radius: var(--radius-md);
  font-size: 18px;
}

/* ============================================
   🎨 RESPONSIVE OVERRIDES ESPECÍFICOS
   ============================================ */

/* Tablet ajustes */
@media (min-width: 768px) and (max-width: 1023px) {
  .sidebar {
    width: 280px;
  }
  
  .sidebar-overlay {
    backdrop-filter: blur(4px);
  }
}

/* Mobile específico */
@media (max-width: 767px) {
  .main-header {
    padding: 0 var(--space-sm);
    gap: var(--space-sm);
  }
  
  .hamburger-btn {
    order: -1;
  }
  
  .header-actions {
    gap: var(--space-xs);
  }
  
  .btn-icon {
    width: 40px;
    height: 40px;
  }
}

/* Desktop específico */
@media (min-width: 1024px) {
  .sidebar {
    box-shadow: none;
  }
  
  .content-container {
    padding: var(--space-xl);
  }
}

/* ============================================
   🎨 ESTADOS ESPECIALES
   ============================================ */

/* Focus trap cuando sidebar abierto en mobile */
.app-container:has(.sidebar.sidebar-open) .main-wrapper {
  pointer-events: none;
}

@media (min-width: 1024px) {
  .app-container:has(.sidebar.sidebar-open) .main-wrapper {
    pointer-events: auto;
  }
}

/* Animación suave para sidebar */
.sidebar {
  transition: transform var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover states solo desktop */
@media (hover: hover) {
  .nav-item:hover {
    transform: translateX(2px);
  }
}
</style>