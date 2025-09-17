<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = [
            'monthlyEarnings' => $this->dashboardService->getMonthlyEarnings(),
            'totalTransactions' => $this->dashboardService->getTotalTransactions(),
            'totalCustomers' => $this->dashboardService->getTotalCustomers(),
            'cancelledTransactions' => $this->dashboardService->getCancelledTransactions(),
            'revenueData' => $this->dashboardService->getRevenueData(),
            'revenueByService' => $this->dashboardService->getRevenueByService(),
            'revenueByBengkel' => $this->dashboardService->getRevenueByBengkelForChart()
        ];

        return view('dashboard', $data);
    }

    public function getChartData()
    {
        try {
            $revenueData = $this->dashboardService->getRevenueData();
            $revenueByService = $this->dashboardService->getRevenueByService();
            $revenueByBengkel = $this->dashboardService->getRevenueByBengkelForChart();

            return response()->json([
                'success' => true,
                'areaChart' => $revenueData,
                'pieChart' => $revenueByService,
                'barChart' => $revenueByBengkel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage(),
                'areaChart' => [
                    'labels' => [],
                    'data' => []
                ],
                'pieChart' => [],
                'barChart' => [
                    'chartData' => [],
                    'bengkels' => []
                ]
            ], 500);
        }
    }
}
