# SIGRA API Restaurante

REST API desarrollado con Laravel 13 para la administración integral del restaurante **La Antigua**.

---

# Tecnologías Utilizadas

* PHP 8.2
* Laravel 13
* MariaDB / MySQL
* XAMPP
* Composer
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
│   ├── Requests/
│   └── Resources/
├── Models/
├── Repositories/
└── Services/
```

---

# Funcionalidades Implementadas

## Gestión de Menú

### Categorías de Menú

CRUD completo para categorías.

### Productos del Menú

CRUD completo para productos.

### Nuevas funcionalidades

* Soporte para imágenes de productos.
* Campo `imagen` agregado a `producto_menu`.
* API retorna `imagen_url` automáticamente.

Ejemplo:

```json
{
    "imagen_url": "http://127.0.0.1:8000/storage/productos/ceviche.jpg"
}
```

### Upload de imágenes

Los productos pueden enviarse usando:

```text
multipart/form-data
```

Campo:

```text
imagen
```

El backend almacena las imágenes en:

```text
storage/app/public/productos
```

---

# Gestión de Mesas

CRUD completo de mesas.

Estados soportados:

* Libre
* Ocupada
* Reservada
* Mantenimiento

---

# Gestión de Clientes

CRUD completo de clientes.

---

# Crédito Empresarial

Se implementó soporte para clientes empresariales con control de crédito.

## Nuevos campos en cliente

* `tipo_cliente`
* `limite_credito`
* `saldo_credito_actual`
* `estado`

## Tipos de cliente

* Individual
* Empresa

## Estados empresariales

* Activo
* Suspendido

---

# Validaciones de Crédito

Cuando un pedido usa:

```text
metodo_pago = Credito
```

el sistema valida:

* Que el cliente exista.
* Que sea tipo Empresa.
* Que esté Activo.
* Que tenga suficiente crédito disponible.

Si el crédito es insuficiente:

```json
{
    "message": "Crédito insuficiente."
}
```

---

# Gestión de Pedidos

CRUD completo implementado.

## Tipos de pedido

* Para Aca
* Para Llevar
* Online

## Estados de pedido

* Pendiente
* Preparando
* Preparado
* Entregado
* Cancelado

## Métodos de pago

* Efectivo
* Tarjeta
* Transferencia
* Credito

---

# Delivery / Pedidos Online

Se agregó soporte para pedidos a domicilio.

## Nuevos campos

* `direccion_entrega`
* `telefono_contacto`

## Validaciones

Cuando:

```text
tipo = Online
```

el campo:

```text
direccion_entrega
```

es obligatorio.

---

# Tracking de Pedidos

El backend ya soporta tracking de pedidos mediante el campo:

```text
estado
```

Los cocineros pueden consultar únicamente pedidos:

```text
Pendiente
Preparando
```

Esto permite construir fácilmente una pantalla de cocina en frontend.

---

# Facturación Electrónica

CRUD completo implementado.

## Estados de factura

* Emitida
* Anulada

## Estados de pago

* Pendiente
* Pagada

---

# Facturación a Crédito

Se agregó soporte para facturas empresariales a crédito.

## Nuevos campos

* `estado_pago`
* `fecha_vencimiento`
* `fecha_pago`
* `id_cliente_deudor`
* `metodo_pago`

## Flujo implementado

Cuando la factura:

```text
metodo_pago = Credito
```

el sistema:

* Marca la factura como `Pendiente`
* Guarda fecha de vencimiento
* Relaciona el cliente deudor

---

# Pago de Facturas

Cuando una factura cambia:

```text
estado_pago = Pagada
```

el sistema:

* Registra automáticamente:

  * `fecha_pago`
* Reduce automáticamente:

  * `saldo_credito_actual`

del cliente empresarial.

---

# Anulación de Facturas

Cuando una factura cambia:

```text
estado = Anulada
```

el sistema:

* Revierte automáticamente el crédito utilizado.
* Reduce el saldo pendiente del cliente.

---

# Inventario

Se inició el módulo de inventario.

## Tabla implementada

```text
inventario_movimiento
```

## Objetivo

Registrar:

* Entradas
* Salidas
* Ajustes
* Consumo de cocina

---

# Cuentas por Pagar

Se agregaron tablas para:

* `proveedor`
* `cuentas_por_pagar`

Esto permitirá manejar:

* Deudas con proveedores
* Compras
* Pagos pendientes

---

# Recursos Humanos

## Empleados

CRUD completo implementado.

Roles soportados:

* Administrador
* Mesero
* Cajero
* Recepcionista
* Cocinero

---

# Planilla

CRUD completo implementado.

---

# Asistencia

Sistema funcional de:

* Clock In
* Clock Out

Validaciones implementadas:

* No múltiples jornadas activas.
* Finalización automática de jornada.

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

Ejemplos:

```text
/api/v1/productos-menu
/api/v1/pedidos
/api/v1/facturas-electronicas
```

---

# Endpoints Importantes

## Crear pedido a crédito

POST `/api/v1/pedidos`

```json
{
    "estado": "Pendiente",
    "tipo": "Para Aca",
    "metodo_pago": "Credito",
    "total": 300.00,
    "id_cliente": 16,
    "id_empleado": 1
}
```

---

## Crear factura a crédito

POST `/api/v1/facturas-electronicas`

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

## Pagar factura

PUT `/api/v1/facturas-electronicas/{id}`

```json
{
    "estado_pago": "Pagada"
}
```

---

## Anular factura

PUT `/api/v1/facturas-electronicas/{id}`

```json
{
    "estado": "Anulada"
}
```

---

# Próximos Módulos

Pendientes o parcialmente implementados:

* Autenticación JWT / Sanctum
* Roles y permisos
* Inventario automático por venta
* Reportería
* Generación PDF de facturas
* Dashboard administrativo
* Cocina en tiempo real
* Cuentas por cobrar completas

---

# Correr el Proyecto

Instalar dependencias:

```bash
composer install
```

Configurar entorno:

```bash
cp .env.example .env
```

Generar key:

```bash
php artisan key:generate
```

Ejecutar migraciones:

```bash
php artisan migrate
```

Crear enlace de storage:

```bash
php artisan storage:link
```

Levantar servidor:

```bash
php artisan serve
```

---

# Autores

Arnold Avila — Backend
Christopher Arellano — Frontend
Derek Lemus — Frontend
