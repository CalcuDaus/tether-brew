<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'rider_id',
        'admin_id',
        'branch_id',
        'type',
        'period_start',
        'period_end',
        'total_cups',
        'gross_income',
        'kasbon_outstanding',
        'kasbon_deducted',
        'minus_outstanding',
        'minus_deducted',
        'uang_makan_adjustment',
        'net_income',
        'status',
        'confirmed_at',
        'confirmed_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function confirmedByAdmin()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function carryOverEntries()
    {
        return $this->hasMany(RiderDailySale::class, 'carry_over_from_payroll_id');
    }

    public function scopeForRider($query, $riderId)
    {
        return $query->where('rider_id', $riderId);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
