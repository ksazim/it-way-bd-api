<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'customer'            => $this->customer->name,
            'product-description' => $this->productDescription(),
            'note'                => $this->note ? $this->note->note : '-',
            'Total Amount'        => $this->total_amount,
        ];
    }

    private function productDescription()
    {
        if ($this->items->isNotEmpty()) {
            $rows = $this->items->map(function ($item) {
                return '
                    <tr>
                        <td style="border:1px solid #ccc; padding:5px;">' . e($item->product->name) . '</td>
                        <td style="border:1px solid #ccc; padding:5px;">' . e($item->quantity) . '</td>
                    </tr>
                ';
            })->implode('');

            return '
                <table style="width:100%; border:1px solid #ccc; border-collapse:collapse; font-size:14px; margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th style="border:1px solid #ccc; padding:5px; text-align:left;">Product Name</th>
                            <th style="border:1px solid #ccc; padding:5px; text-align:left;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $rows . '
                    </tbody>
                </table>
            ';
        }

        return 'No Record Found';
    }

}
