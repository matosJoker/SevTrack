<?php

namespace App\Http\Controllers;

use App\Services\BengkelService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $roleService;
    protected $bengkelService;

    public function __construct(UserService $userService, RoleService $roleService, BengkelService $bengkelService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->bengkelService = $bengkelService;
    }

    public function index()
    {
        $user = $this->userService->all();
        $role = $this->roleService->getRoleNotSuper();
        $bengkel = $this->bengkelService->all();
        return view('user.index', compact('user', 'role', 'bengkel'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'id_role' => 'required|exists:roles,id',
                'id_bengkel' => 'nullable|exists:bengkel,id',
            ]);

            $this->userService->create($validated);

            return response()->json(['success' => 'Bengkel berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan bengkel. ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'id_role' => 'required|exists:roles,id',
                'id_bengkel' => 'nullable|exists:bengkel,id',
            ]);

            $this->userService->update($id, $validated);

            return response()->json(['success' => 'Bengkel berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan bengkel. ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $this->userService->delete($id);
        return response()->json(['success' => 'Bengkel berhasil dihapus.']);
    }
    public function resetPassword($id)
    {
        try {
            $user = $this->userService->find($id);
            $user->password = bcrypt('password');
            $user->save();

            return response()->json(['success' => 'Password berhasil direset.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mereset password. ' . $e->getMessage()], 500);
        }
    }
}
