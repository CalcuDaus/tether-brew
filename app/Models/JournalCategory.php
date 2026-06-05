<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class JournalCategory extends Model
{
    protected $fillable = ['name'];

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
