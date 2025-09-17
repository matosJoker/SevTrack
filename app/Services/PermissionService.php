<?php

namespace App\Services;

use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;

class PermissionService
{
    protected $permissionRepository;
    protected $roleRepository;

    public function __construct(PermissionRepository $permissionRepository, RoleRepository $roleRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getAllPermissions()
    {
        return $this->permissionRepository->all();
    }

    public function getPermissionById($id)
    {
        return $this->permissionRepository->find($id);
    }

    public function createPermission(array $data)
    {
        return $this->permissionRepository->create($data);
    }

    public function updatePermission($id, array $data)
    {
        return $this->permissionRepository->update($id, $data);
    }

    public function deletePermission($id)
    {
        return $this->permissionRepository->delete($id);
    }

    public function assignPermissionToRole($roleId, $permissionId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->permissions()->syncWithoutDetaching([$permissionId]);
    }

    public function removePermissionFromRole($roleId, $permissionId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->permissions()->detach($permissionId);
    }

    public function getRolesNotAssignedToPermission($permissionId)
    {
        $permission = $this->permissionRepository->find($permissionId);
        $assignedRoleIds = $permission->roles->pluck('id')->toArray();

        return $this->roleRepository->getAllExceptIds($assignedRoleIds);
    }
}
