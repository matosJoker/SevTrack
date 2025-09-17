<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = $this->menuService->getAllMenus();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parentMenus = $this->menuService->getAllMenus();
        return view('admin.menus.create', compact('parentMenus'));
    }

    public function store(MenuRequest $request)
    {
        $this->menuService->createMenu($request->validated());
        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    public function show($id)
    {
        $menu = $this->menuService->getMenuById($id);
        return view('admin.menus.show', compact('menu'));
    }

    public function edit($id)
    {
        $menu = $this->menuService->getMenuById($id);
        $parentMenus = $this->menuService->getAllMenus();
        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    public function update(MenuRequest $request, $id)
    {
        $this->menuService->updateMenu($id, $request->validated());
        return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy($id)
    {
        $this->menuService->deleteMenu($id);
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }

    public function assignToRoleForm($menuId)
    {
        $menu = $this->menuService->getMenuById($menuId);
        $roles = $this->menuService->getRolesNotAssignedToMenu($menuId);
        return view('admin.menus.assign-role', compact('menu', 'roles'));
    }

    public function assignToRole(Request $request, $menuId)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $this->menuService->assignMenuToRole($request->role_id, $menuId);
        return redirect()->route('menus.show', $menuId)->with('success', 'Menu assigned to role successfully.');
    }

    public function removeFromRole($menuId, $roleId)
    {
        $this->menuService->removeMenuFromRole($roleId, $menuId);
        return redirect()->route('menus.show', $menuId)->with('success', 'Menu removed from role successfully.');
    }
}
