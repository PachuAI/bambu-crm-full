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

2. **Si en `STATUS.md` la sección “Siguiente” implica una tarea de desarrollo**  
   (p. ej., *frontend*, *UI*, *UX*, *CSS*, *componente*, *vista*, *layout*, *JavaScript*, *Vue*, *Laravel*, *refactor*), leer además, en este orden:
   - `documentacion-proyecto/STACK_TECH.md`
   - `documentacion-proyecto/SYSTEM_ARCHITECTURE.md`
   - `documentacion-proyecto/DESIGN_SYSTEM.md`
   - `documentacion-proyecto/DEV_HANDBOOK_LARAVEL_VUE.md`
   - `documentacion-proyecto/UX_UI_GUIDELINES_SISTEMA_BAMBU.md`

3. Si tras leer lo anterior falta contexto específico del módulo/flujo mencionado en `STATUS.md`, leer los archivos relevantes dentro de `documentacion-proyecto/` indicados por ese estado (solo los necesarios).

4. Restablecer el contexto de trabajo según lo leído.

5. Confirmar: "Contexto actualizado desde `STATUS.md` y `CLAUDE.md`


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
**Última actualización**: 2025-08-07
**Versión**: 1.0.0