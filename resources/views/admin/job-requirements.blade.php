@extends('layouts.admin')

@section('title', 'Syarat Jabatan')
@section('page_title', 'Syarat Jabatan')

@section('content')
<div id="syarat-jabatan" class="space-y-6">
    <p class="text-black">Kelola syarat dan kualifikasi yang dibutuhkan untuk setiap jabatan di berbagai instansi. Halaman ini memungkinkan Anda untuk menambah definisi jabatan baru, mengubah kriteria yang sudah ada, atau menghapus syarat jabatan yang tidak lagi relevan. Setiap syarat jabatan dapat memiliki deskripsi umum dan daftar kriteria spesifik (misalnya, tingkat pendidikan, pengalaman kerja minimum, sertifikasi wajib). Memastikan syarat jabatan selalu diperbarui sangat penting untuk menjaga akurasi proses analisis kompetensi sesuai dengan standar dan kebutuhan organisasi yang berlaku.</p>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar Syarat Jabatan</h2>
            <button onclick="showAddJobRequirementModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah Syarat Jabatan</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($requirements as $req)
            <div class="border p-4 rounded-lg bg-slate-50">
                <h3 class="font-semibold">{{ $req->job_name }}</h3>
                <p class="text-sm text-slate-500 mb-2">Instansi: {{ $req->institution }}</p>
                <p class="text-sm text-slate-600 mb-2">Deskripsi: {{ $req->description }}</p>
                @if(!empty($req->details))
                <h4 class="font-medium text-sm mt-2">Kriteria Spesifik:</h4>
                <ul class="text-sm list-disc list-inside text-slate-600 space-y-1">
                    @foreach($req->details as $detail)
                    <li>{{ $detail }}</li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-slate-500 mt-2">Belum ada kriteria spesifik yang ditambahkan.</p>
                @endif
                <div class="mt-4 text-right space-x-2">
                    {{-- Pastikan institution_id dilewatkan dengan benar --}}
                    <button class="text-sm text-blue-600 hover:underline" onclick="showEditJobRequirementModal({{ $req->id }}, '{{ $req->job_name }}', {{ $req->institution_id ?? 'null' }}, '{{ $req->description }}', {{ json_encode($req->details) }})">Edit</button>
                    <form action="{{ route('admin.job-requirements.destroy', $req->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus syarat jabatan {{ $req->job_name }}? Ini akan memengaruhi analisis kompetensi yang akan datang.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-slate-500 col-span-full text-center">Tidak ada syarat jabatan yang terdaftar. Gunakan tombol "Tambah Syarat Jabatan" untuk mulai mendefinisikan kriteria.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Syarat Jabatan -->
<div id="jobRequirementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="w-full max-w-md max-h-[90vh] bg-white rounded-xl shadow-lg overflow-y-auto p-6">
        <h2 id="jobRequirementModalTitle" class="text-xl font-semibold mb-4">Tambah Syarat Jabatan</h2>
        <form id="jobRequirementForm" method="POST" action="{{ route('admin.job-requirements.store') }}">
            @csrf
            <input type="hidden" name="_method" id="jobRequirementFormMethod" value="POST">
            <input type="hidden" name="job_requirement_id" id="jobRequirementId">

            <div class="mb-4">
                <label for="jobName" class="block text-sm font-medium text-slate-700 mb-1">Nama Jabatan</label>
                <input type="text" id="jobName" name="job_name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="institutionId" class="block text-sm font-medium text-slate-700 mb-1">Instansi (Opsional)</label>
                <select id="institutionId" name="institution_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Instansi (Umum)</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Pilih instansi jika syarat jabatan ini spesifik untuk instansi tertentu.</p>
            </div>
            <div class="mb-4">
                <label for="jobDescription" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="jobDescription" name="description" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <p class="text-xs text-slate-500 mt-1">Berikan deskripsi singkat tentang jabatan ini.</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Kriteria/Syarat Spesifik (Satu per baris)</label>
                <div id="requirementsContainer">
                    <!-- Bidang input kriteria akan ditambahkan di sini -->
                    <input type="text" name="requirements[]" class="w-full px-4 py-2 border border-slate-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Pendidikan S1 Kependidikan">
                </div>
                <button type="button" onclick="addRequirementField()" class="text-sm text-blue-600 hover:underline mt-1">+ Tambah Kriteria</button>
                <p class="text-xs text-slate-500 mt-1">Masukkan setiap kriteria kompetensi atau syarat dalam baris terpisah.</p>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideJobRequirementModal()" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Menampilkan modal untuk menambah syarat jabatan baru.
     * Mengatur ulang form ke keadaan kosong dan default.
     */
    function showAddJobRequirementModal() {
        document.getElementById('jobRequirementModalTitle').textContent = 'Tambah Syarat Jabatan Baru';
        document.getElementById('jobRequirementForm').action = '{{ route('admin.job-requirements.store') }}';
        document.getElementById('jobRequirementFormMethod').value = 'POST';
        document.getElementById('jobRequirementId').value = '';
        document.getElementById('jobName').value = '';
        document.getElementById('institutionId').value = ''; // Reset dropdown instansi
        document.getElementById('jobDescription').value = '';
        const requirementsContainer = document.getElementById('requirementsContainer');
        requirementsContainer.innerHTML = '<input type="text" name="requirements[]" class="w-full px-4 py-2 border border-slate-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Pendidikan S1 Kependidikan">';
        document.getElementById('jobRequirementModal').classList.remove('hidden');
    }

    /**
     * Menampilkan modal untuk mengedit syarat jabatan yang sudah ada.
     * Mengisi form dengan data yang relevan dan mengatur aksi form ke rute update.
     */
    function showEditJobRequirementModal(id, jobName, institutionId, description, requirements) {
        document.getElementById('jobRequirementModalTitle').textContent = 'Edit Syarat Jabatan';
        document.getElementById('jobRequirementForm').action = '{{ url('admin/syarat-jabatan') }}/' + id;
        document.getElementById('jobRequirementFormMethod').value = 'PUT';
        document.getElementById('jobRequirementId').value = id;
        document.getElementById('jobName').value = jobName;
        document.getElementById('institutionId').value = institutionId || ''; // Pilih instansi di dropdown
        document.getElementById('jobDescription').value = description;

        const requirementsContainer = document.getElementById('requirementsContainer');
        requirementsContainer.innerHTML = ''; // Hapus bidang yang ada sebelum mengisi ulang

        // Isi bidang kriteria dari data yang ada
        if (requirements && requirements.length > 0) {
            requirements.forEach(function(req) {
                requirementsContainer.innerHTML += '<input type="text" name="requirements[]" class="w-full px-4 py-2 border border-slate-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="' + req + '">';
            });
        } else {
             // Jika tidak ada kriteria, tambahkan satu bidang kosong sebagai placeholder
             requirementsContainer.innerHTML = '<input type="text" name="requirements[]" class="w-full px-4 py-2 border border-slate-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Pendidikan S1 Kependidikan">';
        }

        document.getElementById('jobRequirementModal').classList.remove('hidden');
    }

    /**
     * Menyembunyikan modal syarat jabatan.
     */
    function hideJobRequirementModal() {
        document.getElementById('jobRequirementModal').classList.add('hidden');
    }

    /**
     * Menambahkan bidang input baru untuk kriteria/syarat tambahan pada form.
     */
    function addRequirementField() {
        const requirementsContainer = document.getElementById('requirementsContainer');
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'requirements[]'; // Menggunakan array notation untuk input multiple
        newInput.className = 'w-full px-4 py-2 border border-slate-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
        newInput.placeholder = 'Kriteria baru';
        requirementsContainer.appendChild(newInput);
    }
</script>
@endsection
