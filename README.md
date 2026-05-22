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

Borra una categoría permanentemente.

### Producto Menu CRUD

CRUD completo implementado para las operaciones de la tabla `producto_menu`.

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
    "nombre": "Pizza Pepperoni",
    "descripcion": "Pizza grande con pepperoni",
    "precio": 85.50,
    "id_categoria": 1,
    "disponible": 1
}
```

PUT `/api/v1/productos-menu/{id}`

Actualiza un producto.

Ejemplo de body:

```json
{
    "nombre": "Pizza Suprema",
    "descripcion": "Pizza familiar especial",
    "precio": 95.00,
    "id_categoria": 1,
    "disponible": 1
}
```

DELETE `/api/v1/productos-menu/{id}`

Borra un producto permanentemente.

### Mesa CRUD

CRUD completo implementado para las operaciones de la tabla `mesa`.

El modulo usa Soft Deletes mediante el campo `deleted_at`.

### Endpoints

GET `/api/v1/mesas`

Leer todas las mesas.

GET `/api/v1/mesas/{id}`

Retorna una mesa en base a su ID.

POST `/api/v1/mesas`

Crear una nueva mesa.

Ejemplo de body:

```json
{
    "numero_mesa": 10,
    "capacidad": 4,
    "estado": "Libre"
}
```

PUT `/api/v1/mesas/{id}`

Actualiza una mesa.

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

## Componentes de arquitectura

### Models

* `CategoriaMenu.php`
* `ProductoMenu.php`
* `Mesa.php`

Representan las tablas usando Eloquent ORM.

### Controllers

* `CategoriaMenuController.php`
* `ProductoMenuController.php`
* `MesaController.php`

Manejan las peticiones y respuestas HTTP.

### Services

* `CategoriaMenuService.php`
* `ProductoMenuService.php`
* `MesaService.php`

Contienen la lógica de negocio.

### Repositories

* `CategoriaMenuRepository.php`
* `ProductoMenuRepository.php`
* `MesaRepository.php`

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

Responsables de validar información entrante solicitada.

### Resources

* `CategoriaMenuResource.php`
* `ProductoMenuResource.php`
* `MesaResource.php`

Crean respuestas API JSON.

## Database Notes

### Categoria Menu

Configuración personalizada de tabla:

* Table: `categoria_menu`
* Primary key: `id_categoria`

### Producto Menu

Configuración personalizada de tabla:

* Table: `producto_menu`
* Primary key: `id_producto`

### Mesa

Configuración personalizada de tabla:

* Table: `mesa`
* Primary key: `id_mesa`

Soft Deletes habilitado:

* deleted_at

Timestamps enabled:

* created_at
* updated_at

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

## Autores

Arnold Avila
Christopher Arellano
Derek Lemus