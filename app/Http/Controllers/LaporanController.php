<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use App\Services\LaporanService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }
    public function transaksi(Request $request)
    {
        $bengkels = Bengkel::all();
        $transactions = $this->laporanService->getFilteredTransactions($request);

        return view('laporan.transaksi', compact('transactions','bengkels'));
    }
    public function pelanggan(Request $request)
    {
        // $bengkels = Bengkel::all();
        $pelanggan = $this->laporanService->getFilteredDataPelanggan($request);
        // dd($pelanggan);

        return view('laporan.pelanggan', compact('pelanggan'));
    }
}
