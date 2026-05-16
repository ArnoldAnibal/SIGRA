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

## Modulo Implementado

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

## Componentes de arquitectura

### Model

`CategoriaMenu.php`

Representa la tabla `categoria_menu` usando Eloquent ORM.

### Controller

`CategoriaMenuController.php`

Maneja las peticiones y respuestas HTTP.

### Service

`CategoriaMenuService.php`

Contiene la lógica de negocio.

### Repository

`CategoriaMenuRepository.php`

Maneja las operaciones de la base de datos.

### Request Validation

* `StoreCategoriaMenuRequest.php`
* `UpdateCategoriaMenuRequest.php`

Responsable de validar información entrante solicitada.

### Resource

`CategoriaMenuResource.php`

Crea respuestas API JSON.

## Database Notes

Configuración personalizada de tabla:

* Table: `categoria_menu`
* Primary key: `id_categoria`

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