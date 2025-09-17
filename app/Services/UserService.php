<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class UserService
{
    protected $userRespository;
    protected $roleRepository;

    public function __construct(UserRepository $userRespository, RoleRepository $roleRepository)
    {
        $this->userRespository = $userRespository;
        $this->roleRepository = $roleRepository;
    }

    public function all()
    {
        return $this->userRespository->all();
    }

    public function find($id)
    {
        return $this->userRespository->find($id);
    }
    public function create(array $data)
    {
        try {
            $data['password'] = bcrypt('password');
            $data['bengkel_id'] = $data['id_bengkel'] ?? null;
            $user = $this->userRespository->create($data);
            if (isset($data['id_role'])) {
                $role = $this->roleRepository->find($data['id_role']);
                $role->users()->syncWithoutDetaching([$user->id]);
            }
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, array $data)
    {
        try {
            $data['bengkel_id'] = $data['id_bengkel'] ?? null;
            $user = $this->userRespository->update($id, $data);
            if (isset($data['id_role'])) {
                $role = $this->roleRepository->find($data['id_role']);
                $role->users()->sync([$user->id]);
            }

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function delete($id)
    {
        return $this->userRespository->delete($id);
    }
}
