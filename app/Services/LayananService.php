<?php

namespace App\Services;

use App\Repositories\LayananRepository;

class LayananService
{
    protected $layananRepository;

    public function __construct(LayananRepository $layananRepository)
    {
        $this->layananRepository = $layananRepository;
    }

    public function all()
    {
        return $this->layananRepository->all();
    }

    public function find($id)
    {
        return $this->layananRepository->find($id);
    }

    public function create(array $data)
    {
        $data['harga'] = isset($data['harga']) ? str_replace('.', '', $data['harga']) : 0;
        return $this->layananRepository->create($data);
    }

    public function update($id, array $data)
    {

        $data['harga'] = isset($data['harga']) ? str_replace('.', '', $data['harga']) : 0;
        return $this->layananRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->layananRepository->delete($id);
    }

    public function changeStatus($id, $status)
    {
        $layanan = $this->layananRepository->find($id);
        $layanan->status = $status;
        $layanan->save();
        return $layanan;
    }
}
