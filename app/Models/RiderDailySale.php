<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDailySale extends Model
{
    protected $fillable = [
        'rider_id',
        'date',
        'cash_amount',
        'actual_setor',
        'minus_amount',
        'qris_amount',
        'total_setoran',
        'total_gross_income',
        'admin_pemeriksa',
        'admin_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(RiderDailySaleItem::class);
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
