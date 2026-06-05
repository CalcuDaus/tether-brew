<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class RiderFinance extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'rider_id',
        'admin_id',
        'branch_id',
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
