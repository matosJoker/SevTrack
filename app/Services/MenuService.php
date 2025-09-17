<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use App\Repositories\RoleRepository;

class MenuService
{
    protected $menuRepository;
    protected $roleRepository;

    public function __construct(MenuRepository $menuRepository, RoleRepository $roleRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getAllMenus()
    {
        return $this->menuRepository->getAllWithChildren();
    }

    public function getMenuById($id)
    {
        return $this->menuRepository->find($id);
    }

    public function createMenu(array $data)
    {
        return $this->menuRepository->create($data);
    }

    public function updateMenu($id, array $data)
    {
        return $this->menuRepository->update($id, $data);
    }

    public function deleteMenu($id)
    {
        return $this->menuRepository->delete($id);
    }

    public function assignMenuToRole($roleId, $menuId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->menus()->syncWithoutDetaching([$menuId]);
    }

    public function removeMenuFromRole($roleId, $menuId)
    {
        $role = $this->roleRepository->find($roleId);
        $role->menus()->detach($menuId);
    }

    public function getActiveMenusForRole($roleId)
    {
        return $this->menuRepository->getActiveMenusByRole($roleId);
    }
    public function getRolesNotAssignedToMenu($menuId)
    {
        $menu = $this->menuRepository->find($menuId);
        $assignedRoleIds = $menu->roles->pluck('id')->toArray();

        return $this->roleRepository->getAllExceptIds($assignedRoleIds);
    }
    // app/Services/MenuService.php

    public function getAllActiveMenus()
    {
        return $this->menuRepository->getModel()
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }

    public function getActiveMenusForRoles(array $roleIds)
    {
        return $this->menuRepository->getModel()
            ->whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('id', $roleIds);
            })
            ->where('is_active', true)
            ->with(['children' => function ($query) use ($roleIds) {
                $query->whereHas('roles', function ($q) use ($roleIds) {
                    $q->whereIn('id', $roleIds);
                })
                    ->where('is_active', true)
                    ->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }
}
