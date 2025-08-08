<template>
  <div class="app-container">
    <!-- Sidebar simple -->
    <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
      <div class="sidebar-content">
        <div class="sidebar-header">
          <div class="sidebar-logo">
            <div class="logo-img">🧪</div>
            <span class="logo-text">BAMBU</span>
          </div>
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
        </nav>
      </div>
    </aside>

    <!-- Main wrapper -->
    <div class="main-wrapper">
      <!-- Header simple -->
      <header class="main-header">
        <button v-if="!isDesktop" @click="toggleSidebar" class="btn-icon hamburger-btn">
          ☰
        </button>
        
        <div class="header-title">
          <h1>Sistema BAMBU</h1>
        </div>
        
        <div class="header-actions">
          <button @click="handleLogout" class="btn-icon">
            🚪
          </button>
        </div>
      </header>

      <!-- Content area -->
      <main class="content-area">
        <slot></slot>
      </main>
    </div>

    <!-- Sidebar overlay -->
    <div 
      v-if="sidebarOpen && !isDesktop" 
      class="sidebar-overlay" 
      @click="closeSidebar"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// Estado responsive simple
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)
const sidebarOpen = ref(false)

const isDesktop = computed(() => windowWidth.value >= 1024)

function handleResize() {
  windowWidth.value = window.innerWidth
  if (isDesktop.value) {
    sidebarOpen.value = false
    document.body.style.overflow = ''
  }
}

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
  if (sidebarOpen.value && !isDesktop.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

function closeSidebar() {
  sidebarOpen.value = false
  document.body.style.overflow = ''
}

function handleKeydown(e) {
  if (e.key === 'Escape' && sidebarOpen.value) {
    closeSidebar()
  }
}

async function handleLogout() {
  await authStore.logout()
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('resize', handleResize)
    document.addEventListener('keydown', handleKeydown)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', handleResize)
    document.removeEventListener('keydown', handleKeydown)
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
/* Layout base infalible */
.app-container {
  display: flex;
  min-height: 100vh;
  background: var(--bg-base);
}

.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0; /* CRÍTICO: evita desborde */
}

/* Header con CSS Grid infalible */
.main-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: var(--space-md);
  height: 64px;
  padding: 0 var(--space-md);
  border-bottom: 1px solid var(--border);
  background: var(--bg-surface);
  position: sticky;
  top: 0;
  z-index: var(--z-sticky);
}

.header-title h1 {
  font-size: var(--font-lg);
  color: var(--text-primary);
  margin: 0;
  font-weight: 600;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

/* Botón ícono infalible */
.btn-icon {
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  background: transparent;
  cursor: pointer;
  transition: var(--transition-normal);
  color: var(--text-secondary);
  font-size: var(--font-lg);
}

.btn-icon:hover {
  background: var(--bg-elevated);
  border-color: var(--border-hover);
  color: var(--text-primary);
}

/* Sidebar responsive */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: var(--bg-surface);
  border-right: 1px solid var(--border);
  transform: translateX(-100%);
  transition: transform var(--transition-normal);
  z-index: var(--z-overlay);
  display: flex;
  flex-direction: column;
}

.sidebar-open {
  transform: translateX(0);
}

/* Desktop: sidebar fijo */
@media (min-width: 1024px) {
  .sidebar {
    transform: none;
    position: relative;
  }
  
  .hamburger-btn {
    display: none;
  }
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.sidebar-header {
  display: flex;
  align-items: center;
  padding: var(--space-lg);
  border-bottom: 1px solid var(--border);
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.logo-img {
  width: 32px;
  height: 32px;
  display: grid;
  place-items: center;
  background: var(--primary-bg);
  border-radius: var(--radius-md);
  font-size: 18px;
}

.logo-text {
  font-size: var(--font-lg);
  font-weight: 600;
  color: var(--primary);
}

.sidebar-nav {
  flex: 1;
  padding: var(--space-md);
}

.nav-item {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  text-decoration: none;
  transition: var(--transition-normal);
  margin-bottom: var(--space-xs);
  min-height: 44px;
}

.nav-item:hover {
  background: var(--bg-elevated);
  color: var(--text-primary);
}

.nav-item.router-link-active {
  background: var(--primary-bg);
  color: var(--primary);
  font-weight: 500;
}

.nav-icon {
  font-size: var(--font-lg);
  width: 20px;
  text-align: center;
}

.nav-text {
  font-size: var(--font-sm);
}

/* Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--bg-overlay);
  z-index: calc(var(--z-overlay) - 1);
  animation: fadeIn var(--transition-fast);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Content area */
.content-area {
  flex: 1;
  overflow-x: auto;
  padding: var(--space-lg);
}

/* Desktop: más padding */
@media (min-width: 1024px) {
  .content-area {
    padding: var(--space-xl);
  }
}

/* Mobile ajustes */
@media (max-width: 767px) {
  .main-header {
    padding: 0 var(--space-sm);
    gap: var(--space-sm);
  }
  
  .btn-icon {
    width: 40px;
    height: 40px;
  }
  
  .content-area {
    padding: var(--space-sm);
  }
}
</style>