<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor Pegawai') - BKPSDM Bulukumba</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('{{ asset('images/background.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="text-white">

<div x-data="{ collapsed: false, showSidebar: false }" class="flex flex-col md:flex-row h-screen relative">

    <!-- Sidebar -->
    <aside
        x-show="showSidebar || window.innerWidth >= 768"
        :class="{
            'w-20 md:w-20': collapsed && window.innerWidth >= 768,
            'w-72 md:w-64': !collapsed || window.innerWidth < 768
        }"
        class="bg-blue-900 transition-all duration-300 ease-in-out flex flex-col fixed md:static inset-0 md:inset-auto z-40 md:z-0 h-full"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-full"
        @keydown.window.escape="showSidebar = false"
        @click.outside.window="if (window.innerWidth < 768) showSidebar = false"
    >
        <!-- Logo dan Toggle -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-10 w-10 shrink-0">
                <div x-show="!collapsed && window.innerWidth >= 768" x-cloak>
                    <div class="leading-tight">
                        <p class="text-sm font-bold">BKPSDM</p>
                        <p class="text-xs text-blue-200">Bulukumba</p>
                    </div>
                </div>
            </div>

            <!-- Tombol collapse untuk desktop -->
            <button class="hidden md:inline-block text-white text-xl" @click="collapsed = !collapsed">
                <ion-icon x-show="!collapsed" name="menu-outline" x-cloak></ion-icon>
                <ion-icon x-show="collapsed" name="arrow-forward-outline" x-cloak></ion-icon>
            </button>

            <!-- Tombol tutup sidebar mobile -->
            <button class="md:hidden text-white text-2xl" @click="showSidebar = false">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <!-- Info User -->
        <div class="px-4 py-3 border-b border-blue-800">
            <template x-if="!collapsed">
                <div>
                    <p class="text-sm font-medium">Pegawai</p>
                    <p class="text-xs text-blue-300 break-words leading-tight">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                </div>
            </template>
            <template x-if="collapsed">
                <p class="text-[10px] text-blue-300 leading-snug break-words text-center">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </p>
            </template>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-800 transition">
                <ion-icon name="home-outline" class="text-xl"></ion-icon>
                <span x-show="!collapsed" x-cloak>Beranda</span>
            </a>
            <a href="{{ route('user.my-institution') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-800 transition">
                <ion-icon name="business-outline" class="text-xl"></ion-icon>
                <span x-show="!collapsed" x-cloak>Instansi</span>
            </a>
            <a href="{{ route('user.documents') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-800 transition">
                <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
                <span x-show="!collapsed" x-cloak>Dokumen Saya</span>
            </a>
            <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-800 transition">
                <ion-icon name="person-circle-outline" class="text-xl"></ion-icon>
                <span x-show="!collapsed" x-cloak>Profil Saya</span>
            </a>
        </nav>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="border-t border-blue-800 mt-auto">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-3 hover:bg-red-700 transition"
                :class="{ 'justify-center': collapsed, 'justify-start gap-3': !collapsed }">
                <ion-icon name="log-out-outline" class="text-2xl"></ion-icon>
                <span x-show="!collapsed" x-cloak>Logout</span>
            </button>
        </form>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 flex flex-col overflow-hidden bg-white/50 backdrop-blur-sm md:rounded-l-xl shadow-lg">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
            @php
                $routeName = Route::currentRouteName();
                $pageTitles = [
                    'user.dashboard' => 'Beranda',
                    'user.documents' => 'Dokumen',
                    'user.my-institution' => 'Instansi',
                    'user.profile' => 'Profil',
                ];
                $pageTitle = $pageTitles[$routeName] ?? 'Dasbor';
            @endphp
            <div class="flex items-center gap-4">
                <!-- Tombol menu (hanya tampil di mobile) -->
                <button class="md:hidden text-blue-700 text-2xl" @click="showSidebar = true">
                    <ion-icon name="menu-outline"></ion-icon>
                </button>
                <h1 class="text-xl font-semibold text-slate-700">{{ $pageTitle }}</h1>
            </div>
        </header>

        <div class="flex-1 p-6 overflow-y-auto text-black">
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
