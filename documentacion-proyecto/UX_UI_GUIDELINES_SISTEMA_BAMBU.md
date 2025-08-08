# 🧪 Guidelines UX/UI - Dominio BAMBU CRM Químico

**Especialización**: CRM Productos Químicos - Alto Valle de Río Negro  
**Versión**: 4.0.1 (Revisado y corregido)  
**Actualizado**: 2025-08-08 (Inconsistencias resueltas + patrones actualizados)  

## Índice
- [Contexto del Negocio](#contexto-del-negocio)
- [Principios UX Bambú](#principios-ux-bambú)
- [Patrones de Interfaz Específicos](#patrones-de-interfaz-específicos)
- [Flujos de Trabajo CRM](#flujos-de-trabajo-crm)
- [Microinteracciones del Dominio](#microinteracciones-del-dominio)
- [Accesibilidad en Contexto Industrial](#accesibilidad-en-contexto-industrial)

> **📋 DOCUMENTOS TÉCNICOS COMPLEMENTARIOS:**
> - 🏗️ [BAMBU_FRONTEND_SYSTEM.md](./BAMBU_FRONTEND_SYSTEM.md) - Sistema técnico core
> - 🎨 [BAMBU_COLOR_SYSTEM.md](./BAMBU_COLOR_SYSTEM.md) - Paleta y variables
> - 📱 [BAMBU_RESPONSIVE_SYSTEM.md](./BAMBU_RESPONSIVE_SYSTEM.md) - Breakpoints y responsive

---

## Contexto del Negocio

### 🧪 Dominio: Productos Químicos Industriales

**Bambú** opera en el **Alto Valle de Río Negro** distribuyendo productos químicos industriales en bidones de 5L, 20L y contenedores IBC de 1000L.

**Usuarios primarios:**
- **Operarios logística**: Gestión de stock, picking, distribución
- **Vendedores**: Cotizaciones, seguimiento pedidos, atención clientes
- **Administración**: KPIs, reportes, facturación
- **Clientes finales**: Consulta pedidos, stock disponible

**Contexto geográfico:**
- **Rutas**: General Roca, Neuquén, Cipolletti, Cinco Saltos
- **Temporalidad**: Ciclos agrícolas estacionales
- **Urgencias**: Stock crítico en temporada alta

### 🎯 Objetivos UX Clave
1. **Eficiencia operativa**: Tareas comunes en < 3 clicks
2. **Prevención errores**: Validaciones críticas en productos peligrosos
3. **Visibilidad stock**: Estado en tiempo real, alertas automáticas
4. **Trazabilidad**: Seguimiento completo lote → cliente → entrega
5. **Movilidad**: Uso efectivo en tablets para logística

---

## Principios UX Bambú

### 🚀 Eficiencia Operativa

**Principio**: **"Minimizar clicks, maximizar información"**

- **Dashboard único**: Todo lo crítico en una pantalla
- **Acciones rápidas**: Bulk operations para tareas masivas
- **Búsqueda omnipresente**: Buscar por código, cliente, producto instantáneamente
- **Estado persistente**: Recordar filtros, vistas, preferencias

```
✅ BUENO: Ver stock + hacer pedido en 2 clicks
❌ MALO: Navegar 5 pantallas para ver disponibilidad
```

### 🛡️ Seguridad Industrial

**Principio**: **"Prevenir errores peligrosos"**

- **Productos peligrosos**: Alertas visuales claras (ácidos, bases)
- **Incompatibilidades**: Avisos automáticos de mezclas prohibidas
- **Fechas vencimiento**: Destacar productos próximos a vencer
- **Confirmaciones críticas**: Double-check en operaciones irreversibles

### 📊 Visibilidad del Negocio

**Principio**: **"Información crítica siempre visible"**

- **Stock crítico**: Indicadores rojos inmediatos
- **Pedidos urgentes**: Prioridad visual clara
- **Métricas clave**: Presentes en todas las vistas
- **Estado sistema**: Indicadores de sincronización, errores

### 🌐 Contexto Geográfico

**Principio**: **"Optimizar para el Alto Valle"**

- **Rutas predefinidas**: Ciudades y zonas preconfiguradas
- **Horarios locales**: Considerar horarios comerciales regionales
- **Clientes frecuentes**: Acceso rápido a clientes regulares
- **Estacionalidad**: Adaptarse a ciclos agrícolas

---

## Patrones de Interfaz Específicos

### 🧪 Tarjeta de Producto Químico

**Patrón**: Información esencial + seguridad visual

```vue
<!-- ProductoQuimicoCard.vue -->
<BambuCard :variant="getTipoVariant(producto.tipo)" clickable>
  <template #header>
    <div class="producto-header">
      <div class="producto-info">
        <h4>{{ producto.nombre }}</h4>
        <code class="producto-codigo">{{ producto.codigo }}</code>
      </div>
      <div class="producto-alertas">
        <span v-if="producto.es_peligroso" class="alerta-peligro">⚠️</span>
        <span v-if="proximoVencimiento" class="alerta-vencimiento">⏰</span>
      </div>
    </div>
  </template>
  
  <div class="producto-detalles">
    <div class="stock-info">
      <StockIndicator 
        :cantidad="producto.stock_actual"
        :minimo="producto.stock_minimo"
        :unidad="producto.unidad_medida"
      />
    </div>
    
    <div class="precio-info">
      <span class="precio">{{ formatPrice(producto.precio_litro) }}/L</span>
      <span class="contenedor">{{ producto.tipo_contenedor }}</span>
    </div>
  </div>
  
  <template #footer>
    <div class="acciones-rapidas">
      <button @click="verStock" class="btn-icon">📊</button>
      <button @click="hacerPedido" class="btn-icon">🛒</button>
      <button @click="verFicha" class="btn-icon">📋</button>
    </div>
  </template>
</BambuCard>
```

**Elementos clave:**
- **Código visible**: Siempre presente para búsqueda rápida
- **Alertas inmediatas**: Peligrosidad y vencimientos
- **Stock contextual**: Color según nivel crítico
- **Acciones rápidas**: Operaciones comunes sin navegar

### 🚨 Patrón de Alertas de Stock

**Patrón**: Estado visual inmediato + información contextual

```vue
<div class="stock-alert-panel">
  <div class="alert-header">
    <h3>⚠️ Productos con Stock Crítico</h3>
    <span class="alert-count">{{ productosAlerta.length }} productos</span>
  </div>
  
  <div class="alert-list">
    <div 
      v-for="producto in productosAlerta" 
      :key="producto.id"
      :class="['alert-item', `alert-${producto.nivel_stock}`]"
    >
      <div class="alert-producto">
        <strong>{{ producto.nombre }}</strong>
        <code>{{ producto.codigo }}</code>
      </div>
      
      <div class="alert-stock">
        <span class="cantidad-actual">{{ producto.stock_actual }}L</span>
        <span class="cantidad-minima">mín: {{ producto.stock_minimo }}L</span>
      </div>
      
      <div class="alert-acciones">
        <button @click="reponerStock(producto)" class="btn-reponer">
          🔄 Reponer
        </button>
        <button @click="verProveedores(producto)" class="btn-proveedores">
          📞 Proveedores
        </button>
      </div>
    </div>
  </div>
</div>
```

**Estados visuales:**
- **🔴 Crítico**: Stock = 0 (fondo rojo suave)
- **🟡 Bajo**: Stock < mínimo (fondo amarillo)
- **🟠 Alerta**: Stock < 20% del mínimo (fondo naranja)

**Acciones contextuales:**
- **Reponer**: Link directo a generar orden de compra
- **Proveedores**: Contactos rápidos de suppliers
- **Historial**: Ver consumo histórico para proyección

### 📋 Patrón de Pedidos en Proceso

**Patrón**: Timeline visual + acciones contextuales

```vue
<div class="pedidos-timeline">
  <div 
    v-for="pedido in pedidosActivos" 
    :key="pedido.id"
    class="pedido-item"
  >
    <div class="pedido-timeline">
      <div class="timeline-dot" :class="getEstadoClass(pedido.estado)"></div>
      <div class="timeline-line" v-if="!isUltimo"></div>
    </div>
    
    <div class="pedido-content">
      <div class="pedido-header">
        <div class="pedido-info">
          <h4>Pedido #{{ pedido.numero }}</h4>
          <span class="cliente">{{ pedido.cliente.nombre }}</span>
          <span class="fecha">{{ formatDate(pedido.fecha_pedido) }}</span>
        </div>
        
        <div class="pedido-estado">
          <PedidoEstadoBadge :estado="pedido.estado" />
        </div>
      </div>
      
      <div class="pedido-productos">
        <div 
          v-for="item in pedido.items" 
          :key="item.id"
          class="producto-item"
        >
          <span class="producto-nombre">{{ item.producto.nombre }}</span>
          <span class="cantidad">{{ item.cantidad }}L</span>
          <span class="precio">{{ formatPrice(item.subtotal) }}</span>
        </div>
      </div>
      
      <div class="pedido-acciones">
        <template v-if="pedido.estado === 'confirmado'">
          <button @click="prepararPicking(pedido)" class="btn-accion">
            📦 Preparar
          </button>
          <button @click="imprimirRemito(pedido)" class="btn-accion">
            🖨️ Remito
          </button>
        </template>
        
        <template v-if="pedido.estado === 'en_preparacion'">
          <button @click="marcarListo(pedido)" class="btn-accion">
            ✅ Listo
          </button>
          <button @click="asignarTransporte(pedido)" class="btn-accion">
            🚚 Asignar Transporte
          </button>
        </template>
        
        <button @click="verDetalle(pedido)" class="btn-secundario">
          👁️ Ver Detalle
        </button>
      </div>
    </div>
  </div>
</div>
```

**Estados del Timeline:**
- **Borrador**: Círculo gris
- **Confirmado**: Círculo azul
- **En preparación**: Círculo amarillo animado
- **Listo**: Círculo verde
- **En reparto**: Círculo naranja
- **Entregado**: Círculo verde sólido

### 🔍 Patrón de Búsqueda Omnipresente

**Patrón**: Búsqueda inteligente + resultados contextuales

```vue
<div class="busqueda-global">
  <div class="search-input-wrapper">
    <input 
      v-model="query"
      type="text"
      placeholder="Buscar productos, clientes, pedidos..."
      class="search-input"
      @focus="mostrarSugerencias = true"
      @input="buscarEnVivo"
    />
    <button class="search-button">
      🔍
    </button>
  </div>
  
  <div v-if="mostrarSugerencias" class="search-dropdown">
    <!-- Productos -->
    <div v-if="resultados.productos.length" class="search-section">
      <h5>🧪 Productos</h5>
      <div 
        v-for="producto in resultados.productos" 
        :key="producto.id"
        class="search-item"
        @click="irAProducto(producto)"
      >
        <div class="item-principal">
          <strong>{{ producto.nombre }}</strong>
          <code>{{ producto.codigo }}</code>
        </div>
        <div class="item-secundario">
          <StockIndicator 
            :cantidad="producto.stock_actual" 
            :minimo="producto.stock_minimo"
            size="sm"
          />
        </div>
      </div>
    </div>
    
    <!-- Clientes -->
    <div v-if="resultados.clientes.length" class="search-section">
      <h5>👥 Clientes</h5>
      <div 
        v-for="cliente in resultados.clientes" 
        :key="cliente.id"
        class="search-item"
        @click="irACliente(cliente)"
      >
        <div class="item-principal">
          <strong>{{ cliente.nombre }}</strong>
          <span class="cliente-tipo">{{ cliente.tipo }}</span>
        </div>
        <div class="item-secundario">
          <span class="ciudad">{{ cliente.ciudad }}</span>
          <span class="ultimo-pedido">Último: {{ cliente.ultimo_pedido }}</span>
        </div>
      </div>
    </div>
    
    <!-- Pedidos -->
    <div v-if="resultados.pedidos.length" class="search-section">
      <h5>📋 Pedidos</h5>
      <div 
        v-for="pedido in resultados.pedidos" 
        :key="pedido.id"
        class="search-item"
        @click="irAPedido(pedido)"
      >
        <div class="item-principal">
          <strong>Pedido #{{ pedido.numero }}</strong>
          <span class="cliente">{{ pedido.cliente.nombre }}</span>
        </div>
        <div class="item-secundario">
          <PedidoEstadoBadge :estado="pedido.estado" size="sm" />
          <span class="fecha">{{ formatDate(pedido.fecha) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
```

**Características:**
- **Búsqueda en vivo**: Resultados al escribir (debounced)
- **Múltiples entidades**: Productos, clientes, pedidos
- **Información contextual**: Stock, estado, fechas
- **Navegación directa**: Click → ir a detalle
- **Accesible**: Funciona con teclado

---

## Flujos de Trabajo CRM

### 🛒 Flujo: Crear Pedido Nuevo

**Objetivo**: Crear pedido completo en < 2 minutos

```
PASO 1: Seleccionar Cliente
├── 🔍 Buscar por nombre/código
├── 📋 Ver historial pedidos
└── ➕ Cliente nuevo (si necesario)

PASO 2: Agregar Productos
├── 🔍 Buscar productos
├── ✅ Verificar stock disponible
├── ⚠️ Alertas incompatibilidades
└── 🧮 Calcular totales automático

PASO 3: Configurar Entrega
├── 📅 Fecha preferida
├── 📍 Dirección (autocompletada)
├── 🚚 Método transporte
└── 💬 Notas especiales

PASO 4: Confirmación
├── 📋 Resumen visual completo
├── 💰 Totales destacados
├── ⚠️ Últimas validaciones
└── ✅ Confirmar pedido
```

**UX Crítico:**
- **Autocompletado**: Cliente frecuente → datos prellenados
- **Validaciones en vivo**: Stock, incompatibilidades
- **Cálculos automáticos**: Precio, peso, volumen
- **Estado persistente**: Guardar borrador automático

### 📦 Flujo: Preparación de Picking

**Objetivo**: Minimizar errores en preparación física

```
PASO 1: Lista de Picking Optimizada
├── 🗺️ Ordenada por ubicación física
├── 📦 Agrupada por tipo contenedor
├── ⚠️ Productos peligrosos destacados
└── ✅ Checkboxes grandes para tablet

PASO 2: Validación de Lotes
├── 📅 Verificar fechas vencimiento
├── 🏷️ Escanear códigos lote
├── ❌ Rechazar productos vencidos
└── 📝 Registrar observaciones

PASO 3: Control de Calidad
├── 📋 Checklist visual productos
├── 🧪 Verificar etiquetas peligrosidad
├── 📦 Confirmar embalaje correcto
└── ✅ Aprobar para despacho
```

**Interfaz Tablet-Friendly:**
- **Checkboxes grandes**: Mínimo 44px para dedo
- **Códigos QR grandes**: Fácil escaneo
- **Estado visual claro**: Verde/rojo inmediato
- **Scroll horizontal**: Lista larga cómoda

### 🚚 Flujo: Seguimiento de Entrega

**Objetivo**: Visibilidad completa cliente + operaciones

```
VISTA OPERACIONES:
├── 🗺️ Mapa rutas tiempo real
├── 📱 Estado chofer (GPS + confirmación)
├── ⏰ ETAs actualizados automático
└── 🚨 Alertas retrasos/problemas

VISTA CLIENTE:
├── 📧 Notificación automática envío
├── 🔗 Link tracking público
├── ⏰ Ventana horaria estimada
└── 📞 Contacto directo chofer

EVENTOS AUTOMÁTICOS:
├── 🚚 "Producto despachado"
├── 📍 "En ruta hacia destino"
├── 🏠 "Llegó a destino"
└── ✅ "Entrega confirmada"
```

**Microinteracciones:**
- **Estados animados**: Dots pulsantes para "en tránsito"
- **Mapas interactivos**: Zoom automático a ruta
- **Notificaciones push**: Updates críticos
- **Colores semánticos**: Verde = OK, Rojo = Problema

---

## Microinteracciones del Dominio

### 🔄 Actualización de Stock en Vivo

**Trigger**: Cambio en stock de producto

```javascript
// Animación de cambio de stock
function animarCambioStock(productoId, stockAnterior, stockNuevo) {
  const elemento = document.querySelector(`[data-producto-id="${productoId}"]`)
  const indicador = elemento.querySelector('.stock-indicator')
  
  // Animación de "pulso" para indicar cambio
  indicador.classList.add('stock-update-pulse')
  
  // Cambiar color basado en dirección del cambio
  if (stockNuevo > stockAnterior) {
    indicador.classList.add('stock-increase') // Verde
  } else {
    indicador.classList.add('stock-decrease') // Naranja/Rojo
  }
  
  // Limpiar clases después de animación
  setTimeout(() => {
    indicador.classList.remove('stock-update-pulse', 'stock-increase', 'stock-decrease')
  }, 1000)
}
```

### 🚨 Alertas de Productos Peligrosos

**Trigger**: Selección de producto ácido/base/tóxico

```css
/* Animación de alerta peligrosidad */
@keyframes peligro-pulse {
  0% { 
    border-color: var(--error);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
  }
  50% { 
    border-color: var(--error-hover);
    box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.1);
  }
  100% { 
    border-color: var(--error);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

.producto-peligroso {
  animation: peligro-pulse 2s infinite;
  border-left: 4px solid var(--error);
}

.alerta-incompatibilidad {
  position: relative;
}

.alerta-incompatibilidad::after {
  content: "⚠️ INCOMPATIBLE";
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--error);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: bold;
  animation: shake 0.5s;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
```

### ✅ Feedback de Acciones Exitosas

**Patrón**: Confirmación visual inmediata

```javascript
// Toast de éxito con contexto específico
function mostrarExitoPedido(pedido) {
  showToast({
    tipo: 'success',
    titulo: '✅ Pedido Creado',
    mensaje: `Pedido #${pedido.numero} para ${pedido.cliente.nombre}`,
    acciones: [
      {
        texto: '👁️ Ver Pedido',
        onClick: () => router.push(`/pedidos/${pedido.id}`)
      },
      {
        texto: '🖨️ Imprimir',
        onClick: () => imprimirPedido(pedido.id)
      }
    ],
    duracion: 5000
  })
}

// Animación de botón después de acción
function animarBotonExito(botonElement) {
  const textoOriginal = botonElement.textContent
  const iconoOriginal = botonElement.querySelector('svg')
  
  // Cambiar a estado "éxito" temporalmente
  botonElement.textContent = '✅ ¡Listo!'
  botonElement.classList.add('btn-success-temp')
  
  setTimeout(() => {
    botonElement.textContent = textoOriginal
    botonElement.classList.remove('btn-success-temp')
  }, 2000)
}
```

### 🔄 Loading States Específicos

```vue
<!-- Loading específico para verificación de stock -->
<div v-if="verificandoStock" class="verificando-stock">
  <div class="spinner-stock"></div>
  <span>Verificando disponibilidad...</span>
</div>

<!-- Loading específico para cálculo de flete -->
<div v-if="calculandoFlete" class="calculando-flete">
  <div class="truck-animation">🚚</div>
  <span>Calculando costo de envío...</span>
</div>

<!-- Loading específico para validación química -->
<div v-if="validandoCompatibilidad" class="validando-quimica">
  <div class="formula-animation">🧪</div>
  <span>Verificando compatibilidad química...</span>
</div>
```

---

## Accesibilidad en Contexto Industrial

### 👓 Consideraciones Visuales

**Contexto**: Operarios con cansancio visual, ambientes con polvo

- **Contraste alto**: Mínimo WCAG AAA para textos críticos
- **Tamaños grandes**: Información crítica en 18px+
- **Colores redundantes**: Nunca solo color para transmitir información
- **Iconografía clara**: Símbolos universales + texto

### 🖱️ Navegación por Teclado

**Contexto**: Uso con guantes industriales, pantallas touch resistivas

```css
/* Focus states más prominentes para contexto industrial */
:focus-visible {
  outline: 3px solid var(--primary);
  outline-offset: 3px;
  background: var(--primary-bg);
}

/* Áreas de click más grandes */
.touch-target {
  min-width: 48px;
  min-height: 48px;
  padding: 12px;
}

/* Estados hover más evidentes */
.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
```

### ⚠️ Alertas Críticas Accesibles

```html
<!-- Alerta de producto peligroso -->
<div 
  class="alerta-peligro" 
  role="alert" 
  aria-live="polite"
  tabindex="0"
>
  <span aria-hidden="true">⚠️</span>
  <span class="sr-only">Atención: Producto Peligroso</span>
  <strong>ÁCIDO MURIÁTICO</strong>
  <p>Manipular con guantes y protección ocular</p>
</div>

<!-- Estado de stock crítico -->
<div 
  class="stock-critico" 
  role="status" 
  aria-live="assertive"
>
  <span aria-hidden="true">🔴</span>
  <span class="sr-only">Stock crítico:</span>
  <span>{{ producto.nombre }} - Solo {{ stock }} unidades restantes</span>
</div>
```

---

## Validación UX Bambú

### ✅ Checklist Específico CRM Químico

**🧪 Seguridad Industrial:**
- [ ] Productos peligrosos destacados visualmente
- [ ] Incompatibilidades químicas alertadas
- [ ] Fechas de vencimiento prominentes
- [ ] Confirmaciones dobles para acciones críticas

**📦 Operaciones Logísticas:**
- [ ] Stock visible en todas las vistas de productos
- [ ] Rutas de entrega precargadas para Alto Valle
- [ ] Estados de pedido claros y actualizados
- [ ] Picking lists optimizadas por ubicación física

**👥 Experiencia Cliente:**
- [ ] Historial de pedidos accesible
- [ ] Búsqueda funciona con códigos internos
- [ ] Precios actualizados y visibles
- [ ] Contacto directo con vendedor asignado

**📱 Uso Móvil/Tablet:**
- [ ] Interfaz funciona con dedos (no stylus)
- [ ] Códigos QR escaneables fácilmente
- [ ] Formularios completables en vertical
- [ ] Tablas con scroll horizontal fluido

### 🎯 Métricas de Éxito UX

- **Tiempo crear pedido**: < 2 minutos
- **Errores en picking**: < 1% mensual
- **Satisfacción usuario**: > 4.5/5
- **Adopción móvil**: > 60% operarios
- **Reducción consultas**: -30% llamadas por estado pedidos

---

## Recursos y Referencias

### 📚 Documentos Técnicos Relacionados
- 🏗️ [BAMBU_FRONTEND_SYSTEM.md](./BAMBU_FRONTEND_SYSTEM.md) - Componentes y arquitectura
- 🎨 [BAMBU_COLOR_SYSTEM.md](./BAMBU_COLOR_SYSTEM.md) - Paleta y variables completas
- 📱 [BAMBU_RESPONSIVE_SYSTEM.md](./BAMBU_RESPONSIVE_SYSTEM.md) - Sistema responsive
- 🛠️ [DEV_HANDBOOK_LARAVEL_VUE.md](./DEV_HANDBOOK_LARAVEL_VUE.md) - Implementación técnica

### 🎯 Contexto del Negocio
- 🏢 [INFORMACION_NEGOCIO_BAMBU.md](./INFORMACION_NEGOCIO_BAMBU.md) - Conocimiento del dominio
- 🗂️ [SYSTEM_ARCHITECTURE.md](./SYSTEM_ARCHITECTURE.md) - Arquitectura general

---

## 🔍 REVISIÓN SENIOR FRONTEND - CAMBIOS PROPUESTOS

**Fecha revisión**: 2025-08-08  
**Estado**: Pendientes de implementación  

Luego de someter el sistema a una revisión exhaustiva por parte de un senior frontend developer, se propusieron los siguientes cambios para mejorar la UX específica del dominio químico:

### 🚨 **CAMBIOS CRÍTICOS**

1. **Alertas de seguridad no-solo-color**
   - **Problema**: Estados críticos dependen solo del color (insuficiente para accesibilidad)
   - **Solución**: Ícono + texto obligatorio + pictogramas GHS/ADR en productos peligrosos
   - **Impacto**: Cumplimiento accesibilidad AA y normas industriales

### ⚡ **CAMBIOS IMPORTANTES**

1. **Definir SLA de búsqueda omnipresente**
   - **Problema**: No hay límites de latencia definidos
   - **Solución**: <150ms local cache, <400ms red, fallback offline con últimos N resultados
   - **Impacto**: UX predecible en depósito con conectividad variable

2. **Estado "Bloqueado por seguridad" en timeline**
   - **Problema**: No consideramos incompatibilidades químicas en flujo de pedidos
   - **Solución**: Estado específico con acciones rápidas para resolver
   - **Impacto**: Prevención de mezclas peligrosas

3. **Modo alta densidad + modo guantes**
   - **Problema**: Solo un tamaño de interfaz para contextos diferentes
   - **Solución**: Modo escritorio (admin) compacto + modo tablet expandido ≥48px
   - **Impacto**: Optimización específica por tipo de usuario

### 📋 **PATRONES ACTUALIZADOS**

**Alertas de productos peligrosos - Patrón accesible:**
```vue
<div 
  class="producto-peligroso" 
  role="alert" 
  aria-live="assertive"
>
  <!-- Ícono + Color + Pictograma -->
  <div class="alerta-visual">
    <span class="icono-peligro" aria-hidden="true">⚠️</span>
    <img src="/pictogramas/ghs-corrosivo.svg" alt="Producto corrosivo" class="ghs-pictogram">
  </div>
  
  <!-- Texto descriptivo siempre presente -->
  <div class="alerta-texto">
    <strong>ÁCIDO MURIÁTICO</strong>
    <span class="instruccion">Manipular con guantes y protección ocular</span>
  </div>
  
  <!-- Patrón visual adicional -->
  <div class="borde-peligro" aria-hidden="true"></div>
</div>
```

**Estados de stock - Sin dependencia solo de color:**
```vue
<div class="stock-indicator" :class="`stock-${nivel}`">
  <!-- Ícono semántico -->
  <div class="stock-icono">
    <component :is="getStockIcon(nivel)" />
  </div>
  
  <!-- Información textual -->
  <div class="stock-info">
    <span class="cantidad">{{ formatCantidad }}</span>
    <span class="nivel-texto">{{ getNivelTexto(nivel) }}</span>
  </div>
  
  <!-- Indicador visual de barras -->
  <div class="stock-bar">
    <div class="stock-fill" :style="`width: ${porcentaje}%`"></div>
  </div>
</div>
```

**Timeline con estado bloqueado:**
```vue
<div class="pedido-timeline-item" v-if="pedido.bloqueado_seguridad">
  <div class="timeline-dot blocked">
    <span aria-hidden="true">🚫</span>
  </div>
  
  <div class="timeline-content">
    <div class="estado-bloqueado">
      <h4>🚨 Bloqueado por Seguridad</h4>
      <p class="motivo">{{ pedido.motivo_bloqueo }}</p>
      
      <div class="acciones-rapidas">
        <button @click="revisarIncompatibilidades" class="btn-warning">
          🧪 Revisar Compatibilidad
        </button>
        <button @click="contactarSeguridad" class="btn-secondary">
          📞 Contactar Seguridad
        </button>
      </div>
    </div>
  </div>
</div>
```

**Búsqueda con SLA y offline:**
```vue
<div class="busqueda-omnipresente">
  <input 
    v-model="query"
    @input="buscarConSLA"
    placeholder="Buscar productos, clientes, pedidos..."
  >
  
  <!-- Indicador de latencia -->
  <div class="search-status">
    <span v-if="searching" class="latency">⏱️ {{ latencia }}ms</span>
    <span v-if="offline" class="offline-mode">📱 Modo offline</span>
  </div>
  
  <!-- Resultados con fallback offline -->
  <div class="search-results">
    <div v-if="offline && cachedResults.length" class="cached-section">
      <h5>📂 Resultados recientes (cache)</h5>
      <!-- Mostrar últimos resultados cacheados -->
    </div>
  </div>
</div>
```

**Modos de densidad:**
```css
/* Modo alta densidad para admin/desktop */
.modo-alta-densidad {
  --card-padding: var(--space-sm);
  --table-row-height: 32px;
  --font-size-base: 14px;
}

/* Modo guantes para logística/tablets */
.modo-guantes {
  --touch-target-min: 48px;
  --card-padding: var(--space-lg);
  --font-size-base: 16px;
}

.modo-guantes .btn,
.modo-guantes .checkbox,
.modo-guantes .form-input {
  min-height: 48px;
  min-width: 48px;
}
```

### ✅ **PRÓXIMOS PASOS DE IMPLEMENTACIÓN**

1. **Actualizar componentes** con patrones no-solo-color
2. **Implementar SLA** de búsqueda con cache offline
3. **Agregar estado "Bloqueado"** en timeline de pedidos
4. **Crear toggle** modo alta densidad / modo guantes
5. **Testing accesibilidad** con lectores de pantalla
6. **Validar pictogramas** GHS/ADR con usuarios reales

---

**🧪 Guidelines UX específicas para CRM Químico Bambú**  
**📅 Actualizado**: 2025-08-08  
**🎯 Optimizado para**: Alto Valle, Productos Químicos, Operaciones Industriales