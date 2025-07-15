@extends('layouts.admin')

@section('title', 'Setting')
@section('page_title', 'Setting')

@section('content')
<div id="setting" class="space-y-10"> {{-- Tambahkan space-y-10 agar antar card punya jarak --}}
    {{-- Deskripsi halaman --}}
    <p class="text-black">
        Konfigurasi pengaturan sistem dan kelola akun administrator. Anda dapat mengaktifkan atau menonaktifkan fitur sinkronisasi dan pencadangan otomatis, serta menambah atau mengubah akses untuk admin lain.
    </p>

    {{-- Card Pengaturan Sistem --}}
    <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto space-y-6">
        <h2 class="text-lg font-semibold border-b pb-3">Pengaturan Sistem</h2>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="flex items-center justify-between mb-4">
                <label for="sync-toggle" class="font-medium">Sinkronisasi Data Otomatis</label>
                <input type="checkbox" name="sync_data_otomatis" id="sync-toggle" class="toggle-checkbox" value="1" {{ $syncToggle ? 'checked' : '' }}/>
            </div>
            <div class="flex items-center justify-between mb-6">
                <label for="backup-toggle" class="font-medium">Pencadangan Otomatis</label>
                <input type="checkbox" name="pencadangan_otomatis" id="backup-toggle" class="toggle-checkbox" value="1" {{ $backupToggle ? 'checked' : '' }}/>
            </div>
            <div class="pt-4 border-t space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                <button type="button" onclick="alert('Fitur reset default belum diimplementasikan.')" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Reset Default</button>
            </div>
        </form>
    </div>

    @can('isSuperAdmin')
        <!-- Form Tambah Admin Baru -->
        <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto space-y-6">
            <h2 class="text-lg font-semibold border-b pb-3">Tambah Akun Admin Baru</h2>
            <form action="{{ route('admin.create-admin') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block font-medium">Nama</label>
                    <input type="text" name="name" required class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-4">
                    <label for="email" class="block font-medium">Email</label>
                    <input type="email" name="email" required class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-4 relative">
                    <label for="password" class="block font-medium">Password</label>
                    <input id="newAdminPassword" type="password" name="password" required class="w-full border rounded px-3 py-2 pr-12 mt-1">
                    <button type="button" id="toggleNewAdminPassword" class="absolute inset-y-12 right-3 flex items-center text-slate-700 focus:outline-none cursor-pointer">
                        <ion-icon name="eye-outline" id="newAdminEyeIcon" class="text-xl"></ion-icon>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="role" class="block font-medium">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2 mt-1" required>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="pt-4 border-t">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Buat Admin</button>
                </div>
            </form>
        </div>

        <!-- Daftar Admin -->
        <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto space-y-4 mt-8">
            <h2 class="text-lg font-semibold border-b pb-3">Daftar Admin</h2>
            <ul class="divide-y">
                @foreach($admins as $admin)
                <li class="py-2 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $admin->name }}</p>
                        <p class="text-sm text-gray-600">{{ $admin->email }} - {{ $admin->role }}</p>
                    </div>
                    @if($admin->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.delete-admin', $admin->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus admin ini?')" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    @endcan

    <script>
        const toggleNewAdminPassword = document.getElementById('toggleNewAdminPassword');
        const newAdminPasswordInput = document.getElementById('newAdminPassword');
        const newAdminEyeIcon = document.getElementById('newAdminEyeIcon');

        toggleNewAdminPassword.addEventListener('click', function () {
            const isPassword = newAdminPasswordInput.type === 'password';
            newAdminPasswordInput.type = isPassword ? 'text' : 'password';
            newAdminEyeIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
        });
    </script>

@endsection
