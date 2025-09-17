<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class RoleService
{
    protected $roleRepository;
    protected $userRepository;

    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllRoles()
    {
        return $this->roleRepository->all();
    }
    public function getRoleNotSuper()
    {
        return $this->roleRepository->roleNotSuper();
    }

    public function getRoleById($id)
    {
        return $this->roleRepository->find($id);
    }

    public function createRole(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function updateRole($id, array $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    public function deleteRole($id)
    {
        return $this->roleRepository->delete($id);
    }

    public function assignUserToRole($roleId, $userId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->users()->syncWithoutDetaching([$userId]);
    }

    public function removeUserFromRole($roleId, $userId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->users()->detach($userId);
    }

    public function getUsersNotAssignedToRole($roleId)
    {
        $role = $this->roleRepository->find($roleId);
        $assignedUserIds = $role->users->pluck('id')->toArray();

        return $this->userRepository->getAllExceptIds($assignedUserIds);
    }
}
