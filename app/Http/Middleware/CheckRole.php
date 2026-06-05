<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = Session::get('role');

        if (!$userRole) {
            if ($request->hasCookie('was_logged_in')) {
                // Clear the cookie so the expired message only appears once
                Cookie::queue(Cookie::forget('was_logged_in'));
                return redirect('/')->with('login_error', 'Sesi Anda telah berakhir.');
            }
            return redirect('/')->with('login_error', 'Silakan login terlebih dahulu.');
        }

        if (!empty($roles) && !in_array($userRole, $roles)) {
            abort(403, 'Forbidden / Unauthorized Access');
        }

        return $next($request);
    }
}
