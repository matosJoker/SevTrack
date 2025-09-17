<?php

namespace App\Repositories;

use App\Models\ServiceAdvisor;

class ServiceAdvisorRepository
{
    public function all()
    {
        return ServiceAdvisor::latest()->get();
    }

    public function find($id)
    {
        return ServiceAdvisor::findOrFail($id);
    }

    public function create(array $data)
    {
        return ServiceAdvisor::create($data);
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
