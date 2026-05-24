<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderFinance extends Model
{
    protected $fillable = [
        'rider_id',
        'admin_id',
        'date',
        'type',
        'amount',
        'reference_cups',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
