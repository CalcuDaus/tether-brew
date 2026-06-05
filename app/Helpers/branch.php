<?php

use App\Models\Branch;

if (!function_exists('activeBranchId')) {
    /**
     * Get the active branch ID from the session.
     * Returns null if no branch is set.
     */
    function activeBranchId(): ?int
    {
        return session('active_branch_id');
    }
}

if (!function_exists('activeBranch')) {
    /**
     * Get the active Branch model instance.
     */
    function activeBranch(): ?Branch
    {
        $id = activeBranchId();
        if (!$id) return null;

        return Branch::find($id);
    }
}
