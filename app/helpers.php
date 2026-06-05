<?php

if (!function_exists('activeBranchId')) {
    /**
     * Get the active branch ID from session.
     */
    function activeBranchId(): ?int
    {
        return session('active_branch_id');
    }
}
