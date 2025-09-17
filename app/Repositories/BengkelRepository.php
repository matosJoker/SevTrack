<?php

namespace App\Repositories;

use App\Models\Bengkel;

class BengkelRepository
{
    public function all()
    {
        return Bengkel::latest()->get();
    }

    public function find($id)
    {
        return Bengkel::findOrFail($id);
    }

    public function create(array $data)
    {
        return Bengkel::create($data);
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
