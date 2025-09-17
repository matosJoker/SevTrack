<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Layanan;
use App\Models\Bengkel;
use Carbon\Carbon;

class DashboardService
{
    public function getMonthlyEarnings()
    {
        $currentMonth = now()->format('Y-m');

        return Transaction::where('status', 'selesai')
            ->where('created_at', 'like', $currentMonth . '%')
            ->sum('total');
    }

    public function getTotalTransactions()
    {
        return Transaction::where('status', 'selesai')->count();
    }

    public function getTotalCustomers()
    {
        return Customer::count();
    }

    public function getCancelledTransactions()
    {
        return Transaction::where('status', 'batal')->count();
    }

    public function getRevenueData($months = 6)
    {
        $revenueData = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->translatedFormat('M Y');

            $revenue = Transaction::select('transaksi.id')
                ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
                ->where('transaksi.status', 'selesai')
                ->where('transaksi.created_at', 'like', $monthYear . '%')
                ->sum('detail_transaksi.harga');

            $revenueData[] = $revenue;
            $labels[] = $monthName;
        }

        return [
            'labels' => $labels,
            'data' => $revenueData
        ];
    }

    public function getRevenueByService()
    {
        return Layanan::select('layanan.id', 'layanan.nama_layanan')
            ->leftJoin('detail_transaksi', 'layanan.id', '=', 'detail_transaksi.id_layanan')
            ->leftJoin('transaksi', function ($join) {
                $join->on('detail_transaksi.id_transaksi', '=', 'transaksi.id')
                    ->where('transaksi.status', 'selesai');
            })
            ->groupBy('layanan.id', 'layanan.nama_layanan')
            ->selectRaw('COALESCE(SUM(detail_transaksi.harga), 0) as total')
            ->get()
            ->map(function ($service) {
                return [
                    'label' => $service->nama_layanan,
                    'value' => $service->total
                ];
            });
    }

    public function getRevenueByBengkel($months = 6)
    {
        // Ambil semua bengkel
        $bengkels = Bengkel::select('id', 'nama_bengkel')->get();

        $result = [];
        $monthsData = [];

        // Generate data untuk 6 bulan terakhir
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->translatedFormat('M Y');

            $monthsData[] = $monthName;

            // Untuk setiap bengkel, hitung pendapatan bulan ini
            foreach ($bengkels as $bengkel) {
                $revenue = Transaction::select('transaksi.id')
                    ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
                    ->where('transaksi.id_bengkel', $bengkel->id)
                    ->where('transaksi.status', 'selesai')
                    ->where('transaksi.created_at', 'like', $monthYear . '%')
                    ->sum('detail_transaksi.harga');

                $result[$bengkel->id]['nama'] = $bengkel->nama_bengkel;
                $result[$bengkel->id]['data'][] = $revenue;
                $result[$bengkel->id]['color'] = $this->generateBengkelColor($bengkel->id);
            }
        }

        return [
            'months' => $monthsData,
            'bengkels' => array_values($result)
        ];
    }

    private function generateBengkelColor($id)
    {
        $colors = [
            '#4e73df',
            '#1cc88a',
            '#36b9cc',
            '#f6c23e',
            '#e74a3b',
            '#858796',
            '#f8f9fc',
            '#5a5c69',
            '#2e59d9',
            '#17a673',
            '#2c9faf',
            '#f4b619',
            '#dc3545',
            '#6c757d',
            '#20c997',
            '#6610f2'
        ];

        return $colors[$id % count($colors)];
    }

    // Format data untuk AmCharts
    public function getRevenueByBengkelForChart($months = 6)
    {
        $data = $this->getRevenueByBengkel($months);

        $chartData = [];

        // Format data untuk stacked bar chart
        foreach ($data['months'] as $monthIndex => $month) {
            $monthData = ['month' => $month];

            foreach ($data['bengkels'] as $bengkel) {
                $monthData[$bengkel['nama']] = $bengkel['data'][$monthIndex] ?? 0;
            }

            $chartData[] = $monthData;
        }

        return [
            'chartData' => $chartData,
            'bengkels' => $data['bengkels']
        ];
    }
}
