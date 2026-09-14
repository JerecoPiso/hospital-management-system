<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage: ->middleware('permission:<module>,<ability>')
     * <ability> is one of: view, create, update, delete
     */
    public function handle(Request $request, Closure $next, string $module, string $ability): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($module, $ability)) {
            return api_response([], false, 'You do not have permission to perform this action.', 403);
        }

        return $next($request);
    }
}
