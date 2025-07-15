@extends('layouts.admin')

@section('title', 'Instansi')
@section('page_title', 'Instansi')

@section('content')
<div id="instansi" class="space-y-6">
    <p class="text-black">Kelola semua instansi dan pegawai yang terdaftar di sistem ini. Anda dapat dengan mudah mencari instansi tertentu untuk melihat detailnya atau daftar pegawainya. Memilih satu instansi dari daftar akan secara dinamis memuat semua daftar pegawai yang berada di bawah instansi tersebut, memungkinkan Anda untuk melakukan operasi seperti penambahan, pengeditan, atau penghapusan data pegawai dengan cepat dan efisien. Ini dirancang untuk alur kerja yang intuitif dalam pengelolaan data organisasi.</p>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4">
            <h2 class="text-lg font-semibold">Daftar Instansi</h2>
            <div class="flex flex-col sm:flex-row items-stretch gap-2 w-full sm:w-auto">
                <!-- Form pencarian instansi -->
                <form action="{{ route('admin.institutions') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-2 w-full">
                    <input type="text" name="search_instansi" placeholder="Cari instansi..." class="w-64 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ request('search_instansi') }}">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full sm:w-auto">Cari</button>
                </form>
                <!-- Tombol untuk menampilkan modal tambah instansi baru -->
                <button onclick="showAddInstitutionModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full sm:w-auto">Tambah Instansi</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama Instansi</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4">Jumlah Pegawai</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($institutions as $institution)
                    <tr class="border-b hover:bg-slate-50 cursor-pointer">
                        <!-- Kolom ini akan mengarahkan ke halaman yang sama tetapi dengan parameter instansi_id untuk memuat pegawai terkait -->
                        <td class="p-4 font-medium" onclick="window.location='{{ route('admin.institutions', ['instansi_id' => $institution->id]) }}'">{{ $institution->name }}</td>
                        <td class="p-4" onclick="window.location='{{ route('admin.institutions', ['instansi_id' => $institution->id]) }}'">{{ $institution->description }}</td>
                        <td class="p-4" onclick="window.location='{{ route('admin.institutions', ['instansi_id' => $institution->id]) }}'">{{ $institution->employees_count }}</td>
                        <td class="p-4 space-x-2">
                            <!-- Tombol untuk melihat detail pegawai dalam instansi ini -->
                            <button class="text-blue-600 hover:underline" onclick="event.stopPropagation(); window.location='{{ route('admin.institutions', ['instansi_id' => $institution->id]) }}'">Lihat Pegawai</button>
                            <!-- Tombol untuk menampilkan modal edit instansi -->
                            <button class="text-green-600 hover:underline" onclick="event.stopPropagation(); showEditInstitutionModal({{ $institution->id }}, '{{ $institution->name }}', '{{ $institution->description }}');">Edit</button>
                            <!-- Form untuk menghapus instansi. Menggunakan metode DELETE HTTP. -->
                            <form action="{{ route('admin.institutions.destroy', $institution->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus instansi {{ $institution->name }}? Ini akan menghapus semua pegawai terkait secara permanen.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-slate-500">Tidak ada instansi ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bagian Daftar Pegawai hanya akan ditampilkan jika ada instansi yang dipilih atau ada data pegawai -->
    @if($selectedInstitutionId || $employees->isNotEmpty())
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar Pegawai: <span class="font-normal">{{ $selectedInstitutionName }}</span></h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
                <!-- Form pencarian pegawai dalam instansi terpilih -->
                <form action="{{ route('admin.institutions', ['instansi_id' => $selectedInstitutionId]) }}" method="GET" class="flex items-center space-x-2">
                    <input type="text" name="search_pegawai" placeholder="Cari pegawai..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ request('search_pegawai') }}">
                    <input type="hidden" name="instansi_id" value="{{ $selectedInstitutionId }}">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
                </form>
                <!-- Tombol untuk menambah pegawai baru ke instansi yang sedang dipilih -->
                @if($selectedInstitutionId)
                <button onclick="showAddEmployeeModal({{ $selectedInstitutionId }})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah Pegawai</button>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b bg-slate-50">
                    <tr>
                        <th class="p-4">Nama Pegawai</th>
                        <th class="p-4">NIP</th>
                        <th class="p-4">Golongan</th>
                        <th class="p-4">Jabatan</th>
                        <th class="p-4">Analisis</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr class="border-b">
                        <td class="p-4">{{ $employee->name }}</td>
                        <td class="p-4">{{ $employee->nip }}</td>
                        <td class="p-4">{{ $employee->golongan }}</td>
                        <td class="p-4">{{ $employee->jabatan }}</td>
                        <td class="p-4">
                            <!-- Visualisasi persentase analisis dengan bilah kemajuan -->
                            <div class="w-full bg-slate-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full {{ $employee->analysis_percentage >= 80 ? 'bg-green-600' : ($employee->analysis_percentage >= 50 ? 'bg-yellow-500' : 'bg-red-600') }}" style="width: {{ $employee->analysis_percentage }}%"></div>
                            </div>
                            <span class="text-xs">{{ $employee->analysis_percentage }}%</span>
                        </td>
                        <td class="p-4 space-x-2">
                            <!-- Tombol edit pegawai -->
                            <button class="text-blue-600 hover:underline" onclick="showEditEmployeeModal({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->nip }}', '{{ $employee->golongan }}', '{{ $employee->jabatan }}', '{{ $employee->email }}', '{{ $employee->phone_number }}', '{{ $employee->institution_id }}', '{{ $employee->profile_picture_path }}')">Edit</button>
                            <!-- Form hapus pegawai (soft delete). Data akan dipindahkan ke riwayat. -->
                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai {{ $employee->name }}? Ini akan dicatat di riwayat.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                            <!-- Tautan untuk langsung menganalisis pegawai ini -->
                            <a href="{{ route('admin.analysis', ['employee_id' => $employee->id]) }}" class="text-indigo-600 hover:underline">Analisis</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-500">Tidak ada pegawai ditemukan di instansi ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Modal Tambah/Edit Instansi -->
<div id="institutionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
        <h2 id="institutionModalTitle" class="text-xl font-semibold mb-4">Tambah Instansi</h2>
        <form id="institutionForm" method="POST" action="{{ route('admin.institutions.store') }}">
            @csrf
            <!-- Input tersembunyi untuk metode HTTP (PUT untuk edit) -->
            <input type="hidden" name="_method" id="institutionFormMethod" value="POST">
            <!-- Input tersembunyi untuk ID instansi saat mode edit -->
            <input type="hidden" name="institution_id" id="institutionId">
            <div class="mb-4">
                <label for="institutionName" class="block text-sm font-medium text-slate-700 mb-1">Nama Instansi</label>
                <input type="text" id="institutionName" name="name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="institutionDescription" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="institutionDescription" name="description" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideInstitutionModal()" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah/Edit Pegawai -->
<div id="employeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg overflow-y-auto max-h-[90vh]">
        <h2 id="employeeModalTitle" class="text-xl font-semibold mb-4">Tambah Pegawai</h2>
        <form id="employeeForm" method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">
            @csrf
            <!-- Input tersembunyi untuk metode HTTP (PUT untuk edit) -->
            <input type="hidden" name="_method" id="employeeFormMethod" value="POST">
            <!-- Input tersembunyi untuk ID pegawai saat mode edit -->
            <input type="hidden" name="employee_id" id="employeeId">
            <!-- Input tersembunyi untuk ID instansi yang akan diisi otomatis -->
            <input type="hidden" name="institution_id" id="employeeInstitutionId">

            <div class="mb-4">
                <label for="employeeName" class="block text-sm font-medium text-slate-700 mb-1">Nama Pegawai</label>
                <input type="text" id="employeeName" name="name" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="employeeNip" class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                <input type="text" id="employeeNip" name="nip" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="employeeGolongan" class="block text-sm font-medium text-slate-700 mb-1">Golongan</label>
                <input type="text" id="employeeGolongan" name="golongan" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="employeeJabatan" class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
                <input type="text" id="employeeJabatan" name="jabatan" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="employeeEmail" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" id="employeeEmail" name="email" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="employeePhone" class="block text-sm font-medium text-slate-700 mb-1">No. Handphone</label>
                <input type="text" id="employeePhone" name="phone_number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="employeeProfilePicture" class="block text-sm font-medium text-slate-700 mb-1">Foto Profil (Opsional)</label>
                <input type="file" id="employeeProfilePicture" name="profile_picture" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideEmployeeModal()" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>


<script>
    /**
     * Menampilkan modal untuk menambah instansi baru.
     * Mengatur judul modal, aksi form, metode HTTP, dan mengosongkan input.
     */
    function showAddInstitutionModal() {
        document.getElementById('institutionModalTitle').textContent = 'Tambah Instansi';
        document.getElementById('institutionForm').action = '{{ route('admin.institutions.store') }}';
        document.getElementById('institutionFormMethod').value = 'POST';
        document.getElementById('institutionName').value = '';
        document.getElementById('institutionDescription').value = '';
        document.getElementById('institutionId').value = ''; // Pastikan ID dikosongkan
        document.getElementById('institutionModal').classList.remove('hidden');
    }

    /**
     * Menampilkan modal untuk mengedit instansi yang sudah ada.
     * Mengisi form dengan data instansi yang dipilih dan mengatur aksi form ke rute update.
     */
    function showEditInstitutionModal(id, name, description) {
        document.getElementById('institutionModalTitle').textContent = 'Edit Instansi';
        // Perbaikan: Pastikan URL di-quote sebagai string literal dalam JavaScript
        document.getElementById('institutionForm').action = "{{ url('admin/instansi') }}/" + id; // URL untuk update
        document.getElementById('institutionFormMethod').value = 'PUT'; // Menggunakan metode PUT untuk update
        document.getElementById('institutionName').value = name;
        document.getElementById('institutionDescription').value = description;
        document.getElementById('institutionId').value = id; // Mengatur ID instansi yang akan diedit
        document.getElementById('institutionModal').classList.remove('hidden');
    }

    /**
     * Menyembunyikan modal instansi.
     */
    function hideInstitutionModal() {
        document.getElementById('institutionModal').classList.add('hidden');
    }

    /**
     * Menampilkan modal untuk menambah pegawai baru.
     * Mengisi otomatis ID instansi berdasarkan instansi yang sedang dilihat.
     */
    function showAddEmployeeModal(institutionId) {
        document.getElementById('employeeModalTitle').textContent = 'Tambah Pegawai';
        document.getElementById('employeeForm').action = '{{ route('admin.employees.store') }}';
        document.getElementById('employeeFormMethod').value = 'POST';
        document.getElementById('employeeId').value = '';
        document.getElementById('employeeInstitutionId').value = institutionId; // Otomatis mengisi ID instansi
        // Mengosongkan semua input form
        document.getElementById('employeeName').value = '';
        document.getElementById('employeeNip').value = '';
        document.getElementById('employeeGolongan').value = '';
        document.getElementById('employeeJabatan').value = '';
        document.getElementById('employeeEmail').value = '';
        document.getElementById('employeePhone').value = '';
        document.getElementById('employeeProfilePicture').value = ''; // Mengosongkan input file
        document.getElementById('employeeModal').classList.remove('hidden');
    }

    /**
     * Menampilkan modal untuk mengedit data pegawai yang sudah ada.
     * Mengisi form dengan data pegawai yang dipilih.
     */
    function showEditEmployeeModal(id, name, nip, golongan, jabatan, email, phone_number, institution_id, profile_picture_path) {
        document.getElementById('employeeModalTitle').textContent = 'Edit Pegawai';
        // Perbaikan: Pastikan URL di-quote sebagai string literal dalam JavaScript
        document.getElementById('employeeForm').action = "{{ url('admin/employees') }}/" + id;
        // Mengubah metode HTTP menjadi PUT
        document.getElementById('employeeFormMethod').value = 'PUT'; 
        document.getElementById('employeeId').value = id;
        document.getElementById('employeeInstitutionId').value = institution_id;
        document.getElementById('employeeName').value = name;
        document.getElementById('employeeNip').value = nip;
        document.getElementById('employeeGolongan').value = golongan;
        document.getElementById('employeeJabatan').value = jabatan;
        document.getElementById('employeeEmail').value = email;
        document.getElementById('employeePhone').value = phone_number;
        // Input file tidak bisa diisi secara programatis karena alasan keamanan browser.
        // Pengguna harus memilih file baru jika ingin mengubah foto profil.
        document.getElementById('employeeProfilePicture').value = ''; 
        document.getElementById('employeeModal').classList.remove('hidden');
    }

    /**
     * Menyembunyikan modal pegawai.
     */
    function hideEmployeeModal() {
        document.getElementById('employeeModal').classList.add('hidden');
    }
</script>
@endsection
