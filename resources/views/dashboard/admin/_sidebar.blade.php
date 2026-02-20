@php $active = $active ?? 'ringkasan'; @endphp

<a href="{{ route('admin.ringkasan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'ringkasan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ $active === 'ringkasan' ? 'text-black' : 'text-gray-500' }}"></i>
    Ringkasan
</a>

<a href="{{ route('admin.ruangan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'ruangan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="door-open" class="w-4 h-4 {{ $active === 'ruangan' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Ruangan
</a>

<a href="{{ route('admin.lagu') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'lagu' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="music" class="w-4 h-4 {{ $active === 'lagu' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Lagu
</a>

<a href="{{ route('admin.kategori') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'kategori' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="layers" class="w-4 h-4 {{ $active === 'kategori' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Kategori
</a>

<a href="{{ route('admin.food-beverages') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'fnb' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="utensils-crossed" class="w-4 h-4 {{ $active === 'fnb' ? 'text-black' : 'text-gray-500' }}"></i>
    Food & Beverages
</a>

<a href="{{ route('admin.keuangan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'keuangan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="wallet" class="w-4 h-4 {{ $active === 'keuangan' ? 'text-black' : 'text-gray-500' }}"></i>
    Keuangan
</a>

<a href="{{ route('admin.billing') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'billing' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="receipt" class="w-4 h-4 {{ $active === 'billing' ? 'text-black' : 'text-gray-500' }}"></i>
    Riwayat Billing
</a>

<a href="{{ route('admin.akun') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'akun' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="users" class="w-4 h-4 {{ $active === 'akun' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Akun
</a>

<a href="{{ route('admin.activity-log') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'activity-log' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="activity" class="w-4 h-4 {{ $active === 'activity-log' ? 'text-black' : 'text-gray-500' }}"></i>
    Log Aktivitas
</a>

<a href="{{ route('admin.operator') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'operator' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="hard-hat" class="w-4 h-4 {{ $active === 'operator' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Operator
</a>



<a href="{{ route('admin.manajemen-absensi') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'manajemen-absensi' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="clock" class="w-4 h-4 {{ $active === 'manajemen-absensi' ? 'text-black' : 'text-gray-500' }}"></i>
    Manajemen Absensi
</a>

<a href="{{ route('admin.membership') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'membership' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="crown" class="w-4 h-4 {{ $active === 'membership' ? 'text-black' : 'text-gray-500' }}"></i>
    Membership
</a>

<a href="{{ route('admin.pemesanan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'pemesanan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="shopping-cart" class="w-4 h-4 {{ $active === 'pemesanan' ? 'text-black' : 'text-gray-500' }}"></i>
    Buat Pesanan
</a>

<a href="{{ route('admin.reservasi-room') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'reservasi-room' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="calendar-days" class="w-4 h-4 {{ $active === 'reservasi-room' ? 'text-black' : 'text-gray-500' }}"></i>
    Reservasi Room
</a>

<a href="{{ route('admin.pesanan-aktif') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'pesanan-aktif' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="clipboard-list" class="w-4 h-4 {{ $active === 'pesanan-aktif' ? 'text-black' : 'text-gray-500' }}"></i>
    Pesanan Aktif
</a>

<a href="{{ route('admin.panggilan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'panggilan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <div class="relative">
        <i data-lucide="bell-ring" class="w-4 h-4 {{ $active === 'panggilan' ? 'text-black' : 'text-gray-500' }}"></i>
        <span id="call-notification-dot" class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-[#1e1e1e]"></span>
    </div>
    Panggilan Room
    <span id="call-notification-count" class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1 py-0 rounded-md">2</span>
</a>

{{-- Laporan Menu --}}
<a href="{{ route('admin.laporan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-all border {{ $active === 'laporan' ? 'bg-[#D0B75B] text-black border-[#D0B75B] font-bold' : 'text-gray-400 border-transparent hover:text-[#D0B75B] hover:bg-white/5 font-medium' }}">
    <i data-lucide="flag" class="w-4 h-4 {{ $active === 'laporan' ? 'text-black' : 'text-gray-500' }}"></i>
    Laporan
</a>
