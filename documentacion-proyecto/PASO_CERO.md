# PASO CERO - Preparación del Entorno Sistema BAMBU

## Introducción

Este documento describe todos los pasos necesarios para preparar un entorno de desarrollo desde cero y recrear el MVP del Sistema BAMBU. Incluye verificaciones, configuraciones y comandos específicos para evitar los problemas conocidos del proyecto original.

---

## 1. CHECKLIST DE REQUISITOS PREVIOS

### 1.1 Entorno Base (Windows con Laragon)
```yaml
Sistema Operativo:
  ✓ Windows 10/11
  ✓ Laragon Full 6.0+ (recomendado) o XAMPP 8.2+
  ✓ Git for Windows 2.40+

Servidores:
  ✓ Apache 2.4+ (incluido en Laragon)
  ✓ MySQL 8.0+ o MariaDB 10.6+
  ✓ phpMyAdmin (opcional, para visualización)
```

### 1.2 PHP y Extensiones
```yaml
PHP:
  ✓ PHP 8.2+ (mínimo) o 8.3+ (recomendado)
  ✓ Composer 2.5+

Extensiones PHP Requeridas:
  ✓ BCMath
  ✓ Ctype
  ✓ cURL
  ✓ DOM
  ✓ Fileinfo
  ✓ JSON
  ✓ Mbstring
  ✓ OpenSSL
  ✓ PCRE
  ✓ PDO
  ✓ PDO_MySQL
  ✓ Tokenizer
  ✓ XML
  ✓ Zip (opcional para Filament)
  ✓ GD o Imagick (para manejo de imágenes)
  ✓ Redis (opcional, recomendado)
```

### 1.3 Node.js y NPM (Para Assets)
```yaml
Node.js:
  ✓ Node.js 18+ LTS (recomendado 20+)
  ✓ npm 9+ o pnpm 8+ (más rápido)
  ✓ Git Bash (para comandos Unix-like)
```

### 1.4 Editor y Herramientas
```yaml
Editor:
  ✓ VSCode o PhpStorm
  ✓ Extensiones PHP (PHP IntelliSense, Laravel Extension Pack)

Herramientas opcionales:
  ✓ TablePlus/HeidiSQL (DB client)
  ✓ Postman/Insomnia (API testing)
  ✓ Redis Commander (Redis GUI)
```

---

## 2. CONFIGURACIONES RECOMENDADAS

### 2.1 Configuración PHP (php.ini)
```ini
; Ubicación típica: C:\laragon\bin\php\php-8.3.x\php.ini

; Memory y Time Limits
memory_limit = 512M
max_execution_time = 300
max_input_time = 300

; File Uploads
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
max_file_uploads = 20

; Extensions necesarias
extension=bcmath
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=zip

; Timezone
date.timezone = America/Argentina/Buenos_Aires

; Display Errors (solo desarrollo)
display_errors = On
display_startup_errors = On
log_errors = On
```

### 2.2 Configuración MySQL/MariaDB
```sql
-- my.cnf o my.ini
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 128M
max_connections = 200
query_cache_size = 32M
tmp_table_size = 64M
max_heap_table_size = 64M

-- Character set para soporte UTF-8 completo
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
```

### 2.3 Configuración Git
```bash
# Configuración inicial de Git
git config --global user.name "Tu Nombre"
git config --global user.email "tu.email@ejemplo.com"
git config --global init.defaultBranch main

# Para evitar problemas con line endings en Windows
git config --global core.autocrlf false
git config --global core.safecrlf false
```

### 2.4 Variables de Entorno PATH
```bash
# Agregar a PATH del sistema (Variables de Entorno Windows)
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64
C:\laragon\bin\composer
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin
C:\Program Files\nodejs
```

---

## 3. VERIFICACIONES A REALIZAR

### 3.1 Verificar Servicios Base
```bash
# Verificar PHP
php --version
# Debe mostrar: PHP 8.2+ o superior

# Verificar Composer
composer --version
# Debe mostrar: Composer version 2.5+ o superior

# Verificar Node.js
node --version
npm --version
# Node: v18+ o superior, npm: 9+ o superior

# Verificar Git
git --version
# Git version 2.40+ o superior
```

### 3.2 Verificar Extensiones PHP
```bash
# Verificar extensiones críticas
php -m | grep -i bcmath
php -m | grep -i curl
php -m | grep -i fileinfo
php -m | grep -i gd
php -m | grep -i mbstring
php -m | grep -i mysqli
php -m | grep -i pdo_mysql
php -m | grep -i openssl
php -m | grep -i zip

# Comando Windows equivalente
php -m | findstr /i "bcmath curl fileinfo gd mbstring mysqli pdo_mysql openssl zip"
```

### 3.3 Verificar Conexión a Base de Datos
```bash
# Desde línea de comandos
mysql -u root -p -e "SELECT VERSION();"

# O crear archivo test-db.php
<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "✓ Conexión MySQL exitosa\n";
    echo "Versión: " . $pdo->query('SELECT VERSION()')->fetchColumn() . "\n";
} catch (PDOException $e) {
    echo "✗ Error conexión: " . $e->getMessage() . "\n";
}
?>
```

### 3.4 Test de Laravel
```bash
# Crear proyecto Laravel temporal para verificar
composer create-project laravel/laravel test-laravel
cd test-laravel
php artisan --version
# Debe mostrar Laravel Framework sin errores

# Limpiar después del test
cd ..
rm -rf test-laravel
```

---

## 4. OBSERVACIONES Y NOTAS TÉCNICAS

### 4.1 Problemas Conocidos de Windows/Laragon

#### Path Issues
```bash
# ❌ Problema: "composer: command not found"
# ✅ Solución: Usar path completo o configurar PATH
export PATH="/c/laragon/bin/php/php-8.3.16-Win32-vs16-x64:$PATH"
/c/laragon/bin/composer/composer [comando]
```

#### Permisos de Archivos
```bash
# ❌ Problema: Permission denied en storage/logs
# ✅ Solución: Configurar permisos (desde Git Bash)
chmod -R 775 storage bootstrap/cache
```

#### Vite/NPM en Windows
```bash
# ❌ Problema: Vite no compila correctamente
# ✅ Solución temporal: Usar CDN para CSS/JS en desarrollo
# ✅ Solución definitiva: Configurar Vite específicamente para Windows
```

### 4.2 Configuraciones Específicas del Proyecto

#### Scout Driver
```env
# Para desarrollo local, usar collection driver (no requiere Algolia/Meilisearch)
SCOUT_DRIVER=collection
```

#### Base de Datos
```env
# IMPORTANTE: El proyecto original usó MySQL pero recomendamos PostgreSQL para nuevo desarrollo
# Para compatibilidad inmediata con seeders existentes, usar MySQL inicialmente
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
```

#### Filament
```bash
# Filament 3 puede requerir flag especial en Windows
composer require filament/filament:"^3.2" --ignore-platform-req=ext-zip
```

### 4.3 Livewire - Bugs Conocidos
```php
// ❌ NUNCA usar referencias en Livewire (causa corrupción de arrays)
foreach ($this->items as &$item) { /* MALO */ }

// ✅ Siempre usar índices
foreach ($this->items as $key => $item) {
    $this->items[$key]['field'] = $newValue;
}
```

### 4.4 Bootstrap vs Tailwind
```html
<!-- El MVP actual usa Bootstrap 5 vía CDN -->
<!-- Para nueva versión, migrar a Tailwind + build tools -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
```

---

## 5. COMANDOS Y PASOS INICIALES SUGERIDOS

### 5.1 Crear Estructura del Proyecto
```bash
# 1. Navegar al directorio de proyectos
cd /c/laragon/www/

# 2. Crear nuevo proyecto Laravel
composer create-project laravel/laravel bambu-sistema-v2
cd bambu-sistema-v2

# 3. Configurar Git
git init
git add .
git commit -m "Initial Laravel installation"
```

### 5.2 Instalar Dependencias del Proyecto
```bash
# Dependencias principales del MVP original
composer require laravel/scout
composer require spatie/laravel-permission
composer require filament/filament:"^3.2" --ignore-platform-req=ext-zip

# Dependencias adicionales recomendadas
composer require barryvdh/laravel-debugbar --dev
composer require larastan/larastan --dev
composer require laravel/pint --dev

# Si se va a usar PostgreSQL en lugar de MySQL
# composer require postgres/postgres
```

### 5.3 Configurar Entorno (.env)
```bash
# Copiar y modificar .env
cp .env.example .env

# Generar APP_KEY
php artisan key:generate
```

### 5.4 Configuración .env Base
```env
APP_NAME="Sistema BAMBU"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://bambu-sistema-v2.test

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bambu_sistema_v2
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Scout Configuration
SCOUT_DRIVER=collection

# Filament
FILAMENT_PATH=admin

# Timezone
APP_TIMEZONE="America/Argentina/Buenos_Aires"
```

### 5.5 Crear Base de Datos
```sql
-- En phpMyAdmin o línea de comandos MySQL
CREATE DATABASE bambu_sistema_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5.6 Configurar NPM/Assets (Opcional)
```bash
# Instalar dependencias NPM
npm install

# Para desarrollo (si no se usa CDN)
npm run dev

# Para producción
npm run build
```

### 5.7 Ejecutar Migraciones y Seeders Iniciales
```bash
# Ejecutar migraciones básicas de Laravel
php artisan migrate

# Si se van a migrar desde MVP original:
# 1. Copiar migraciones desde proyecto original
# 2. Copiar seeders desde proyecto original
# 3. Ejecutar:
# php artisan migrate:fresh --seed
```

### 5.8 Configurar Filament
```bash
# Instalar panel de admin
php artisan filament:install --panels

# Crear usuario admin
php artisan make:filament-user
# Email: admin@bambu.com
# Password: admin123 (cambiar en producción)
```

### 5.9 Verificar Instalación
```bash
# Iniciar servidor de desarrollo
php artisan serve --host=127.0.0.1 --port=8000

# Verificar rutas disponibles
php artisan route:list

# Limpiar cachés si hay problemas
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

### 5.10 URLs de Verificación
```
Aplicación: http://127.0.0.1:8000
Admin Panel: http://127.0.0.1:8000/admin
```

---

## 6. ESTRUCTURA FINAL ESPERADA

```
bambu-sistema-v2/
├── app/
├── database/
├── resources/
├── routes/
├── .env (configurado)
├── composer.json (con dependencias)
└── README.md
```

---

## 7. COMANDOS DE EMERGENCIA

### Resetear Proyecto Completo
```bash
# Si algo sale mal y necesitas empezar de nuevo
php artisan migrate:fresh --seed
php artisan config:clear && php artisan cache:clear && php artisan view:clear
composer dump-autoload
```

### Debug Common Issues
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar configuración actual
php artisan config:show

# Verificar estado de migraciones
php artisan migrate:status

# Reindexar Scout si las búsquedas no funcionan
php artisan scout:flush "App\Models\Product"
php artisan scout:import "App\Models\Product"
```

---

## 8. CHECKLIST FINAL DE VERIFICACIÓN

Antes de comenzar el desarrollo, verificar:

- [ ] PHP 8.2+ funcionando
- [ ] Composer instalado y accesible
- [ ] MySQL/MariaDB corriendo
- [ ] Base de datos creada
- [ ] Laravel instalado sin errores
- [ ] Dependencias principales instaladas
- [ ] .env configurado correctamente
- [ ] Migraciones ejecutadas
- [ ] Filament accesible
- [ ] Servidor de desarrollo funcionando
- [ ] Git configurado y primer commit realizado

---

**¡IMPORTANTE!** Una vez completado este PASO CERO, el entorno estará listo para comenzar el desarrollo siguiendo el roadmap definido en `ROADMAP_DESARROLLO_2025.md`.

---

**Documento creado**: Enero 2025  
**Versión**: 1.0  
**Mantenedor**: Equipo BAMBU  
**Actualización**: Con cada cambio de stack o dependencias