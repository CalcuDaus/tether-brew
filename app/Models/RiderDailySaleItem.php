<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDailySaleItem extends Model
{
    protected $fillable = [
        'rider_daily_sale_id',
        'product_id',
        'stock_out',
        'stock_added',
        'stock_return',
        'stock_sold'
    ];

    public function sale()
    {
        return $this->belongsTo(RiderDailySale::class, 'rider_daily_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
