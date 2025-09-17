<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelTransactionRequest;
use App\Services\TransactionService;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Customer;
use App\Models\Layanan;
use App\Models\ServiceAdvisor;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $transactions = Transaction::with(['customer', 'serviceAdvisor', 'details.service'])
            ->where('created_by', Auth::user()->id)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('transactions.index', compact('transactions'));
    }
    public function create()
    {
        $services = Layanan::all();
        $serviceadvisor = ServiceAdvisor::all()->where('id_bengkel', Auth::user()->bengkel_id);
        return view('transactions.create', compact('services', 'serviceadvisor'));
    }

    public function store(StoreTransactionRequest $request)
    {
        try {
            $request->merge([
                'id_bengkel' => Auth::user()->bengkel_id
            ]);
            $transaction = $this->transactionService->createTransaction($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function findByPlat(Request $request)
    {
        $platNomor = $request->input('plat_nomor');
        $customer = Customer::where('plat_nomor', $platNomor)->first();

        return response()->json([
            'exists' => !is_null($customer),
            'customer' => $customer
        ]);
    }

    public function show(Transaction $transaction)
    {
        // Memastikan user hanya bisa melihat transaksi yang dibuat olehnya
        if ($transaction->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->load(['customer', 'serviceAdvisor', 'details.service']);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        // Memastikan user hanya bisa mengedit transaksi yang dibuat olehnya
        if ($transaction->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->load(['customer', 'serviceAdvisor', 'details.service']);
        $services = Layanan::all();
        $serviceadvisor = ServiceAdvisor::all()->where('id_bengkel', Auth::user()->bengkel_id);

        return view('transactions.edit', compact('transaction', 'services', 'serviceadvisor'));
    }

    // Ganti metode update dengan kode berikut
    
    public function update(Request $request, Transaction $transaction)
    {
        // Memastikan user hanya bisa mengupdate transaksi yang dibuat olehnya
        if ($transaction->created_by != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah transaksi ini.'
            ], 403);
        }
    
        try {
            // Validasi request
            $validated = $request->validate([
                'status' => 'required|in:proses,selesai',
                // Tambahkan validasi lain sesuai kebutuhan
            ]);
    
            // Update transaksi menggunakan service
            $updatedTransaction = $this->transactionService->updateTransaction($transaction, $request->all());
    
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $updatedTransaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan transaksi
     *
     * @param CancelTransactionRequest $request
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function cancel(CancelTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        try {
            $validated = $request->validated();

            $cancelledTransaction = $this->transactionService->cancelTransaction(
                $transaction,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan.',
                'data' => $cancelledTransaction
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }
    public function report(Request $request)
    {
        $transactions = $this->transactionService->getFilteredTransactions($request);

        return view('transactions.report', compact('transactions'));
    }
}
