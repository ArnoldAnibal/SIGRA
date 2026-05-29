# SIGRA API Restaurante

REST API desarrollado con Laravel 13 para la administración integral del restaurante **La Antigua**.

---

# Tecnologías Utilizadas

* PHP 8.2
* Laravel 13
* MariaDB / MySQL
* XAMPP
* Composer
* Laravel Sanctum
* Laravel Eloquent ORM
* Laravel Resources
* Laravel Form Requests
* Soft Deletes

---

# Arquitectura Actual

El proyecto sigue una arquitectura en capas utilizando buenas prácticas de Laravel.

## Estructura

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Repositories/
└── Services/
```

---

# Autenticación

El sistema utiliza autenticación mediante:

```text
Laravel Sanctum
```

---

# Seguridad Implementada

El backend implementa:

* Password hashing mediante bcrypt
* Tokens API con Laravel Sanctum
* Middleware de autenticación
* Middleware de roles
* Validaciones mediante Form Requests
* Protección de rutas privadas
* Soft Deletes
* Validaciones de acceso por rol

---

# Autenticación de Empleados

Los empleados pueden iniciar sesión utilizando:

* username
* password

---

## Endpoint Login

POST `/api/v1/auth/empleados/login`

Body:

```json
{
    "login": "arnold",
    "password": "123456"
}
```

Respuesta:

```json
{
    "message": "Login exitoso",
    "token": "TOKEN_AQUI",
    "empleado": {
        "id_empleado": 1,
        "nombre": "Arnold Avila",
        "rol": "Administrador",
        "username": "arnold"
    }
}
```

---

## Logout

POST `/api/v1/auth/empleados/logout`

Headers:

```text
Authorization: Bearer TOKEN
```

---

## Usuario autenticado

GET `/api/v1/auth/empleados/me`

Headers:

```text
Authorization: Bearer TOKEN
```

---

# Roles Implementados

Roles soportados:

* Administrador
* Mesero
* Cajero
* Cocinero

---

# Middleware de Roles

El sistema utiliza:

```text
role
```

Ejemplo:

```php
Route::middleware('role:Administrador')
```

---

# Seguridad de Endpoints

## Rutas públicas

No requieren autenticación:

* Login
* Consulta de productos
* Consulta de categorías

## Rutas protegidas

Requieren:

```text
Authorization: Bearer TOKEN
```

Además, algunas rutas requieren roles específicos.

---

# Permisos por Rol

## Administrador

Acceso total:

* Empleados
* Planillas
* Productos
* Categorías
* Clientes
* Facturas
* Pagos
* Pedidos
* Mesas
* Asistencias

---

## Cajero

Acceso:

* Clientes
* Facturas
* Pagos
* Mesas
* Pedidos

---

## Mesero

Acceso:

* Pedidos
* Mesas
* Detalles de pedido

---

## Cocinero

Acceso:

* Actualización de pedidos
* Consulta de cocina

---

# Gestión de Menú

## Categorías de Menú

CRUD completo para categorías.

---

## Endpoints

GET `/api/v1/categorias-menu`

GET `/api/v1/categorias-menu/{id}`

POST `/api/v1/categorias-menu`

PUT `/api/v1/categorias-menu/{id}`

DELETE `/api/v1/categorias-menu/{id}`

---

## Body Categoría

```json
{
    "nombre": "Bebidas"
}
```

---

# Productos del Menú

CRUD completo para productos.

---

## Endpoints

GET `/api/v1/productos-menu`

GET `/api/v1/productos-menu/{id}`

POST `/api/v1/productos-menu`

PUT `/api/v1/productos-menu/{id}`

DELETE `/api/v1/productos-menu/{id}`

---

## Body Producto

```json
{
    "nombre": "Pizza Suprema",
    "descripcion": "Pizza grande familiar",
    "precio": 120.50,
    "stock": 25,
    "stock_minimo": 5,
    "estado": "Disponible",
    "id_categoria": 1
}
```

---

# Upload de imágenes

Los productos aceptan:

```text
multipart/form-data
```

Campo:

```text
imagen
```

Ruta de almacenamiento:

```text
storage/app/public/productos
```

Respuesta:

```json
{
    "imagen_url": "http://127.0.0.1:8000/storage/productos/pizza.jpg"
}
```

---

# Gestión de Mesas

CRUD completo de mesas.

---

## Estados soportados

* Libre
* Ocupada
* Reservada
* Mantenimiento

---

## Endpoints

GET `/api/v1/mesas`

GET `/api/v1/mesas/{id}`

POST `/api/v1/mesas`

PUT `/api/v1/mesas/{id}`

DELETE `/api/v1/mesas/{id}`

---

## Body Mesa

```json
{
    "numero_mesa": 12,
    "capacidad": 6,
    "estado": "Libre"
}
```

---

# Gestión de Clientes

CRUD completo de clientes.

---

## Tipos de Cliente

* Individual
* Empresa

---

## Estados

* Activo
* Suspendido

---

## Endpoints

GET `/api/v1/clientes`

GET `/api/v1/clientes/{id}`

POST `/api/v1/clientes`

PUT `/api/v1/clientes/{id}`

DELETE `/api/v1/clientes/{id}`

---

## Body Cliente Individual

```json
{
    "nombre": "Juan Perez",
    "telefono": "55554444",
    "email": "email",
    "direccion": "Zona 1",
    "tipo_cliente": "Individual",
    "estado": "Activo",
    "username": "juan",
    "password": "123456"
}
```

---

## Body Cliente Empresa

```json
{
    "nombre": "Empresa XYZ",
    "telefono": "22223333",
    "direccion": "Zona 10",
    "tipo_cliente": "Empresa",
    "limite_credito": 10000,
    "saldo_credito_actual": 0,
    "estado": "Activo",
    "username": "empresa_xyz",
    "password": "123456"
}
```

---

# Gestión de Pedidos

CRUD completo implementado.

---

## Tipos de pedido

* Para Aca
* Para Llevar
* Online

---

## Estados de pedido

* Pendiente
* Preparando
* Preparado
* Entregado
* Cancelado

---

## Métodos de pago

* Efectivo
* Tarjeta
* Transferencia
* Credito

---

## Endpoints

GET `/api/v1/pedidos`

GET `/api/v1/pedidos/{id}`

POST `/api/v1/pedidos`

PUT `/api/v1/pedidos/{id}`

DELETE `/api/v1/pedidos/{id}`

---

## Body Pedido

```json
{
    "estado": "Pendiente",
    "tipo": "Para Aca",
    "metodo_pago": "Efectivo",
    "total": 300.00,
    "id_cliente": 1,
    "id_empleado": 1
}
```

---

## Body Pedido Online

```json
{
    "estado": "Pendiente",
    "tipo": "Online",
    "metodo_pago": "Tarjeta",
    "total": 500.00,
    "direccion_entrega": "Zona 15",
    "telefono_contacto": "55557777",
    "id_cliente": 2,
    "id_empleado": 1
}
```

---

# Flujo Operativo de Cocina

## Mesero

Crea pedidos en estado:

```text
Pendiente
```

## Cocinero

Actualiza pedidos:

```text
Preparando
Preparado
```

## Cajero

Finaliza pedidos:

```text
Entregado
```

---

# Validaciones de Crédito

Cuando:

```text
metodo_pago = Credito
```

El sistema valida:

* Cliente existente
* Cliente tipo Empresa
* Estado Activo
* Crédito disponible

---

# Gestión de Detalle Pedido

CRUD completo implementado.

---

## Endpoints

GET `/api/v1/detalles-pedido`

GET `/api/v1/detalles-pedido/{id}`

POST `/api/v1/detalles-pedido`

PUT `/api/v1/detalles-pedido/{id}`

DELETE `/api/v1/detalles-pedido/{id}`

---

## Body

```json
{
    "cantidad": 2,
    "precio_unitario": 50,
    "subtotal": 100,
    "id_pedido": 1,
    "id_producto": 3
}
```

---

# Facturación Electrónica

CRUD completo implementado.

---

## Estados de factura

* Emitida
* Anulada

---

## Estados de pago

* Pendiente
* Pagada

---

## Endpoints

GET `/api/v1/facturas-electronicas`

GET `/api/v1/facturas-electronicas/{id}`

POST `/api/v1/facturas-electronicas`

PUT `/api/v1/facturas-electronicas/{id}`

DELETE `/api/v1/facturas-electronicas/{id}`

---

## Body Factura

```json
{
    "uuid_sat": "550e8400-e29b-41d4-a716-446655440000",
    "fecha_emision": "2026-05-27 12:00:00",
    "nit_receptor": "1234567-8",
    "monto_total": 300.00,
    "estado": "Emitida",
    "metodo_pago": "Credito",
    "estado_pago": "Pendiente",
    "id_cliente_deudor": 16,
    "fecha_vencimiento": "2026-06-27",
    "id_pedido": 15
}
```

---

# Gestión de Pagos

CRUD completo implementado.

---

## Endpoints

GET `/api/v1/pagos`

GET `/api/v1/pagos/{id}`

POST `/api/v1/pagos`

PUT `/api/v1/pagos/{id}`

DELETE `/api/v1/pagos/{id}`

---

## Body Pago

```json
{
    "monto": 300,
    "metodo_pago": "Transferencia",
    "fecha_pago": "2026-05-28",
    "id_factura": 1
}
```

---

# Gestión de Empleados

CRUD completo implementado.

---

## Endpoints

GET `/api/v1/empleados`

GET `/api/v1/empleados/{id}`

POST `/api/v1/empleados`

PUT `/api/v1/empleados/{id}`

DELETE `/api/v1/empleados/{id}`

---

## Body Empleado

```json
{
    "nombre": "Carlos Perez",
    "rol": "Cajero",
    "username": "carlos",
    "password": "123456"
}
```

---

# Planilla

CRUD completo implementado.

---

## Endpoints

GET `/api/v1/planillas`

GET `/api/v1/planillas/{id}`

POST `/api/v1/planillas`

PUT `/api/v1/planillas/{id}`

DELETE `/api/v1/planillas/{id}`

---

## Body Planilla

```json
{
    "fecha_inicio": "2026-05-01",
    "fecha_fin": "2026-05-15",
    "salario_base": 4500,
    "bonificacion": 250,
    "descuento": 100,
    "id_empleado": 2
}
```

---

# Asistencia

Sistema funcional de:

* Clock In
* Clock Out

---

## Características

El sistema de asistencia funciona automáticamente utilizando:

```text
Laravel Sanctum
```

El empleado autenticado se obtiene desde el token:

```text
Authorization: Bearer TOKEN
```

Ya NO es necesario enviar:

```text
id_empleado
```

desde el frontend.

---

## Validaciones

* No múltiples jornadas activas
* Finalización automática de jornada
* Clock In automático con fecha/hora del servidor
* Clock Out automático con hora del servidor
* Identificación automática mediante token

---

## Endpoints

GET `/api/v1/asistencias`

GET `/api/v1/asistencias/{id}`

POST `/api/v1/asistencias`

PUT `/api/v1/asistencias/{id}`

DELETE `/api/v1/asistencias/{id}`

---

# Clock In

POST `/api/v1/asistencias`

## Headers

```text
Authorization: Bearer TOKEN
```

## Body

```json
{}
```

---

## Respuesta

```json
{
    "message": "Entrada registrada correctamente.",
    "data": {
        "id_asistencia": 1,
        "id_empleado": 2,
        "fecha": "2026-05-28",
        "hora_entrada": "08:00:00",
        "hora_salida": null,
        "estado": "Activa"
    }
}
```

---

# Clock Out Automático

PUT `/api/v1/asistencias/cerrar-jornada`

## Headers

```text
Authorization: Bearer TOKEN
```

## Body

```json
{}
```

---

## Respuesta

```json
{
    "message": "Salida registrada correctamente.",
    "data": {
        "id_asistencia": 1,
        "hora_salida": "17:00:00",
        "estado": "Finalizada"
    }
}
```


# Inventario

## Tabla Implementada

```text
inventario_movimiento
```

---

## Objetivo

Registrar:

* Entradas
* Salidas
* Ajustes
* Consumo cocina

---

# Cuentas por Pagar

Tablas implementadas:

* proveedor
* cuentas_por_pagar

---

# Eliminación Lógica

Los módulos con Soft Deletes:

* no eliminan físicamente los registros
* permiten recuperación futura
* mantienen trazabilidad histórica

---

# Soft Deletes

Módulos que utilizan Soft Deletes:

* categoria_menu
* producto_menu
* mesa
* cliente
* empleado
* planilla
* pedido
* factura_electronica
* detalle_pedido

---

# Versionamiento API

Todas las rutas utilizan:

```text
/api/v1/
```

---

# Headers Requeridos

Para rutas protegidas:

```text
Authorization: Bearer TOKEN
```

---

# Códigos HTTP Utilizados

| Código | Significado           |
| ------ | --------------------- |
| 200    | OK                    |
| 201    | Recurso creado        |
| 401    | No autenticado        |
| 403    | No autorizado         |
| 404    | Recurso no encontrado |
| 422    | Error de validación   |
| 500    | Error interno         |

---

# Convenciones REST

| Método | Acción     |
| ------ | ---------- |
| GET    | Consultar  |
| POST   | Crear      |
| PUT    | Actualizar |
| DELETE | Eliminar   |

---

# Próximos Módulos

Pendientes:

* Inventario automático por venta
* Dashboard administrativo
* PDF de facturas
* Cocina en tiempo real
* Reportería
* Cuentas por cobrar
* Auditoría de acciones

---

# Instalación

Instalar dependencias:

```bash
composer install
```

---

## Configurar entorno

```bash
cp .env.example .env
```

---

## Generar key

```bash
php artisan key:generate
```

---

## Ejecutar migraciones

```bash
php artisan migrate
```

---

## Storage Link

```bash
php artisan storage:link
```

---

## Levantar servidor

```bash
php artisan serve
```

---

# Autores

Arnold Avila — Backend

Christopher Arellano — Frontend

Derek Lemus — Frontend
