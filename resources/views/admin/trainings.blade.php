@extends('layouts.admin')

@section('title', 'Pelatihan')
@section('page_title', 'Pelatihan')

@section('content')
<div id="pelatihan" class="space-y-6">
    <p class="text-black">Kelola semua program pelatihan yang tersedia dan relevan untuk pengembangan kompetensi pegawai. Halaman ini memungkinkan Anda untuk menambah program pelatihan baru dengan detail lengkap, memperbarui informasi pelatihan yang sudah ada jika ada perubahan, atau menghapus program yang sudah tidak relevan lagi. Setiap program pelatihan dapat dikaitkan dengan jabatan-jabatan spesifik yang memerlukan pengembangan tersebut, memastikan bahwa rekomendasi pelatihan yang diberikan kepada pegawai sangat sesuai dengan kebutuhan mereka dan tujuan organisasi. Informasi ini merupakan bagian krusial dalam siklus rekomendasi pengembangan kompetensi.</p>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar Pelatihan</h2>
            <button onclick="showAddTrainingModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah Pelatihan</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama Pelatihan</th>
                        <th class="p-4">Detail Pelatihan</th>
                        <th class="p-4">Jabatan Target</th>
                        <th class="p-4">Tanggal Pelaksanaan</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $training)
                    <tr class="border-b">
                        <td class="p-4 font-medium">{{ $training->name }}</td>
                        <td class="p-4">{{ $training->details }}</td>
                        <td class="p-4">{{ implode(', ', $training->target_jabatan ?? []) }}</td>
                        <td class="p-4">{{ $training->training_date}}</td>
                        <td class="p-4 space-x-2">
                            <button class="text-blue-600 hover:underline" onclick="showEditTrainingModal({{ $training->id }}, '{{ $training->name }}', '{{ $training->details }}', {{ json_encode($training->target_jabatan) }}, '{{ $training->training_date }}')">Edit</button>
                            <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelatihan {{ $training->name }}? Ini akan memengaruhi rekomendasi pelatihan yang akan datang.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-slate-500">Belum ada pelatihan yang terdaftar. Gunakan tombol "Tambah Pelatihan" untuk mulai mendefinisikan program.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Pelatihan -->
<div id="trainingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
        <h2 id="trainingModalTitle" class="text-xl font-semibold mb-4">Tambah Pelatihan</h2>
        <form id="trainingForm" method="POST" action="{{ route('admin.trainings.store') }}">
            @csrf
            <input type="hidden" name="_method" id="trainingFormMethod" value="POST">
            <input type="hidden" name="training_id" id="trainingId">

            <div class="mb-4">
                <label for="trainingName" class="block text-sm font-medium text-slate-700 mb-1">Nama Pelatihan</label>
                <input type="text" id="trainingName" name="name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="trainingDetails" class="block text-sm font-medium text-slate-700 mb-1">Detail Pelatihan</label>
                <textarea id="trainingDetails" name="details" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <p class="text-xs text-slate-500 mt-1">Berikan deskripsi singkat atau silabus pelatihan.</p>
            </div>
            <div class="mb-4">
                <label for="targetJabatan" class="block text-sm font-medium text-slate-700 mb-1">Jabatan yang Memerlukan (Pilih Beberapa)</label>
                <select id="targetJabatan" name="target_jabatan[]" multiple class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-32">
                    @foreach($jobs as $job)
                        <option value="{{ $job }}">{{ $job }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Tekan `Ctrl` (Windows) atau `Cmd` (Mac) untuk memilih lebih dari satu jabatan.</p>
            </div>
            <div class="mb-4">
                <label for="trainingDate" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pelaksanaan</label>
                {{-- Pastikan nama input adalah 'training_date' --}}
                <input type="date" id="trainingDate" name="training_date" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideTrainingModal()" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Menampilkan modal untuk menambah pelatihan baru.
     * Mengatur ulang form ke keadaan kosong dan default.
     */
    function showAddTrainingModal() {
        document.getElementById('trainingModalTitle').textContent = 'Tambah Pelatihan Baru';
        document.getElementById('trainingForm').action = '{{ route('admin.trainings.store') }}';
        document.getElementById('trainingFormMethod').value = 'POST';
        document.getElementById('trainingId').value = '';
        document.getElementById('trainingName').value = '';
        document.getElementById('trainingDetails').value = '';
        // Membatalkan pilihan pada semua opsi di multi-select target jabatan
        Array.from(document.getElementById('targetJabatan').options).forEach(option => {
            option.selected = false;
        });
        document.getElementById('trainingDate').value = ''; // Mengosongkan tanggal
        document.getElementById('trainingModal').classList.remove('hidden');
    }

    /**
     * Menampilkan modal untuk mengedit pelatihan yang sudah ada.
     * Mengisi form dengan data pelatihan yang dipilih dan mengatur aksi form ke rute update.
     */
    function showEditTrainingModal(id, name, details, targetJabatan, trainingDate) {
        document.getElementById('trainingModalTitle').textContent = 'Edit Pelatihan';
        document.getElementById('trainingForm').action = '{{ url('admin/pelatihan') }}/' + id;
        document.getElementById('trainingFormMethod').value = 'PUT';
        document.getElementById('trainingId').value = id;
        document.getElementById('trainingName').value = name;
        document.getElementById('trainingDetails').value = details;

        // Memilih opsi di multi-select target jabatan berdasarkan data yang ada
        const targetJabatanSelect = document.getElementById('targetJabatan');
        Array.from(targetJabatanSelect.options).forEach(option => {
            // Periksa apakah `targetJabatan` adalah array dan apakah nilai opsi ada di dalamnya
            option.selected = targetJabatan && Array.isArray(targetJabatan) && targetJabatan.includes(option.value);
        });
 
        // Pastikan format tanggal sesuai untuk input type="date" (YYYY-MM-DD)
        document.getElementById('trainingDate').value = trainingDate;
        document.getElementById('trainingModal').classList.remove('hidden');
    }

    /**
     * Menyembunyikan modal pelatihan.
     */
    function hideTrainingModal() {
        document.getElementById('trainingModal').classList.add('hidden');
    }
</script>
@endsection
