<?php
// app/Http/Requests/CancelTransactionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'alasan' => 'required|string|min:10|max:500'
        ];
    }

    public function messages()
    {
        return [
            'alasan.required' => 'Alasan pembatalan harus diisi',
            'alasan.min' => 'Alasan pembatalan minimal 10 karakter',
            'alasan.max' => 'Alasan pembatalan maksimal 500 karakter'
        ];
    }
}
