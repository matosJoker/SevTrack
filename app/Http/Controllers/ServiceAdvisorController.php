<?php

namespace App\Http\Controllers;

use App\Services\BengkelService;
use App\Services\ServiceAdvisorService;
use Illuminate\Http\Request;

class ServiceAdvisorController extends Controller
{
    protected $serviceAdvisorService;
    protected $bengkelService;

    public function __construct(ServiceAdvisorService $serviceAdvisorService, BengkelService $bengkelService)
    {
        $this->serviceAdvisorService = $serviceAdvisorService;
        $this->bengkelService = $bengkelService;
    }

    public function index()
    {
        $serviceadvisor = $this->serviceAdvisorService->all();
        $bengkel = $this->bengkelService->all();
        return view('serviceadvisor.index', compact('serviceadvisor', 'bengkel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_service_advisor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_bengkel' => 'required|exists:bengkel,id',
        ]);

        $this->serviceAdvisorService->create($validated);

        return response()->json(['success' => 'Service Advisor berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_service_advisor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_bengkel' => 'required|exists:bengkel,id',
        ]);

        $this->serviceAdvisorService->update($id, $validated);

        return response()->json(['success' => 'Service Advisor berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $this->serviceAdvisorService->delete($id);
        return response()->json(['success' => 'Service Advisor berhasil dihapus.']);
    }
}
