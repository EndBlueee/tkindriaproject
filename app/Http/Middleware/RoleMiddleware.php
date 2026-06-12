<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,  string $role): Response
    {

        // Cek apakah user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah akun masih aktif
        if (! $request->user()->is_aktif) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi sekolah.');
        }

        // Cek apakah role user sesuai
        if ($request->user()->role !== $role) {
            // Jika tidak sesuai, redirect ke dashboard sesuai role masing-masing
            if ($request->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('orangtua.dashboard.index');
        }
        
        return $next($request);
    }
}
