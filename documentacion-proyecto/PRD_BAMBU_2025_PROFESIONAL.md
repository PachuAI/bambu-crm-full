# Product Requirements Document (PRD)
# Sistema de Gestión Integral BAMBU 2025

## 1. RESUMEN EJECUTIVO

### 1.1 Visión del Producto
El Sistema BAMBU es una plataforma web integral diseñada para centralizar y automatizar todas las operaciones comerciales de la empresa BAMBU, fabricante de productos químicos de limpieza. La solución reemplazará el actual ecosistema fragmentado de hojas de cálculo con una aplicación unificada que gestione cotizaciones, pedidos, inventario y logística de distribución.

### 1.2 Objetivos de Negocio
- **Eliminar procesos manuales** que generan errores y pérdida de tiempo
- **Unificar la información** en una única fuente de verdad
- **Automatizar cálculos** de precios y descuentos
- **Optimizar la logística** de reparto con planificación inteligente
- **Proveer visibilidad en tiempo real** del estado del negocio

### 1.3 Métricas de Éxito
- Reducción del 90% en tiempo de creación de cotizaciones
- Eliminación del 100% de errores de cálculo manual
- Reducción del 50% en tiempo de planificación logística
- Aumento del 30% en eficiencia de rutas de reparto
- Disponibilidad del sistema del 99.9%

## 2. CONTEXTO Y PROBLEMA

### 2.1 Situación Actual
La empresa BAMBU opera actualmente con:
- **Múltiples hojas de cálculo** para cotizaciones, cada vendedor con su versión
- **Planillas separadas** para logística, actualizadas manualmente
- **Sin control unificado** de stock en tiempo real
- **Procesos propensos a errores** por duplicación de datos

### 2.2 Problemas Identificados
1. **Fragmentación de Información**: Datos dispersos en múltiples archivos
2. **Errores Manuales**: Cálculos incorrectos, datos desactualizados
3. **Falta de Trazabilidad**: Sin historial de cambios o auditoría
4. **Ineficiencia Operativa**: Procesos lentos y repetitivos
5. **Sin Visibilidad**: Imposible obtener métricas en tiempo real

### 2.3 Oportunidad
Desarrollar un sistema web moderno que:
- Centralice toda la operación comercial
- Automatice procesos repetitivos
- Provea información en tiempo real
- Escale con el crecimiento del negocio

## 3. USUARIOS Y STAKEHOLDERS

### 3.1 Usuarios Primarios
1. **Vendedores** (5-10 usuarios)
   - Crean cotizaciones diariamente
   - Necesitan respuesta rápida a clientes
   - Requieren información de stock actualizada

2. **Personal de Logística** (3-5 usuarios)
   - Planifican rutas de reparto
   - Asignan vehículos y conductores
   - Monitorean entregas

3. **Administradores** (2-3 usuarios)
   - Gestionan catálogo de productos
   - Configuran precios y descuentos
   - Analizan métricas del negocio

### 3.2 Stakeholders
- **Gerencia**: Requiere dashboards y reportes
- **Clientes**: Esperan cotizaciones precisas y entregas puntuales
- **IT**: Responsable del mantenimiento y escalabilidad

## 4. REQUERIMIENTOS FUNCIONALES

### 4.1 Gestión de Datos Maestros

#### 4.1.1 Clientes
- **Crear, editar y eliminar** clientes con soft delete
- **Campos requeridos**:
  - Razón social/Nombre comercial
  - CUIT/DNI
  - Dirección completa (calle, número, ciudad, CP)
  - Teléfono principal y alternativo
  - Email
  - Persona de contacto
  - Condición frente al IVA
- **Búsqueda avanzada** por cualquier campo
- **Historial** de pedidos y cotizaciones
- **Categorización** por tipo de cliente (mayorista, minorista, etc.)

#### 4.1.2 Productos
- **Gestión completa** con control de versiones
- **Atributos del producto**:
  - Código SKU único
  - Nombre comercial
  - Descripción detallada
  - Precio base (L1)
  - Stock actual y mínimo
  - Peso/Volumen para logística
  - Categoría (limpieza, desinfección, etc.)
  - Estado (activo, discontinuado)
- **Control de stock** con alertas de mínimos
- **Trazabilidad** de movimientos
- **Preparado para multi-marca** (inicialmente solo BAMBU)

#### 4.1.3 Configuración del Sistema
- **Niveles de descuento** configurables:
  - L1: 0% (precio base)
  - L2: 5% (desde $X)
  - L3: 10% (desde $Y)  
  - L4: 15% (desde $Z)
- **Ciudades y zonas** de reparto
- **Vehículos** con capacidad en bultos
- **Días de reparto** por zona

### 4.2 Módulo de Cotizaciones

#### 4.2.1 Creación de Cotizaciones
- **Interfaz intuitiva** con búsqueda predictiva
- **Selección de cliente** con autocompletado
- **Agregar productos** con validación de stock
- **Cálculo automático** de:
  - Subtotales por línea
  - Descuento según volumen
  - IVA y percepciones
  - Total final
- **Vista previa** en tiempo real
- **Guardar como borrador** o confirmar

#### 4.2.2 Reglas de Negocio
- **Descuentos automáticos** según monto total
- **Productos combo** excluidos del cálculo de descuento
- **Validación de stock** antes de confirmar
- **Precios especiales** por cliente (futuro)
- **Vigencia** de la cotización configurable

#### 4.2.3 Salidas
- **PDF profesional** con logo y datos fiscales
- **Envío por email** directo desde el sistema
- **Compartir por WhatsApp** con link
- **Historial** de versiones

### 4.3 Gestión de Pedidos

#### 4.3.1 Conversión de Cotizaciones
- **Un click** para convertir cotización en pedido
- **Reserva automática** de stock
- **Número único** de pedido
- **Estados del pedido**:
  - Pendiente
  - Confirmado
  - En preparación
  - Listo para despacho
  - Entregado
  - Cancelado

#### 4.3.2 Gestión de Stock
- **Descuento automático** al confirmar pedido
- **Movimientos trazables** con usuario y timestamp
- **Ajustes manuales** con justificación
- **Alertas** de stock bajo
- **Reporte** de rotación

### 4.4 Módulo de Logística

#### 4.4.1 Planificación de Rutas
- **Vista calendario** semanal
- **Drag & drop** para asignar pedidos
- **Capacidad visual** de vehículos
- **Optimización** por zona geográfica
- **Restricciones** horarias por cliente

#### 4.4.2 Gestión de Repartos
- **Hoja de ruta** imprimible
- **Orden optimizado** de visitas
- **Check-in/out** por entrega
- **Firma digital** del cliente
- **Foto** del comprobante

#### 4.4.3 Monitoreo en Tiempo Real
- **Dashboard** de entregas del día
- **Estados**: En ruta, Entregado, No entregado
- **Notificaciones** de problemas
- **Métricas** de cumplimiento

### 4.5 Reportes y Analytics

#### 4.5.1 Dashboards
- **KPIs en tiempo real**:
  - Ventas del día/mes
  - Pedidos pendientes
  - Stock crítico
  - Eficiencia de entregas
- **Gráficos interactivos**
- **Filtros** por período, vendedor, zona

#### 4.5.2 Reportes Operativos
- **Ventas** por producto, cliente, vendedor
- **Stock** valorizado, movimientos, proyección
- **Logística**: cumplimiento, tiempos, costos
- **Clientes**: ranking, morosidad, frecuencia

#### 4.5.3 Exportación
- **Excel** con formato
- **PDF** para impresión
- **API** para integraciones

## 5. REQUERIMIENTOS NO FUNCIONALES

### 5.1 Rendimiento
- Tiempo de carga < 2 segundos
- Búsquedas < 500ms
- Soporte para 50 usuarios concurrentes
- Procesamiento de 1000 pedidos/día

### 5.2 Seguridad
- Autenticación con 2FA
- Encriptación SSL/TLS
- Backups automáticos diarios
- Logs de auditoría completos
- Cumplimiento RGPD

### 5.3 Usabilidad
- Interfaz responsive (desktop, tablet, mobile)
- Diseño intuitivo sin capacitación
- Accesibilidad WCAG 2.1 AA
- Soporte multi-idioma (preparado)

### 5.4 Disponibilidad
- Uptime 99.9%
- RTO < 4 horas
- RPO < 1 hora
- Mantenimiento sin downtime

### 5.5 Escalabilidad
- Arquitectura cloud-ready
- Horizontal scaling
- Cache distribuido
- CDN para assets

## 6. RESTRICCIONES Y SUPUESTOS

### 6.1 Restricciones Técnicas
- Presupuesto inicial limitado
- Equipo de desarrollo pequeño
- Hosting en servidor compartido inicial
- Integración con sistemas legacy

### 6.2 Restricciones de Negocio
- Go-live en 3 meses
- Migración sin pérdida de datos
- Operación paralela por 1 mes
- Capacitación mínima requerida

### 6.3 Supuestos
- Usuarios con conocimientos básicos de web
- Conexión a internet estable
- Navegadores modernos (Chrome, Firefox, Edge)
- Datos históricos en formato Excel

## 7. ROADMAP DE IMPLEMENTACIÓN

### Fase 1: MVP (Mes 1)
- Gestión de clientes y productos
- Cotizador básico
- Autenticación y permisos

### Fase 2: Pedidos y Stock (Mes 2)
- Conversión cotización → pedido
- Control de stock
- Reportes básicos

### Fase 3: Logística (Mes 3)
- Planificación de rutas
- Tracking de entregas
- Dashboard gerencial

### Fase 4: Optimizaciones (Mes 4+)
- App móvil para repartidores
- Integraciones (WhatsApp, Maps)
- BI avanzado

## 8. CRITERIOS DE ACEPTACIÓN

### 8.1 Para el MVP
- [ ] CRUD completo de entidades maestras
- [ ] Cotizador funcionando con cálculos correctos
- [ ] Sistema de permisos por rol
- [ ] Migración de datos exitosa
- [ ] Performance según SLA

### 8.2 Para Go-Live
- [ ] Todos los módulos operativos
- [ ] Usuarios capacitados
- [ ] Documentación completa
- [ ] Backups configurados
- [ ] Monitoreo activo

## 9. RIESGOS Y MITIGACIONES

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Resistencia al cambio | Alta | Alto | Capacitación intensiva, champions |
| Datos incorrectos migrados | Media | Alto | Validación exhaustiva, período de prueba |
| Performance inadecuado | Baja | Alto | Pruebas de carga, optimización temprana |
| Scope creep | Alta | Medio | Fases bien definidas, change control |

## 10. ANEXOS

### 10.1 Glosario
- **Bulto**: Unidad logística (bidón de 5kg)
- **L1-L4**: Niveles de descuento
- **SKU**: Stock Keeping Unit
- **Combo**: Producto compuesto

### 10.2 Referencias
- Sistema actual (screenshots)
- Planillas Excel de ejemplo
- Mockups de UI/UX
- Arquitectura técnica

---

**Documento creado**: Enero 2025  
**Versión**: 1.0  
**Estado**: En Revisión  
**Próxima revisión**: Febrero 2025