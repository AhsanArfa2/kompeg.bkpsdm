@extends('layouts.user')

@section('title', 'Riwayat Saya')
@section('page_title', 'Riwayat Saya')

@section('content')
<div id="user-my-history" class="space-y-6">
    <p class="text-slate-1000">Halaman ini menyimpan semua riwayat penting yang terkait dengan profil kompetensi Anda. Di sini Anda dapat meninjau hasil analisis kompetensi Anda yang pernah dilakukan di masa lalu, memberikan gambaran tentang perkembangan Anda. Selain itu, Anda juga dapat melihat daftar berkas atau dokumen yang telah Anda unggah ke sistem, memastikan semua referensi penting tersimpan dengan aman dan mudah diakses.</p>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Riwayat Hasil Analisis Kompetensi</h2>
        @if($analysisResults->isNotEmpty())
        <div class="space-y-4">
            @foreach($analysisResults as $result)
            @php
                // Tentukan warna berdasarkan result_status atau analysis_percentage
                $cardClass = '';
                if ($result->result_status === 'Siap Naik Jabatan') {
                    $cardClass = 'bg-green-50 border-green-300 text-green-800';
                } elseif ($result->result_status === 'Perlu Pelatihan') {
                    $cardClass = 'bg-red-50 border-red-300 text-red-800';
                } else { // Termasuk 'Perlu Pengembangan Lanjutan' atau 'Status Cukup'
                    $cardClass = 'bg-yellow-50 border-yellow-300 text-yellow-800';
                }
            @endphp
            <div class="p-4 border rounded-lg {{ $cardClass }}">
                <p class="font-semibold text-base mb-1">Status: {{ $result->result_status }}</p>
                <p class="text-sm mb-1">Persentase: <span class="font-bold">{{ $result->analysis_percentage ?? 'N/A' }}%</span></p>
                <p class="text-sm mb-1">Tanggal Analisis: {{ $result->analysis_date->format('d F Y') }}</p>
                <p class="text-sm">Jabatan Target: {{ $result->jobRequirement->job_name ?? 'Tidak diketahui' }}</p>
                @if(!empty($result->details_json))
                    <p class="text-sm font-medium mt-2">Rekomendasi:</p>
                    <ul class="list-disc list-inside text-sm pl-5">
                        @foreach ($result->details_json as $detail)
                            <li>{{ $detail }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="text-slate-500 text-center">Belum ada riwayat hasil analisis kompetensi untuk Anda.</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Riwayat Dokumen yang Diunggah</h2>
        @if($employeeDocuments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama File</th>
                        <th class="p-4">Jenis Dokumen</th>
                        <th class="p-4">Tanggal Unggah</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employeeDocuments as $document)
                    <tr class="border-b">
                        <td class="p-4">{{ $document->file_name }}</td>
                        <td class="p-4">{{ $document->document_type ?? '-' }}</td>
                        <td class="p-4">{{ $document->created_at->format('d F Y H:i') }}</td>
                        <td class="p-4">
                            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-slate-500 text-center">Belum ada dokumen yang diunggah oleh Anda.</p>
        @endif
    </div>
</div>
@endsection
