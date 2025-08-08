<!-- 
  AppShell.vue - Layout Principal BAMBU
  Implementa el patrón canónico del revisor para evitar bugs de layout
  
  Estructura:
  - Sidebar: overlay en mobile, columna real en desktop
  - Header: Grid auto 1fr auto
  - Content: área principal con router-view
  - Scrim: overlay para cerrar sidebar en mobile
-->

<template>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'is-open': isMobile && sidebarOpen }">
      <div class="sidebar-header">
        <img src="/logo.svg" alt="BAMBU" class="sidebar-logo">
        <span class="sidebar-logo-text">BAMBU</span>
      </div>
      
      <nav class="sidebar-nav">
        <router-link 
          v-for="item in menuItems" 
          :key="item.path"
          :to="item.path" 
          class="nav-link"
          @click="isMobile && closeSidebar()"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span class="nav-text">{{ item.label }}</span>
        </router-link>
      </nav>
      
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <span class="user-avatar">👤</span>
          <span class="user-name">Usuario</span>
        </div>
      </div>
    </aside>
    
    <!-- Shell (contenedor principal) -->
    <div class="shell">
      <!-- Header con Grid -->
      <header class="header">
        <!-- Hamburguesa (solo mobile) -->
        <button 
          v-if="isMobile" 
          class="btn-icon hamburger"
          @click="toggleSidebar"
          aria-label="Abrir menú"
        >
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>
        
        <!-- Búsqueda (centrada) -->
        <div class="header-search">
          <input 
            type="search" 
            class="search-input"
            placeholder="Buscar productos, clientes..."
          >
        </div>
        
        <!-- Acciones (derecha) -->
        <div class="header-actions">
          <button class="btn-icon" aria-label="Notificaciones">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </button>
          
          <button class="btn-icon" aria-label="Tema" @click="toggleTheme">
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="5"/>
              <line x1="12" y1="1" x2="12" y2="3"/>
              <line x1="12" y1="21" x2="12" y2="23"/>
              <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
              <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
              <line x1="1" y1="12" x2="3" y2="12"/>
              <line x1="21" y1="12" x2="23" y2="12"/>
              <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
              <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
          </button>
          
          <button class="btn-icon" aria-label="Configuración">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 1v6m0 6v6m4.22-13.22l-3.54 3.54m-1.36 1.36l-3.54 3.54M20.5 12h-6m-6 0h-6m13.22 4.22l-3.54-3.54m-1.36-1.36l-3.54-3.54"/>
            </svg>
          </button>
        </div>
      </header>
      
      <!-- Content Area -->
      <main class="content" role="main">
        <div class="content-container">
          <router-view />
        </div>
      </main>
    </div>
    
    <!-- Scrim (overlay mobile) -->
    <button 
      v-if="isMobile && sidebarOpen"
      class="scrim"
      @click="closeSidebar"
      aria-label="Cerrar menú"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useResponsive } from '@/composables/useResponsive'

// Composable responsive
const { 
  sidebarOpen, 
  isMobile, 
  isDesktop,
  toggleSidebar, 
  closeSidebar 
} = useResponsive()

// Estado del tema
const isDark = ref(true)

// Toggle theme
const toggleTheme = () => {
  isDark.value = !isDark.value
  
  // Aplicar clase al html
  if (isDark.value) {
    document.documentElement.setAttribute('data-theme', 'dark')
  } else {
    document.documentElement.setAttribute('data-theme', 'light')
  }
  
  // Guardar preferencia
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

// Items del menú
const menuItems = [
  { 
    path: '/dashboard', 
    label: 'Dashboard', 
    icon: '📊' 
  },
  { 
    path: '/productos', 
    label: 'Productos', 
    icon: '🧪' 
  },
  { 
    path: '/clientes', 
    label: 'Clientes', 
    icon: '👥' 
  },
  { 
    path: '/pedidos', 
    label: 'Pedidos', 
    icon: '📋' 
  },
  { 
    path: '/stock', 
    label: 'Stock', 
    icon: '📦' 
  },
  { 
    path: '/reportes', 
    label: 'Reportes', 
    icon: '📈' 
  }
]

// Inicializar tema al montar
if (typeof window !== 'undefined') {
  const savedTheme = localStorage.getItem('theme') || 'dark'
  isDark.value = savedTheme === 'dark'
  document.documentElement.setAttribute('data-theme', savedTheme)
}
</script>

<style scoped>
/* Estilos adicionales específicos del componente */
.sidebar-footer {
  margin-top: auto;
  padding: var(--space-md);
  border-top: 1px solid var(--border);
}

.sidebar-user {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm);
}

.user-avatar {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-elevated);
  border-radius: var(--radius-full);
  font-size: var(--font-lg);
}

.user-name {
  font-size: var(--font-sm);
  color: var(--text-secondary);
}

/* Asegurar que las líneas del hamburger funcionen */
.sidebar-open .hamburger .hamburger-line:nth-child(1) {
  transform: rotate(45deg) translateY(8px);
}

.sidebar-open .hamburger .hamburger-line:nth-child(2) {
  opacity: 0;
}

.sidebar-open .hamburger .hamburger-line:nth-child(3) {
  transform: rotate(-45deg) translateY(-8px);
}
</style>