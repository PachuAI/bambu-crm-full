# Architecture Decision Records (ADR) - Decisiones 2025

**Versión**: 1.0.0 (Decisiones consolidadas)  
**Actualizado**: 2025-08-08 (Fecha estandarizada + inconsistencias resueltas)  
**Estado**: Registro oficial de decisiones arquitectónicas  

## Introducción

Este documento registra las decisiones arquitectónicas tomadas el **2025-08-08** durante la refactorización completa del sistema BAMBU. Estas decisiones reemplazan y actualizan los ADRs previos.

---

## ADR-008: Desarrollo Local Sin Docker

### Estado: ✅ Aceptado
### Fecha: Agosto 2025
### Contexto
El equipo evaluó usar Docker para desarrollo vs desarrollo local directo.

### Decisión
**Desarrollo local directo** usando Laragon en Windows.

### Razones
1. **Equipo pequeño**: 1-2 desarrolladores, no necesitamos portabilidad
2. **Setup más simple**: Laragon ya provee todo lo necesario
3. **Performance**: Sin overhead de virtualización
4. **Debugging**: Más directo y familiar

### Consecuencias
- ✅ Setup más rápido
- ✅ Menos complejidad
- ✅ Mejor performance local
- ❌ Menos portabilidad (pero no la necesitamos)

---

## ADR-009: PostgreSQL como Base de Datos Principal

### Estado: ✅ Aceptado (Decisión Corregida)
### Fecha: Agosto 2025
### Contexto
Evaluación inicial incorrecta sugirió MySQL por conveniencia. Revisión con mentalidad de owner.

### Decisión
**PostgreSQL 15+** como base de datos principal.

### Razones (Arquitectura Correcta)
1. **JSON Support Superior**: Cotizador maneja estructuras complejas
2. **Performance**: Queries complejas y reportes más eficientes
3. **Escalabilidad**: Particionamiento nativo para crecimiento
4. **Data Integrity**: ACID compliance más robusto
5. **Futuro**: Features avanzadas que necesitaremos

### Consecuencias
- ✅ Arquitectura sólida a largo plazo
- ✅ Mejor performance en casos complejos  
- ✅ Escalabilidad nativa
- ❌ Setup inicial ligeramente más complejo (justificado)

---

## ADR-010: Vue.js 3 SPA Puro (Sin Livewire)

### Estado: ✅ Aceptado
### Fecha: Agosto 2025
### Contexto
MVP actual mezcla Livewire + Blade. Nueva versión necesita consistencia.

### Decisión
**Vue.js 3 SPA puro** + TypeScript + Pinia, sin mezclar con Livewire.

### Razones
1. **Consistencia**: Una sola tecnología frontend
2. **UX moderna**: SPA ofrece mejor experiencia
3. **Separación clara**: API backend + SPA frontend
4. **Escalabilidad**: Más fácil mantener y extender

### Consecuencias
- ✅ UX/UI consistente
- ✅ Separación de responsabilidades
- ✅ Más fácil testing
- ❌ Requiere migrar toda la lógica de Livewire

---

## ADR-011: Deploy Manual (Sin CI/CD)

### Estado: ✅ Aceptado
### Fecha: Agosto 2025
### Contexto
Documentación original incluía CI/CD automático.

### Decisión
**Deploy manual** en VPS cuando el MVP esté listo.

### Razones
1. **Simplicidad**: No necesitamos automation compleja
2. **Control**: Deploy manual permite revisión
3. **Escala**: Para equipo pequeño es suficiente
4. **Foco**: Priorizamos funcionalidad sobre automatización

### Consecuencias
- ✅ Menos complejidad
- ✅ Control total del proceso
- ✅ Foco en desarrollo
- ❌ Deploy más lento (pero poco frecuente)

---

## ADR-012: Sin Herramientas de Monitoreo Pagos

### Estado: ✅ Aceptado
### Fecha: Agosto 2025
### Contexto
Documentación original incluía Sentry y servicios pagos.

### Decisión
**Logs nativos de Laravel** para monitoreo y debugging.

### Razones
1. **Costo**: Evitamos gastos innecesarios
2. **Suficiencia**: Laravel logs cubren nuestras necesidades
3. **Simplicidad**: Una herramienta menos que configurar
4. **Interno**: Sistema interno, no necesita monitoreo 24/7

### Consecuencias
- ✅ Sin costos adicionales
- ✅ Setup más simple
- ✅ Logs centralizados en Laravel
- ❌ Menos features avanzadas (pero no las necesitamos)

---

## ADR-013: Roadmap de 20 Semanas (No 16)

### Estado: ✅ Aceptado
### Fecha: Agosto 2025
### Contexto
Timeline original de 16 semanas era optimista para refactorización completa.

### Decisión
**20 semanas (5 meses)** con buffer para calidad.

### Razones
1. **Realismo**: Migración completa requiere más tiempo
2. **Calidad**: Preferimos código bien hecho que rápido
3. **Testing**: Incluye tiempo para tests desde día 1
4. **Buffer**: Margen para imprevistos

### Consecuencias
- ✅ Timeline más realista
- ✅ Mejor calidad final
- ✅ Menos estrés en desarrollo
- ❌ Go-live más tardío (pero más sólido)

---

## RESUMEN DE DECISIONES

### Stack Final Confirmado
- **Backend**: Laravel 11 + PostgreSQL + Sanctum
- **Frontend**: Vue 3 + TypeScript + Pinia + Tailwind
- **Desarrollo**: Laragon local (no Docker)
- **Deploy**: Manual en VPS
- **Monitoreo**: Laravel logs nativos

### Principios de Desarrollo
1. **Pragmatismo sobre perfección**
2. **Simplicidad sobre complejidad**
3. **Calidad sobre velocidad**
4. **Foco en funcionalidad del negocio**

---

---

## LECCIÓN APRENDIDA - CORRECCIÓN IMPORTANTE

### ⚠️ Error de Perspectiva Inicial
Durante la configuración inicial, se consideró cambiar de PostgreSQL a MySQL por **conveniencia técnica** (disponibilidad en Laragon). 

### ✅ Corrección con Mentalidad de Owner
Se revirtió la decisión priorizando:
1. **Arquitectura correcta** sobre conveniencia
2. **Visión a largo plazo** sobre facilidad inmediata  
3. **Calidad del producto** sobre velocidad de setup

### 📖 Principio Confirmado
> **Owner piensa en el producto y la arquitectura correcta, no en la conveniencia de desarrollo.**

---

**Documento creado**: Agosto 2025  
**Estado**: Decisiones finales aprobadas (PostgreSQL confirmado)  
**Próxima revisión**: Al finalizar Fase 0