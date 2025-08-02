<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Customer;
use App\Models\SalesItem;
use App\Models\Note;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id', 
        'discount', 
        'total_amount', 
        'sale_date'
    ];

    public function items() 
    {
        return $this->hasMany(SalesItem::class);
    }

    public function customer() 
    {
        return $this->belongsTo(Customer::class);
    }

    public function note() 
    {
        return $this->morphOne(Note::class, 'noteable');
    }

    protected static function booted()
    {
        static::deleting(function ($sale) {
            if (! $sale->isForceDeleting()) {
                $sale->items()->delete();
                $sale->note()->delete();
            }
        });

        static::restoring(function ($sale) {
            $sale->items()->withTrashed()->restore();
            $sale->note()->withTrashed()->restore();
        });

        static::forceDeleting(function ($sale) {
            $sale->items()->withTrashed()->forceDelete();
            $sale->note()->withTrashed()->forceDelete();
        });
    }
}
