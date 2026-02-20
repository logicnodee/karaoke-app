<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     class="fixed w-full z-50 top-0 transition-all duration-300" 
     :class="{ 'bg-black/90 backdrop-blur-md py-2': scrolled, 'bg-transparent py-6': !scrolled }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="group">
                    <img src="{{ asset('assets/img/global/krls_logo.png') }}" alt="KRLS Karaoke" class="h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                </a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden md:flex space-x-8 items-center" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                <a href="{{ route('home') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">HOME</a>
                <a href="{{ route('about') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">ABOUT</a>
                <a href="{{ route('rooms') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">BOOK A ROOM</a>
                <a href="{{ route('food-beverages') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">FOOD & BEVERAGES</a>
                <a href="{{ route('rooms') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">PACKAGES</a>
                <a href="{{ route('faq') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">FAQ</a>
                <a href="{{ route('contact') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-gray-300 transition-colors">CONTACT US</a>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-300 focus:outline-none transition-colors">
                    <span class="sr-only">Open main menu</span>
                    <template x-if="!open">
                        <i data-lucide="menu" class="w-8 h-8"></i>
                    </template>
                    <template x-if="open">
                        <i data-lucide="x" class="w-8 h-8"></i>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden bg-black/95 backdrop-blur-xl absolute top-full left-0 w-full border-t border-white/10">
        <div class="px-4 pt-4 pb-6 space-y-2 flex flex-col items-center" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">HOME</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">ABOUT</a>
            <a href="{{ route('rooms') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">BOOK A ROOM</a>
            <a href="{{ route('food-beverages') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">FOOD & BEVERAGES</a>
            <a href="{{ route('rooms') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">PACKAGES</a>
            <a href="{{ route('faq') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">FAQ</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 text-sm font-bold uppercase tracking-widest text-white hover:text-yellow-500 transition-colors">CONTACT US</a>
        </div>
    </div>
</nav>

