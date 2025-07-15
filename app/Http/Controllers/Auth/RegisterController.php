<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User; // Pastikan model User diimpor
use App\Models\Employee; // Impor model Employee
use App\Models\Institution; // Impor model Institution
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request; // Impor Request

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     * Validasi disesuaikan untuk menyertakan NIP, institution_id, dan golongan.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:255', 'unique:employees'], // NIP harus unik di tabel employees
            'institution_id' => ['required', 'exists:institutions,id'], // institution_id wajib dan harus ada di tabel institutions
            'golongan' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Email harus unik di tabel users
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * Membuat record User dan Employee yang terhubung setelah validasi berhasil.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Membuat User baru
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pegawai', // Set peran default sebagai 'pegawai'
        ]);

        // Membuat record Employee yang terhubung dengan User yang baru dibuat
        Employee::create([
            'user_id' => $user->id, // Hubungkan dengan ID User
            'institution_id' => $data['institution_id'],
            'name' => $data['name'],
            'nip' => $data['nip'],
            'golongan' => $data['golongan'],
            'jabatan' => 'Belum ditentukan', // Jabatan awal bisa diisi default, admin bisa mengedit nanti
            'email' => $data['email'],
            'phone_number' => null, // Atau bisa tambahkan input phone_number di form register
            'profile_picture_path' => null, // Akan diisi nanti di profil user jika ada upload
            'analysis_percentage' => 0, // Inisialisasi persentase analisis
        ]);

        return $user;
    }

    /**
     * Menampilkan formulir pendaftaran.
     * Overrides default method to pass institutions to the view.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $institutions = Institution::all(); // Ambil semua instansi dari database
        return view('auth.register', compact('institutions')); // Kirim data instansi ke view
    }
}
