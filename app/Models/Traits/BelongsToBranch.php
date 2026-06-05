<?php

namespace App\Models\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a Branch.
 * Adds branch relationship and forBranch scope.
 */
trait BelongsToBranch
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to filter by branch_id.
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where($this->getTable() . '.branch_id', $branchId);
    }
}
