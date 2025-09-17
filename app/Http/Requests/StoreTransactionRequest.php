<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:15', 
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'plat_nomor' => 'required|string|max:20',
            'vin' => 'nullable|string|max:50',
            'services' => 'required|array|min:1',
            'id_service_advisor' => 'required|exists:service_advisors,id',
            'services.*.id_layanan' => 'required|exists:layanan,id',
            'services.*.harga' => 'required|numeric|min:0',
            'services.*.flag_harga_khusus' => 'nullable|string|max:1',
            'services.*.keterangan' => 'nullable|string|max:255',
            'services.*.foto_layanan' => 'nullable|array',
        ];
    }
    public function messages()
    {
        return [
            'services.required' => 'Minimal satu layanan harus dipilih.',
            'services.*.id_layanan.required' => 'Layanan harus dipilih.',
            'services.*.harga.required' => 'Harga layanan harus diisi.'
        ];
    }
}
