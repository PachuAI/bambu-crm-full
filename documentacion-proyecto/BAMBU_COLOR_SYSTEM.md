# 🎨 Sistema de Colores BAMBU - Guía Definitiva

## Índice
- [Filosofía del Sistema](#filosofía-del-sistema)
- [Paleta Base HSL](#paleta-base-hsl)
- [Variables CSS Definitivas](#variables-css-definitivas)
- [Implementación en Vue/Tailwind](#implementación-en-vuetailwind)
- [Uso Práctico](#uso-práctico)
- [Testing de Colores](#testing-de-colores)

---

## Filosofía del Sistema

### Principios Core
1. **Dark Mode por defecto** - Profesional y moderno
2. **Light Mode automático** - Usando la fórmula de inversión con lógica visual
3. **Nomenclatura agnóstica** - Variables que funcionan en ambos temas
4. **Mínima complejidad** - Solo los colores necesarios

### Lo que necesitamos (y nada más)
- **3 niveles de neutrales** para jerarquía visual
- **1 color primario** (violeta #6366f1) con variaciones
- **4 colores semánticos** para estados
- **Efectos visuales** (bordes, sombras, highlights)

---

## Paleta Base HSL

### 🎯 Color Primario - Violeta BAMBU
```css
/* Hue base del violeta que ya usamos */
--brand-hue: 238; /* Violeta/Índigo */
```

### 🌑 Dark Mode (Predeterminado)

```css
:root {
  /* === CONFIGURACIÓN BASE === */
  --brand-hue: 238;
  --neutral-sat: 15%; /* Saturación sutil para neutrales */
  
  /* === FONDOS - 3 Niveles con 5% de diferencia === */
  --bg-base: hsl(var(--brand-hue), var(--neutral-sat), 5%);      /* #0a0a0f - Más oscuro */
  --bg-surface: hsl(var(--brand-hue), var(--neutral-sat), 10%);  /* #14141f - Cards */
  --bg-elevated: hsl(var(--brand-hue), var(--neutral-sat), 15%); /* #1f1f2e - Elevado */
  
  /* === TEXTOS - Contraste perfecto === */
  --text-primary: hsl(var(--brand-hue), 5%, 95%);    /* Casi blanco, suave */
  --text-secondary: hsl(var(--brand-hue), 5%, 70%);  /* Gris azulado claro */
  --text-muted: hsl(var(--brand-hue), 5%, 50%);      /* Texto terciario */
  
  /* === BORDES Y LÍNEAS === */
  --border: hsl(var(--brand-hue), 10%, 20%);         /* Sutil pero visible */
  --border-hover: hsl(var(--brand-hue), 10%, 30%);   /* Más claro en hover */
  
  /* === COLOR PRIMARIO - Violeta === */
  --primary: hsl(238, 84%, 67%);         /* #6366f1 - Violeta base */
  --primary-hover: hsl(238, 84%, 62%);   /* Más oscuro en hover */
  --primary-active: hsl(238, 84%, 57%);  /* Más oscuro en click */
  --primary-bg: hsl(238, 84%, 67%, 0.1); /* Fondo transparente */
  
  /* === COLORES SEMÁNTICOS === */
  --success: hsl(142, 71%, 45%);         /* Verde éxito */
  --warning: hsl(38, 92%, 50%);          /* Amarillo advertencia */
  --error: hsl(0, 84%, 60%);             /* Rojo error */
  --info: hsl(199, 89%, 48%);            /* Azul información */
  
  /* === EFECTOS VISUALES === */
  --highlight: hsl(var(--brand-hue), 20%, 25%);      /* Borde superior iluminado */
  --shadow: none;                                      /* Sin sombras en dark */
  --glow: 0 0 20px hsl(238, 84%, 67%, 0.3);         /* Resplandor violeta */
}
```

### ☀️ Light Mode

```css
body.light-mode {
  /* === FONDOS - Invertidos con lógica visual === */
  --bg-base: hsl(var(--brand-hue), 10%, 98%);      /* Casi blanco */
  --bg-surface: hsl(var(--brand-hue), 10%, 100%);  /* Blanco puro para cards */
  --bg-elevated: hsl(var(--brand-hue), 10%, 96%);  /* Ligeramente gris */
  
  /* === TEXTOS - Invertidos === */
  --text-primary: hsl(var(--brand-hue), 15%, 10%);   /* Casi negro */
  --text-secondary: hsl(var(--brand-hue), 10%, 35%); /* Gris oscuro */
  --text-muted: hsl(var(--brand-hue), 8%, 55%);      /* Gris medio */
  
  /* === BORDES Y LÍNEAS === */
  --border: hsl(var(--brand-hue), 10%, 88%);         /* Gris muy claro */
  --border-hover: hsl(var(--brand-hue), 10%, 78%);   /* Más oscuro en hover */
  
  /* === COLOR PRIMARIO - Mismo violeta === */
  --primary: hsl(238, 84%, 67%);         /* Mantiene el violeta */
  --primary-hover: hsl(238, 84%, 62%);
  --primary-active: hsl(238, 84%, 57%);
  --primary-bg: hsl(238, 84%, 67%, 0.08);
  
  /* === COLORES SEMÁNTICOS - Ajustados para light === */
  --success: hsl(142, 76%, 36%);         /* Verde más oscuro */
  --warning: hsl(32, 95%, 44%);          /* Naranja más oscuro */
  --error: hsl(0, 72%, 51%);             /* Rojo más oscuro */
  --info: hsl(201, 96%, 32%);            /* Azul más oscuro */
  
  /* === EFECTOS VISUALES === */
  --highlight: hsl(0, 0%, 100%);         /* Blanco puro para highlight */
  --shadow: 0 1px 3px hsla(var(--brand-hue), 15%, 0%, 0.12),
            0 1px 2px hsla(var(--brand-hue), 15%, 0%, 0.24);
  --glow: 0 0 20px hsl(238, 84%, 67%, 0.2);
}
```

---

## Variables CSS Definitivas

### Archivo: `app.css` o `variables.css`

```css
/* === SISTEMA DE COLORES BAMBU === */
/* Este es el archivo definitivo - NO modificar sin consenso del equipo */

:root {
  /* === CONFIGURACIÓN === */
  --brand-hue: 238;
  --neutral-sat: 15%;
  
  /* === TRANSICIONES === */
  --transition-fast: 150ms ease;
  --transition-normal: 300ms ease;
  --transition-slow: 500ms ease;
  
  /* === DARK MODE (Default) === */
  --bg-base: hsl(var(--brand-hue), var(--neutral-sat), 5%);
  --bg-surface: hsl(var(--brand-hue), var(--neutral-sat), 10%);
  --bg-elevated: hsl(var(--brand-hue), var(--neutral-sat), 15%);
  
  --text-primary: hsl(var(--brand-hue), 5%, 95%);
  --text-secondary: hsl(var(--brand-hue), 5%, 70%);
  --text-muted: hsl(var(--brand-hue), 5%, 50%);
  
  --border: hsl(var(--brand-hue), 10%, 20%);
  --border-hover: hsl(var(--brand-hue), 10%, 30%);
  
  --primary: hsl(238, 84%, 67%);
  --primary-hover: hsl(238, 84%, 62%);
  --primary-active: hsl(238, 84%, 57%);
  --primary-bg: hsla(238, 84%, 67%, 0.1);
  
  --success: hsl(142, 71%, 45%);
  --success-bg: hsla(142, 71%, 45%, 0.1);
  --warning: hsl(38, 92%, 50%);
  --warning-bg: hsla(38, 92%, 50%, 0.1);
  --error: hsl(0, 84%, 60%);
  --error-bg: hsla(0, 84%, 60%, 0.1);
  --info: hsl(199, 89%, 48%);
  --info-bg: hsla(199, 89%, 48%, 0.1);
  
  --highlight: hsl(var(--brand-hue), 20%, 25%);
  --shadow: none;
  --glow: 0 0 20px hsla(238, 84%, 67%, 0.3);
}

/* === LIGHT MODE === */
body.light-mode {
  --bg-base: hsl(var(--brand-hue), 10%, 98%);
  --bg-surface: hsl(var(--brand-hue), 10%, 100%);
  --bg-elevated: hsl(var(--brand-hue), 10%, 96%);
  
  --text-primary: hsl(var(--brand-hue), 15%, 10%);
  --text-secondary: hsl(var(--brand-hue), 10%, 35%);
  --text-muted: hsl(var(--brand-hue), 8%, 55%);
  
  --border: hsl(var(--brand-hue), 10%, 88%);
  --border-hover: hsl(var(--brand-hue), 10%, 78%);
  
  --success: hsl(142, 76%, 36%);
  --warning: hsl(32, 95%, 44%);
  --error: hsl(0, 72%, 51%);
  --info: hsl(201, 96%, 32%);
  
  --highlight: hsl(0, 0%, 100%);
  --shadow: 0 1px 3px hsla(var(--brand-hue), 15%, 0%, 0.12),
            0 1px 2px hsla(var(--brand-hue), 15%, 0%, 0.24);
}

/* === MEDIA QUERY AUTOMÁTICO === */
@media (prefers-color-scheme: light) {
  :root:not(.dark-mode-forced) {
    /* Aplica light mode si el sistema lo prefiere */
    --bg-base: hsl(var(--brand-hue), 10%, 98%);
    /* ... resto de variables light ... */
  }
}
```

---

## Implementación en Vue/Tailwind

### Configuración Tailwind (`tailwind.config.js`)

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        // Base backgrounds
        'bg-base': 'var(--bg-base)',
        'bg-surface': 'var(--bg-surface)',
        'bg-elevated': 'var(--bg-elevated)',
        
        // Text colors
        'text-primary': 'var(--text-primary)',
        'text-secondary': 'var(--text-secondary)',
        'text-muted': 'var(--text-muted)',
        
        // Brand colors
        'primary': 'var(--primary)',
        'primary-hover': 'var(--primary-hover)',
        'primary-active': 'var(--primary-active)',
        
        // Semantic colors
        'success': 'var(--success)',
        'warning': 'var(--warning)',
        'error': 'var(--error)',
        'info': 'var(--info)',
        
        // Borders
        'border-default': 'var(--border)',
        'border-hover': 'var(--border-hover)',
      },
      boxShadow: {
        'default': 'var(--shadow)',
        'glow': 'var(--glow)',
      }
    }
  }
}
```

### Composable Vue para Theme Toggle

```javascript
// composables/useTheme.js
import { ref, onMounted } from 'vue'

export function useTheme() {
  const isDark = ref(true)
  
  const toggleTheme = () => {
    isDark.value = !isDark.value
    updateTheme()
  }
  
  const updateTheme = () => {
    if (isDark.value) {
      document.body.classList.remove('light-mode')
      localStorage.setItem('theme', 'dark')
    } else {
      document.body.classList.add('light-mode')
      localStorage.setItem('theme', 'light')
    }
  }
  
  onMounted(() => {
    // Cargar preferencia guardada
    const saved = localStorage.getItem('theme')
    if (saved === 'light') {
      isDark.value = false
      updateTheme()
    }
  })
  
  return { isDark, toggleTheme }
}
```

---

## Uso Práctico

### Ejemplos de Componentes

#### Card con todos los efectos

```vue
<template>
  <div class="card">
    <h3 class="card-title">Título</h3>
    <p class="card-text">Contenido</p>
  </div>
</template>

<style scoped>
.card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-top: 1px solid var(--highlight);
  border-radius: 4px;
  padding: 1.5rem;
  transition: var(--transition-fast);
}

.card:hover {
  background: var(--bg-elevated);
  border-color: var(--border-hover);
  box-shadow: var(--glow);
  transform: translateY(-2px);
}

.card-title {
  color: var(--text-primary);
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.card-text {
  color: var(--text-secondary);
  line-height: 1.6;
}
</style>
```

#### Botón Primario con Estados

```vue
<template>
  <button class="btn-primary">
    Acción Principal
  </button>
</template>

<style scoped>
.btn-primary {
  background: var(--primary);
  color: white;
  padding: 0.625rem 1.25rem;
  border-radius: 4px;
  font-weight: 500;
  transition: var(--transition-fast);
  border: none;
  cursor: pointer;
}

.btn-primary:hover {
  background: var(--primary-hover);
  box-shadow: var(--glow);
  transform: translateY(-1px);
}

.btn-primary:active {
  background: var(--primary-active);
  transform: translateY(0);
}
</style>
```

#### Estados Semánticos

```vue
<template>
  <div class="alert" :class="`alert-${type}`">
    {{ message }}
  </div>
</template>

<style scoped>
.alert {
  padding: 1rem;
  border-radius: 4px;
  border: 1px solid;
  font-weight: 500;
}

.alert-success {
  background: var(--success-bg);
  border-color: var(--success);
  color: var(--success);
}

.alert-warning {
  background: var(--warning-bg);
  border-color: var(--warning);
  color: var(--warning);
}

.alert-error {
  background: var(--error-bg);
  border-color: var(--error);
  color: var(--error);
}

.alert-info {
  background: var(--info-bg);
  border-color: var(--info);
  color: var(--info);
}
</style>
```

---

## Testing de Colores

### Checklist de Validación

#### Contraste WCAG AA
- [ ] Text primary sobre bg-base: > 7:1
- [ ] Text secondary sobre bg-base: > 4.5:1
- [ ] Primary sobre white: > 4.5:1
- [ ] Estados semánticos legibles

#### Consistencia Visual
- [ ] Cards se distinguen del fondo
- [ ] Hover states son visibles
- [ ] Bordes son sutiles pero perceptibles
- [ ] Jerarquía visual clara

#### Pruebas Reales
- [ ] Dark mode en pantalla OLED
- [ ] Light mode con luz ambiente alta
- [ ] Transición suave entre temas
- [ ] Sin flasheo al cargar

### Herramienta de Test Rápido

```html
<!-- test-colors.html -->
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="./variables.css">
  <style>
    body { 
      background: var(--bg-base);
      padding: 2rem;
      font-family: system-ui;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }
    .swatch {
      padding: 2rem 1rem;
      border-radius: 4px;
      text-align: center;
      color: white;
      font-weight: 600;
    }
    .toggle {
      position: fixed;
      top: 1rem;
      right: 1rem;
      padding: 0.5rem 1rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <button class="toggle" onclick="document.body.classList.toggle('light-mode')">
    Toggle Theme
  </button>
  
  <div class="grid">
    <div class="swatch" style="background: var(--bg-base)">bg-base</div>
    <div class="swatch" style="background: var(--bg-surface)">bg-surface</div>
    <div class="swatch" style="background: var(--bg-elevated)">bg-elevated</div>
    <div class="swatch" style="background: var(--primary)">primary</div>
    <div class="swatch" style="background: var(--success)">success</div>
    <div class="swatch" style="background: var(--warning)">warning</div>
    <div class="swatch" style="background: var(--error)">error</div>
    <div class="swatch" style="background: var(--info)">info</div>
  </div>
</body>
</html>
```

---

## 📋 Resumen Ejecutivo

### Lo que logramos:
1. **Sistema simple**: Solo 3 fondos, 3 textos, 1 primario, 4 semánticos
2. **Dark/Light automático**: Variables que funcionan en ambos modos
3. **Violeta BAMBU**: #6366f1 como identidad visual
4. **Profesional**: Bordes mínimos, sin redondeos excesivos
5. **Efectos elegantes**: Glows, highlights, transiciones suaves

### Reglas de oro:
- **NUNCA** cambiar los valores HSL base sin consenso
- **SIEMPRE** usar las variables CSS, no valores hardcodeados
- **Dark mode** es el default, light mode es la excepción
- **Testear** en ambos modos antes de cualquier commit

---

*Sistema de colores definitivo BAMBU v1.0 - No modificar sin aprobación del equipo*