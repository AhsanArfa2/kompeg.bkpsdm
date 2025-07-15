<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth; // Pastikan ini diimpor
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // Ganti properti $redirectTo dengan metode redirectTo()
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Redirect user based on their role after successful login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin' || $user->role === 'admin') {
            return route('admin.dashboard'); // Misalnya halaman dashboard sama untuk admin dan superadmin
        } elseif ($user->role === 'pegawai') {
            return route('user.dashboard');
        }

        // Fallback jika role tidak dikenali
        return RouteServiceProvider::HOME;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        \Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
