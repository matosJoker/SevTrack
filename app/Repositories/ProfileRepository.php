<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function updateProfile(array $data)
    {
        $user = $this->getCurrentUser();
        
        // Update data user
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->no_telp = $data['no_telp'];
        $user->alamat = $data['alamat'];

        // Handle upload foto
        if (isset($data['foto'])) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists('profil-foto/' . basename($user->foto))) {
                Storage::disk('public')->delete('profil-foto/' . basename($user->foto));
            }

            // Simpan foto baru
            $fileName = time() . '.' . $data['foto']->extension();
            $path = $data['foto']->storeAs('profil-foto', $fileName, 'public');
            $user->foto = $path;
        }

        $user->save();
        return $user;
    }

    public function updatePassword(array $data)
    {
        $user = $this->getCurrentUser();
        $user->password = Hash::make($data['password']);
        $user->save();
        return $user;
    }

    public function checkCurrentPassword($password)
    {
        $user = $this->getCurrentUser();
        return Hash::check($password, $user->password);
    }
}