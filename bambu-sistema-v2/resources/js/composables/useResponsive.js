// ============================================
// 📱 useResponsive.js - COMPOSABLE INFALIBLE
// ============================================

import { ref, computed, readonly, onMounted, onUnmounted } from 'vue'

export function useResponsive() {
  // Estado reactivo
  const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)
  const sidebarOpen = ref(false)
  
  // Breakpoints computados
  const isMobile = computed(() => windowWidth.value < 768)
  const isTablet = computed(() => windowWidth.value >= 768 && windowWidth.value < 1024)
  const isDesktop = computed(() => windowWidth.value >= 1024)
  
  // Manejador de resize
  function handleResize() {
    windowWidth.value = window.innerWidth
    
    // Auto-cerrar sidebar en desktop (método probado)
    if (isDesktop.value) {
      sidebarOpen.value = false
      // Limpiar body overflow por si acaso
      document.body.style.overflow = ''
    }
  }
  
  // Toggle sidebar con control body overflow
  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
    
    // Control body overflow SOLO en mobile
    if (sidebarOpen.value && isMobile.value) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
  
  // Cerrar sidebar
  function closeSidebar() {
    sidebarOpen.value = false
    document.body.style.overflow = ''
  }
  
  // Abrir sidebar (para casos específicos)
  function openSidebar() {
    sidebarOpen.value = true
    
    if (isMobile.value) {
      document.body.style.overflow = 'hidden'
    }
  }
  
  // Manejador de teclas (Escape para cerrar)
  function handleKeydown(event) {
    if (event.key === 'Escape' && sidebarOpen.value) {
      closeSidebar()
    }
  }
  
  // Manejador de click fuera del sidebar
  function handleClickOutside(event) {
    if (sidebarOpen.value && isMobile.value) {
      const sidebar = document.querySelector('.sidebar')
      const isClickInsideSidebar = sidebar && sidebar.contains(event.target)
      
      if (!isClickInsideSidebar) {
        closeSidebar()
      }
    }
  }
  
  // Lifecycle hooks
  onMounted(() => {
    // Solo ejecutar en cliente
    if (typeof window !== 'undefined') {
      window.addEventListener('resize', handleResize)
      document.addEventListener('keydown', handleKeydown)
      
      // Actualizar tamaño inicial
      handleResize()
    }
  })
  
  onUnmounted(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('resize', handleResize)
      document.removeEventListener('keydown', handleKeydown)
      
      // Limpiar body overflow al desmontar
      document.body.style.overflow = ''
    }
  })
  
  // API pública
  return {
    // Estado
    windowWidth: readonly(windowWidth),
    sidebarOpen: readonly(sidebarOpen),
    
    // Breakpoints computados
    isMobile,
    isTablet,
    isDesktop,
    
    // Métodos públicos
    toggleSidebar,
    closeSidebar,
    openSidebar,
    
    // Utilidades adicionales
    handleClickOutside,
    
    // Breakpoints como valores para uso directo
    breakpoints: {
      mobile: 768,
      tablet: 1024,
      desktop: 1024
    }
  }
}

// Utilidad para detectar si es SSR
export function isSSR() {
  return typeof window === 'undefined'
}

// Utilidad para obtener viewport info
export function getViewportInfo() {
  if (isSSR()) {
    return {
      width: 1024,
      height: 768,
      type: 'desktop'
    }
  }
  
  const width = window.innerWidth
  const height = window.innerHeight
  
  let type = 'mobile'
  if (width >= 1024) type = 'desktop'
  else if (width >= 768) type = 'tablet'
  
  return {
    width,
    height,
    type
  }
}