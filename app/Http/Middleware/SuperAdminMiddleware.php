<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya superadmin yang dapat mengakses.');
    }
}
