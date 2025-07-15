<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Mengimpor facade Auth
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Tangani permintaan masuk.
     * Middleware ini memeriksa apakah pengguna yang terautentikasi memiliki peran 'admin'.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Memastikan pengguna sudah login (Auth::check())
        // DAN peran pengguna adalah 'admin' (Auth::user()->role === 'admin').
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Jika valid, lanjutkan permintaan ke rute tujuan
        }

        // Jika pengguna tidak login atau bukan admin, arahkan mereka ke halaman login
        // dengan pesan error menggunakan flash session.
        return redirect('/login')->with('error', 'Anda tidak memiliki akses sebagai Admin. Silakan login dengan akun yang sesuai.');
        // Alternatif: abort(403, 'Unauthorized.'); akan menampilkan halaman 403 Forbidden.
    }
}
