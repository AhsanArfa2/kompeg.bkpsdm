@extends('layouts.admin')

@section('title', 'Riwayat')
@section('page_title', 'Riwayat')

@section('content')
<div id="riwayat" class="space-y-6">
    <p class="text-black">Halaman ini berisi catatan riwayat pegawai yang datanya telah dihapus dari instansi tersebut.</p>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Riwayat Pegawai Dihapus</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama</th>
                        <th class="p-4">NIP</th>
                        <th class="p-4">Instansi Terakhir</th>
                        <th class="p-4">Jabatan Terakhir</th>
                        <th class="p-4">Golongan</th>
                        <th class="p-4">Tanggal Dihapus</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deletedEmployees as $employee)
                    <tr class="border-b">
                        <td class="p-4">{{ $employee->employee_name }}</td>
                        <td class="p-4">{{ $employee->nip }}</td>
                        <td class="p-4">{{ $employee->last_institution }}</td>
                        <td class="p-4">{{ $employee->last_jabatan }}</td>
                        <td class="p-4">{{ $employee->golongan }}</td>
                        <td class="p-4">{{ $employee->deleted_at->format('d F Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-500">Tidak ada riwayat pegawai yang dihapus.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
