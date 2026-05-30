<?php

namespace App\Services;

use App\Repositories\CuentaPorPagarRepository;

// Servicio para gestionar las cuentas por pagar a proveedores, utilizando el repositorio CuentaPorPagarRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todas las cuentas por pagar, encontrar una cuenta por su ID, crear una nueva cuenta, actualizar una cuenta existente y eliminar una cuenta.
class CuentaPorPagarService
{
    protected $repository;

    // Constructor que recibe una instancia de CuentaPorPagarRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con las cuentas por pagar.
    public function __construct(
        CuentaPorPagarRepository $repository
    ) {
        $this->repository = $repository;
    }

    // Método para obtener todas las cuentas por pagar. Utiliza el método getAll del repositorio para obtener los datos de las cuentas y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener una cuenta por pagar por su ID. Utiliza el método findById del repositorio para obtener la cuenta correspondiente al ID proporcionado y luego la devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear una nueva cuenta por pagar. Recibe un array de datos y utiliza el método create del repositorio para crear la cuenta en la base de datos y devuelve la cuenta creada.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar una cuenta por pagar existente. Recibe la cuenta a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar la cuenta en la base de datos y devuelve la cuenta actualizada.
    public function update(
        $cuenta,
        array $data
    ) {
        return $this->repository->update(
            $cuenta,
            $data
        );
    }

    // Método para eliminar una cuenta por pagar. Recibe la cuenta a eliminar, utiliza el método delete del repositorio para eliminar la cuenta de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($cuenta)
    {
        return $this->repository->delete(
            $cuenta
        );
    }
}