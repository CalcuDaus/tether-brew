<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalCategory extends Model
{
    protected $fillable = ['name'];

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
