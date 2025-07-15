<!DOCTYPE html>
<html lang="id" x-data="{ collapsed: false, mobileOpen: false }">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Dasbor Admin') - BKPSDM Bulukumba</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon" />

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
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #64748b; }
  </style>
</head>
<body class="text-slate-800">

<div class="flex h-screen" x-data="{ collapsed: false, mobileOpen: false }">
  
  <!-- Overlay Mobile -->
  <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false"
       class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

  <!-- Sidebar -->
  <aside :class="[
           collapsed ? 'w-20' : 'w-64',
           mobileOpen ? 'translate-x-0' : '-translate-x-full',
           'transition-all duration-300 ease-in-out bg-blue-900 text-white fixed z-50 top-0 left-0 h-full md:relative md:translate-x-0 md:z-auto md:flex-shrink-0'
         ]">

    <!-- Tombol collapse desktop -->
    <button @click="collapsed = !collapsed"
      class="absolute -right-3 top-4 bg-white text-blue-900 p-1 rounded-full shadow z-50 border border-blue-900 transition hidden md:block">
      <ion-icon :name="collapsed ? 'menu-outline' : 'arrow-back-outline'" class="text-xl"></ion-icon>
    </button>

    <!-- Logo -->
    <div class="px-4 py-5 border-b border-blue-800 flex items-center justify-center">
      <img src="{{ asset('images/Logo.png') }}" class="h-10" alt="Logo" />
      <div x-show="!collapsed" x-cloak class="ml-3">
        <p class="text-sm font-semibold">BKPSDM</p>
        <p class="text-xs text-blue-200">Bulukumba</p>
      </div>
    </div>

    <!-- Admin Info -->
    <div class="px-4 py-3 border-b border-blue-800 text-center">
      <ion-icon name="person-circle-outline" class="mx-auto text-2xl text-white mb-1"></ion-icon>
      <p class="text-xs font-semibold">Admin</p>
      <p class="text-[11px] text-blue-300 truncate" x-show="!collapsed" x-cloak>{{ Auth::user()->name ?? 'Pengguna' }}</p>
    </div>

    <!-- Menu Utama -->
    <nav class="flex-1 px-2 py-4 space-y-1">
      @php
        $menus = [
          ['route' => 'admin.dashboard', 'icon' => 'home-outline', 'label' => 'Beranda'],
          ['route' => 'admin.institutions', 'icon' => 'business-outline', 'label' => 'Instansi'],
          ['route' => 'admin.analysis', 'icon' => 'bar-chart-outline', 'label' => 'Analisis'],
          ['route' => 'admin.job-requirements', 'icon' => 'document-text-outline', 'label' => 'Syarat Jabatan'],
          ['route' => 'admin.trainings', 'icon' => 'school-outline', 'label' => 'Pelatihan'],
          ['route' => 'admin.history', 'icon' => 'time-outline', 'label' => 'Riwayat'],
        ];
      @endphp
      @foreach ($menus as $menu)
        <a href="{{ route($menu['route']) }}"
           @click="mobileOpen = false"
           class="flex justify-center md:justify-start items-center px-4 py-2 text-white rounded-lg hover:bg-blue-700 transition">
          <ion-icon name="{{ $menu['icon'] }}" class="text-xl w-5 h-5"></ion-icon>
          <span x-show="!collapsed" x-cloak class="ml-3">{{ $menu['label'] }}</span>
        </a>
      @endforeach
    </nav>

    <!-- Menu Bawah -->
    <div class="px-2 py-4 border-t border-blue-800 space-y-2">
      <a href="{{ route('admin.settings') }}" @click="mobileOpen = false"
         class="flex justify-center md:justify-start items-center px-4 py-2 hover:bg-blue-700 rounded-lg transition">
        <ion-icon name="settings-outline" class="text-xl w-5 h-5"></ion-icon>
        <span x-show="!collapsed" x-cloak class="ml-3">Pengaturan</span>
      </a>
      <a href="{{ route('admin.profile') }}" @click="mobileOpen = false"
         class="flex justify-center md:justify-start items-center px-4 py-2 hover:bg-blue-700 rounded-lg transition">
        <ion-icon name="person-outline" class="text-xl w-5 h-5"></ion-icon>
        <span x-show="!collapsed" x-cloak class="ml-3">Profil</span>
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" @click="mobileOpen = false"
                class="w-full flex justify-center md:justify-start items-center px-4 py-2 hover:bg-red-700 rounded-lg transition">
          <ion-icon name="log-out-outline" class="text-xl w-5 h-5"></ion-icon>
          <span x-show="!collapsed" x-cloak class="ml-3">Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Konten Utama -->
  <main class="flex-1 flex flex-col bg-white/50 backdrop-blur-sm m-2 md:m-4 rounded-xl shadow-lg overflow-hidden transition-all duration-300 ease-in-out"
      :class="'md:ml-20' : 'md:ml-64'">

        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 md:px-6 rounded-t-xl relative">
        @php
            $titles = [
            'admin.dashboard' => 'Beranda',
            'admin.institutions' => 'Instansi',
            'admin.analysis' => 'Analisis',
            'admin.job-requirements' => 'Syarat Jabatan',
            'admin.trainings' => 'Pelatihan',
            'admin.history' => 'Riwayat',
            'admin.settings' => 'Pengaturan',
            'admin.profile' => 'Profil',
            ];
            $pageTitle = $titles[Route::currentRouteName()] ?? 'Dasbor';
        @endphp
        <h1 class="text-lg md:text-xl font-semibold text-slate-700">{{ $pageTitle }}</h1>

        <!-- Hamburger Mobile -->
        <button @click="mobileOpen = !mobileOpen"
                class="md:hidden absolute right-4 top-1/2 transform -translate-y-1/2">
            <ion-icon name="menu-outline" class="text-2xl text-slate-700"></ion-icon>
        </button>
        </header>

        <!-- Konten -->
        <div class="flex-1 overflow-y-auto px-4 py-4 md:p-6">
        <div class="container mx-auto max-w-7xl">
            @yield('content')
        </div>
        </div>
  </main>
</div>

</body>
</html>
