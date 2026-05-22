<?php

namespace App\Services;

// Servicio para manejar la lógica de negocio relacionada con la entidad Mesa. Este servicio utiliza el repositorio MesaRepository para interactuar con la base de datos y realizar operaciones CRUD. El servicio se encarga de procesar los datos y aplicar cualquier lógica de negocio necesaria antes de devolver los resultados a los controladores o recursos que lo consumen.

use App\Repositories\MesaRepository;
use App\Http\Resources\MesaResource;

class MesaService
{
    // Protected porque solo se accede a través de este servicio, no directamente desde el controlador. El servicio se encarga de manejar la lógica de negocio y delega las operaciones de acceso a datos al repositorio.
    protected $repository;

    public function __construct(MesaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todas las mesas disponibles en la base de datos utilizando el método getAll() del repositorio. Devuelve una colección de recursos MesaResource formateados a partir de los datos obtenidos del repositorio, lo que permite devolver una respuesta JSON estructurada y consistente para las mesas.
    public function getAll()
    {
        return MesaResource::collection(
            $this->repository->getAll()
        );
    }

    // Obtener mesa por ID
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crear mesa
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Actualizar mesa
    public function update($mesa, array $data)
    {
        return $this->repository->update($mesa, $data);
    }

    // Eliminar mesa
    public function delete($mesa)
    {
        return $this->repository->delete($mesa);
    }
}