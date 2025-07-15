<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KomPeg - BKPSDM Bulukumba</title>
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
            inset: 0;
            z-index: -1;
        }
        #app {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Logo di kiri atas -->
    <div class="absolute top-6 left-6 z-20">
        <img src="{{ asset('images/Logo BKPSDM.png') }}" alt="Logo BKPSDM Bulukumba" class="h-16 w-auto">
    </div>

    <!-- Box login putih transparan -->
    <div id="app" class="w-full max-w-md bg-white bg-opacity-40 backdrop-blur-md rounded-xl shadow-2xl p-8 space-y-6 md:p-10  border-slate-500">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-slate-800">Selamat Datang</h1>
            <p class="text-md text-slate-600 mt-1">Badan Kepegawaian dan Sumber Daya Manusia</p>
            <p class="text-md text-slate-600">Kabupaten Bulukumba</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="relative">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>

                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-2 pr-14 border border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none @error('password') border-red-500 @enderror">

                {{-- Ikon Mata --}}
                <button type="button" id="togglePassword"
                        class="absolute inset-y-11 right-3 flex items-center text-slate-700 focus:outline-none cursor-pointer"
                        tabindex="-1">
                    <ion-icon name="eye-outline" id="eyeIcon" class="text-2xl"></ion-icon>
                </button>

                @error('password')
                    <span class="text-red-500 text-sm mt-1 block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-slate-900">Ingat Saya</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-800 hover:underline" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition duration-150 hover:scale-105">
                    Login
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <p class="mt-6 text-center text-sm text-slate-700">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        @endif
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
        });
    </script>

</body>
</html>
