# REGLAS IMPERATIVAS PARA CLAUDE - SISTEMA BAMBU

## 📋 REGLAS DE DOCUMENTACIÓN

### 🚨 REGLA IMPERATIVA #1: UBICACIÓN DE DOCUMENTACIÓN
- **TODA la documentación DEBE estar en `documentacion-proyecto/`**
- **NUNCA crear, buscar o leer documentación dentro de `bambu-sistema-v2/`**
- La carpeta `bambu-sistema-v2/` es EXCLUSIVAMENTE para código de la aplicación
- Al hacer deploy, la carpeta `bambu-sistema-v2/` NO debe contener documentación

### 🚨 REGLA IMPERATIVA #2: PROHIBICIONES ABSOLUTAS
- **NUNCA** crear archivos .md dentro de `bambu-sistema-v2/`
- **NUNCA** buscar documentación dentro de `bambu-sistema-v2/`
- **NUNCA** leer documentación desde `bambu-sistema-v2/`
- La única excepción son archivos técnicos como README.md del framework si son necesarios

### 🚨 REGLA IMPERATIVA #3: ESTRUCTURA OBLIGATORIA
```
bambu_crm_full/
├── bambu-sistema-v2/          # ← SOLO CÓDIGO DE LA APLICACIÓN
│   ├── app/
│   ├── resources/
│   ├── database/
│   └── ... (resto del código)
└── documentacion-proyecto/     # ← TODA LA DOCUMENTACIÓN AQUÍ
    ├── DESIGN_SYSTEM.md
    ├── SYSTEM_ARCHITECTURE.md
    ├── API_ENDPOINTS.md
    └── ... (resto de docs)
```

### 🚨 REGLA IMPERATIVA #4: RITUAL DE COMMIT

**Trigger:** cuando el usuario diga `commit` o cualquier variante como `comittear`, `cerramos y comiteamos`.

**Procedimiento:**
1. **VERIFICAR UBICACIÓN**: Ejecutar `pwd` para confirmar directorio actual
2. **VERIFICAR CAMBIOS**: Ejecutar `git status` para ver qué archivos cambiar
3. Actualizar el archivo `STATUS.md`:
   - **Ruta exacta**: `C:\laragon\www\bambu_crm_full\STATUS.md` (raíz del proyecto)
   - **Si estás en bambu-sistema-v2/**: usar `../STATUS.md`
   - **Si estás en raíz**: usar `STATUS.md`
   - Agregar un bloque que incluya:
     - **Hecho:** resumen breve de lo que se completó.
     - **Siguiente:** lista corta (1–3 puntos) de próximos pasos concretos.
   - Mantener el formato y estilo ya usado.

4. **VERIFICAR NUEVAMENTE**: Ejecutar `git status` para confirmar STATUS.md modificado
5. **COMMIT**: `git add` con ruta correcta y realizar commit
6. **CONFIRMAR**: Verificar que commit fue exitoso

### 🚨 REGLA IMPERATIVA #5: REINICIO DE CONTEXTO

**Trigger:** cuando el usuario diga `ponete al día` o variante similar.

**Procedimiento:**
1. Leer y procesar SIEMPRE (en este orden):
   - `STATUS.md`
   - `CLAUDE.md`
   - Reglas imperativas vigentes

2. **Si en `STATUS.md` la sección "Siguiente" implica una tarea de desarrollo**  
   (p. ej., *frontend*, *UI*, *UX*, *CSS*, *componente*, *vista*, *layout*, *JavaScript*, *Vue*, *Laravel*, *refactor*), leer además, en este orden:
   - `documentacion-proyecto/STACK_TECH.md`
   - `documentacion-proyecto/SYSTEM_ARCHITECTURE.md`
   - `documentacion-proyecto/BAMBU_FRONTEND_SYSTEM.md` **[CORE OBLIGATORIO]**
   - `documentacion-proyecto/BAMBU_COLOR_SYSTEM.md`
   - `documentacion-proyecto/BAMBU_RESPONSIVE_SYSTEM.md`
   - `documentacion-proyecto/UX_UI_GUIDELINES_SISTEMA_BAMBU.md`
   - `documentacion-proyecto/DEV_HANDBOOK_LARAVEL_VUE.md`

3. Si tras leer lo anterior falta contexto específico del módulo/flujo mencionado en `STATUS.md`, leer los archivos relevantes dentro de `documentacion-proyecto/` indicados por ese estado (solo los necesarios).

4. Restablecer el contexto de trabajo según lo leído.

5. Confirmar: "Contexto actualizado desde `STATUS.md` y `CLAUDE.md`


### 🚨 REGLA IMPERATIVA #6: SISTEMA DE DISEÑO OBLIGATORIO

**Trigger:** Cualquier desarrollo frontend (componentes, vistas, estilos)

**Documentos OBLIGATORIOS a seguir (en orden):**
1. **BAMBU_FRONTEND_SYSTEM.md** - Sistema técnico CORE (CSS reset, utilidades, componentes)
2. **BAMBU_COLOR_SYSTEM.md** - Paleta y variables de colores
3. **BAMBU_RESPONSIVE_SYSTEM.md** - Breakpoints y sistema responsive
4. **UX_UI_GUIDELINES_SISTEMA_BAMBU.md** - Patrones UX específicos del dominio

**Prohibiciones ABSOLUTAS:**
- NUNCA hardcodear colores (usar SIEMPRE variables CSS)
- NUNCA empezar por desktop (SIEMPRE mobile-first)
- NUNCA border-radius mayor a 4px
- NUNCA improvisar breakpoints (usar los definidos)
- NUNCA omitir CSS reset del BAMBU_FRONTEND_SYSTEM.md
- NUNCA crear componentes sin seguir los patrones definidos

### 🚨 REGLA IMPERATIVA #7: DESARROLLO MOBILE-FIRST

**Procedimiento obligatorio:**
1. Diseñar y codear para 320px-375px PRIMERO
2. Agregar tablet styles con @media (min-width: 768px)
3. Agregar desktop styles con @media (min-width: 1024px)
4. Testear en los 5 viewports críticos antes de continuar

### 🚨 REGLA IMPERATIVA #8: ESTRUCTURA DE ARCHIVOS CSS/VUE

**Ubicación obligatoria:**
```
bambu-sistema-v2/resources/
├── css/
│   ├── app.css           # Variables de colores
│   ├── responsive.css    # Media queries
│   └── components.css    # Estilos componentes
└── js/
    ├── composables/
    │   ├── useTheme.js
    │   └── useResponsive.js
    └── components/
        └── [componentes].vue
```

### 🚨 REGLA IMPERATIVA #9: CHECKLIST PRE-COMMIT FRONTEND

**Antes de CUALQUIER commit con cambios frontend:**
- Verificar dark mode funciona
- Verificar light mode funciona
- Testear en 320px, 768px, 1024px
- Sin colores hardcodeados
- Sin console.log()
- Sin debugger statements
- Componentes siguen nomenclatura PascalCase
- Storybook build OK
- Tests de regresión visual pasando

## 🔍 REGLAS NUEVAS - POST REVISIÓN SENIOR FRONTEND

**Fecha**: 2025-08-08  
**Origen**: Revisión exhaustiva por senior frontend developer

### 🚨 REGLA IMPERATIVA #10: TOKENS ÚNICOS

**Trigger:** Cualquier definición de variables CSS

**Obligatorio:**
- **TODO** estilo debe usar tokens de `tokens.css`
- **PROHIBIDO** definir `--space-*`, `--font-*`, `--shadow-*`, `--transition-*` fuera de `tokens.css`
- **ÚNICA** fuente de verdad para todas las variables del sistema

### 🚨 REGLA IMPERATIVA #11: ESTRUCTURA CSS CONSISTENTE

**Trigger:** Creación o modificación de archivos CSS

**Estructura OBLIGATORIA:**
```
resources/css/
├── app.css           # Entry point (importa tokens, components, responsive)
├── tokens.css        # ÚNICA fuente de verdad para variables
├── components.css    # Estilos componentes
└── responsive.css    # Media queries
```

**PROHIBIDO:**
- Crear `reset.css`, `variables.css`, `utilities.css` como archivos separados
- Inconsistencia con esta estructura

### 🚨 REGLA IMPERATIVA #12: ACCESIBILIDAD OPERATIVA

**Trigger:** Cualquier estado crítico o alert de seguridad

**Obligatorio:**
- **NINGÚN** estado crítico depende solo del color
- **OBLIGATORIO** ícono + texto en alertas de seguridad y stock
- **PICTOGRAMAS** GHS/ADR en productos químicos peligrosos
- **ARIA** labels y roles en elementos críticos

### 🚨 REGLA IMPERATIVA #13: SIDEBAR ACCESIBLE

**Trigger:** Implementación o modificación del sidebar overlay

**Cuando `sidebarOpen === true`:**
- `body` sin scroll (`overflow: hidden`)
- `main` con `inert` attribute
- Focus-trap activo dentro del sidebar
- Tecla `Esc` cierra el overlay
- Navegación por teclado funcional

### 🚨 REGLA IMPERATIVA #14: CALIDAD UI

**Trigger:** Antes de cualquier merge a master

**Checklist OBLIGATORIO:**
- Storybook build exitoso sin errores
- Tests de regresión visual pasando (Loki/Chromatic/Playwright)
- `npm run lint` sin warnings
- Touch targets ≥48px en vistas logísticas
- Media queries por capabilities implementadas

## 🛠️ COMANDOS DE DESARROLLO

### Testing
```bash
# Ejecutar tests
cd bambu-sistema-v2
php artisan test

# Tests específicos
php artisan test --filter=NombreDelTest
```

### Linting y Code Style
```bash
# PHP (Laravel Pint)
cd bambu-sistema-v2
./vendor/bin/pint

# JavaScript/Vue (ESLint + Prettier)
cd bambu-sistema-v2
npm run lint
npm run lint:fix
```

### Build y Compilación
```bash
# Desarrollo
cd bambu-sistema-v2
npm run dev

# Producción
cd bambu-sistema-v2
npm run build
```

## 📝 CÓMO AGREGAR REGLAS ADICIONALES

Para agregar nuevas reglas a este archivo:

1. **Reglas Imperativas**: Agregar en la sección correspondiente con formato `🚨 REGLA IMPERATIVA #N`
2. **Comandos**: Agregar en la sección "COMANDOS DE DESARROLLO" con ejemplos
3. **Estructura**: Actualizar diagramas de estructura si es necesario
4. **Documentación**: Siempre seguir el formato establecido con emojis y secciones claras

### Ejemplo de nueva regla:
```markdown
### 🚨 REGLA IMPERATIVA #4: NUEVA REGLA
- **Descripción clara de la regla**
- **Ejemplos de qué hacer y qué NO hacer**
- **Justificación si es necesaria**
```

## 🔄 FLUJO DE TRABAJO

1. **Antes de cualquier tarea**: Verificar que se cumplan las reglas de documentación
2. **Durante desarrollo**: Solo trabajar en `bambu-sistema-v2/` para código
3. **Para documentación**: Siempre usar `documentacion-proyecto/`
4. **Antes de commit**: Ejecutar linting y tests
5. **Deploy**: Solo `bambu-sistema-v2/` va a producción

## 📞 CONTACTO Y SOPORTE

- **Desarrollador Principal**: [Agregar información de contacto]
- **Repositorio**: [Agregar URL del repositorio]
- **Documentación Completa**: Ver `documentacion-proyecto/INDICE.md`

---

## 🔍 REVISIÓN SENIOR FRONTEND - CAMBIOS PROPUESTOS

**Fecha revisión**: 2025-08-08  
**Estado**: Pendientes de implementación  

Luego de someter el sistema a una revisión exhaustiva por parte de un senior frontend developer, se propusieron los siguientes cambios críticos para mejorar la robustez y mantenibilidad del sistema:

### 🚨 **CAMBIOS CRÍTICOS IMPLEMENTADOS EN REGLAS**

1. **Tokens únicos centralizados (Regla #10)**
   - **Problema**: Duplicación de variables CSS entre documentos
   - **Solución**: `tokens.css` como única fuente de verdad
   - **Impacto**: Eliminación de inconsistencias y deuda técnica

2. **Estructura CSS consistente (Regla #11)**
   - **Problema**: Conflicto entre CLAUDE.md #8 y BAMBU_FRONTEND_SYSTEM.md
   - **Solución**: Estructura CSS única y obligatoria
   - **Impacto**: Claridad para desarrolladores junior

3. **Accesibilidad operativa (Regla #12)**
   - **Problema**: Estados críticos dependientes solo del color
   - **Solución**: Ícono + texto + pictogramas GHS/ADR obligatorios
   - **Impacto**: Cumplimiento normativas industriales y accesibilidad

4. **Sidebar completamente accesible (Regla #13)**
   - **Problema**: Overlay solo visual, sin bloqueo de navegación
   - **Solución**: Focus-trap + inert + Esc + overflow control
   - **Impacto**: Accesibilidad completa para usuarios de teclado

5. **Calidad UI garantizada (Regla #14)**
   - **Problema**: Sin validación visual sistemática
   - **Solución**: Storybook + regresión visual + touch targets
   - **Impacto**: Calidad consistente en producción

### ⚡ **PRÓXIMOS PASOS DE IMPLEMENTACIÓN**

**Prioridad ALTA (1-3 días):**
1. Crear `tokens.css` unificado con todas las variables
2. Corregir `BambuCard.vue` - agregar `const emit = defineEmits(['click'])`
3. Implementar focus-trap completo en sidebar overlay
4. Agregar `--shadow-sm/md/lg` tokens faltantes

**Prioridad MEDIA (1 semana):**
5. Migrar a `data-theme` en `<html>` lugar de `body.light-mode`
6. Agregar `--bg-overlay` y `--on-*` tokens
7. Subir contraste de bordes dark mode (20% → 26-28% L)
8. Implementar media queries por capabilities

**Prioridad BAJA (2 semanas):**
9. Eliminar utilidades duplicadas de Tailwind
10. Setup de tests de regresión visual
11. Documentar casos edge de responsive

### 💡 **BENEFICIOS ESPERADOS**

- **Mantenibilidad**: Sistema único de tokens y estructura
- **Accesibilidad**: Cumplimiento AA y normas industriales
- **Performance**: Menos CSS duplicado, mejor cascada
- **Developer Experience**: Claridad en estructura, menos confusión
- **Calidad**: Validación visual automática, menos bugs

---

**Última actualización**: 2025-08-08
**Versión**: 2.0.0 (Post-revisión senior frontend)