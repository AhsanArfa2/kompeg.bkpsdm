<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - BKPSDM Bulukumba</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        #forgot-box {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div id="forgot-box" class="w-full max-w-md bg-white bg-opacity-80 backdrop-blur-md rounded-xl shadow-xl p-8 md:p-10 space-y-6">
        <div class="text-center">
            <img src="{{ asset('images/Logo BKPSDM.png') }}" alt="Logo BKPSDM" class="h-24 w-auto mx-auto mb-4">
            <h2 class="text-2xl font-bold text-slate-800">Lupa Password</h2>
            <p class="text-sm text-slate-600">Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mereset password Anda.</p>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow hover:scale-105 transition">
                    Kirim Link Reset
                </button>
            </div>

            <div class="text-center text-sm text-slate-600 mt-4">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>
