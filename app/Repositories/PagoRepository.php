<?php

namespace App\Repositories;

use App\Models\Pago;

class PagoRepository
{
    public function getAll()
    {
        return Pago::all();
    }

    public function findById(int $id)
    {
        return Pago::find($id);
    }

    public function create(array $data)
    {
        return Pago::create($data);
    }

    public function update(Pago $pago, array $data)
    {
        $pago->update($data);

        return $pago;
    }

    public function delete(Pago $pago)
    {
        return $pago->delete();
    }
}