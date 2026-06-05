<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpoiledProductItem extends Model
{
    use HasFactory;

    protected $fillable = ['spoiled_product_id', 'product_id', 'quantity', 'notes'];

    public function spoiledProduct()
    {
        return $this->belongsTo(SpoiledProduct::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
