/**
 * useResponsive.js - Composable para manejo responsive del layout
 * 
 * Implementa el patrón canónico del revisor para evitar bugs de layout.
 * Maneja sidebar overlay/columna, breakpoints y control de scroll.
 */

import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'

export function useResponsive() {
  // Estado del sidebar
  const sidebarOpen = ref(false)
  const isMobile = ref(true)
  
  // Media query para desktop (1024px)
  const mq = window.matchMedia('(min-width: 1024px)')
  
  // Actualizar estado mobile/desktop
  const update = () => {
    isMobile.value = !mq.matches
    
    // Auto-cerrar sidebar al cambiar a desktop
    if (!isMobile.value) {
      sidebarOpen.value = false
    }
  }
  
  // Toggle sidebar
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
  }
  
  // Cerrar sidebar
  const closeSidebar = () => {
    sidebarOpen.value = false
  }
  
  // Manejar tecla Escape
  const handleEscape = (e) => {
    if (e.key === 'Escape' && sidebarOpen.value && isMobile.value) {
      closeSidebar()
    }
  }
  
  // Control de scroll del body cuando sidebar está abierto
  watch(sidebarOpen, (isOpen) => {
    if (isOpen && isMobile.value) {
      // Bloquear scroll del body
      document.body.style.overflow = 'hidden'
      
      // Marcar main como inert (opcional, para mejor accesibilidad)
      const main = document.querySelector('main')
      if (main) {
        main.setAttribute('inert', '')
      }
    } else {
      // Restaurar scroll
      document.body.style.overflow = ''
      
      // Quitar inert
      const main = document.querySelector('main')
      if (main) {
        main.removeAttribute('inert')
      }
    }
  })
  
  // También observar cambios en isMobile para limpiar cuando cambia a desktop
  watch(isMobile, (mobile) => {
    if (!mobile && sidebarOpen.value) {
      closeSidebar()
    }
  })
  
  // Computed properties útiles
  const isTablet = computed(() => {
    const width = window.innerWidth
    return width >= 768 && width < 1024
  })
  
  const isDesktop = computed(() => !isMobile.value)
  
  // Lifecycle hooks
  onMounted(() => {
    // Inicializar estado
    update()
    
    // Agregar listeners
    mq.addEventListener('change', update)
    window.addEventListener('keydown', handleEscape)
  })
  
  onBeforeUnmount(() => {
    // Limpiar listeners
    mq.removeEventListener('change', update)
    window.removeEventListener('keydown', handleEscape)
    
    // Restaurar body overflow
    document.body.style.overflow = ''
    
    // Quitar inert si existe
    const main = document.querySelector('main')
    if (main) {
      main.removeAttribute('inert')
    }
  })
  
  // API pública del composable
  return {
    // Estado
    sidebarOpen,
    isMobile,
    isTablet,
    isDesktop,
    
    // Métodos
    toggleSidebar,
    closeSidebar
  }
}