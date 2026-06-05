<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'date',
        'description',
        'type',
        'amount',
        'journal_category_id',
        'branch_id',
        'created_by',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(JournalCategory::class, 'journal_category_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
