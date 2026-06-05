<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use App\Models\Kegiatan;

class CheckDepartmentAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            // Sanctum / authenticated API user
            $userRole = $user->roles->first()?->name ?? '';
            $userJurusan = $user->nama_jurusan;
        } else {
            // Web session user
            $userRole = Session::get('role') ?? '';
            $userJurusan = Session::get('jurusan');
        }

        $userRole = strtolower($userRole);

        // Only enforce department protection for 'admin' role
        if ($userRole === 'admin' && !empty($userJurusan)) {
            // Check for route parameter 'id' or request input 'kegiatan_id'
            $kegiatanId = $request->route('id') ?? $request->input('kegiatan_id');

            if ($kegiatanId) {
                $kegiatan = Kegiatan::find($kegiatanId);

                if ($kegiatan && $kegiatan->jurusan_penyelenggara !== $userJurusan) {
                    // Check if it is an API or JSON request
                    if ($request->is('api/*') || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki hak untuk melihat data ini',
                        ], 403);
                    }
                    
                    abort(403, 'Anda tidak memiliki hak untuk melihat data ini');
                }
            }
        }

        return $next($request);
    }
}
