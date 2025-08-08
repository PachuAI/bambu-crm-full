/**
 * BAMBU useResponsive Composable - Sistema Mobile-First
 * 
 * Versión: 1.0.0
 * Fecha: 2025-08-08
 * 
 * ⚡ Engineered by ÍTERA
 * https://iteraestudio.com
 * 
 * Maneja breakpoints, sidebar responsive y capabilities del dispositivo
 */

import { ref, computed, onMounted, onUnmounted } from 'vue'

// Breakpoints definidos en BAMBU_RESPONSIVE_SYSTEM.md
const BREAKPOINTS = {
  mobile: 0,
  tablet: 768,
  desktop: 1024,
  wide: 1440
} as const

export function useResponsive() {
  const windowWidth = ref(window.innerWidth)
  const windowHeight = ref(window.innerHeight)
  const sidebarOpen = ref(false)
  
  // ========================================
  // COMPUTED - BREAKPOINTS
  // ========================================
  
  const isMobile = computed(() => windowWidth.value < BREAKPOINTS.tablet)
  const isTablet = computed(() => 
    windowWidth.value >= BREAKPOINTS.tablet && windowWidth.value < BREAKPOINTS.desktop
  )
  const isDesktop = computed(() => windowWidth.value >= BREAKPOINTS.desktop)
  const isWide = computed(() => windowWidth.value >= BREAKPOINTS.wide)
  
  // Helpers adicionales
  const isTabletOrMobile = computed(() => windowWidth.value < BREAKPOINTS.desktop)
  const isDesktopOrWide = computed(() => windowWidth.value >= BREAKPOINTS.desktop)
  
  // ========================================
  // COMPUTED - DEVICE CAPABILITIES
  // ========================================
  
  const hasTouch = computed(() => 'ontouchstart' in window)
  const hasHover = computed(() => window.matchMedia('(hover: hover)').matches)
  const prefersReducedMotion = computed(() => 
    window.matchMedia('(prefers-reduced-motion: reduce)').matches
  )
  const isLandscape = computed(() => windowWidth.value > windowHeight.value)
  
  // ========================================
  // COMPUTED - LAYOUT
  // ========================================
  
  const sidebarMode = computed(() => {
    if (isDesktop.value) return 'fixed'
    return 'overlay'
  })
  
  const showHamburger = computed(() => !isDesktop.value)
  const showHeaderSearch = computed(() => !isMobile.value)
  
  // Grid columns por breakpoint
  const gridCols = computed(() => {
    if (isMobile.value) return 1
    if (isTablet.value) return 2
    if (isDesktop.value) return 4
    return 4
  })
  
  // Container max-width
  const containerClass = computed(() => {
    if (isMobile.value) return 'container'
    if (isTablet.value) return 'container'
    if (isWide.value) return 'container container-wide'
    return 'container'
  })
  
  // ========================================
  // METHODS - SIDEBAR CONTROL
  // ========================================
  
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
    
    // Prevenir scroll del body cuando sidebar está abierto en mobile
    if (sidebarMode.value === 'overlay') {
      if (sidebarOpen.value) {
        document.body.style.overflow = 'hidden'
      } else {
        document.body.style.overflow = ''
      }
    }
  }
  
  const closeSidebar = () => {
    if (sidebarOpen.value) {
      sidebarOpen.value = false
      document.body.style.overflow = ''
    }
  }
  
  const openSidebar = () => {
    if (!sidebarOpen.value && sidebarMode.value === 'overlay') {
      sidebarOpen.value = true
      document.body.style.overflow = 'hidden'
    }
  }
  
  // ========================================
  // METHODS - ACCESSIBILITY & FOCUS
  // ========================================
  
  const setupSidebarFocusTrap = () => {
    if (!sidebarOpen.value || sidebarMode.value === 'fixed') return
    
    const sidebar = document.querySelector('[data-sidebar]')
    const main = document.querySelector('main')
    
    if (sidebar && main) {
      // Marcar main como inert (no interactuable)
      main.setAttribute('inert', '')
      
      // Focus primer elemento focusable del sidebar
      const focusableElements = sidebar.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
      const firstFocusable = focusableElements[0] as HTMLElement
      firstFocusable?.focus()
    }
  }
  
  const removeSidebarFocusTrap = () => {
    const main = document.querySelector('main')
    if (main) {
      main.removeAttribute('inert')
    }
  }
  
  // ========================================
  // METHODS - KEYBOARD HANDLING
  // ========================================
  
  const handleEscapeKey = (event: KeyboardEvent) => {
    if (event.key === 'Escape' && sidebarOpen.value) {
      closeSidebar()
    }
  }
  
  // ========================================
  // METHODS - RESIZE HANDLING
  // ========================================
  
  const handleResize = () => {
    windowWidth.value = window.innerWidth
    windowHeight.value = window.innerHeight
    
    // Auto-cerrar sidebar cuando cambia a desktop
    if (isDesktop.value && sidebarOpen.value) {
      closeSidebar()
    }
  }
  
  // ========================================
  // METHODS - VIEWPORT UTILITIES
  // ========================================
  
  const getViewportInfo = () => ({
    width: windowWidth.value,
    height: windowHeight.value,
    breakpoint: isMobile.value ? 'mobile' : isTablet.value ? 'tablet' : 'desktop',
    orientation: isLandscape.value ? 'landscape' : 'portrait',
    hasTouch: hasTouch.value,
    hasHover: hasHover.value,
    reducedMotion: prefersReducedMotion.value
  })
  
  const isBreakpoint = (bp: keyof typeof BREAKPOINTS) => {
    return windowWidth.value >= BREAKPOINTS[bp]
  }
  
  // ========================================
  // METHODS - TOUCH TARGET UTILITIES
  // ========================================
  
  const getTouchTargetClass = () => {
    if (hasTouch.value && !hasHover.value) {
      return 'modo-guantes' // Touch device
    }
    if (isDesktop.value) {
      return 'modo-alta-densidad' // Desktop
    }
    return ''
  }
  
  // ========================================
  // LIFECYCLE
  // ========================================
  
  onMounted(() => {
    // Event listeners
    window.addEventListener('resize', handleResize, { passive: true })
    document.addEventListener('keydown', handleEscapeKey)
    
    // Watch sidebar changes for accessibility
    const sidebarWatcher = computed(() => sidebarOpen.value)
    sidebarWatcher.effect(() => {
      if (sidebarOpen.value && sidebarMode.value === 'overlay') {
        setupSidebarFocusTrap()
      } else {
        removeSidebarFocusTrap()
      }
    })
  })
  
  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    document.removeEventListener('keydown', handleEscapeKey)
    
    // Cleanup body styles
    document.body.style.overflow = ''
  })
  
  // ========================================
  // RETURN API
  // ========================================
  
  return {
    // State
    windowWidth,
    windowHeight,
    sidebarOpen,
    
    // Breakpoints
    isMobile,
    isTablet,
    isDesktop,
    isWide,
    isTabletOrMobile,
    isDesktopOrWide,
    
    // Device capabilities
    hasTouch,
    hasHover,
    prefersReducedMotion,
    isLandscape,
    
    // Layout computed
    sidebarMode,
    showHamburger,
    showHeaderSearch,
    gridCols,
    containerClass,
    
    // Sidebar control
    toggleSidebar,
    closeSidebar,
    openSidebar,
    
    // Utilities
    getViewportInfo,
    isBreakpoint,
    getTouchTargetClass,
    
    // Constants
    BREAKPOINTS
  }
}