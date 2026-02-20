<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard - SGRT Karaoke')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/global/krls_logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-black text-white overflow-x-hidden">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true, mobileSidebar: false }">

        {{-- ═══════════════════════════════════════
            SIDEBAR
        ═══════════════════════════════════════ --}}
        {{-- Mobile overlay --}}
        <div x-show="mobileSidebar" x-transition.opacity 
             @click="mobileSidebar = false"
             class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

        <aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:sticky top-0 left-0 z-50 h-screen w-56 bg-[#080808] border-r border-white/5 flex flex-col transition-transform duration-300 ease-in-out">
            
            {{-- Logo + Role Badge --}}
            <div class="px-4 py-5 border-b border-white/5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/global/krls_logo.png') }}" alt="MICS" class="h-8 opacity-80">
                </a>
                <div class="flex items-center gap-2">
                    @hasSection('dashboard-role')
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#D0B75B]/10 border border-[#D0B75B]/20">
                        <i data-lucide="@yield('dashboard-role-icon', 'shield')" class="w-3 h-3 text-[#D0B75B]"></i>
                        <span class="text-[9px] font-bold uppercase tracking-[0.15em] text-[#D0B75B]">@yield('dashboard-role')</span>
                    </div>
                    @endif
                    <button @click="mobileSidebar = false" class="lg:hidden text-gray-500 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto" 
                 x-data 
                 x-init="$el.scrollTop = localStorage.getItem('adminSidebarScroll') || 0"
                 @scroll.debounce.100ms="localStorage.setItem('adminSidebarScroll', $el.scrollTop)">
                @yield('sidebar-nav')
            </nav>

            {{-- Logout --}}
            <div class="px-4 py-6 border-t border-white/5">
                <a href="{{ route('logout') }}"
                   class="flex items-center gap-2 text-red-500 hover:text-red-400 text-xs font-semibold transition-colors px-3 py-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/10">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout / Keluar
                </a>
            </div>
        </aside>

        {{-- ═══════════════════════════════════════
            MAIN CONTENT
        ═══════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">
            {{-- Page Content --}}
            <main class="p-4 md:p-6 lg:p-8 bg-black min-h-screen relative">
                {{-- Mobile Menu Trigger --}}
                <button @click="mobileSidebar = true" class="lg:hidden absolute top-4 right-4 z-30 p-2 text-gray-400 hover:text-white bg-black/50 rounded-lg backdrop-blur-sm border border-white/10">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                @yield('dashboard-content')
            </main>
        </div>
    </div>
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
