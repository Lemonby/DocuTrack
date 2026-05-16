<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = Session::get('role');

        if (!$userRole) {
            return redirect('/')->with('login_error', 'Silakan login terlebih dahulu.');
        }

        if (!empty($roles) && !in_array($userRole, $roles)) {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
