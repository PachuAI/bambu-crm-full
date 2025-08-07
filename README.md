# Sistema BAMBU v2.0 
## CRM Integral para Gestión Comercial

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4.x-cyan.svg)](https://tailwindcss.com)

---

## 🚀 Quick Start

### Requisitos Previos
- **PHP 8.3+** (Laragon recomendado)
- **PostgreSQL 15+**
- **Node.js 18+**
- **Composer 2.5+**

### Instalación Local

```bash
# 1. Clonar el repositorio
git clone [repository-url]
cd bambu-sistema-v2

# 2. Instalar dependencias backend
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=pgsql
DB_DATABASE=bambu_sistema_v2
DB_USERNAME=postgres
DB_PASSWORD=

# 5. Crear base de datos
createdb bambu_sistema_v2

# 6. Ejecutar migraciones
php artisan migrate

# 7. Instalar dependencias frontend
npm install

# 8. Compilar assets
npm run dev

# 9. Iniciar servidor
php artisan serve
```

### URLs del Sistema
- **App Principal**: http://127.0.0.1:8000
- **API Documentation**: http://127.0.0.1:8000/docs (próximamente)

---

## 🏗️ Arquitectura

### Stack Tecnológico
- **Backend**: Laravel 11 + PostgreSQL + Sanctum API
- **Frontend**: Vue 3 SPA + TypeScript + Pinia
- **CSS**: Tailwind CSS 4.0
- **Build**: Vite 7.0

### Estructura del Proyecto
```
bambu-sistema-v2/
├── app/                    # Backend Laravel
│   ├── Http/Controllers/   # API Controllers
│   ├── Models/            # Eloquent Models
│   └── Services/          # Business Logic
├── resources/
│   ├── js/                # Frontend Vue.js
│   │   ├── components/    # Componentes reutilizables
│   │   ├── views/         # Páginas de la app
│   │   ├── stores/        # Pinia stores
│   │   └── router/        # Vue Router
│   └── css/               # Estilos CSS
├── database/
│   ├── migrations/        # Migraciones BD
│   └── seeders/          # Datos de prueba
└── tests/                 # Tests automatizados
```

---

## 📋 Funcionalidades

### ✅ Implementado
- [x] Setup Laravel 11 + Vue 3 SPA
- [x] Autenticación API con Sanctum
- [x] Base de datos MySQL configurada
- [x] Sistema de build con Vite + Tailwind

### 🔄 En Desarrollo
- [ ] Migración de modelos desde MVP
- [ ] API REST endpoints
- [ ] Sistema de autenticación frontend
- [ ] CRUD de productos y clientes

### 📅 Próximas Features
- [ ] Módulo de cotizaciones
- [ ] Gestión de pedidos
- [ ] Sistema de logística
- [ ] Dashboard gerencial

---

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage
```

---

## 📖 Documentación

### Documentación Técnica
- [📋 **INDICE.md**](./documentacion-proyecto/INDICE.md) - Guía completa de documentación
- [🗺️ **ROADMAP**](./documentacion-proyecto/ROADMAP_DESARROLLO_2025.md) - Plan de desarrollo 20 semanas
- [🏗️ **ARQUITECTURA**](./documentacion-proyecto/ARQUITECTURA_TECNICA_2025.md) - Diseño técnico
- [📝 **ADRs**](./documentacion-proyecto/ADR_NUEVAS_DECISIONES_2025.md) - Decisiones arquitectónicas

### Guías de Desarrollo
- [⚙️ **Setup Inicial**](./documentacion-proyecto/PASO_CERO.md) - Configuración completa
- [📋 **PRD**](./documentacion-proyecto/PRD_BAMBU_2025_PROFESIONAL.md) - Requerimientos del producto
- [📊 **Análisis MVP**](./documentacion-proyecto/RESUMEN_EJECUTIVO_ANALISIS_BAMBU.md) - Contexto del proyecto

---

## 🚢 Deployment

### Desarrollo Local
```bash
# Servidor Laravel
php artisan serve

# Assets en tiempo real
npm run dev
```

### Producción
```bash
# Build assets
npm run build

# Optimizar Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
```

---

## 🤝 Contribución

### Flujo de Trabajo
1. **Crear feature branch** desde `main`
2. **Desarrollar** siguiendo estándares del proyecto
3. **Tests** - Asegurar cobertura >70%
4. **Pull Request** con descripción clara
5. **Review** y merge

### Estándares de Código
- **Laravel**: PSR-12 + Laravel conventions
- **Vue.js**: Composition API + TypeScript
- **CSS**: Tailwind classes + BEM methodology

---

## 📊 Estado del Proyecto

### Fase Actual: **FASE 0 - Setup y Documentación** (Semana 2/4)

**Progreso General**: `▓▓▓▓▓▓▓░░░` 70% Fase 0

**Próximos Hitos**:
- ✅ Setup técnico completado
- 🔄 Documentación refactorizada 
- ⏳ Migración modelos (Semana 3)
- ⏳ API base + Auth (Semana 4)

---

## 📞 Soporte

### Equipo de Desarrollo
- **Lead Developer**: [Nombre]
- **Frontend**: Vue.js + TypeScript
- **Backend**: Laravel + MySQL

### Issues y Bugs
Para reportar problemas o sugerir mejoras, usar el sistema de issues del repositorio.

---

## 📄 Licencia

Sistema interno BAMBU - Todos los derechos reservados.

---

**Creado**: Agosto 2025  
**Última actualización**: Agosto 2025  
**Versión**: 2.0-alpha  