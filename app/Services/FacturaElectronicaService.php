<?php

namespace App\Services;

use App\Repositories\FacturaElectronicaRepository;

// Servicio para manejar la lógica de negocio relacionada con las facturas electrónicas. Este servicio utiliza el repositorio FacturaElectronicaRepository para interactuar con la base de datos y proporciona métodos para obtener todas las facturas, obtener una factura por su ID, crear una nueva factura, actualizar una factura existente y eliminar una factura. Al utilizar este servicio, se abstrae la lógica de negocio relacionada con las facturas electrónicas, lo que facilita el mantenimiento y la reutilización del código en los controladores que interactúan con esta entidad.
class FacturaElectronicaService
{
    protected $repository;

    // Constructor que recibe una instancia de FacturaElectronicaRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con las facturas electrónicas.
    public function __construct(FacturaElectronicaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todas las facturas electrónicas. Utiliza el método getAll del repositorio para obtener los datos de las facturas electrónicas y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener una factura electrónica por su ID. Utiliza el método findById del repositorio para obtener la factura electrónica correspondiente al ID proporcionado y luego la devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear una nueva factura electrónica. Recibe un array de datos, utiliza el método create del repositorio para crear la factura en la base de datos y devuelve la factura creada.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar una factura electrónica existente. Recibe la factura a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar la factura en la base de datos y devuelve la factura actualizada.
    public function update($factura, array $data)
    {
        return $this->repository->update($factura, $data);
    }

    // Método para eliminar una factura electrónica. Recibe la factura a eliminar, utiliza el método delete del repositorio para eliminar la factura de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($factura)
    {
        return $this->repository->delete($factura);
    }
}