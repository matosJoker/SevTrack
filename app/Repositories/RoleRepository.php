<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }

    public function getAllExceptIds(array $ids)
    {
        return $this->model->whereNotIn('id', $ids)->get();
    }

    public function roleNotSuper()
    {
        return $this->model->where('name', '!=', 'super-admin')->get();
    }
}
