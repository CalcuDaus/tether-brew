<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class RiderSalesJournalConfirmation extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'date',
        'branch_id',
        'total_cash',
        'total_qris',
        'total_minus',
        'total_omset',
        'rider_count',
        'confirmed_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function confirmedByAdmin()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
