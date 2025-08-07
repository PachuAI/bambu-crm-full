import { ref, watch, onMounted } from 'vue'

const THEME_KEY = 'bambu_theme'

export function useTheme() {
  const isDark = ref(true) // Dark mode by default
  
  // Apply theme to HTML element
  const applyTheme = (dark: boolean) => {
    if (dark) {
      document.documentElement.classList.remove('light')
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
      document.documentElement.classList.add('light')
    }
  }
  
  // Toggle theme
  const toggleTheme = () => {
    isDark.value = !isDark.value
  }
  
  // Set theme explicitly
  const setTheme = (dark: boolean) => {
    isDark.value = dark
  }
  
  // Initialize theme from localStorage
  const initTheme = () => {
    const savedTheme = localStorage.getItem(THEME_KEY)
    
    if (savedTheme) {
      isDark.value = savedTheme === 'dark'
    } else {
      // Check system preference
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      isDark.value = prefersDark
    }
    
    applyTheme(isDark.value)
  }
  
  // Watch for theme changes
  watch(isDark, (newValue) => {
    applyTheme(newValue)
    localStorage.setItem(THEME_KEY, newValue ? 'dark' : 'light')
  })
  
  // Initialize on mount
  onMounted(() => {
    initTheme()
  })
  
  return {
    isDark,
    toggleTheme,
    setTheme,
    initTheme
  }
}