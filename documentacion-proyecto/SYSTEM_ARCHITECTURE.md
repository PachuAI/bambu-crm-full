# SISTEMA BAMBU CRM v2 - ARQUITECTURA COMPLETA
**Última actualización**: 2025-08-08 (Inconsistencias resueltas)
**Estado**: Sistema completamente implementado - Documentación actualizada

## 🏗️ OVERVIEW ARQUITECTÓNICO

```
┌─────────────────────────────────────────────────────────────────┐
│                      BAMBU SISTEMA v2                          │
│                 Sistema de Gestión Logística                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│   FRONTEND      │  │    BACKEND      │  │   DATABASE      │
│   Vue 3 + TS    │◄─┤  Laravel 11     │◄─┤   PostgreSQL    │
│   Tailwind CSS  │  │  API RESTful    │  │   Relacional    │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

## 📁 ESTRUCTURA DE DIRECTORIOS

```
bambu-sistema-v2/
├── 📂 app/                          # Backend Laravel
│   ├── 📂 Filament/                 # Panel Admin
│   │   └── 📂 Resources/            # Recursos CRUD
│   │       ├── ClienteResource.php
│   │       ├── ProductoResource.php
│   │       ├── PedidoResource.php
│   │       ├── VehiculoResource.php
│   │       └── ConfiguracionResource.php
│   │
│   ├── 📂 Http/Controllers/Api/     # API REST Controllers
│   │   ├── AuthController.php       # 🔐 Autenticación
│   │   ├── ClienteController.php    # 👥 Gestión clientes
│   │   ├── ProductoController.php   # 📦 Catálogo productos
│   │   ├── StockController.php      # 📊 Control inventario
│   │   ├── VehiculoController.php   # 🚛 Gestión flota
│   │   ├── RepartoController.php    # 🗺️ Planificación/Seguimiento
│   │   └── ReporteController.php    # 📈 Analytics/Reportes
│   │
│   ├── 📂 Models/                   # Modelos Eloquent
│   │   ├── User.php                 # Usuario sistema
│   │   ├── Cliente.php              # Clientes
│   │   ├── Producto.php             # Productos
│   │   ├── Pedido.php               # Pedidos
│   │   ├── PedidoItem.php          # Items de pedido
│   │   ├── Vehiculo.php             # Vehículos flota
│   │   ├── Reparto.php              # Repartos/Entregas
│   │   ├── MovimientoStock.php      # Movimientos stock
│   │   └── SystemLog.php            # Logs sistema
│   │
│   └── 📂 Services/                 # Lógica de negocio
│       └── StockService.php         # Servicio gestión stock
│
├── 📂 database/                     # Base de datos
│   ├── 📂 migrations/               # Migraciones BD
│   │   ├── create_users_table.php
│   │   ├── create_clientes_table.php
│   │   ├── create_productos_table.php
│   │   ├── create_pedidos_table.php
│   │   ├── create_vehiculos_table.php
│   │   ├── create_repartos_table.php
│   │   └── update_pedidos_estados_logisticos.php
│   │
│   ├── 📂 factories/                # Factories para testing
│   │   ├── ClienteFactory.php
│   │   ├── ProductoFactory.php
│   │   ├── VehiculoFactory.php
│   │   └── RepartoFactory.php
│   │
│   └── 📂 seeders/                  # Datos iniciales
│       ├── AdminUserSeeder.php
│       ├── ProductosSeeder.php
│       └── ConfiguracionesSeeder.php
│
├── 📂 resources/js/                 # Frontend Vue.js
│   ├── 📂 components/               # Componentes reutilizables
│   │   └── 📂 dashboard/
│   │       └── MetricCard.vue       # Card métricas dashboard
│   │
│   ├── 📂 layouts/                  # Layouts principales
│   │   └── MainLayout.vue           # Layout principal sistema
│   │
│   ├── 📂 views/                    # Vistas/Páginas
│   │   ├── DashboardView.vue        # 📊 Dashboard principal
│   │   ├── 📂 clientes/             # Módulo clientes
│   │   │   ├── ClientesIndex.vue
│   │   │   ├── ClientesCreate.vue
│   │   │   ├── ClientesEdit.vue
│   │   │   └── ClientesShow.vue
│   │   │
│   │   ├── 📂 productos/            # Módulo productos
│   │   │   ├── ProductosIndex.vue
│   │   │   ├── ProductosCreate.vue
│   │   │   └── ProductosEdit.vue
│   │   │
│   │   ├── 📂 pedidos/              # Módulo pedidos
│   │   │   ├── PedidosIndex.vue
│   │   │   ├── PedidosCreate.vue
│   │   │   └── PedidosShow.vue
│   │   │
│   │   ├── 📂 stock/                # Módulo stock
│   │   │   ├── StockIndex.vue
│   │   │   └── MovimientosIndex.vue
│   │   │
│   │   ├── 📂 vehiculos/            # Módulo vehículos
│   │   │   └── VehiculosIndex.vue
│   │   │
│   │   ├── 📂 planificacion/        # Módulo planificación
│   │   │   └── PlanificacionIndex.vue
│   │   │
│   │   ├── 📂 seguimiento/          # Módulo seguimiento
│   │   │   └── SeguimientoIndex.vue
│   │   │
│   │   └── 📂 reportes/             # Módulo reportes
│   │       └── ReportesIndex.vue
│   │
│   ├── 📂 router/                   # Router Vue
│   │   └── index.ts                 # Definición rutas
│   │
│   ├── 📂 stores/                   # Estado global (Pinia)
│   │   └── auth.ts                  # Store autenticación
│   │
│   └── App.vue                      # Componente raíz Vue
│
├── 📂 tests/                        # Suite de testing
│   ├── 📂 Feature/                  # Tests integración
│   │   ├── VehiculosTest.php        # Tests módulo vehículos
│   │   ├── RepartosTest.php         # Tests módulo repartos
│   │   ├── ReportesTest.php         # Tests módulo reportes
│   │   └── IntegracionLogisticaTest.php # Tests flujos completos
│   │
│   └── 📂 Unit/                     # Tests unitarios
│       └── ExampleTest.php
│
├── 📂 routes/                       # Definición rutas
│   ├── web.php                      # Rutas web
│   └── api.php                      # Rutas API REST
│
├── DESIGN_SYSTEM.md                 # Sistema diseño UI/UX
└── SYSTEM_ARCHITECTURE.md          # Este documento
```

## 🗃️ MODELO DE BASE DE DATOS

### Entidades Principales

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   USERS     │    │  CLIENTES   │    │  PRODUCTOS  │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ id          │    │ id          │    │ id          │
│ name        │    │ razon_social│    │ nombre      │
│ email       │    │ nombre_com..│    │ descripcion │
│ password    │    │ cuit        │    │ precio      │
│ created_at  │    │ direccion   │    │ stock_actual│
│ updated_at  │    │ telefono    │    │ stock_minimo│
└─────────────┘    │ email       │    │ peso_kg     │
                   │ activo      │    │ activo      │
                   │ created_at  │    │ created_at  │
                   │ updated_at  │    │ updated_at  │
                   └─────────────┘    └─────────────┘
                          │                   │
                          │                   │
                          ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  VEHICULOS  │    │   PEDIDOS   │    │PEDIDO_ITEMS │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ id          │    │ id          │    │ id          │
│ patente     │    │ cliente_id  │────┤ pedido_id   │
│ marca       │    │ nivel_desc..│    │ producto_id │───┐
│ modelo      │    │ monto_bruto │    │ cantidad    │   │
│ anio        │    │ monto_final │    │ precio_unit │   │
│ capacidad_kg│    │ estado      │    │ created_at  │   │
│ capac_bultos│    │ fecha_rep.. │    │ updated_at  │   │
│ activo      │    │ created_at  │    └─────────────┘   │
│ observ..    │    │ updated_at  │                      │
│ created_at  │    │ deleted_at  │                      │
│ updated_at  │    └─────────────┘                      │
└─────────────┘           │                            │
       │                  │                            │
       │                  ▼                            │
       │           ┌─────────────┐                     │
       └───────────│   REPARTOS  │                     │
                   ├─────────────┤                     │
                   │ id          │                     │
                   │ pedido_id   │                     │
                   │ vehiculo_id │                     │
                   │ fecha_prog..│                     │
                   │ hora_salida │                     │
                   │ hora_entrega│                     │
                   │ estado      │                     │
                   │ observ..    │                     │
                   │ km_recorr.. │                     │
                   │ created_at  │                     │
                   │ updated_at  │                     │
                   └─────────────┘                     │
                                                       │
┌─────────────┐                                       │
│MOV_STOCK    │                                       │
├─────────────┤                                       │
│ id          │                                       │
│ producto_id │───────────────────────────────────────┘
│ tipo        │
│ cantidad    │
│ motivo      │
│ usuario_id  │
│ created_at  │
│ updated_at  │
└─────────────┘
```

### Estados del Sistema

```
ESTADOS PEDIDOS:
borrador → confirmado → listo_envio → en_transito → entregado
                                                  └── fallido
                    └─── cancelado

ESTADOS REPARTOS:
programado → en_ruta → entregado
                   └── fallido

ESTADOS VEHÍCULOS:
disponible | programado | en_ruta | libre | inactivo
```

## 🔄 FLUJO DE DATOS

### Flujo Principal de Entrega

```
1. CREACIÓN PEDIDO
   └── Cliente + Productos → Pedido (estado: confirmado)

2. PLANIFICACIÓN
   └── Pedido + Vehículo → Reparto (estado: programado)
   └── Pedido.estado → listo_envio

3. SEGUIMIENTO
   └── Iniciar reparto → Reparto.estado: en_ruta
   └── Pedido.estado → en_transito

4. FINALIZACIÓN
   └── Completar → Reparto.estado: entregado
   └── Pedido.estado → entregado
   └── Registro KM + tiempo
```

### API Endpoints

> **🔗 Documentación completa de API**: Ver [API_ENDPOINTS.md](./API_ENDPOINTS.md) como fuente de verdad única
>
> El archivo API_ENDPOINTS.md contiene:
> - 49+ endpoints operativos con documentación completa
> - Ejemplos de request/response
> - Validaciones y códigos de error
> - Testing y casos de uso

**Resumen arquitectónico:**
- Autenticación: Laravel Sanctum
- Versión API: v1
- Formato: REST + JSON
- Estado: 49+ endpoints operativos

## 🎨 SISTEMA DE DISEÑO

### Tema Oscuro (Slate)
- **Backgrounds**: `bg-slate-800`, `bg-slate-900`
- **Borders**: `border-slate-700`, `border-slate-600`
- **Text**: `text-white`, `text-slate-400`, `text-slate-500`
- **Cards**: altura fija `h-[120px]`, padding `p-6`

### Colores Semánticos
- **Success**: `bg-green-500/20 text-green-400`
- **Warning**: `bg-orange-500/20 text-orange-400`
- **Error**: `bg-red-500/20 text-red-400`
- **Info**: `bg-blue-500/20 text-blue-400`

## 🧪 TESTING

### Cobertura de Tests
- ✅ **VehiculosTest.php** - CRUD completo vehículos
- ✅ **RepartosTest.php** - Planificación y seguimiento
- ✅ **ReportesTest.php** - Dashboard y analytics
- ✅ **IntegracionLogisticaTest.php** - Flujos completos

### Comandos Testing
```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test tests/Feature/VehiculosTest.php
php artisan test tests/Feature/IntegracionLogisticaTest.php

# Con cobertura
php artisan test --coverage
```

## 🚀 DEPLOYMENT

### Requisitos Sistema
- **PHP**: 8.2+
- **Database**: PostgreSQL 14+
- **Web Server**: Nginx/Apache
- **Node.js**: 18+ (para build frontend)

### Comandos Deployment
```bash
# Instalar dependencias
composer install --no-dev
npm install && npm run build

# Migraciones
php artisan migrate
php artisan db:seed

# Optimizaciones
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 MÉTRICAS SISTEMA

### Dashboard Principal
- **Entregas Hoy**: Repartos completados
- **Pendientes Entrega**: En ruta + programados
- **Vehículos Activos**: Flota disponible
- **Efectividad**: % entregas exitosas
- **KM Recorridos**: Kilómetros totales del día

### Reportes Disponibles
1. **Vehículos**: Rendimiento por vehículo
2. **Entregas**: Análisis entregas por cliente/período
3. **Operativo**: Tiempos, capacidades, horarios

## 🔧 CONFIGURACIÓN

### Variables Entorno (.env)
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bambu_sistema
DB_USERNAME=user
DB_PASSWORD=pass

SANCTUM_STATEFUL_DOMAINS=localhost
```

### Panel Admin (Filament)
- URL: `/admin`
- Gestión de todos los modelos
- Dashboard administrativo
- Configuraciones del sistema

---

## ✅ ESTADO IMPLEMENTACIÓN

| Módulo | Backend | Frontend | Testing | Estado |
|--------|---------|-----------|---------|--------|
| **Autenticación** | ✅ | ✅ | ✅ | Completo |
| **Clientes** | ✅ | ✅ | ✅ | Completo |
| **Productos** | ✅ | ✅ | ✅ | Completo |
| **Pedidos** | ✅ | ✅ | ✅ | Completo |
| **Stock** | ✅ | ✅ | ✅ | Completo |
| **Vehículos** | ✅ | ✅ | ✅ | **NUEVO** |
| **Planificación** | ✅ | ✅ | ✅ | **NUEVO** |
| **Seguimiento** | ✅ | ✅ | ✅ | **NUEVO** |
| **Reportes** | ✅ | ✅ | ✅ | **NUEVO** |

### Módulos Implementados Hoy
- 🚛 **Vehículos**: Gestión completa de flota
- 📋 **Planificación**: Vista semanal + asignación pedidos/rutas
- 📍 **Seguimiento**: Dashboard tiempo real con estados
- 📊 **Reportes**: 4 tipos de reportes con métricas

### Tests Creados
- 47 tests de funcionalidad
- Factories para datos de prueba (incluye ciudades del Alto Valle)
- Tests de integración end-to-end
- Cobertura completa de los nuevos módulos
- VehiculosTest: 12/12 pasando

### Frontend Actualizado (07/08/2025)
- ✅ Sidebar con nuevos módulos logísticos
- ✅ Dashboard conectado con API backend real
- ✅ Métricas dinámicas con auto-refresh
- ✅ Build de producción ejecutado

---

## 📝 PENDIENTE FEEDBACK DEL USUARIO
El usuario indicó que tiene feedback sobre varias partes del sistema.
Se esperan ajustes y mejoras según sus requerimientos específicos.

---

*Documento actualizado - Sistema BAMBU v2 - Versión Operativa*
*Fecha: 2025-08-08 (Inconsistencias resueltas)*