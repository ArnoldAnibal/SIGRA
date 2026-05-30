<?php

namespace App\Repositories;

use App\Models\CuentaPorPagar;

// Repositorio para manejar las operaciones relacionadas con las cuentas por pagar a proveedores. Este repositorio proporciona métodos para obtener todas las cuentas por pagar, encontrar una cuenta por su ID, crear una nueva cuenta, actualizar una cuenta existente y eliminar una cuenta.
class CuentaPorPagarRepository
{
    // Método para obtener todas las cuentas por pagar. Utiliza el método all del modelo CuentaPorPagar para obtener todas las cuentas de la base de datos y luego las devuelve.
    public function getAll()
    {
        return CuentaPorPagar::all();
    }

    // Método para encontrar una cuenta por pagar por su ID. Recibe el ID de la cuenta y utiliza el método find del modelo CuentaPorPagar para obtener la cuenta correspondiente de la base de datos y luego la devuelve.
    public function findById(int $id)
    {
        return CuentaPorPagar::find($id);
    }

    // Método para crear una nueva cuenta por pagar. Recibe un array de datos y utiliza el método create del modelo CuentaPorPagar para crear la cuenta en la base de datos y devuelve la cuenta creada.
    public function create(array $data)
    {
        return CuentaPorPagar::create($data);
    }

    // Método para actualizar una cuenta por pagar existente. Recibe la cuenta a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo CuentaPorPagar para actualizar la cuenta en la base de datos y devuelve la cuenta actualizada.
    public function update(
        CuentaPorPagar $cuenta,
        array $data
    ) {
        $cuenta->update($data);

        return $cuenta;
    }

    // Método para eliminar una cuenta por pagar. Recibe la cuenta a eliminar, utiliza el método delete del modelo CuentaPorPagar para eliminar la cuenta de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(CuentaPorPagar $cuenta)
    {
        return $cuenta->delete();
    }
}