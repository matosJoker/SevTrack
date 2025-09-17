<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getAllExceptIds(array $ids)
    {
        return $this->model->whereNotIn('id', $ids)->get();
    }
    public function all()
    {
        return User::latest()
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->leftJoin('bengkel', 'users.bengkel_id', '=', 'bengkel.id')
            ->where('roles.name', '!=', 'super-admin')
            ->select('users.*', 'roles.name as role_name', 'bengkel.nama_bengkel as bengkel','roles.id as role_id')
            ->get();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
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
