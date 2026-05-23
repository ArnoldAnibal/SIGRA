<?php

namespace App\Services;

use App\Repositories\PlanillaRepository;

// Servicio para manejar la lógica de negocio relacionada con las planillas. Este servicio utiliza el PlanillaRepository para interactuar con la base de datos y proporciona métodos para obtener todas las planillas, obtener una planilla por su ID, crear una nueva planilla, actualizar una planilla existente y eliminar una planilla. El uso de un servicio ayuda a mantener la lógica de negocio separada de la lógica de acceso a datos, lo que facilita el mantenimiento y la escalabilidad del código.
class PlanillaService
{
    protected $repository;

    // Constructor que recibe una instancia de PlanillaRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con las planillas.
    public function __construct(PlanillaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todas las planillas. Utiliza el método getAll del repositorio para obtener los datos de las planillas y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener una planilla por su ID. Utiliza el método findById del repositorio para obtener la planilla correspondiente al ID proporcionado y luego la devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear una nueva planilla. Recibe un array de datos, utiliza el método create del repositorio para crear la planilla en la base de datos y devuelve la planilla creada.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar una planilla existente. Recibe la planilla a actualizar y un array de datos con los nuevos valores. Devuelve la planilla actualizada.
    public function update($planilla, array $data)
    {
        return $this->repository->update($planilla, $data);
    }

    // Método para eliminar una planilla. Recibe la planilla a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($planilla)
    {
        return $this->repository->delete($planilla);
    }
}