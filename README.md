# BAMBU v2.0 - CRM Integral
**Sistema web profesional para gestión comercial completa**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue.svg)](https://www.postgresql.org)
[![Tests](https://img.shields.io/badge/Tests-47%20tests%20pasando-green.svg)](#)
[![Filament](https://img.shields.io/badge/Filament-v3-orange.svg)](https://filamentphp.com)

---

## 🚀 Quick Start

```bash
# 1. Clonar e instalar
git clone [repository-url]
cd bambu-sistema-v2
composer install && npm install

# 2. Configurar y migrar
cp .env.example .env
php artisan key:generate
createdb bambu_sistema_v2
php artisan migrate
php artisan db:seed

# 3. Ejecutar
php artisan serve    # Backend Laravel
npm run dev          # Frontend Vue SPA

# 4. Acceder al admin (opcional)
# URL: http://127.0.0.1:8000/admin
# Usuario: admin@bambu.com
# Contraseña: password
```

---

## 📊 Estado Actual

### **🟢 SISTEMA COMPLETAMENTE OPERATIVO** ✅
**Estado**: Sistema funcionando con todos los módulos implementados

**✅ Completado**: Módulos logísticos (Vehículos, Planificación, Seguimiento, Reportes)
**✅ Completado**: Sistema de Diseño y refinamientos UI  
**✅ Completado**: Backend Laravel + Frontend Vue SPA completamente funcional  
**🟢 ACTUAL**: Sistema operativo - Esperando próximo feedback

**📈 [Ver estado detallado →](./STATUS.md)**

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test
```

**Estado**: ✅ **47 tests implementados** cubriendo todos los módulos

---

## 📚 Documentación

### 🎯 Esencial (LEER PRIMERO)
- **📊 [STATUS.md](./STATUS.md)** ← **Única fuente de verdad absoluta**
- **📚 [DEV HANDBOOK](./documentacion-proyecto/DEV_HANDBOOK_LARAVEL_VUE.md)** ← **Biblia lecciones aprendidas**
- **📋 [ÍNDICE COMPLETO](./documentacion-proyecto/INDICE.md)** ← Navegación documentación

### 🛠️ Técnico
- **🏗️ [ARQUITECTURA](./SYSTEM_ARCHITECTURE.md)** - Arquitectura completa del sistema
- **🎨 [SISTEMA DE DISEÑO](./DESIGN_SYSTEM.md)** - Guías UI/UX implementadas
- **🗃️ [ESQUEMA BD](./documentacion-proyecto/ESQUEMA_BASE_DATOS.md)** - Base de datos PostgreSQL
- **🌐 [API ENDPOINTS](./documentacion-proyecto/API_ENDPOINTS.md)** - Documentación API REST

### 🏢 Negocio
- **🏭 [INFO NEGOCIO](./documentacion-proyecto/INFORMACION_NEGOCIO_BAMBU.md)** - Lógica de negocio BAMBU
- **⚙️ [STACK TÉCNICO](./documentacion-proyecto/STACK_TECH.md)** - Decisiones tecnológicas

---

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 11 + PostgreSQL + Filament v3 ✅ **Funcionando**
- **API REST**: 49+ endpoints con Sanctum Auth ✅ **Operativa**
- **Frontend**: Vue 3 + TypeScript + Tailwind CSS ✅ **SPA Completamente Funcional**
- **Testing**: 47 tests ✅ **Cobertura completa de módulos**
- **Módulos**: Vehículos, Planificación, Seguimiento, Reportes ✅ **Implementados**

---

## 🎯 Funcionalidades Implementadas

### Core Básico ✅
- Gestión de Clientes completa
- Catálogo de Productos con stock integrado
- Sistema de Pedidos completo
- Control de Stock con movimientos y auditoría
- Dashboard operativo con métricas en tiempo real
- Configuraciones del sistema

### Módulos Logísticos ✅
- **🚛 Gestión de Vehículos** - CRUD + estados + disponibilidad
- **📋 Planificación Semanal** - Asignación pedidos/rutas + modal programación  
- **📍 Seguimiento Tiempo Real** - Dashboard live + auto-refresh + control estados
- **📊 Sistema de Reportes** - 4 tipos de análisis con métricas calculadas

### Sistema de Diseño ✅
- Dashboard con coherencia visual total
- Componentes refinados con sistema tipográfico sistemático
- Espaciado matemático consistente aplicado globalmente
- Colores semánticos y transiciones suaves

---

## 📞 Desarrollo

**Documentación central**: [STATUS.md](./STATUS.md) - Siempre actualizado  
**Setup**: Instalación < 30 minutos con Laragon + PostgreSQL  
**Tests**: `php artisan test` - Suite completa implementada

---

**Última actualización**: 07/08/2025 | **Estado**: 🟢 OPERATIVO | **[Estado actual →](./STATUS.md)**