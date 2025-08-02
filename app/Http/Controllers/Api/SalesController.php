<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Note;

use Carbon\Carbon;
use App\Http\Resources\SalesResource;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'customer_id'     => 'required',
            'sale_date'       => 'required'
        ]);

        if($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->errors()
            ]);
        }

        try {
            \DB::transaction(function() use($request) {
                $sales = Sale::create([
                    'customer_id'  => $request->customer_id,
                    'discount'     => $request->discount,
                    'sale_date'    => $request->sale_date,
                    'total_amount' => $request->total_amount,
                ]);

                if($request->note) {
                   Note::create([
                        'note'          => $request->note,
                        'noteable_type' => Sale::class,
                        'noteable_id'   => $sales->id
                    ]); 
                }

                foreach($request->products as $key => $value) {
                    SalesItem::create([
                        'sale_id'     => $sales->id,
                        'product_id'  => $value["product_id"],
                        'quantity'    => $value["quantity"],
                        'price'       => $value["price"],
                        'sub_total'   => $value["sub_total"],
                    ]);
                }
            });

            return response()->json([
                'status'  => 200,
                'message' => 'Congratulations ! Sales Record was created successfully'
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status'  => 400,
                'message' => $e
            ]);
        }
    }

    public function list($paginate, $customerId, $productId, $date_starts, $date_ends) {
        $date_starts = $date_starts !== 'null' ? $date_starts : null;
        $date_ends = $date_ends !== 'null' ? $date_ends : null;
        $customerId = $customerId !== 'null' ? $customerId : null;
        $productId = $productId !== 'null' ? $productId : null;

        // $start = $date_starts ? Carbon::parse($date_starts)->startOfDay() : null;
        // $end = $date_ends ? Carbon::parse($date_ends)->endOfDay() : null;

        $query = Sale::query();

        if (!empty($customerId) && $customerId !== 'null') {
            $query->where('customer_id', $customerId);
        }

        if ($date_starts && $date_ends) {
            $query->whereBetween('sale_date', [$date_starts, $date_ends]);
        } elseif ($date_starts) {
            $query->whereBetween('sale_date', [$date_starts, now()]);
        } elseif ($date_ends) {
            $query->whereBetween('sale_date', [Carbon::minValue(), $date_ends]);
        }

        if (!empty($productId) && $productId !== 'null') {
            $query->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        $data = $query->with(['customer', 'items.product', 'note'])->orderBy('sale_date', 'DESC')->paginate($paginate);

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

    public function destroy($id)
    {
        $sales = Sale::find($id);

        if(!$sales) {
            return response()->json([
                'status'   => 404,
                'message'  => 'No Data Found !'
            ]);    
        }

        $sales->delete();

        return response()->json([
            'status'   => 200
        ]);
    }
}
