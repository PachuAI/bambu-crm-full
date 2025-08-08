import { ref } from 'vue'

const THEME_KEY = 'bambu_theme'

// Estado global del tema
const isDark = ref(false) // Light mode por defecto

export function useTheme() {
  // Aplicar tema usando data-theme attribute (sistema tokens.css)
  const applyTheme = (dark) => {
    console.log('Aplicando tema BAMBU:', dark ? 'dark' : 'light')
    
    // Usar data-theme en HTML como definido en tokens.css
    const html = document.documentElement
    
    if (dark) {
      html.setAttribute('data-theme', 'dark')
      console.log('Applied data-theme="dark"')
    } else {
      html.setAttribute('data-theme', 'light') 
      console.log('Applied data-theme="light"')
    }
  }
  
  // Toggle theme
  const toggleTheme = () => {
    isDark.value = !isDark.value
    applyTheme(isDark.value)
    localStorage.setItem(THEME_KEY, isDark.value ? 'dark' : 'light')
    console.log('Theme toggled to:', isDark.value ? 'dark' : 'light')
  }
  
  // Inicializar tema
  const initTheme = () => {
    const savedTheme = localStorage.getItem(THEME_KEY)
    
    if (savedTheme) {
      isDark.value = savedTheme === 'dark'
      console.log('Tema guardado encontrado:', savedTheme)
    } else {
      // Detectar preferencia del sistema si no hay tema guardado
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      isDark.value = prefersDark
      console.log('Tema del sistema detectado:', prefersDark ? 'dark' : 'light')
    }
    
    applyTheme(isDark.value)
  }
  
  return {
    isDark,
    toggleTheme,
    initTheme
  }
}