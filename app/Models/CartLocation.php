<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartLocation extends Model
{
    protected $fillable = ['cart_id', 'latitude', 'longitude'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
