@extends('layouts.admin')

@section('title', 'Beranda')
@section('page_title', 'Beranda')

@section('content')
<div id="beranda" class="space-y-6">
    <p class="text-black">Selamat datang di dasbor admin BKPSDM Bulukumba. Bagian ini menyediakan tinjauan cepat tentang metrik kepegawaian kunci dalam sistem. Anda dapat melihat jumlah total pegawai yang terdaftar, berapa banyak yang memerlukan intervensi pelatihan, dan berapa banyak yang telah diidentifikasi siap untuk peluang promosi jabatan. Semua data ini disajikan secara sekilas melalui kartu statistik yang jelas dan visualisasi grafik yang intuitif untuk membantu Anda memahami tren dan kebutuhan secara efisien.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                <!-- Ikon untuk Jumlah Pegawai -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Jumlah Pegawai Terdaftar</p>
                <p class="text-2xl font-bold">{{ $totalEmployees }}</p>
                <p class="text-xs text-slate-500 mt-1">Total seluruh data pegawai aktif dalam sistem.</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="p-3 bg-yellow-100 rounded-full text-yellow-600">
                <!-- Ikon untuk Perlu Pelatihan -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Pegawai Perlu Pelatihan</p>
                <p class="text-2xl font-bold">{{ $employeesNeedingTraining }}</p>
                <p class="text-xs text-slate-500 mt-1">Pegawai yang hasil analisisnya menunjukkan kebutuhan pelatihan.</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="p-3 bg-green-100 rounded-full text-green-600">
                <!-- Ikon untuk Siap Naik Jabatan -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award">
                    <circle cx="12" cy="8" r="7"/>
                    <path d="M8.21 13.89 7 22l5-3 5 3-1.21-8.11"/>
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Pegawai Siap Naik Jabatan</p>
                <p class="text-2xl font-bold">{{ $employeesReadyForPromotion }}</p>
                <p class="text-xs text-slate-500 mt-1">Pegawai dengan kompetensi tinggi dan siap untuk promosi.</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Distribusi Status Kompetensi Pegawai</h2>
        <p class="text-slate-600 mb-4">Garafik data Pegawai</p>
        <div class="chart-container">
            <canvas id="pegawaiChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Memastikan elemen canvas ada dan mendapatkan konteks 2D-nya
        const ctx = document.getElementById('pegawaiChart').getContext('2d');
        // Membuat instance Chart.js dan menyimpannya di window untuk menghindari duplikasi
        window.pegawaiChartInstance = new Chart(ctx, {
            type: 'doughnut', // Jenis grafik: donat
            data: {
                // Label untuk setiap segmen grafik, diambil dari data yang dilewatkan oleh controller
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Status Pegawai',
                    // Data numerik untuk setiap segmen
                    data: @json($chartData['data']),
                    // Warna latar belakang untuk setiap segmen, memberikan visualisasi yang berbeda
                    backgroundColor: @json($chartData['backgroundColor']),
                    borderColor: '#ffffff', // Warna border antar segmen
                    borderWidth: 4, // Lebar border
                    hoverOffset: 8 // Offset saat kursor diarahkan ke segmen
                }]
            },
            options: {
                responsive: true, // Membuat grafik responsif terhadap ukuran kontainer
                maintainAspectRatio: false, // Penting! Memungkinkan Chart.js mengabaikan rasio aspek canvas,
                                           // sehingga ukuran grafik dapat dikontrol sepenuhnya oleh CSS kontainer.
                cutout: '70%', // Ukuran lubang di tengah donat
                plugins: {
                    legend: {
                        position: 'bottom', // Posisi legenda di bagian bawah grafik
                        labels: {
                            padding: 20, // Padding antar label legenda
                            boxWidth: 12, // Lebar kotak warna di samping label
                            font: {
                                size: 14 // Ukuran font label legenda
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Callback kustom untuk format tooltip
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' Pegawai'; // Menambahkan jumlah pegawai
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
