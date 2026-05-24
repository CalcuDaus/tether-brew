<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'date',
        'description',
        'type',
        'amount',
        'journal_category_id',
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
