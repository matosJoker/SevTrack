<?php

namespace App\Http\Controllers;

use App\Services\LayananService;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    protected $layananService;

    public function __construct(LayananService $layananService)
    {
        $this->layananService = $layananService;
    }

    public function index()
    {
        $layanan = $this->layananService->all();
        return view('layanan.index', compact('layanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'id_bengkel' => 'nullable|exists:bengkel,id',
        ]);

        $this->layananService->create($validated);

        return response()->json(['success' => 'Layanan berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'id_bengkel' => 'nullable|exists:bengkel,id',
        ]);

        $this->layananService->update($id, $validated);

        return response()->json(['success' => 'Layanan berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $this->layananService->delete($id);
        return response()->json(['success' => 'Layanan berhasil dihapus.']);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;
        $result = $this->layananService->changeStatus($id, $status);
        return response()->json(['success' => 'Status layanan berhasil dirubah.']);
    }
}
