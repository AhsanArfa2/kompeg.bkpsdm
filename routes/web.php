    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\UserController;
    use Illuminate\Support\Facades\Auth;
    // Tidak perlu lagi mengimpor LoginController atau kontroler Auth lainnya secara eksplisit di sini,
    // karena Auth::routes() akan menanganinya dari App\Http\Controllers\Auth\

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute ini
    | dimuat oleh RouteServiceProvider dan semuanya akan ditetapkan ke grup middleware "web".
    | Buat sesuatu yang hebat!
    |
    */

    // Rute dasar untuk mengarahkan ke halaman login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Mendaftarkan semua rute otentikasi standar Laravel (login, register, logout, reset password)
    // Ini akan menggunakan kontroler yang dibuat oleh laravel/ui di App\Http\Controllers\Auth\
    Auth::routes();

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    // Rute dashboard default Laravel UI. Kita akan menimpanya dengan logika role-based di RedirectIfAuthenticated.
    // Jika Anda ingin mempertahankan default /home, Anda bisa biarkan ini,
    // tetapi RedirectIfAuthenticated akan tetap mengarahkan sesuai peran.
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


    // Grup rute untuk area Admin
    // Rute-rute di sini hanya bisa diakses oleh pengguna yang sudah login DAN memiliki peran 'admin'.
    Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/instansi', [AdminController::class, 'institutions'])->name('institutions');
        Route::post('/instansi', [AdminController::class, 'storeInstitution'])->name('institutions.store');
        Route::put('/instansi/{institution}', [AdminController::class, 'updateInstitution'])->name('institutions.update');
        Route::delete('/instansi/{institution}', [AdminController::class, 'destroyInstitution'])->name('institutions.destroy');

        Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
        Route::put('/employees/{employee}', [AdminController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{employee}', [AdminController::class, 'destroyEmployee'])->name('employees.destroy');

        Route::get('/analisis', [AdminController::class, 'analysis'])->name('analysis');
        Route::post('/analisis', [AdminController::class, 'performAnalysis'])->name('analysis.perform');

        Route::get('/syarat-jabatan', [AdminController::class, 'jobRequirements'])->name('job-requirements');
        Route::post('/syarat-jabatan', [AdminController::class, 'storeJobRequirement'])->name('job-requirements.store');
        Route::put('/syarat-jabatan/{jobRequirement}', [AdminController::class, 'updateJobRequirement'])->name('job-requirements.update');
        Route::delete('/syarat-jabatan/{jobRequirement}', [AdminController::class, 'destroyJobRequirement'])->name('job-requirements.destroy');

        Route::get('/pelatihan', [AdminController::class, 'trainings'])->name('trainings');
        Route::post('/pelatihan', [AdminController::class, 'storeTraining'])->name('trainings.store');
        Route::put('/pelatihan/{training}', [AdminController::class, 'updateTraining'])->name('trainings.update');
        Route::delete('/pelatihan/{training}', [AdminController::class, 'destroyTraining'])->name('trainings.destroy');

        Route::get('/riwayat', [AdminController::class, 'history'])->name('history');

        Route::get('/setting', [AdminController::class, 'settings'])->name('settings');
        Route::post('/setting', [AdminController::class, 'updateSettings'])->name('settings.update');

        // Khusus Superadmin
        Route::middleware(['role:superadmin'])->group(function () {
            Route::post('/create-admin', [AdminController::class, 'createAdmin'])->name('create-admin');
            Route::delete('/delete-admin/{user}', [AdminController::class, 'deleteAdmin'])->name('delete-admin');
        });

        Route::get('/profil', [AdminController::class, 'profile'])->name('profile');
        Route::post('/profil', [AdminController::class, 'updateProfile'])->name('profile.update');
    });

    // Grup rute untuk area Pengguna Biasa (Pegawai)
    // Rute-rute di sini hanya bisa diakses oleh pengguna yang sudah login DAN memiliki peran 'pegawai'.
    Route::middleware(['auth', 'role:pegawai'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard'); // Beranda user
        Route::get('/instansi-saya', [UserController::class, 'myInstitution'])->name('my-institution'); // Detail instansi user
        Route::get('/riwayat-saya', [UserController::class, 'myHistory'])->name('my-history'); // Riwayat analisis & dokumen user
        Route::get('/profil-saya', [UserController::class, 'profile'])->name('profile'); // Profil user
        Route::post('/profil-saya', [UserController::class, 'updateProfile'])->name('profile.update'); // Update profil user
        Route::get('/dokumen-saya', [UserController::class, 'documents'])->name('documents'); // Unggah/lihat dokumen
        Route::post('/dokumen-saya/upload', [UserController::class, 'uploadDocument'])->name('documents.upload'); // Upload dokumen
        // Jika Anda ingin menambahkan rute untuk menghapus dokumen oleh user, Anda bisa menambahkannya di sini:
        // Route::delete('/dokumen-saya/{employeeDocument}', [UserController::class, 'deleteDocument'])->name('documents.delete');
    });
    