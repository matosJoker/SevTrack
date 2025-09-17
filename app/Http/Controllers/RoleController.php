<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(RoleRequest $request)
    {
        $this->roleService->createRole($request->validated());
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show($id)
    {
        $role = $this->roleService->getRoleById($id);
        return view('admin.roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = $this->roleService->getRoleById($id);
        return view('admin.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, $id)
    {
        $this->roleService->updateRole($id, $request->validated());
        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $this->roleService->deleteRole($id);
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function assignUserForm($roleId)
    {
        $role = $this->roleService->getRoleById($roleId);
        $users = $this->roleService->getUsersNotAssignedToRole($roleId);
        return view('admin.roles.assign-user', compact('role', 'users'));
    }

    public function assignUser(Request $request, $roleId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $this->roleService->assignUserToRole($roleId, $request->user_id);
        return redirect()->route('roles.show', $roleId)->with('success', 'User assigned to role successfully.');
    }

    public function removeUser($roleId, $userId)
    {
        $this->roleService->removeUserFromRole($roleId, $userId);
        return redirect()->route('roles.show', $roleId)->with('success', 'User removed from role successfully.');
    }
}
