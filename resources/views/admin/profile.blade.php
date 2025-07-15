@extends('layouts.admin')

@section('title', 'Profil')
@section('page_title', 'Profil')

@section('content')
<div id="profil" class="space-y-6">
    <p class="text-black">Kelola detail profil admin Anda. Di sini Anda dapat memperbarui informasi pribadi, mengubah kata sandi, dan mengganti foto profil Anda.</p>
    <div class="bg-white p-6 rounded-xl shadow-md max-w-3xl mx-auto">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0 flex flex-col items-center space-y-4">
                    <img src="{{ Storage::url($adminUser->profile_picture_path ?? '') ?: 'https://placehold.co/128x128/E2E8F0/475569?text=A' }}" class="rounded-full w-32 h-32 object-cover" alt="Profil Admin">
                    <label for="profile_picture_upload" class="w-full cursor-pointer px-4 py-2 bg-slate-200 text-slate-800 rounded-lg text-sm hover:bg-slate-300 text-center">
                        Upload Foto Baru
                        <input type="file" id="profile_picture_upload" name="profile_picture" class="hidden" accept="image/*">
                    </label>
                </div>
                <div class="flex-1 space-y-4">
                    <h2 class="text-lg font-semibold border-b pb-3">Detail Profil Admin</h2>
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-slate-700">Nama</label>
                        <input type="text" id="admin_name" name="name" value="{{ old('name', $adminUser->name) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50" required>
                    </div>
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-slate-700">Email / Username</label>
                        <input type="email" id="admin_email" name="email" value="{{ old('email', $adminUser->email) }}" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                        <input type="password" id="password" name="password" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="pt-4 border-t space-x-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
