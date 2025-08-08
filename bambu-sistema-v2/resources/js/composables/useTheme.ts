import { ref, watch, onMounted, computed } from 'vue'

const THEME_KEY = 'bambu_theme'

/**
 * Composable para manejo de temas dark/light según BAMBU Color System
 * - Dark mode por defecto (profesional)
 * - Light mode opcional
 * - Respeta prefers-color-scheme del sistema
 * - Persistencia en localStorage
 */
export function useTheme() {
  const isDark = ref(true) // Dark mode by default (BAMBU)
  const isTransitioning = ref(false)
  
  // Computed para tema actual como string
  const currentTheme = computed(() => isDark.value ? 'dark' : 'light')
  
  // Aplicar tema al body (según BAMBU_COLOR_SYSTEM.md)
  const applyTheme = (dark: boolean) => {
    // Prevenir FOUC con transición suave
    isTransitioning.value = true
    
    if (dark) {
      document.body.classList.remove('light-mode')
      document.body.classList.remove('dark-mode-forced')
    } else {
      document.body.classList.add('light-mode')
    }
    
    // Limpiar flag de transición
    setTimeout(() => {
      isTransitioning.value = false
    }, 300)
  }
  
  // Toggle theme con animación suave
  const toggleTheme = () => {
    isDark.value = !isDark.value
  }
  
  // Set theme explícitamente
  const setTheme = (dark: boolean) => {
    isDark.value = dark
  }
  
  // Forzar dark mode (ignora system preference)
  const forceDarkMode = () => {
    isDark.value = true
    document.body.classList.add('dark-mode-forced')
    localStorage.setItem(THEME_KEY, 'dark-forced')
  }
  
  // Inicializar tema desde localStorage o sistema
  const initTheme = () => {
    const savedTheme = localStorage.getItem(THEME_KEY)
    
    if (savedTheme) {
      if (savedTheme === 'dark-forced') {
        forceDarkMode()
        return
      }
      isDark.value = savedTheme === 'dark'
    } else {
      // Por defecto dark mode, pero respeta system preference
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      isDark.value = prefersDark
    }
    
    applyTheme(isDark.value)
  }
  
  // Escuchar cambios del sistema
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  const handleSystemChange = (e: MediaQueryListEvent) => {
    // Solo auto-cambiar si no hay preferencia guardada
    const savedTheme = localStorage.getItem(THEME_KEY)
    if (!savedTheme || !savedTheme.includes('forced')) {
      isDark.value = e.matches
    }
  }
  
  // Watch para cambios de tema
  watch(isDark, (newValue) => {
    applyTheme(newValue)
    // No guardar si está forzado
    if (!document.body.classList.contains('dark-mode-forced')) {
      localStorage.setItem(THEME_KEY, newValue ? 'dark' : 'light')
    }
  })
  
  // Initialize on mount
  onMounted(() => {
    initTheme()
    mediaQuery.addEventListener('change', handleSystemChange)
  })
  
  // Cleanup
  const cleanup = () => {
    mediaQuery.removeEventListener('change', handleSystemChange)
  }
  
  return {
    isDark,
    currentTheme,
    isTransitioning,
    toggleTheme,
    setTheme,
    forceDarkMode,
    initTheme,
    cleanup
  }
}