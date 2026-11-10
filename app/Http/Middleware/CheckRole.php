<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if(!$user || !$user->role || !in_array($user->role->role_name, $roles)) {
            return response()->json(['message' => 'Forbidden - insufficient role privileges.'], 403);
        }
        return $next($request);
    }
}