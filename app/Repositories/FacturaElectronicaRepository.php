<?php

namespace App\Repositories;

use App\Models\FacturaElectronica;

// Repositorio para manejar las operaciones de la entidad FacturaElectronica. Este repositorio proporciona métodos para obtener todas las facturas, encontrar una factura por su ID, crear una nueva factura, actualizar una factura existente y eliminar una factura. Al utilizar este repositorio, se abstraen las operaciones de la base de datos relacionadas con las facturas electrónicas, lo que facilita el mantenimiento y la reutilización del código en los controladores y servicios que interactúan con esta entidad.
class FacturaElectronicaRepository
{
    // Método para obtener todas las facturas electrónicas de la base de datos. Devuelve una colección de todas las facturas electrónicas disponibles.
    public function getAll()
    {
        return FacturaElectronica::all();
    }

    // Método para encontrar una factura electrónica por su ID. Recibe un ID como parámetro y devuelve la factura electrónica correspondiente si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return FacturaElectronica::find($id);
    }

    // Método para crear una nueva factura electrónica en la base de datos. Recibe un array de datos como parámetro, utiliza el método create del modelo FacturaElectronica para crear la factura en la base de datos y devuelve la factura creada.
    public function create(array $data)
    {
        return FacturaElectronica::create($data);
    }

    // Método para actualizar una factura electrónica existente. Recibe la factura a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo FacturaElectronica para actualizar la factura en la base de datos y devuelve la factura actualizada.
    public function update(FacturaElectronica $factura, array $data)
    {
        $factura->update($data);

        return $factura;
    }

    // Método para eliminar una factura electrónica. Recibe la factura a eliminar y utiliza el método delete del modelo FacturaElectronica para eliminar la factura de la base de datos. Devuelve un booleano indicando si la operación fue exitosa.
    public function delete(FacturaElectronica $factura)
    {
        return $factura->delete();
    }
}