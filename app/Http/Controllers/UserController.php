<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Institution;
use App\Models\AnalysisResult;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; 

class UserController extends Controller
{
    /**
     * Menampilkan dasbor untuk pengguna biasa (pegawai).
     * Akan menampilkan informasi relevan bagi pegawai yang login, termasuk hasil analisis.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee; // Mengambil data pegawai yang terhubung dengan user

        // Mengambil hasil analisis kompetensi terbaru untuk pegawai ini
        $latestAnalysisResult = null; // Pastikan baris ini ADA
        if ($employee) {
            // Memastikan Anda memiliki relasi analysisResults() di model Employee
            $latestAnalysisResult = $employee->analysisResults()->latest('analysis_date')->first();
        }

        // Data untuk chart (jika ada) - Anda bisa tambahkan ini jika ingin Chart.js di dashboard user
        // $chartData = [
        //     'labels' => ['Kompetensi Saat Ini', 'Kebutuhan Jabatan'],
        //     'data' => [
        //         $employee ? $employee->analysis_percentage : 0, // Kompetensi aktual
        //         80 // Contoh kebutuhan (bisa diambil dari JobRequirement jika dianalisis)
        //     ]
        // ];

        return view('user.dashboard', compact('user', 'employee', 'latestAnalysisResult'));
    }

    /**
     * Menampilkan detail instansi pegawai yang sedang login
     * dan daftar pegawai lain di instansi yang sama.
     */
    public function myInstitution()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $institution = null;
        $employeesInSameInstitution = collect();

        if ($employee && $employee->institution) {
            $institution = $employee->institution;
            // Ambil semua pegawai di instansi yang sama, kecuali diri sendiri
            $employeesInSameInstitution = $institution->employees()
                                                    ->where('id', '!=', $employee->id)
                                                    ->get();
        }

        return view('user.institutions', compact('user', 'employee', 'institution', 'employeesInSameInstitution'));
    }

    /**
     * Menampilkan riwayat analisis kompetensi dan dokumen yang diunggah
     * untuk pegawai yang sedang login.
     */
    public function myHistory()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $analysisResults = collect();
        $employeeDocuments = collect();

        if ($employee) {
            // Urutkan hasil analisis dari yang terbaru
            $analysisResults = $employee->analysisResults()->orderByDesc('analysis_date')->get();
            // Urutkan dokumen dari yang terbaru
            $employeeDocuments = $employee->documents()->orderByDesc('created_at')->get();
        }

        return view('user.history', compact('user', 'employee', 'analysisResults', 'employeeDocuments'));
    }

    /**
     * Menampilkan halaman profil untuk pegawai yang sedang login.
     * Memungkinkan pegawai untuk melihat dan memperbarui informasi pribadi.
     */
    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee; // Pastikan data pegawai terhubung

        return view('user.profile', compact('user', 'employee'));
    }

    /**
     * Memperbarui profil pegawai yang sedang login.
     * Mengelola pembaruan nama, email, password, dan foto profil.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Validasi untuk data User
        $userValidationRules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Validasi untuk data Employee (jika ada)
        $employeeValidationRules = [];
        if ($employee) {
            $employeeValidationRules = [
                'nip' => ['required', 'string', 'max:255', Rule::unique('employees')->ignore($employee->id)],
                'golongan' => 'required|string|max:255',
                'jabatan' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi file gambar
            ];
        }

        $request->validate(array_merge($userValidationRules, $employeeValidationRules));

        // Update data User
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle profile picture upload for User model (for generic access like navbar)
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar profil lama User jika ada
            if ($user->profile_picture_path && Storage::disk('public')->exists($user->profile_picture_path)) {
                Storage::disk('public')->delete($user->profile_picture_path);
            }
            // Simpan gambar baru ke folder 'user_profile_pictures' (untuk User model)
            $user->profile_picture_path = $request->file('profile_picture')->store('user_profile_pictures', 'public');
        } else {
            // Jika tidak ada file baru diunggah, tapi input ada, bisa jadi pengguna ingin menghapus foto
            // Anda bisa menambahkan checkbox "Hapus Foto Profil" di form untuk ini
            // Untuk saat ini, jika tidak ada file baru, path lama tetap dipertahankan
            // kecuali jika ada logika 'clear image' di frontend
        }
        $user->save(); // Simpan perubahan pada User model

        // Update data Employee (jika ada) dan sinkronkan foto profil
        if ($employee) {
            $employeeData = $request->only(['nip', 'golongan', 'jabatan', 'phone_number']);
            $employeeData['email'] = $user->email; // Pastikan email di employee tetap sinkron dengan email di user

            // Sinkronkan jalur foto profil dari User ke Employee
            $employeeData['profile_picture_path'] = $user->profile_picture_path;
            
            $employee->update($employeeData); // Perbarui data pegawai
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Menampilkan daftar dokumen yang diunggah oleh pegawai yang sedang login.
     * Memungkinkan pengunggahan dokumen baru.
     */
    public function documents()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $employeeDocuments = collect();

        if ($employee) {
            $employeeDocuments = $employee->documents()->orderByDesc('created_at')->get();
        }

        return view('user.documents', compact('user', 'employee', 'employeeDocuments'));
    }

    /**
     * Mengelola pengunggahan dokumen baru oleh pegawai.
     * Dokumen ini akan terintegrasi untuk analisis di masa mendatang.
     */
    public function uploadDocument(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Anda belum memiliki data pegawai yang terhubung. Tidak dapat mengunggah dokumen.');
        }

        $request->validate([
            'document_file' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png', // Maks 5MB, format umum
            'document_type' => 'nullable|string|max:255',
        ]);

        $filePath = $request->file('document_file')->store('employee_documents/' . $employee->id, 'public');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'file_name' => $request->file('document_file')->getClientOriginalName(),
            'file_path' => $filePath,
            'document_type' => $request->document_type ?? 'Umum',
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah!');
    }
}
