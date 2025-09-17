<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        $this->permissionService->createPermission($request->validated());
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function show($id)
    {
        $permission = $this->permissionService->getPermissionById($id);
        return view('admin.permissions.show', compact('permission'));
    }

    public function edit($id)
    {
        $permission = $this->permissionService->getPermissionById($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(PermissionRequest $request, $id)
    {
        $this->permissionService->updatePermission($id, $request->validated());
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $this->permissionService->deletePermission($id);
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function assignToRoleForm($permissionId)
    {
        $permission = $this->permissionService->getPermissionById($permissionId);
        $roles = $this->permissionService->getRolesNotAssignedToPermission($permissionId);
        return view('admin.permissions.assign-role', compact('permission', 'roles'));
    }

    public function assignToRole(Request $request, $permissionId)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $this->permissionService->assignPermissionToRole($request->role_id, $permissionId);
        return redirect()->route('permissions.show', $permissionId)->with('success', 'Permission assigned to role successfully.');
    }

    public function removeFromRole($permissionId, $roleId)
    {
        $this->permissionService->removePermissionFromRole($roleId, $permissionId);
        return redirect()->route('permissions.show', $permissionId)->with('success', 'Permission removed from role successfully.');
    }
}
