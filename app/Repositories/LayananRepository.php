<?php

namespace App\Repositories;

use App\Models\Layanan;

class LayananRepository
{
    public function all()
    {
        return Layanan::latest()->get();
    }

    public function find($id)
    {
        return Layanan::findOrFail($id);
    }

    public function create(array $data)
    {
        return Layanan::create($data);
    }

    public function update($id, array $data)
    {
        $bengkel = $this->find($id);
        $bengkel->update($data);
        return $bengkel;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
