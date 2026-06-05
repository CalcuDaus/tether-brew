<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeKasbonPayment extends Model
{
    protected $fillable = [
        'office_kasbon_id',
        'admin_id',
        'date',
        'amount',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function kasbon()
    {
        return $this->belongsTo(OfficeKasbon::class, 'office_kasbon_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
