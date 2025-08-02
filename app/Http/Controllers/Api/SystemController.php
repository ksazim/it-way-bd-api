<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\ProductResource;

class SystemController extends Controller
{
    public function customers()
    {
        try {
            $list = CustomerResource::collection(Customer::get());
            return response()->json([
                'status' => 200,
                'list'  => $list
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'error'  => $e
            ]);
        }
    }

    public function products(Request $request)
    {
        try {
            $list = ProductResource::collection(
                Product::whereNotIn('id', $request->items)->get()
            );
            
            return response()->json([
                'status' => 200,
                'list'  => $list
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'error'  => $e
            ]);
        }
    }
}
