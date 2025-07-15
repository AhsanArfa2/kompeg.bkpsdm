@extends('layouts.user')

@section('title', 'Instansi Saya')
@section('page_title', 'Instansi Saya')

@section('content')
<div id="user-my-institution" class="space-y-6">
    <p class="text-slate-1000">Halaman ini menyajikan informasi detail mengenai instansi tempat Anda bernaung, serta daftar lengkap rekan-rekan pegawai yang berada dalam instansi yang sama. Anda dapat melihat deskripsi instansi dan daftar pegawai untuk memahami struktur organisasi di lingkungan kerja Anda.</p>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Detail Instansi Anda</h2>
        @if($institution)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-700">
            <div>
                <p class="font-medium">Nama Instansi:</p>
                <p>{{ $institution->name }}</p>
            </div>
            <div>
                <p class="font-medium">Deskripsi:</p>
                <p>{{ $institution->description ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium">Total Pegawai di Instansi:</p>
                <p>{{ $institution->employees->count() }}</p>
            </div>
        </div>
        @else
        <p class="text-slate-500 text-center">Data instansi Anda belum terhubung. Silakan hubungi administrator.</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Daftar Rekan Pegawai di Instansi yang Sama</h2>
        @if($employeesInSameInstitution->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama Pegawai</th>
                        <th class="p-4">NIP</th>
                        <th class="p-4">Jabatan</th>
                        <th class="p-4">Golongan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employeesInSameInstitution as $otherEmployee)
                    <tr class="border-b">
                        <td class="p-4">{{ $otherEmployee->name }}</td>
                        <td class="p-4">{{ $otherEmployee->nip }}</td>
                        <td class="p-4">{{ $otherEmployee->jabatan }}</td>
                        <td class="p-4">{{ $otherEmployee->golongan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-slate-500 text-center">Tidak ada rekan pegawai lain yang terdaftar di instansi Anda saat ini.</p>
        @endif
    </div>
</div>
@endsection