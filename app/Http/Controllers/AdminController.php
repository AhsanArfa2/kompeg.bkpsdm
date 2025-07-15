<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Employee;
use App\Models\JobRequirement;
use App\Models\Training;
use App\Models\AnalysisResult;
use App\Models\HistoryDeletedEmployee;
use App\Models\Setting;
use App\Models\User;
use App\Models\EmployeeDocument; // Make sure this is imported if used
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Import DB facade
use Carbon\Carbon; // Import Carbon

class AdminController extends Controller
{
    /**
     * Menampilkan dasbor untuk admin.
     * Akan menampilkan statistik ringkasan.
     */
    public function dashboard()
    {
        $totalEmployees = Employee::count();
        // Menghitung berdasarkan hasil analisis terbaru untuk setiap pegawai
        // Gunakan subquery untuk mendapatkan analysis_result terbaru per employee
        $latestAnalysisPerEmployee = AnalysisResult::select('employee_id', DB::raw('MAX(analysis_date) as latest_date'))
                                                ->groupBy('employee_id');

        $employeesReadyForPromotion = AnalysisResult::joinSub($latestAnalysisPerEmployee, 'latest_analyses', function ($join) {
                                            $join->on('analysis_results.employee_id', '=', 'latest_analyses.employee_id')
                                                 ->on('analysis_results.analysis_date', '=', 'latest_analyses.latest_date');
                                        })
                                        ->where('result_status', 'Siap Naik Jabatan')
                                        ->count();

        $employeesNeedingTraining = AnalysisResult::joinSub($latestAnalysisPerEmployee, 'latest_analyses', function ($join) {
                                            $join->on('analysis_results.employee_id', '=', 'latest_analyses.employee_id')
                                                 ->on('analysis_results.analysis_date', '=', 'latest_analyses.latest_date');
                                        })
                                        ->whereIn('result_status', ['Perlu Pelatihan Khusus', 'Perlu Pengembangan Lanjutan'])
                                        ->count();
        
        // Menghitung status cukup
        $employeesStatusCukup = $totalEmployees - $employeesReadyForPromotion - $employeesNeedingTraining;
        if ($employeesStatusCukup < 0) $employeesStatusCukup = 0; // Pastikan tidak negatif


        // Data untuk Chart.js, dienkapsulasi untuk kemudahan passing ke view
        $chartData = [
            'labels' => ['Siap Naik Jabatan', 'Perlu Pelatihan / Pengembangan', 'Status Cukup / Belum Dianalisis'],
            'data' => [$employeesReadyForPromotion, $employeesNeedingTraining, $employeesStatusCukup],
            'backgroundColor' => [
                '#16a34a', // green-600 untuk siap
                '#f59e0b', // yellow-500 untuk perlu pelatihan/pengembangan
                '#3b82f6'  // blue-500 untuk status cukup
            ]
        ];

        return view('admin.dashboard', compact('totalEmployees', 'employeesNeedingTraining', 'employeesReadyForPromotion', 'chartData'));
    }

    /**
     * Menampilkan daftar instansi dan formulir tambah/edit.
     */
    public function institutions(Request $request) 
    {
        $searchInstitution = $request->query('search_instansi');
        $searchEmployee = $request->query('search_pegawai');
        $selectedInstitutionId = $request->query('instansi_id'); 

        // Mengambil daftar instansi dengan jumlah pegawai terkait
        $institutionsQuery = Institution::query();
        if ($searchInstitution) {
            $institutionsQuery->where('name', 'like', '%' . $searchInstitution . '%');
        }
        $institutions = $institutionsQuery->withCount('employees')->get();

        $employees = collect();
        $selectedInstitutionName = 'Pilih Instansi'; 

        // Logic to display employees based on selected institution
        if ($selectedInstitutionId) {
            $selectedInstitution = Institution::find($selectedInstitutionId);
            if ($selectedInstitution) {
                $selectedInstitutionName = $selectedInstitution->name;
                $employeesQuery = $selectedInstitution->employees();
                if ($searchEmployee) {
                    $employeesQuery->where(function($q) use ($searchEmployee) {
                        $q->where('name', 'like', '%' . $searchEmployee . '%')
                          ->orWhere('nip', 'like', '%' . $searchEmployee . '%');
                    });
                }
                $employees = $employeesQuery->get();
            }
        } elseif ($institutions->isNotEmpty()) {
            // If no explicit institution is selected, show employees from the first institution
            $selectedInstitution = $institutions->first();
            $selectedInstitutionId = $selectedInstitution->id; 
            $selectedInstitutionName = $selectedInstitution->name;
            $employees = $selectedInstitution->employees()->get();
        }

        return view('admin.institutions', compact('institutions', 'employees', 'selectedInstitutionId', 'selectedInstitutionName'));
    }

    /**
     * Menyimpan instansi baru.
     */
    public function storeInstitution(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
            'description' => 'nullable|string|max:1000',
        ]);

        Institution::create($request->all());

        return back()->with('success', 'Instansi berhasil ditambahkan!');
    }

    /**
     * Memperbarui instansi yang ada.
     */
    public function updateInstitution(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('institutions')->ignore($institution->id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $institution->update($request->all());

        return back()->with('success', 'Instansi berhasil diperbarui!');
    }

    /**
     * Menghapus instansi.
     */
    public function destroyInstitution(Institution $institution)
    {
        // Sebelum menghapus instansi, pastikan tidak ada pegawai yang terkait
        if ($institution->employees()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus instansi karena masih ada pegawai yang terkait.');
        }

        $institution->delete();

        return back()->with('success', 'Instansi berhasil dihapus!');
    }

    /**
     * Menyimpan pegawai baru.
     */
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id', 
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:255|unique:employees,nip',
            'golongan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone_number' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $employeeData = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $employeeData['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        Employee::create($employeeData);

        return back()->with('success', 'Pegawai berhasil ditambahkan!');
    }

    /**
     * Memperbarui data pegawai.
     */
    public function updateEmployee(Request $request, Employee $employee)
    {
        $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'nip' => ['required', 'string', 'max:255', Rule::unique('employees')->ignore($employee->id)],
            'golongan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees')->ignore($employee->id)],
            'phone_number' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $employeeData = $request->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($employee->profile_picture_path) {
                Storage::disk('public')->delete($employee->profile_picture_path);
            }
            // Simpan foto baru
            $employeeData['profile_picture_path'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee->update($employeeData);

        return back()->with('success', 'Data pegawai berhasil diperbarui!');
    }

    /**
     * Menghapus pegawai (soft delete) dan memindahkannya ke riwayat.
     */
    public function destroyEmployee(Employee $employee)
    {
        // Simpan data pegawai ke tabel history_deleted_employees sebelum dihapus
        HistoryDeletedEmployee::create([
            'employee_name' => $employee->name,
            'nip' => $employee->nip,
            'last_institution' => $employee->institution->name ?? 'N/A', 
            'last_jabatan' => $employee->jabatan,
            'golongan' => $employee->golongan,
            'deleted_at' => now(), 
        ]);

        // Hapus file gambar profil jika ada
        if ($employee->profile_picture_path) {
            Storage::disk('public')->delete($employee->profile_picture_path);
        }

        // Hapus semua dokumen terkait pegawai
        foreach ($employee->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }

        // Hapus semua hasil analisis terkait pegawai
        $employee->analysisResults()->delete();

        $employee->delete(); 

        return back()->with('success', 'Pegawai berhasil dihapus dan dipindahkan ke riwayat!');
    }

    /**
     * Menampilkan halaman analisis kompetensi.
     */
    public function analysis()
    {
        $employees = Employee::all();
        $jobRequirements = JobRequirement::all();
        return view('admin.analysis', compact('employees', 'jobRequirements'));
    }

    /**
     * Melakukan analisis kompetensi.
     * Ini adalah contoh sederhana; logika analisis sebenarnya akan lebih kompleks.
     */
    public function performAnalysis(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'job_requirement_id' => 'required|exists:job_requirements,id',
            'employee_documents.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png', 
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $jobRequirement = JobRequirement::findOrFail($request->job_requirement_id);

        // ✅ Cek apakah sudah pernah dianalisis berdasarkan job_requirement dan tidak ada dokumen baru
        $latestAnalysis = AnalysisResult::where('employee_id', $employee->id)
                                ->where('job_requirement_id', $jobRequirement->id)
                                ->latest('analysis_date')
                                ->first();

        $hasNewDocuments = $request->hasFile('employee_documents');

        if ($latestAnalysis && !$hasNewDocuments) {
            return back()->with('error', 'Analisis sudah pernah dilakukan dan tidak ada dokumen baru yang ditambahkan.');
        }

        // ✅ Simpan dokumen baru jika ada
        if ($hasNewDocuments) {
            foreach ($request->file('employee_documents') as $file) {
                $filePath = $file->store('employee_documents/' . $employee->id, 'public');
                EmployeeDocument::create([
                    'employee_id' => $employee->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'document_type' => 'Umum',
                ]);
            }
        }

        // ✅ Hitung tingkat kecocokan berkas terhadap syarat jabatan
        $requirements = $jobRequirement->requirements_json ?? [];
        $employeeDocuments = $employee->documents;

        $matchedCount = 0;

        foreach ($requirements as $requirement) {
            foreach ($employeeDocuments as $doc) {
                if (stripos($doc->file_name, $requirement) !== false) {
                    $matchedCount++;
                    break;
                }
            }
        }

        $totalRequirements = count($requirements);
        $matchPercentage = $totalRequirements > 0 
            ? round(($matchedCount / $totalRequirements) * 100)
            : 50;

        // ✅ Tentukan status analisis berdasarkan persentase
        if ($matchPercentage >= 80) {
            $resultStatus = 'Siap Naik Jabatan';
            $details = ['Kompetensi terpenuhi dengan baik.', 'Direkomendasikan untuk promosi.'];
        } elseif ($matchPercentage >= 50) {
            $resultStatus = 'Perlu Pengembangan Lanjutan';
            $details = ['Beberapa syarat belum terpenuhi.', 'Direkomendasikan mengikuti pelatihan tambahan.'];
        } else {
            $resultStatus = 'Perlu Pelatihan Khusus';
            $details = ['Banyak syarat belum terpenuhi.', 'Pelatihan intensif diperlukan.'];
        }

        // ✅ Simpan hasil analisis baru
        AnalysisResult::create([
            'employee_id' => $employee->id,
            'job_requirement_id' => $jobRequirement->id,
            'analysis_date' => now(),
            'result_status' => $resultStatus,
            'analysis_percentage' => $matchPercentage,
            'details_json' => $details,
        ]);

        // Simpan % terakhir di data pegawai
        $employee->update(['analysis_percentage' => $matchPercentage]);

        return back()->with('success', 'Analisis kompetensi berhasil dilakukan!')
                    ->with('analysisResult', [
                        'status' => $resultStatus,
                        'percentage' => $matchPercentage,
                        'details' => $details,
                        'analysis_date' => now()->toDateString()
                    ]);
    }

    /**
     * Menampilkan daftar syarat jabatan.
     */
    public function jobRequirements()
    {
        $requirements = JobRequirement::with('institution')->get()->map(function($req) {
            return (object)[
                'id' => $req->id,
                'job_name' => $req->job_name,
                'institution' => $req->institution ? $req->institution->name : 'Umum', // Tampilkan nama instansi atau 'Umum'
                'institution_id' => $req->institution_id, // Sertakan ID instansi untuk form edit
                'description' => $req->description,
                'details' => $req->requirements_json ?? [] // Pastikan ini selalu array
            ];
        });
        $institutions = Institution::all(); // Untuk dropdown di form
        return view('admin.job-requirements', compact('requirements', 'institutions'));
    }

    /**
     * Menyimpan syarat jabatan baru.
     */
    public function storeJobRequirement(Request $request)
    {
        $request->validate([
            'job_name' => 'required|string|max:255|unique:job_requirements',
            'institution_id' => 'nullable|exists:institutions,id',
            'description' => 'nullable|string|max:1000',
            'requirements' => 'nullable|array', 
            'requirements.*' => 'string|max:255', 
        ]);

        JobRequirement::create([
            'job_name' => $request->job_name,
            'institution_id' => $request->institution_id,
            'description' => $request->description,
            'requirements_json' => $request->requirements, 
        ]);

        return back()->with('success', 'Syarat jabatan berhasil ditambahkan!');
    }

    /**
     * Memperbarui syarat jabatan yang ada.
     */
    public function updateJobRequirement(Request $request, JobRequirement $jobRequirement)
    {
        $request->validate([
            'job_name' => 'required|string|max:255',
            'institution_id' => 'nullable|exists:institutions,id',
            'description' => 'nullable|string|max:1000',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
        ]);

        $jobRequirement->update([
            'job_name' => $request->job_name,
            'institution_id' => $request->institution_id,
            'description' => $request->description,
            'requirements_json' => $request->requirements,
        ]);

        return back()->with('success', 'Syarat jabatan berhasil diperbarui!');
    }

    /**
     * Menghapus syarat jabatan.
     */
    public function destroyJobRequirement(JobRequirement $jobRequirement)
    {
        // Pastikan tidak ada hasil analisis yang terkait dengan syarat jabatan ini sebelum menghapus
        if ($jobRequirement->analysisResults()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus syarat jabatan karena masih ada hasil analisis yang terkait.');
        }

        $jobRequirement->delete();
        return back()->with('success', 'Syarat jabatan berhasil dihapus!');
    }

    /**
     * Menampilkan daftar pelatihan.
     */
    public function trainings()
    {
        $trainings = Training::all();
        // Mengambil daftar jabatan unik dari tabel job_requirements untuk dropdown
        $jobs = JobRequirement::select('job_name')->distinct()->get()->pluck('job_name');
        return view('admin.trainings', compact('trainings', 'jobs'));
    }

    /**
     * Menyimpan pelatihan baru.
     */
    public function storeTraining(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
            'target_jabatan' => 'nullable|array', 
            'training_date' => 'nullable|date',
        ]);

        Training::create([
            'name' => $request->name,
            'details' => $request->details,
            'target_jabatan' => $request->target_jabatan, 
            'training_date' => $request->training_date,
        ]);
        return back()->with('success', 'Pelatihan berhasil ditambahkan!');
    }

    /**
     * Memperbarui pelatihan yang ada.
     */
    public function updateTraining(Request $request, Training $training)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
            'target_jabatan' => 'nullable|array',
            'training_date' => 'nullable|date',
        ]);

        $training->update([
            'name' => $request->name,
            'details' => $request->details,
            'target_jabatan' => $request->target_jabatan,
            'training_date' => $request->training_date,
        ]);
        return back()->with('success', 'Pelatihan berhasil diperbarui!');
    }

    /**
     * Menghapus pelatihan.
     */
    public function destroyTraining(Training $training)
    {
        $training->delete();
        return back()->with('success', 'Pelatihan berhasil dihapus!');
    }

    /**
     * Menampilkan riwayat pegawai yang dihapus (soft delete).
     */
    public function history()
    {
        // Mengambil semua riwayat pegawai yang dihapus, diurutkan berdasarkan waktu penghapusan terbaru
        $deletedEmployees = HistoryDeletedEmployee::orderByDesc('deleted_at')->get();
        return view('admin.history', compact('deletedEmployees'));
    }

    /**
     * Menampilkan halaman pengaturan sistem.
     */
    public function settings()
    {
        $settings = Setting::pluck('value', 'key')->toArray(); 
        // Berikan nilai default jika setting belum ada di DB
        $syncToggle = $settings['sync_data_otomatis'] ?? false;
        $backupToggle = $settings['pencadangan_otomatis'] ?? false;

        $admins = auth()->user()->role === 'superadmin'
        ? User::whereIn('role', ['admin', 'superadmin'])->get()
        : collect(); // kosongkan jika bukan superadmin

        return view('admin.settings', compact('syncToggle', 'backupToggle', 'admins'));
    }

    /**
     * Memperbarui pengaturan sistem.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'sync_data_otomatis' => 'boolean',
            'pencadangan_otomatis' => 'boolean',
        ]);

        // Simpan atau perbarui setiap setting
        Setting::updateOrCreate(
            ['key' => 'sync_data_otomatis'],
            ['value' => $request->has('sync_data_otomatis')] 
        );
        Setting::updateOrCreate(
            ['key' => 'pencadangan_otomatis'],
            ['value' => $request->has('pencadangan_otomatis')]
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,superadmin', // validasi hanya menerima role valid
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun admin berhasil dibuat.');
    }

    public function deleteAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }


    /**
     * Menampilkan halaman profil admin.
     */
    public function profile()
    {
        $adminUser = auth()->user(); 
        return view('admin.profile', compact('adminUser'));
    }

    /**
     * Memperbarui profil admin.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture_path) { 
                Storage::disk('public')->delete($user->profile_picture_path);
            }
            // Simpan gambar baru dan update path di database
            $user->profile_picture_path = $request->file('profile_picture')->store('admin_profile_pictures', 'public');
        }

        $user->save();

        return back()->with('success', 'Profil admin berhasil diperbarui!');
    }
}
