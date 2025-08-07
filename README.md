# BAMBU v2.0 - CRM Integral
**Sistema web profesional para gestión comercial completa**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue.svg)](https://www.postgresql.org)
[![Tests](https://img.shields.io/badge/Tests-72%2F72%20passing-green.svg)](#)
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

**📋 [Setup completo paso a paso →](./documentacion-proyecto/PASO_CERO.md)**

---

## 📊 Estado Actual

### **🚀 FRONTEND VUE SPA FUNCIONANDO** ✅
**Progreso**: `██████████████████░░` 93% total

**✅ Fase 0**: PostgreSQL + Tests + UX/UI Guidelines completada  
**✅ Fase 1**: Backend Core + API REST + Admin Panel funcionando  
**✅ Fase 2**: Frontend Vue 3 SPA + Auth + Dashboard operativo  
**🔄 ACTUAL**: Refinamiento UI/UX antes de escalar módulos

**📈 [Ver estado completo →](./STATUS.md)**

---

## 🧪 Testing

```bash
# Ejecutar tests (72/72 pasando)
php artisan test --testdox
```

**Estado**: ✅ **72/72 tests pasando (491 assertions)**

---

## 📚 Documentación

### 🎯 Esencial
- **📊 [ESTADO ACTUAL](./STATUS.md)** ← Información siempre actualizada
- **📋 [ÍNDICE COMPLETO](./documentacion-proyecto/INDICE.md)** ← Todos los documentos
- **🎨 [UX/UI GUIDELINES](./documentacion-proyecto/UX_UI_GUIDELINES_SISTEMA_BAMBU.md)** ← Sistema diseño

### 📖 Referencia  
- **🗺️ [Roadmap](./documentacion-proyecto/ROADMAP_DESARROLLO_2025.md)** - Plan completo
- **⚙️ [Setup](./documentacion-proyecto/PASO_CERO.md)** - Instalación detallada
- **📋 [PRD](./documentacion-proyecto/PRD_BAMBU_2025_PROFESIONAL.md)** - Requerimientos

---

## 🛠️ Stack

- **Backend**: Laravel 11 + PostgreSQL + Filament v3 ✅ **Funcionando**
- **API REST**: 15+ endpoints con Sanctum Auth ✅ **Operativa**
- **Frontend**: Vue 3 + TypeScript + Tailwind 4 ✅ **SPA Funcionando**
- **Testing**: 72/72 tests ✅ **Cobertura completa**
- **CSS**: Arquitectura híbrida Tailwind + CSS Variables ✅ **Estrategia definida**

---

## 📞 Desarrollo

**Issues y mejoras**: Usar sistema de issues del repositorio  
**Documentación**: Todo centralizado en [STATUS.md](./STATUS.md)

---

**Creado**: Agosto 2025 | **Versión**: 2.0 | **[Estado →](./STATUS.md)**