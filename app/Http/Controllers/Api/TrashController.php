<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sale;
use App\Http\Resources\SalesResource;

class TrashController extends Controller
{
    public function list($paginate) {
        $data = $trashedItems = Sale::with(['customer', 'items.product', 'note'])->orderBy('sale_date', 'DESC')->onlyTrashed()->paginate($paginate);

        $list = SalesResource::collection($data);

        return response()->json([
            'list' => $list,
            'pagination' => [
                'current_page'  => $data->currentPage(),
                'last_page'     => $data->lastPage(),
                'per_page'      => $data->perPage(),
                'from'          => $data->firstItem(),
                'to'            => $data->lastItem(),
                'total'         => $data->total(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'links' => [
                    'first' => $data->url(1),
                    'last'  => $data->url($data->lastPage()),
                    'prev'  => $data->previousPageUrl(),
                    'next'  => $data->nextPageUrl(),
                ],
            ],
            'status' => 200
        ]);
    }

    public function restore(Request $request)
    {
        $sale = Sale::withTrashed()->findOrFail($request->input('id'));
        $sale->restore(); 
        return response()->json(['message' => 'Sale restored.']);
    }

    public function forceDelete($id)
    {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->forceDelete(); // also force deletes items and note
        return response()->json(['message' => 'Sale permanently deleted.']);
    }
}
