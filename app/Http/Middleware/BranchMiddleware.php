<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Owner: can switch branches, default to first active branch
        if ($user->isOwner()) {
            if (!session()->has('active_branch_id')) {
                $defaultBranch = Branch::active()->first();
                if ($defaultBranch) {
                    session(['active_branch_id' => $defaultBranch->id]);
                }
            }

            // Share branch data with all views
            $branches = Branch::active()->get();
            $activeBranch = Branch::find(session('active_branch_id'));

            view()->share('__branches', $branches);
            view()->share('__activeBranch', $activeBranch);

            return $next($request);
        }

        // Admin & Bar: locked to assigned branch
        if ($user->isAdmin() || $user->isBar()) {
            if (!$user->branch_id) {
                abort(403, 'Anda belum di-assign ke cabang manapun. Hubungi Owner atau Admin untuk penugasan cabang.');
            }

            session(['active_branch_id' => $user->branch_id]);

            $activeBranch = Branch::find($user->branch_id);
            view()->share('__activeBranch', $activeBranch);
            view()->share('__branches', collect([$activeBranch]));

            return $next($request);
        }

        // Rider: locked to assigned branch (for rider dashboard, etc.)
        if ($user->isRider() && $user->branch_id) {
            session(['active_branch_id' => $user->branch_id]);
        }

        return $next($request);
    }
}
