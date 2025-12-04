<?php
// app/Services/TransactionService.php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\DetailTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class TransactionService
{
    /**
     * Kompres dan simpan gambar
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param int $quality (1-100)
     * @param int|null $maxWidth
     * @return string|null
     */
    private function compressAndStoreImage($file, $folder = 'photos', $quality = 60, $maxWidth = 800)
    {
        try {
            if (!$file || !$file->isValid()) {
                return null;
            }

            // Buat folder jika belum ada
            $folderPath = 'public/' . $folder;
            if (!Storage::exists($folderPath)) {
                Storage::makeDirectory($folderPath);
            }

            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $folder . '/' . $filename;

            $image = Image::make($file);

            // Resize jika melebihi lebar maksimum
            if ($maxWidth && $image->width() > $maxWidth) {
                $image->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            // Simpan dengan kualitas terkompresi
            $image->encode($extension, $quality);

            Storage::disk('public')->put($path, $image);

            return Storage::url($path);
        } catch (\Exception $e) {
            Log::error('Image compression error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kompres multiple gambar
     *
     * @param array $files
     * @param string $folder
     * @param int $quality
     * @param int|null $maxWidth
     * @return array
     */
    private function compressAndStoreMultipleImages($files, $folder = 'photos', $quality = 60, $maxWidth = 800)
    {
        $paths = [];

        if (!is_array($files)) {
            return $paths;
        }

        foreach ($files as $file) {
            if (is_object($file) && method_exists($file, 'isValid')) {
                $path = $this->compressAndStoreImage($file, $folder, $quality, $maxWidth);
                if ($path) {
                    $paths[] = $path;
                }
            } elseif (is_string($file) && strpos($file, '/storage/') === 0) {
                // Jika sudah berupa path, tambahkan langsung
                $paths[] = $file;
            }
        }

        return $paths;
    }

    /**
     * Process service photos - kompres dan simpan gambar
     *
     * @param array $servicesData
     * @return array
     */
    public function processServicePhotos($servicesData)
    {
        foreach ($servicesData as $key => $service) {
            // Check if foto_layanan exists and is an array
            if (isset($service['foto_layanan']) && is_array($service['foto_layanan'])) {
                // Kompres gambar dengan kualitas 60% dan maksimal lebar 800px
                $fotoPaths = $this->compressAndStoreMultipleImages(
                    $service['foto_layanan'],
                    'photos/before',
                    60, // Quality (1-100)
                    800 // Max width
                );

                $servicesData[$key]['foto_before'] = $fotoPaths;
            }
        }

        return $servicesData;
    }

    /**
     * Create transaction dengan handling gambar
     *
     * @param array $data
     * @return Transaction
     */
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Cari atau buat customer berdasarkan plat nomor
            $customerData = [
                // 'nama' => $data['nama'],
                // 'no_telp' => $data['no_telp'],
                // 'email' => $data['email'] ?? null,
                // 'alamat' => $data['alamat'] ?? null,
                // 'vin' => $data['vin'] ?? null,
                'tipe_kendaraan' => $data['tipe_kendaraan'],
                'no_wo' => $data['no_wo'],
                'kilometer' => $data['kilometer'],
                'id_bengkel' => Auth::user()->bengkel_id,
                'created_by' => Auth::id()
            ];

            $customer = Customer::firstOrCreate(
                ['plat_nomor' => $data['plat_nomor']],
                $customerData
            );

            // Update data customer jika ada perubahan
            if (!$customer->wasRecentlyCreated) {
                $customer->update($customerData);
            }
            // 2. Buat transaksi
            $total = 0;

            foreach ($data['services'] as $service) {
                $total += $service['harga'];
            }
            $transaction = Transaction::create([
                'id_bengkel' => Auth::user()->bengkel_id,
                'id_service_advisors' => $data['id_service_advisor'],
                'id_customers' => $customer->id,
                'status' => 'proses',
                'total' => $total,
                'kilometer' => $data['kilometer'],
                'created_by' => Auth::id()
            ]);

            // 3. Buat detail transaksi dalam batch
            $detailTransactions = [];
            foreach ($data['services'] as $service) {

                //save foto 
                $fotoPaths = $this->compressAndStoreMultipleImages(
                    $service['foto_layanan'],
                    'photos/before',
                    60, // Quality (1-100)
                    800 // Max width
                );
                $detailTransactions[] = [
                    'id_transaksi' => $transaction->id,
                    'id_layanan' => $service['id_layanan'],
                    'harga' => $service['harga'],
                    'flag_harga_khusus' => $service['flag_harga_khusus'] ?? false,
                    'keterangan' => $service['keterangan'] ?? null,
                    'foto_sebelum' => $fotoPaths[0] ?? null,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insert batch untuk performa lebih baik
            DetailTransaction::insert($detailTransactions);

            return $transaction->load('details');
        });
    }

    /**
     * Batalkan transaksi
     *
     * @param Transaction $transaction
     * @param array $data
     * @return Transaction
     * @throws \Exception
     */
    public function cancelTransaction(Transaction $transaction, array $data)
    {
        // Cek authorization
        if ($transaction->created_by !== Auth::id()) {
            throw new \Exception('Anda tidak memiliki izin untuk membatalkan transaksi ini.', 403);
        }

        // Cek status transaksi
        if ($transaction->status === 'batal') {
            throw new \Exception('Transaksi sudah dibatalkan sebelumnya.', 422);
        }

        if ($transaction->status === 'selesai') {
            throw new \Exception('Transaksi yang sudah selesai tidak dapat dibatalkan.', 422);
        }

        // Update transaksi
        $transaction->update([
            'status' => 'batal',
            'alasan_batal' => $data['alasan'],
            'dibatalkan_oleh' => Auth::id(),
            'dibatalkan_pada' => now()
        ]);

        // Log activity
        Log::info('Transaksi dibatalkan', [
            'transaction_id' => $transaction->id,
            'user_id' => Auth::id(),
            'alasan' => $data['alasan'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return $transaction->fresh();
    }

    /**
     * Validasi data pembatalan
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateCancelData(array $data)
    {
        $validator = validator($data, [
            'alasan' => 'required|string|min:10|max:500'
        ], [
            'alasan.required' => 'Alasan pembatalan harus diisi',
            'alasan.min' => 'Alasan pembatalan minimal 10 karakter',
            'alasan.max' => 'Alasan pembatalan maksimal 500 karakter'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
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

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return $paginate ? $query->paginate(10) : $query->get();
    }

    // Tambahkan metode ini di class TransactionService

    /**
     * Update transaksi dengan foto sesudah
     *
     * @param Transaction $transaction
     * @param array $data
     * @return Transaction
     */
    public function updateTransaction(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Update status transaksi
            $transaction->update([
                'status' => $data['status'],
                'updated_by' => Auth::id()
            ]);

            // Proses foto sesudah untuk setiap detail transaksi
            if (isset($data['services']) && is_array($data['services'])) {
                foreach ($data['services'] as $key => $serviceData) {
                    $detailId = $transaction->details[$key]->id ?? null;

                    if ($detailId && isset($serviceData['foto_layanan']) && !empty($serviceData['foto_layanan'])) {
                        $detail = DetailTransaction::find($detailId);

                        if ($detail) {
                            // Kompres dan simpan foto sesudah
                            $fotoPaths = $this->compressAndStoreMultipleImages(
                                $serviceData['foto_layanan'],
                                'photos/after',
                                60, // Quality
                                800 // Max width
                            );

                            if (!empty($fotoPaths)) {
                                $detail->foto_sesudah = $fotoPaths[0] ?? null;
                                $detail->save();
                            }
                        }
                    }
                }
            }

            return $transaction->fresh()->load(['customer', 'serviceAdvisor', 'details.service']);
        });
    }
}
