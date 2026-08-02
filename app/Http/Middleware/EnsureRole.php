<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak. Role tidak sesuai.');
        }
        return $next($request);
    }
}
