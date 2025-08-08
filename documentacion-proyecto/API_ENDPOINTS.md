# API REST Endpoints - Sistema BAMBU v2.0

## 🌐 Base URL
```
Desarrollo: http://localhost:8000/api/v1
Producción: https://bambu-crm.com/api/v1
```

## 🔐 Autenticación
**Sistema**: Laravel Sanctum  
**Tipo**: Bearer Token

```bash
# Obtener token de autenticación
POST /api/login
{
  "email": "admin@bambu.com",
  "password": "password"
}

# Usar token en requests
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Accept: application/json" \
     https://api.bambu-crm.com/api/v1/stock
```

---

## 📦 PRODUCTOS

### **GET /productos** (Público)
Lista de productos con paginación

**Parámetros de consulta**:
```yaml
search: string          # Buscar en nombre y SKU
marca: string          # Filtrar por marca
stock_bajo: boolean    # Solo productos con stock <= stock_minimo
sin_stock: boolean     # Solo productos sin stock (0)
per_page: integer      # Items por página (default: 20)
```

**Ejemplo**:
```bash
GET /api/v1/productos?search=shampoo&marca=BAMBU&per_page=10
```

**Respuesta**:
```json
{
  "data": [
    {
      "id": 1,
      "nombre": "BAMBU Shampoo Natural 300ml",
      "sku": "BAMBU-SH-NAT-300",
      "precio_base_l1": "3200.00",
      "stock_actual": 75,
      "stock_minimo": 10,
      "estado_stock": "stock_ok",
      "marca_producto": "BAMBU",
      "peso_kg": "0.350",
      "es_combo": false
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### **GET /productos/{id}** (Público)
Detalle de producto específico

### **POST /productos** 🔒 (Autenticación requerida)
Crear nuevo producto

**Body**:
```json
{
  "nombre": "NUEVO Producto Test",
  "sku": "TEST-001",
  "precio_base_l1": 1500.00,
  "stock_actual": 10,
  "stock_minimo": 5,
  "marca_producto": "BAMBU",
  "descripcion": "Descripción del producto",
  "peso_kg": 0.250,
  "es_combo": false,
  "fabricar_siguiente": false
}
```

### **PUT /productos/{id}** 🔒
Actualizar producto existente

### **DELETE /productos/{id}** 🔒
Eliminar producto (soft delete)

---

## 👥 CLIENTES

### **GET /clientes** 🔒
Lista de clientes con paginación

**Parámetros**:
```yaml
search: string         # Buscar en nombre, apellido, teléfono
nivel: string         # Filtrar por nivel de precio (L1, L2, L3, L4)
activo: boolean       # Solo clientes activos
per_page: integer     # Default: 20
```

### **POST /clientes** 🔒
Crear nuevo cliente

**Body**:
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "telefono": "+54911234567",
  "email": "juan@email.com",
  "direccion": "Av. Rivadavia 1234, CABA",
  "nivel_precio": "L2",
  "notas": "Cliente preferencial"
}
```

### **GET /clientes/buscar/{termino}** 🔒
Búsqueda rápida por teléfono o nombre

---

## ⚙️ CONFIGURACIONES

### **GET /configuraciones** (Público)
Configuraciones públicas del sistema

**Respuesta**:
```json
{
  "data": [
    {
      "clave": "radio_entrega_gratuita_km",
      "valor": "50",
      "tipo": "integer",
      "descripcion": "Radio en km para entrega gratuita",
      "categoria": "logistica"
    }
  ]
}
```

### **POST /configuraciones** 🔒
Crear/actualizar configuración (Solo administradores)

---

## 📊 STOCK MANAGEMENT (NUEVO) ⭐

### **GET /stock** 🔒
Lista de productos con información de stock

**Parámetros**:
```yaml
stock_bajo: boolean    # Solo productos con stock bajo
sin_stock: boolean     # Solo productos sin stock
marca: string         # Filtrar por marca
search: string        # Buscar en nombre/SKU
per_page: integer     # Default: 20
```

**Respuesta**:
```json
{
  "data": [
    {
      "id": 1,
      "nombre": "BAMBU Shampoo Natural 300ml",
      "sku": "BAMBU-SH-NAT-300",
      "stock_actual": 5,
      "stock_minimo": 10,
      "fabricar_siguiente": false,
      "estado_stock": "stock_bajo",
      "precio_base_l1": "3200.00"
    }
  ]
}
```

### **POST /stock/ajustar** 🔒 ⚠️
Ajustar stock manualmente (AUDITORÍA COMPLETA)

**Body**:
```json
{
  "producto_id": 1,
  "nueva_cantidad": 50,
  "motivo": "Reconteo de inventario - diferencia detectada",
  "lote_produccion": "2025-AGO-001"  // opcional
}
```

**Respuesta**:
```json
{
  "message": "Stock ajustado correctamente",
  "movimiento": {
    "id": 15,
    "producto_id": 1,
    "tipo": "ajuste_positivo",
    "cantidad": "45.000",
    "stock_anterior": 5,
    "stock_nuevo": 50,
    "motivo": "Reconteo de inventario",
    "usuario": {
      "id": 1,
      "name": "Administrador"
    },
    "created_at": "2025-08-07T10:30:15.000000Z"
  },
  "producto": {
    "id": 1,
    "nombre": "BAMBU Shampoo Natural 300ml",
    "stock_anterior": 5,
    "stock_actual": 50,
    "estado_stock": "stock_ok"
  }
}
```

### **POST /stock/ingreso** 🔒
Ingreso de stock por producción

**Body**:
```json
{
  "producto_id": 2,
  "cantidad": 100,
  "motivo": "Producción terminada - Lote Agosto",
  "lote_produccion": "2025-AGO-002"
}
```

### **GET /stock/alertas** 🔒
Productos con stock bajo (alertas)

**Respuesta**:
```json
{
  "productos_stock_bajo": [
    {
      "id": 3,
      "nombre": "BAMBU Crema Hidratante 250ml",
      "stock_actual": 2,
      "stock_minimo": 10,
      "estado_stock": "stock_bajo",
      "dias_stock_restante": 7,
      "fabricar_siguiente": false
    }
  ],
  "total": 1
}
```

### **GET /stock/para-fabricar** 🔒
Cola de productos para fabricar

**Respuesta**:
```json
{
  "productos_para_fabricar": [
    {
      "id": 4,
      "nombre": "BAMBU Jabón Artesanal",
      "stock_actual": 0,
      "stock_minimo": 5,
      "fabricar_siguiente": false,
      "prioridad": "alta"
    },
    {
      "id": 2,
      "nombre": "BAMBU Pasta Dental",
      "stock_actual": 3,
      "stock_minimo": 10,
      "fabricar_siguiente": false,
      "prioridad": "media"
    },
    {
      "id": 5,
      "nombre": "BAMBU Desinfectante",
      "stock_actual": 25,
      "stock_minimo": 15,
      "fabricar_siguiente": true,
      "prioridad": "baja"
    }
  ],
  "total": 3
}
```

### **GET /stock/historial/{producto}** 🔒
Historial completo de movimientos de un producto

**Parámetros**:
```yaml
limite: integer       # Máximo movimientos (default: 50)
```

**Respuesta**:
```json
{
  "producto": {
    "id": 1,
    "nombre": "BAMBU Shampoo Natural 300ml",
    "sku": "BAMBU-SH-NAT-300",
    "stock_actual": 75,
    "stock_minimo": 10,
    "estado_stock": "stock_ok"
  },
  "movimientos": [
    {
      "id": 12,
      "tipo": "ajuste_positivo",
      "cantidad": "45.000",
      "stock_anterior": 5,
      "stock_nuevo": 50,
      "motivo": "Reconteo inventario",
      "usuario": {
        "id": 1,
        "name": "Juan Administrador"
      },
      "created_at": "2025-08-07T10:30:15.000000Z",
      "lote_produccion": "2025-AGO-001"
    },
    {
      "id": 11,
      "tipo": "venta",
      "cantidad": "20.000",
      "stock_anterior": 25,
      "stock_nuevo": 5,
      "motivo": "Venta - Pedido #45",
      "pedido_id": 45,
      "usuario": {
        "id": 2,
        "name": "María Operadora"
      },
      "created_at": "2025-08-06T15:20:10.000000Z"
    }
  ]
}
```

### **POST /stock/marcar-fabricar/{producto}** 🔒
Marcar/desmarcar producto para fabricar

**Body**:
```json
{
  "fabricar": true  // true o false
}
```

---

## 📋 CÓDIGOS DE RESPUESTA

### **HTTP Status Codes**
```yaml
200: OK                    # Éxito
201: Created              # Recurso creado
204: No Content           # Eliminación exitosa
400: Bad Request          # Error en datos enviados
401: Unauthorized         # Sin autenticación
403: Forbidden            # Sin permisos
404: Not Found           # Recurso no encontrado
422: Unprocessable Entity # Errores validación
500: Internal Server Error # Error servidor
```

### **Estructura de Errores**
```json
{
  "message": "Datos inválidos",
  "errors": {
    "motivo": [
      "El motivo es obligatorio para ajustes negativos"
    ],
    "producto_id": [
      "El producto seleccionado no existe"
    ]
  }
}
```

---

## 🔒 SEGURIDAD Y VALIDACIONES

### **Stock Adjustments** ⚠️
- **Motivo obligatorio** para ajustes negativos
- **Usuario registrado** en todos los movimientos
- **Validación de existencia** del producto
- **Transacciones de BD** para consistencia

### **Autenticación**
- **Bearer token** requerido para endpoints protegidos
- **Tokens no expiran** (configuración actual de desarrollo)
- **Rate limiting**: 60 requests/minuto por IP

### **Validaciones Comunes**
```yaml
Productos:
  - SKU único obligatorio
  - Precio > 0
  - Stock >= 0
  - Nombre min: 3 caracteres

Stock:
  - Cantidad > 0 para ingresos
  - Motivo obligatorio si cantidad < stock_anterior
  - Producto debe existir

Clientes:
  - Teléfono formato válido
  - Email único si se proporciona
  - Nivel precio válido (L1-L4)
```

---

## 🧪 TESTING Y EXAMPLES

### **Curl Examples**

**1. Obtener productos con stock bajo**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://localhost:8000/api/v1/stock?stock_bajo=true"
```

**2. Ajustar stock con auditoría**:
```bash
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "producto_id": 1,
       "nueva_cantidad": 75,
       "motivo": "Ingreso de producción - Lote AGO-001",
       "lote_produccion": "AGO-001"
     }' \
     "http://localhost:8000/api/v1/stock/ajustar"
```

**3. Ver alertas de stock**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "http://localhost:8000/api/v1/stock/alertas"
```

### **Testing Suite**
- **47 tests implementados** con PHPUnit
- **Cobertura completa** de endpoints
- **Casos edge** incluidos (stock negativo, productos inexistentes)

---

**📅 Última actualización**: 2025-08-08 - Inconsistencias resueltas + referencias SAPHIRUS eliminadas  
**🔄 Estado**: Sistema Completamente Operativo  
**🧪 Tests**: 47 tests implementados ✅  
**📊 Endpoints**: 49+ operativos ✅