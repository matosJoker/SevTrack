<?php

namespace App\Services;

use App\Repositories\BengkelRepository;

class BengkelService
{
    protected $bengkelRepository;

    public function __construct(BengkelRepository $bengkelRepository)
    {
        $this->bengkelRepository = $bengkelRepository;
    }

    public function all()
    {
        return $this->bengkelRepository->all();
    }

    public function find($id)
    {
        return $this->bengkelRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->bengkelRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->bengkelRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->bengkelRepository->delete($id);
    }
}
