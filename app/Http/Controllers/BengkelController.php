<?php

namespace App\Http\Controllers;

use App\Services\BengkelService;
use Illuminate\Http\Request;

class BengkelController extends Controller
{
    protected $bengkelService;

    public function __construct(BengkelService $bengkelService)
    {
        $this->bengkelService = $bengkelService;
    }

    public function index()
    {
        $bengkels = $this->bengkelService->all();
        return view('bengkels.index', compact('bengkels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $this->bengkelService->create($validated);

        return response()->json(['success' => 'Bengkel berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $this->bengkelService->update($id, $validated);

        return response()->json(['success' => 'Bengkel berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $this->bengkelService->delete($id);
        return response()->json(['success' => 'Bengkel berhasil dihapus.']);
    }
}
