<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $validator = $this->profileService->validateProfileUpdate($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->profileService->updateProfile($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui profil',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = $this->profileService->validatePasswordUpdate($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->profileService->updatePassword($request->all());

            if (!$result['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors']
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengubah password',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
}
