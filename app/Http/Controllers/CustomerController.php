<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function findByPlat(Request $request)
    {
        $platNomor = $request->input('plat_nomor');
        $customer = Customer::where('plat_nomor', $platNomor)->first();

        return response()->json([
            'exists' => !is_null($customer),
            'customer' => $customer
        ]);
    }
}
