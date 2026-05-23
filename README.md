# SIGRA API Restaurante

REST API desarrollado con Laravel 13 para la administración de un restaurante.

## Tecnologías Utilizadas

* PHP 8.2
* Laravel 13
* MariaDB / MySQL
* XAMPP
* Composer
* Laravel Eloquent ORM

## Arquitectura Actual

El proyecto sigue una arquitectura en capas usando las mejores practicas de laravel

### Estructura

app/

* Http/

  * Controllers/
  * Requests/
  * Resources/
* Models/
* Repositories/
* Services/

## Modulos Implementados

### Categoria Menu CRUD

CRUD completo implementado para las operaciones de la tabla `categoria_menu`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/categorias-menu`

Leer todas las categorias.

GET `/api/v1/categorias-menu/{id}`

Retorna una categoría en base a su ID.

POST `/api/v1/categorias-menu`

Crear una nueva categoría.

Ejemplo de body:

```json
{
    "nombre": "Bebidas"
}
```

PUT `/api/v1/categorias-menu/{id}`

Actualiza una categoria.

Ejemplo de body:

```json
{
    "nombre": "Postres"
}
```

DELETE `/api/v1/categorias-menu/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Producto Menu CRUD

CRUD completo implementado para las operaciones de la tabla `producto_menu`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/productos-menu`

Leer todos los productos.

GET `/api/v1/productos-menu/{id}`

Retorna un producto en base a su ID.

POST `/api/v1/productos-menu`

Crear un nuevo producto.

Ejemplo de body:

```json
{
    "id_categoria": 3,
    "nombre": "Pizza Pepperoni",
    "descripcion": "Pizza grande con pepperoni",
    "precio_venta": 85.50,
    "disponible": 1
}
```

PUT `/api/v1/productos-menu/{id}`

Actualiza un producto.

Ejemplo de body:

```json
{
    "id_categoria": 3,
    "nombre": "Pizza Suprema",
    "descripcion": "Pizza familiar especial",
    "precio_venta": 95.00,
    "disponible": 1
}
```

DELETE `/api/v1/productos-menu/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Mesa CRUD

CRUD completo implementado para las operaciones de la tabla `mesa`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/mesas`

Leer todas las mesas.

GET `/api/v1/mesas/{id}`

Retorna una mesa en base a su ID.

POST `/api/v1/mesas`

Crear una nueva mesa. Las opciones de estado son "Libre", "Ocupada", "Reservada" o "Mantenimiento".

Ejemplo de body:

```json
{
    "numero_mesa": 10,
    "capacidad": 4,
    "estado": "Libre"
}
```

PUT `/api/v1/mesas/{id}`

Actualiza una mesa. Las opciones son 'Libre', 'Ocupada', 'Reservada', 'Mantenimiento'.

Ejemplo de body:

```json
{
    "numero_mesa": 12,
    "capacidad": 6,
    "estado": "Reservada"
}
```

DELETE `/api/v1/mesas/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Cliente CRUD

CRUD completo implementado para las operaciones de la tabla `cliente`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/clientes`

Leer todos los clientes.

GET `/api/v1/clientes/{id}`

Retorna un cliente en base a su ID.

POST `/api/v1/clientes`

Crear un nuevo cliente.

Ejemplo de body:

```json
{
    "nombre": "Juan Perez",
    "telefono": "5555-5555",
    "nit": "10818598-1",
    "correo": "juanperez@email.com",
    "direccion": "Ciudad de Guatemala"
}
```

PUT `/api/v1/clientes/{id}`

Actualiza un cliente.

Ejemplo de body:

```json
{
    "nombre": "Juan Carlos Perez",
    "telefono": "4444-4444",
    "correo": "juancarlos@email.com",
    "direccion": "Antigua Guatemala"
}
```

DELETE `/api/v1/clientes/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Empleado CRUD

CRUD completo implementado para las operaciones de la tabla `empleado`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/empleados`

Leer todos los empleados.

GET `/api/v1/empleados/{id}`

Retorna un empleado en base a su ID.

POST `/api/v1/empleados`

Crear un nuevo empleado. Las opciones de rol son: "Administrador", "Mesero", "Cajero", "Recepcionista", y "Cocinero".

Ejemplo de body:

```json
{
    "nombre": "Carlos Lopez",
    "rol": "Mesero",
    "username": "carlos123",
    "password": "123456"
}
```

PUT `/api/v1/empleados/{id}`

Actualiza un empleado. Las opciones de rol son: "Administrador", "Mesero", "Cajero", "Recepcionista", y "Cocinero".

Ejemplo de body:

```json
{
    "nombre": "Carlos Lopez",
    "rol": "Administrador",
    "username": "carlos_admin"
}
```

Actualizar contraseña:

```json
{
    "password": "nuevaPassword123"
}
```

DELETE `/api/v1/empleados/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Planilla CRUD

CRUD completo implementado para las operaciones de la tabla `planilla`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/planillas`

Leer todas las planillas.

GET `/api/v1/planillas/{id}`

Retorna una planilla en base a su ID.

POST `/api/v1/planillas`

Crear una nueva planilla.

Ejemplo de body:

```json
{
    "id_empleado": 1,
    "periodo": "Mayo 2026",
    "salario_neto": 4500.00
}
```

PUT `/api/v1/planillas/{id}`

Actualiza una planilla.

Ejemplo de body:

```json
{
    "periodo": "Junio 2026",
    "salario_neto": 5000.00
}
```

DELETE `/api/v1/planillas/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Pedido CRUD

CRUD completo implementado para las operaciones de la tabla `pedido`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/pedidos`

Leer todos los pedidos.

GET `/api/v1/pedidos/{id}`

Retorna un pedido en base a su ID.

POST `/api/v1/pedidos`

Crear un nuevo pedido.

Ejemplo de body:

```json
{
    "estado": "Pendiente",
    "tipo": "Para Aca",
    "total": 150.00,
    "id_mesa": 2,
    "id_cliente": 1,
    "id_empleado": 1
}
```

PUT `/api/v1/pedidos/{id}`

Actualiza un pedido.

Ejemplo de body:

```json
{
    "estado": "Preparando",
    "total": 175.00
}
```

DELETE `/api/v1/pedidos/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Factura Electronica CRUD

CRUD completo implementado para las operaciones de la tabla `factura_electronica`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/facturas-electronicas`

Leer todas las facturas electrónicas.

GET `/api/v1/facturas-electronicas/{id}`

Retorna una factura electrónica en base a su ID.

POST `/api/v1/facturas-electronicas`

Crear una nueva factura electrónica. El UUID SAT debe ser único, el monto positivo, y las opciones de estado son: "Emitida" o "Anulada".

Ejemplo de body:

```json
{
    "uuid_sat": "550e8400-e29b-41d4-a716-446655440000",
    "fecha_emision": "2026-05-23 10:30:00",
    "nit_receptor": "1234567-8",
    "monto_total": 250.75,
    "estado": "Emitida",
    "id_pedido": 1
}
```

PUT `/api/v1/facturas-electronicas/{id}`

Actualiza una factura electrónica. El UUID SAT debe ser único, el monto positivo, y las opciones de estado son: "Emitida" o "Anulada".

Ejemplo de body:

```json
{
    "monto_total": 300.50,
    "estado": "Emitida"
}
```

DELETE `/api/v1/facturas-electronicas/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

### Detalle Pedido CRUD

CRUD completo implementado para las operaciones de la tabla `detalle_pedido`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/detalles-pedido`

Leer todos los detalles de pedido.

GET `/api/v1/detalles-pedido/{id}`

Retorna un detalle de pedido en base a su ID.

POST `/api/v1/detalles-pedido`

Crear un nuevo detalle de pedido.

Ejemplo de body:

```json
{
    "id_pedido": 1,
    "id_producto": 2,
    "cantidad": 3,
    "subtotal": 120.00,
    "notas": "Sin cebolla"
}
```

PUT `/api/v1/detalles-pedido/{id}`

Actualiza un detalle de pedido.

Ejemplo de body:

```json
{
    "cantidad": 5,
    "subtotal": 200.00,
    "notas": "Extra queso"
}
```

DELETE `/api/v1/detalles-pedido/{id}`

Realiza un borrado lógico usando Soft Deletes.

---

## Componentes de arquitectura

### Models

* `CategoriaMenu.php`
* `ProductoMenu.php`
* `Mesa.php`
* `Cliente.php`
* `Empleado.php`
* `Planilla.php`
* `Pedido.php`
* `FacturaElectronica.php`
* `DetallePedido.php`

Representan las tablas usando Eloquent ORM.

### Controllers

* `CategoriaMenuController.php`
* `ProductoMenuController.php`
* `MesaController.php`
* `ClienteController.php`
* `EmpleadoController.php`
* `PlanillaController.php`
* `PedidoController.php`
* `FacturaElectronicaController.php`
* `DetallePedidoController.php`

Manejan las peticiones y respuestas HTTP.

### Services

* `CategoriaMenuService.php`
* `ProductoMenuService.php`
* `MesaService.php`
* `ClienteService.php`
* `EmpleadoService.php`
* `PlanillaService.php`
* `PedidoService.php`
* `FacturaElectronicaService.php`
* `DetallePedidoService.php`

Contienen la lógica de negocio.

### Repositories

* `CategoriaMenuRepository.php`
* `ProductoMenuRepository.php`
* `MesaRepository.php`
* `ClienteRepository.php`
* `EmpleadoRepository.php`
* `PlanillaRepository.php`
* `PedidoRepository.php`
* `FacturaElectronicaRepository.php`
* `DetallePedidoRepository.php`

Manejan las operaciones de la base de datos.

### Request Validation

Categoria Menu:

* `StoreCategoriaMenuRequest.php`
* `UpdateCategoriaMenuRequest.php`

Producto Menu:

* `StoreProductoMenuRequest.php`
* `UpdateProductoMenuRequest.php`

Mesa:

* `StoreMesaRequest.php`
* `UpdateMesaRequest.php`

Cliente:

* `StoreClienteRequest.php`
* `UpdateClienteRequest.php`

Empleado:

* `StoreEmpleadoRequest.php`
* `UpdateEmpleadoRequest.php`

Planilla:

* `StorePlanillaRequest.php`
* `UpdatePlanillaRequest.php`

Pedido:

* `StorePedidoRequest.php`
* `UpdatePedidoRequest.php`

Factura Electronica:

* `StoreFacturaElectronicaRequest.php`
* `UpdateFacturaElectronicaRequest.php`

Detalle Pedido:

* `StoreDetallePedidoRequest.php`
* `UpdateDetallePedidoRequest.php`

Responsables de validar información entrante solicitada.

### Resources

* `CategoriaMenuResource.php`
* `ProductoMenuResource.php`
* `MesaResource.php`
* `ClienteResource.php`
* `EmpleadoResource.php`
* `PlanillaResource.php`
* `PedidoResource.php`
* `FacturaElectronicaResource.php`
* `DetallePedidoResource.php`

Crean respuestas API JSON.

---

## Database Notes

### Categoria Menu

Configuración personalizada de tabla:

* Table: `categoria_menu`
* Primary key: `id_categoria`

Soft Deletes habilitado:

* deleted_at

### Producto Menu

Configuración personalizada de tabla:

* Table: `producto_menu`
* Primary key: `id_producto`

Soft Deletes habilitado:

* deleted_at

### Mesa

Configuración personalizada de tabla:

* Table: `mesa`
* Primary key: `id_mesa`

Soft Deletes habilitado:

* deleted_at

### Cliente

Configuración personalizada de tabla:

* Table: `cliente`
* Primary key: `id_cliente`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* nombre
* telefono
* correo
* direccion

### Empleado

Configuración personalizada de tabla:

* Table: `empleado`
* Primary key: `id_empleado`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* username
* password
* remember_token

### Planilla

Configuración personalizada de tabla:

* Table: `planilla`
* Primary key: `id_planilla`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* id_empleado
* periodo
* salario_neto

### Pedido

Configuración personalizada de tabla:

* Table: `pedido`
* Primary key: `id_pedido`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* estado
* tipo
* total
* id_mesa
* id_cliente
* id_empleado

### Factura Electronica

Configuración personalizada de tabla:

* Table: `factura_electronica`
* Primary key: `id_factura`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* uuid_sat
* fecha_emision
* nit_receptor
* monto_total
* estado
* id_pedido

### Detalle Pedido

Configuración personalizada de tabla:

* Table: `detalle_pedido`
* Primary key: `id_detalle`

Soft Deletes habilitado:

* deleted_at

Campos importantes:

* id_pedido
* id_producto
* cantidad
* subtotal
* notas

Timestamps enabled:

* created_at
* updated_at

---

## Versionamiento de API

Las rutas usan versionamiento:

```text
/api/v1/
```

Ejemplo:

```text
/api/v1/categorias-menu
```

```text
/api/v1/productos-menu
```

```text
/api/v1/mesas
```

```text
/api/v1/clientes
```

```text
/api/v1/empleados
```

```text
/api/v1/planillas
```

```text
/api/v1/pedidos
```

```text
/api/v1/facturas-electronicas
```

```text
/api/v1/detalles-pedido
```

---

## Correr el proyecto

Instala las dependencias:

```bash
composer install
```

Configurar el ambiente:

```bash
cp .env.example .env
```

Genera la llave de aplicación:

```bash
php artisan key:generate
```

Corre las migraciones:

```bash
php artisan migrate
```

Inicia el servidor:

```bash
php artisan serve
```

---

## Autores

Arnold Avila - Backend  
Christopher Arellano - Frontend  
Derek Lemus - Frontend