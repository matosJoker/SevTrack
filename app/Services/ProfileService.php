<?php

namespace App\Services;

use App\Repositories\ProfileRepository;
use Illuminate\Support\Facades\Validator;

class ProfileService
{
    protected $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getCurrentUser()
    {
        return $this->profileRepository->getCurrentUser();
    }

    public function validateProfileUpdate(array $data)
    {
        $user = $this->getCurrentUser();

        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_telp.max' => 'Nomor telepon tidak boleh lebih dari 15 karakter',
            'alamat.max' => 'Alamat tidak boleh lebih dari 500 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran gambar tidak boleh lebih dari 2MB'
        ]);
    }

    public function validatePasswordUpdate(array $data)
    {
        return Validator::make($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password'
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.different' => 'Password baru harus berbeda dengan password saat ini'
        ]);
    }
    public function updateProfile(array $data)
    {
        try {
            return $this->profileRepository->updateProfile($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updatePassword(array $data)
    {
        try {
            // Cek password saat ini
            if (!$this->profileRepository->checkCurrentPassword($data['current_password'])) {
                return [
                    'status' => false,
                    'message' => 'Password saat ini tidak valid',
                    'errors' => ['password' => ['Password saat ini tidak valid']]
                ];
            }

            $this->profileRepository->updatePassword($data);
            return ['status' => true];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
