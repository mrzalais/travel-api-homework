<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(401);
        }

        /** @var User $user */
        $user = auth()->user();

        if (!$user->roles()->where('name', $role)->exists()) {
            abort(403);
        }

        return $next($request);
    }
}
