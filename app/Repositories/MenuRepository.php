<?php

namespace App\Repositories;

use App\Models\Menu;

class MenuRepository
{
    protected $model;

    public function __construct(Menu $menu)
    {
        $this->model = $menu;
    }
    public function getModel()
    {
        return $this->model;
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

    public function getAllWithChildren()
    {
        return $this->model->with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }
    public function getAll()
    {
        return $this->model
            ->orderBy('order')
            ->get();
    }

    public function getActiveMenusByRole($roleId)
    {
        $menu = $this->model->whereHas('roles', function ($query) use ($roleId) {
            $query->where('id', $roleId);
        })
            ->where('is_active', true)
            ->with(['children' => function ($query) use ($roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('id', $roleId);
                })
                    ->where('is_active', true)
                    ->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        return $menu;
    }
}
