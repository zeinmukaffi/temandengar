<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Memastikan pengguna yang login memiliki peran yang diizinkan
     * (serta profil konselor yang aktif jika perannya 'konselor').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, 'Kamu tidak memiliki izin untuk mengakses halaman ini.');
        }

        if ($user->role === 'konselor') {
            $counselor = $user->counselor;
            if (!$counselor || !$counselor->is_active) {
                abort(403, 'Profil konselor kamu belum aktif.');
            }
        }

        return $next($request);
    }
}