<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BKPSDM Bulukumba</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('{{ asset('images/background.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }
        #app {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div id="app" class="w-full max-w-md bg-white bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl p-8 space-y-6 md:p-10">
        <div class="text-center">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo Bulukumba" class="mx-auto h-24 w-auto">
            <h1 class="text-3xl font-bold text-slate-800 mt-4">Daftar Akun Baru Pegawai</h1>
            <p class="text-md text-slate-600 mt-1">Badan Kepegawaian dan Sumber Daya Manusia</p>
            <p class="text-md text-slate-600">Kabupaten Bulukumba</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('name') border-red-500 @enderror">
                @error('name')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                <input id="nip" type="text" name="nip" value="{{ old('nip') }}" required
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 @error('nip') border-red-500 @enderror">
                @error('nip')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label for="institution_id" class="block text-sm font-medium text-slate-700 mb-1">Instansi</label>
                <select id="institution_id" name="institution_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 @error('institution_id') border-red-500 @enderror">
                    <option value="">Pilih Instansi</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                            {{ $institution->name }}
                        </option>
                    @endforeach
                </select>
                @error('institution_id')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label for="golongan" class="block text-sm font-medium text-slate-700 mb-1">Golongan</label>
                <select id="golongan" name="golongan" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('golongan') border-red-500 @enderror">
                    <option value="">Pilih Golongan</option>
                    <option value="I/a" {{ old('golongan') == 'I/a' ? 'selected' : '' }}>I/a – Juru Muda</option>
                    <option value="I/b" {{ old('golongan') == 'I/b' ? 'selected' : '' }}>I/b – Juru Muda Tingkat I</option>
                    <option value="I/c" {{ old('golongan') == 'I/c' ? 'selected' : '' }}>I/c – Juru</option>
                    <option value="I/d" {{ old('golongan') == 'I/d' ? 'selected' : '' }}>I/d – Juru Tingkat I</option>
                    <option value="II/a" {{ old('golongan') == 'II/a' ? 'selected' : '' }}>II/a – Pengatur Muda</option>
                    <option value="II/b" {{ old('golongan') == 'II/b' ? 'selected' : '' }}>II/b – Pengatur Muda Tingkat I</option>
                    <option value="II/c" {{ old('golongan') == 'II/c' ? 'selected' : '' }}>II/c – Pengatur</option>
                    <option value="II/d" {{ old('golongan') == 'II/d' ? 'selected' : '' }}>II/d – Pengatur Tingkat I</option>
                    <option value="III/a" {{ old('golongan') == 'III/a' ? 'selected' : '' }}>III/a – Penata Muda</option>
                    <option value="III/b" {{ old('golongan') == 'III/b' ? 'selected' : '' }}>III/b – Penata Muda Tingkat I</option>
                    <option value="III/c" {{ old('golongan') == 'III/c' ? 'selected' : '' }}>III/c – Penata</option>
                    <option value="III/d" {{ old('golongan') == 'III/d' ? 'selected' : '' }}>III/d – Penata Tingkat I</option>
                    <option value="IV/a" {{ old('golongan') == 'IV/a' ? 'selected' : '' }}>IV/a – Pembina</option>
                    <option value="IV/b" {{ old('golongan') == 'IV/b' ? 'selected' : '' }}>IV/b – Pembina Tingkat I</option>
                    <option value="IV/c" {{ old('golongan') == 'IV/c' ? 'selected' : '' }}>IV/c – Pembina Utama Muda</option>
                    <option value="IV/d" {{ old('golongan') == 'IV/d' ? 'selected' : '' }}>IV/d – Pembina Utama Madya</option>
                    <option value="IV/e" {{ old('golongan') == 'IV/e' ? 'selected' : '' }}>IV/e – Pembina Utama</option>
                </select>
                @error('golongan')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="relative">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full px-4 py-2 pr-12 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('password') border-red-500 @enderror">
                <button type="button" id="togglePassword"
                        class="absolute inset-y-11 right-3 flex items-center text-slate-700 focus:outline-none cursor-pointer">
                    <ion-icon id="passwordEyeIcon" name="eye-outline" size="small"></ion-icon>
                </button>
                @error('password')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="relative">
                <label for="password-confirm" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full px-4 py-2 pr-12 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none ">
                <button type="button" id="toggleConfirmPassword"
                        class="absolute inset-y-11 right-3 flex items-center text-slate-700 focus:outline-none cursor-pointer">
                    <ion-icon id="confirmPasswordEyeIcon" name="eye-outline" size="small"></ion-icon>
                </button>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition duration-150 hover:scale-105">
                    Daftar
                </button>
            </div>
        </form>

        @if (Route::has('login'))
            <p class="mt-6 text-center text-sm text-slate-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                    Login di sini
                </a>
            </p>
        @endif
    </div>

    <script>
    // Toggle Password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordEyeIcon = document.getElementById('passwordEyeIcon');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordEyeIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
        });

        // Toggle Confirm Password
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('password-confirm');
        const confirmPasswordEyeIcon = document.getElementById('confirmPasswordEyeIcon');

        toggleConfirmPassword.addEventListener('click', function () {
            const isPassword = confirmPasswordInput.type === 'password';
            confirmPasswordInput.type = isPassword ? 'text' : 'password';
            confirmPasswordEyeIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
        });
    </script>


</body>
</html>
