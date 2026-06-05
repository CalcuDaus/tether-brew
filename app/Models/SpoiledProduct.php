<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpoiledProduct extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToBranch;

    protected $fillable = ['date', 'branch_id', 'user_id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SpoiledProductItem::class);
    }
}
