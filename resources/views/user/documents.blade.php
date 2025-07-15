@extends('layouts.user')

@section('title', 'Dokumen Saya')
@section('page_title', 'Dokumen Saya')

@section('content')
<div id="user-documents" class="space-y-6">
    <p class="text-slate-1000">Halaman ini memungkinkan Anda untuk mengelola semua dokumen penting yang terkait dengan profil kepegawaian dan analisis kompetensi Anda. Anda dapat melihat daftar dokumen yang sudah diunggah, mengunggah berkas baru (seperti CV, sertifikat pelatihan, transkrip nilai, atau dokumen pendukung lainnya), dan memastikan semua informasi yang diperlukan untuk analisis kompetensi selalu tersedia. Dokumen yang Anda unggah di sini akan terintegrasi ke dalam proses analisis kompetensi oleh administrator.</p>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Unggah Dokumen Baru</h2>
        <form action="{{ route('user.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="document_file" class="block text-sm font-medium text-slate-700 mb-1">Pilih Berkas untuk Diunggah</label>
                <input type="file" id="document_file" name="document_file" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                <p class="text-xs text-slate-500 mt-1">Ukuran maksimal 5MB. Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG.</p>
            </div>
            <div>
                <label for="document_type" class="block text-sm font-medium text-slate-700 mb-1">Jenis Dokumen (Opsional)</label>
                <input type="text" id="document_type" name="document_type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: CV, Sertifikat Pelatihan, Transkrip Nilai">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Unggah Dokumen</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Daftar Dokumen Anda</h2>
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
                            {{-- Tambahkan tombol hapus dokumen jika diinginkan --}}
                            {{-- <form action="{{ route('user.documents.delete', $document->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form> --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-slate-500 text-center">Anda belum mengunggah dokumen apa pun. Gunakan formulir di atas untuk mulai mengunggah.</p>
        @endif
    </div>
</div>
@endsection
