@extends('layouts.user')

@section('title', 'Dasbor Pegawai')
@section('page_title', 'Dasbor Saya')

@section('content')
<div id="user-dashboard" class="space-y-6">
    <p class="text-slate-1000">Selamat datang, {{ $user->name ?? 'Pegawai' }}! Halaman ini adalah pusat informasi personal Anda, dirancang untuk memberikan akses cepat ke detail profil, hasil analisis kompetensi terbaru Anda, serta rekomendasi pelatihan yang relevan. Di sini, Anda dapat memantau perkembangan kompetensi Anda dan mengidentifikasi area untuk pengembangan lebih lanjut. Kami berharap informasi ini membantu Anda dalam perjalanan karier dan pengembangan profesional di BKPSDM Bulukumba.</p>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Informasi Pribadi Pegawai</h2>
        @if($employee)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-700">
            <div>
                <p class="font-medium">Nama Lengkap:</p>
                <p>{{ $employee->name }}</p>
            </div>
            <div>
                <p class="font-medium">NIP:</p>
                <p>{{ $employee->nip }}</p>
            </div>
            <div>
                <p class="font-medium">Instansi:</p>
                <p>{{ $employee->institution->name ?? 'Tidak diketahui' }}</p>
            </div>
            <div>
                <p class="font-medium">Jabatan:</p>
                <p>{{ $employee->jabatan }}</p>
            </div>
            <div>
                <p class="font-medium">Golongan:</p>
                <p>{{ $employee->golongan }}</p>
            </div>
            <div>
                <p class="font-medium">Email:</p>
                <p>{{ $employee->email ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium">No. Handphone:</p>
                <p>{{ $employee->phone_number ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium">Persentase Analisis:</p>
                <div class="w-full bg-slate-200 rounded-full h-2.5 mt-1">
                    <div class="h-2.5 rounded-full {{ $employee->analysis_percentage >= 80 ? 'bg-green-600' : ($employee->analysis_percentage >= 50 ? 'bg-yellow-500' : 'bg-red-600') }}" style="width: {{ $employee->analysis_percentage }}%"></div>
                </div>
                <span class="text-xs text-slate-500">{{ $employee->analysis_percentage }}%</span>
            </div>
        </div>
        @else
        <p class="text-slate-500 text-center">Data pegawai Anda belum terhubung. Silakan hubungi administrator untuk melengkapi data Anda. Setelah data terhubung, informasi lengkap Anda akan ditampilkan di sini.</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Hasil Analisis Kompetensi Terakhir Anda</h2>
        @if($latestAnalysisResult)
            <div class="p-4 border rounded-lg {{ $latestAnalysisResult->analysis_percentage >= 80 ? 'bg-green-50 border-green-300 text-green-800' : ($latestAnalysisResult->analysis_percentage >= 50 ? 'bg-yellow-50 border-yellow-300 text-yellow-800' : 'bg-red-50 border-red-300 text-red-800') }}">
                <p class="font-semibold text-lg mb-2">Status Analisis: {{ $latestAnalysisResult->result_status }}</p>
                <p class="text-sm mb-2">Persentase Kompetensi: <span class="font-bold">{{ $latestAnalysisResult->analysis_percentage }}%</span></p>
                <p class="text-sm">Tanggal Analisis: {{ $latestAnalysisResult->analysis_date->format('d F Y') }}</p>
                @if(!empty($latestAnalysisResult->details_json))
                    <p class="text-sm font-medium mt-3">Detail Rekomendasi:</p>
                    <ul class="list-disc list-inside text-sm pl-5">
                        @foreach ($latestAnalysisResult->details_json as $detail)
                            <li>{{ $detail }}</li>
                        @endforeach
                    </ul>
                @endif
                <p class="text-xs text-slate-600 mt-3">Hasil ini mencerminkan analisis terbaru atas kompetensi Anda.</p>
            </div>
        @else
            <p class="text-slate-500 text-center">Belum ada hasil analisis kompetensi terbaru untuk Anda. Silakan hubungi administrator Anda untuk melakukan analisis.</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Rekomendasi Pelatihan untuk Anda</h2>
        <p class="text-slate-1000">Berdasarkan hasil analisis kompetensi dan kebutuhan jabatan Anda, daftar pelatihan yang direkomendasikan akan muncul di sini. Informasi ini akan membantu Anda dalam memilih program pengembangan yang paling sesuai untuk meningkatkan kemampuan Anda.</p>
        <!-- Di sini akan ada logika untuk menampilkan daftar pelatihan yang relevan
             Misalnya, query pelatihan yang target jabatannya sesuai dengan jabatan pegawai yang login. -->
        <ul class="mt-4 space-y-2 text-slate-700">
            {{-- Contoh dummy rekomendasi --}}
            {{--
            <li>
                <span class="font-medium">Pelatihan Peningkatan Keterampilan Komunikasi</span> - <span class="text-sm text-slate-500">2025-08-15</span>
            </li>
            <li>
                <span class="font-medium">Workshop Pengelolaan Proyek Efektif</span> - <span class="text-sm text-slate-500">2025-09-01</span>
            </li>
            --}}
            @if($latestAnalysisResult && !empty($latestAnalysisResult->details_json))
                @php
                    $trainingRecommendations = collect();
                    // Iterasi melalui detail rekomendasi dari analisis
                    foreach ($latestAnalysisResult->details_json as $detail) {
                        // Coba cocokkan detail rekomendasi dengan nama pelatihan yang ada
                        $matchedTrainings = \App\Models\Training::where('details', 'like', '%' . $detail . '%')
                                            ->orWhereJsonContains('target_jabatan', $employee->jabatan)
                                            ->get(); // Tambahkan kriteria lain jika perlu
                        foreach ($matchedTrainings as $training) {
                            // Hindari duplikasi rekomendasi pelatihan
                            if (!$trainingRecommendations->contains('id', $training->id)) {
                                $trainingRecommendations->push($training);
                            }
                        }
                    }
                @endphp

                @if($trainingRecommendations->isNotEmpty())
                    @foreach($trainingRecommendations as $training)
                        <li>
                            <span class="font-medium">{{ $training->name }}</span> - <span class="text-sm text-slate-500">{{ $training->training_date->format('d F Y') }}</span>
                            <p class="text-xs text-slate-600 mt-1 ml-4">{{ $training->details }}</p>
                        </li>
                    @endforeach
                @else
                    <li class="text-slate-500">Belum ada rekomendasi pelatihan spesifik yang cocok dengan analisis terakhir.</li>
                @endif
            @else
                <li class="text-slate-500">Belum ada rekomendasi pelatihan yang tersedia saat ini. Rekomendasi akan muncul di sini setelah analisis kompetensi Anda dilakukan oleh Administrator sesuai dengan syarat jabatan yang tidak terpenuhi.</li>
            @endif
        </ul>
    </div>
</div>
@endsection