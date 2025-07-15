@extends('layouts.admin')

@section('title', 'Analisis')
@section('page_title', 'Analisis')

@section('content')
<div id="analisis" class="space-y-6">
    <p class="text-black">Lakukan analisis kompetensi pegawai untuk memberikan rekomendasi pengembangan yang personal dan relevan. Pada bagian ini, Anda dapat mengunggah berkas-berkas penting pegawai seperti CV atau sertifikat, kemudian memilih nama pegawai dan jabatan target yang ingin dianalisis. Setelah itu, cukup klik tombol "Mulai Analisis" untuk memicu proses. Hasil analisis akan segera muncul di sisi kanan, memberikan keterangan apakah pegawai tersebut siap untuk naik jabatan atau memerlukan pelatihan lebih lanjut untuk memenuhi kualifikasi. Data hasil analisis ini juga akan terhubung dan memperbarui persentase kompetensi di halaman pegawai serta statistik di dasbor utama, memastikan semua informasi tetap sinkron.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md space-y-4">
            <h2 class="text-lg font-semibold">Formulir Analisis Kompetensi</h2>
            <!-- Form untuk mengunggah berkas dan memilih pegawai/jabatan -->
            <form action="{{ route('admin.analysis.perform') }}" method="POST" enctype="multipart/form-data">
                @csrf <!-- Token CSRF untuk keamanan form -->
                <div>
                    <label for="employee_select" class="block text-sm font-medium text-slate-700 mb-1">Pilih Pegawai</label>
                    <select id="employee_select" name="employee_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @forelse($employees as $employee)
                        <!-- Nilai default jika ada employee_id di URL (dari klik di halaman Instansi) -->
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @empty
                        <option value="">Tidak ada pegawai tersedia</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label for="job_select" class="block text-sm font-medium text-slate-700 mb-1">Jabatan yang Dianalisis</label>
                    <select id="job_select" name="job_requirement_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @forelse($jobRequirements as $jobRequirement) {{-- Variabel diubah menjadi $jobRequirements --}}
                        <option value="{{ $jobRequirement->id }}">{{ $jobRequirement->job_name }}</option> {{-- Mengakses properti yang benar --}}
                        @empty
                        <option value="">Tidak ada jabatan tersedia</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label for="document_upload" class="block text-sm font-medium text-slate-700 mb-1">Upload Berkas Pegawai (Opsional, Max 5MB per file)</label>
                    <!-- Input untuk mengunggah multiple files -->
                    <input type="file" id="document_upload" name="employee_documents[]" multiple class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    <p class="text-xs text-slate-500 mt-1">Dukungan format: PDF, DOC, DOCX, JPG, JPEG, PNG. Ukuran maksimal: 5MB per berkas.</p>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Mulai Analisis</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold mb-4">Hasil Analisis</h2>
            <!-- Menampilkan hasil analisis jika tersedia di session (setelah form submit) -->
            @if (session('analysisResult'))
                <div class="p-4 border rounded-lg {{ session('analysisResult.percentage') >= 80 ? 'bg-green-50 border-green-300 text-green-800' : (session('analysisResult.percentage') >= 50 ? 'bg-yellow-50 border-yellow-300 text-yellow-800' : 'bg-red-50 border-red-300 text-red-800') }}">
                    <p class="font-semibold text-lg mb-2">Status: {{ session('analysisResult.status') }}</p>
                    <p class="text-sm mb-2">Persentase Kompetensi: <span class="font-bold">{{ session('analysisResult.percentage') }}%</span></p>
                    <p class="text-sm">Tanggal Analisis: {{ \Carbon\Carbon::parse(session('analysisResult.analysis_date'))->format('d F Y') }}</p>
                    @if(!empty(session('analysisResult.details')))
                        <p class="text-sm font-medium mt-3">Detail Rekomendasi:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach (session('analysisResult.details') as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @else
                <div class="text-center p-8 border-2 border-dashed border-slate-300 rounded-lg">
                    <p class="text-slate-500">Hasil akan ditampilkan di sini setelah proses analisis selesai. Pastikan untuk memilih pegawai, jabatan, dan mengunggah berkas yang relevan untuk hasil yang akurat.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
