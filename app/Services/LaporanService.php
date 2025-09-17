<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LaporanService
{
    public function getFilteredDataPelanggan(Request $request)
    {
        $query = Customer::with(['bengkel'])
            ->orderBy('nama', 'asc');
        // Filter by status
        if ($request->has('id_bengkel') && $request->status != '') {
            $query->where('id_bengkel', $request->status);
        }

        return $query->get();
    }
    public function getFilteredTransactions(Request $request, $paginate = true)
    {
        $query = Transaction::with(['customer', 'serviceAdvisor', 'details.service'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        // Filter by bengkel
        if ($request->has('bengkel_id') && $request->bengkel_id != '') {
            $query->where('id_bengkel', $request->bengkel_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return $paginate ? $query->paginate(10) : $query->get();
    }
}
