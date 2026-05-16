<?php

namespace App\Services;

use App\Repositories\CategoriaMenuRepository;

// Servicio para manejar la lógica de negocio relacionada con la entidad CategoriaMenu, interactua con el repositorio CategoriaMenuRepository para realizar operaciones CRUD y aplicar cualquier lógica adicional necesaria antes de devolver los resultados al controlador. Este servicio es utilizado por el controlador CategoriaMenuController para abstraer la lógica de negocio de la lógica de acceso a datos.

class CategoriaMenuService
{
    // Protected porque solo se accede a través de este servicio, no directamente desde el controlador. El servicio se encarga de manejar la lógica de negocio y delega las operaciones de acceso a datos al repositorio.
    protected $repository;

    // El constructor recibe una instancia del repositorio CategoriaMenuRepository a través de inyección de dependencias, lo que permite al servicio utilizar el repositorio para realizar operaciones CRUD en la base de datos.
    public function __construct(CategoriaMenuRepository $repository)
    {
        $this->repository = $repository;
    }

    // Devuelve todas las categorías de menú disponibles en la base de datos utilizando el método getAll() del repositorio.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Busca una categoría de menú por su ID utilizando el método findById() del repositorio.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crea una nueva categoría de menú utilizando el método create() del repositorio. Recibe un arreglo de datos validados desde el controlador y devuelve la categoría creada.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Actualiza una categoría de menú existente con los nuevos datos proporcionados. Devuelve la categoría actualizada. Recibe la instancia de la categoría a actualizar y el arreglo de datos validados desde el controlador.
    public function update($categoria, array $data)
    {
        return $this->repository->update($categoria, $data);
    }

    // Elimina una categoría de menú existente de la base de datos. Devuelve un booleano indicando si la eliminación fue exitosa. Recibe la instancia de la categoría a eliminar desde el controlador.
    public function delete($categoria)
    {
        return $this->repository->delete($categoria);
    }
}