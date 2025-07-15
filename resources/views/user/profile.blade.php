@extends('layouts.user')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div id="user-profile" class="space-y-6">
    <p class="text-slate-1000 mb-6">Kelola detail profil pribadi Anda di sini. Anda dapat memperbarui informasi akun dasar seperti nama dan email, mengubah kata sandi Anda untuk menjaga keamanan, serta mengunggah atau mengganti foto profil Anda. Pastikan semua informasi yang tertera akurat dan terbaru.</p>
    <div class="bg-white p-6 rounded-xl shadow-md max-w-3xl mx-auto">
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0 flex flex-col items-center space-y-4">
                    <!-- Menampilkan foto profil user atau placeholder -->
                    <img src="{{ Auth::user()->profile_picture_path ? Storage::url(Auth::user()->profile_picture_path) : 'https://placehold.co/128x128/E2E8F0/475569?text=P' }}" class="rounded-full w-32 h-32 object-cover" alt="Profil Pegawai">
                    <label for="profile_picture_upload" class="w-full cursor-pointer px-4 py-2 bg-slate-200 text-slate-800 rounded-lg text-sm hover:bg-slate-300 text-center">
                        Upload Foto Baru
                        <input type="file" id="profile_picture_upload" name="profile_picture" class="hidden" accept="image/*">
                    </label>
                    <p class="text-xs text-slate-500 text-center">Ukuran maksimal 2MB. Format: JPG, PNG, GIF, SVG.</p>
                </div>
                <div class="flex-1 space-y-4">
                    <h2 class="text-lg font-semibold border-b pb-3">Detail Akun</h2>
                    <div>
                        <label for="user_name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <input type="text" id="user_name" name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50" required>
                    </div>
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-slate-700">Alamat Email</label>
                        <input type="email" id="user_email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                        <input type="password" id="password" name="password" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-slate-500 mt-1">Kosongkan bidang ini jika Anda tidak ingin mengubah password Anda. Password minimal 8 karakter.</p>
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    @if($employee)
                    <h2 class="text-lg font-semibold border-b pb-3 mt-6">Detail Pegawai (Dikelola Admin)</h2>
                    <p class="text-sm text-slate-600 mb-4">Informasi di bawah ini terkait dengan data kepegawaian Anda yang dikelola oleh Administrator. Jika ada perubahan pada data ini (NIP, Jabatan, Golongan, dll.), silakan hubungi tim BKPSDM.</p>
                    <div>
                        <label for="employee_nip" class="block text-sm font-medium text-slate-700">NIP</label>
                        <input type="text" id="employee_nip" name="nip" value="{{ old('nip', $employee->nip) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-100 cursor-not-allowed" required readonly>
                    </div>
                    <div>
                        <label for="employee_institution" class="block text-sm font-medium text-slate-700">Instansi</label>
                        {{-- Menampilkan nama instansi, tidak dapat diedit langsung oleh user --}}
                        <input type="text" id="employee_institution" value="{{ $employee->institution->name ?? 'Tidak diketahui' }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-100 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="employee_jabatan" class="block text-sm font-medium text-slate-700">Jabatan</label>
                        <input type="text" id="employee_jabatan" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-100 cursor-not-allowed" required readonly>
                    </div>
                    <div>
                        <label for="employee_golongan" class="block text-sm font-medium text-slate-700">Golongan</label>
                        <input type="text" id="employee_golongan" name="golongan" value="{{ old('golongan', $employee->golongan) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-100 cursor-not-allowed" required readonly>
                    </div>
                    <div>
                        <label for="employee_phone" class="block text-sm font-medium text-slate-700">No. Handphone</label>
                        <input type="text" id="employee_phone" name="phone_number" value="{{ old('phone_number', $employee->phone_number) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50">
                    </div>
                    @else
                        <p class="text-slate-500 mt-6 text-center">Data pegawai Anda belum terhubung dengan akun ini. Silakan hubungi administrator.</p>
                    @endif

                    <div class="pt-4 border-t space-x-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan Perubahan Profil</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
