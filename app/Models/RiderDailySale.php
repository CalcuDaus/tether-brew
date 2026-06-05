<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class RiderDailySale extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'rider_id',
        'branch_id',
        'date',
        'cash_amount',
        'actual_setor',
        'minus_amount',
        'minus_paid',
        'minus_status',
        'minus_source',
        'minus_notes',
        'carry_over_from_payroll_id',
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

    public function carryOverFromPayroll()
    {
        return $this->belongsTo(PayrollRecord::class, 'carry_over_from_payroll_id');
    }
}
