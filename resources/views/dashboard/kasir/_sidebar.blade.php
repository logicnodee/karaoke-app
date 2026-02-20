@php $active = $active ?? 'ruangan'; @endphp

<a href="{{ route('kasir.pemesanan') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs transition-all border {{ $active === 'pemesanan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="calculator" class="w-4 h-4 {{ $active === 'pemesanan' ? 'text-black' : 'text-gray-500 group-hover:text-[#D0B75B]' }}"></i>
    Buat Pesanan
</a>

<a href="{{ route('kasir.ruangan') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs transition-all border {{ $active === 'ruangan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="door-open" class="w-4 h-4 {{ $active === 'ruangan' ? 'text-black' : 'text-gray-500 group-hover:text-[#D0B75B]' }}"></i>
    Manajemen Ruangan
</a>

<a href="{{ route('kasir.food-beverages') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs transition-all border {{ $active === 'fnb' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="utensils-crossed" class="w-4 h-4 {{ $active === 'fnb' ? 'text-black' : 'text-gray-500 group-hover:text-[#D0B75B]' }}"></i>
    Food & Beverages
</a>

<a href="{{ route('kasir.pesanan-aktif') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs transition-all border {{ $active === 'pesanan-aktif' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="clipboard-list" class="w-4 h-4 {{ $active === 'pesanan-aktif' ? 'text-black' : 'text-gray-500 group-hover:text-[#D0B75B]' }}"></i>
    Pesanan Aktif
</a>

<a href="{{ route('kasir.panggilan') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs transition-all border {{ $active === 'panggilan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <div class="relative">
        <i data-lucide="bell-ring" class="w-4 h-4 {{ $active === 'panggilan' ? 'text-black' : 'text-gray-500 group-hover:text-[#D0B75B]' }}"></i>
        <span id="call-notification-dot" class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-[#1e1e1e]"></span>
    </div>
    Panggilan Room
    <span id="call-notification-count" class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1 py-0 rounded-md">2</span>
</a>
