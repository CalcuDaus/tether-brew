<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class OfficeKasbon extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'admin_id',
        'name',
        'date',
        'amount',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payments()
    {
        return $this->hasMany(OfficeKasbonPayment::class);
    }
}
