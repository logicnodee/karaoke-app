@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Ringkasan - Admin Dashboard')
@section('page-title', 'Ringkasan')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'ringkasan'])
@endsection

@section('dashboard-content')
    <div class="space-y-6">
        
        {{-- SECTION 1: METRIK UTAMA (Ruangan & Pendapatan) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Status Ruangan (Link to Room Management) --}}
            <a href="{{ route('admin.ruangan') }}" class="bg-[#080808] border border-white/5 rounded-2xl p-5 flex flex-col justify-between group hover:border-[#D0B75B]/50 hover:bg-white/[0.02] transition-all cursor-pointer">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold tracking-wider group-hover:text-gray-400 transition-colors">Okupansi Ruangan</p>
                        <h3 class="text-2xl font-black text-white mt-1" style="font-family: 'Inter';">{{ $statusRuangan['digunakan'] }} <span class="text-sm text-gray-600 font-medium group-hover:text-gray-500 transition-colors">/ {{ $statusRuangan['total'] }}</span></h3>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-[#D0B75B]/10 flex items-center justify-center text-[#D0B75B] group-hover:bg-[#D0B75B] group-hover:text-black transition-all">
                        <i data-lucide="mic-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <span class="text-[9px] px-2 py-0.5 rounded bg-green-500/10 text-green-500 border border-green-500/20 font-bold group-hover:bg-green-500/20 transition-colors">{{ $statusRuangan['kosong'] }} Tersedia</span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 border border-blue-500/20 font-bold group-hover:bg-blue-500/20 transition-colors">{{ $statusRuangan['cleaning'] }} Bersih</span>
                </div>
            </a>

            {{-- Pendapatan Hari Ini (Link to Billing History) --}}
            <a href="{{ route('admin.billing') }}" class="bg-[#080808] border border-white/5 rounded-2xl p-5 flex flex-col justify-between group hover:border-[#D0B75B]/50 hover:bg-white/[0.02] transition-all cursor-pointer">
                 <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold tracking-wider group-hover:text-gray-400 transition-colors">Pendapatan Hari Ini</p>
                        <h3 class="text-2xl font-black text-white mt-1" style="font-family: 'Inter';">Rp {{ number_format($pendapatanHariIni/1000000, 1, ',', '.') }}jt</h3>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-green-500/10 flex items-center justify-center text-green-500 group-hover:bg-green-500 group-hover:text-black transition-all">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                    </div>
                </div>
                 <div class="mt-4">
                    <p class="text-[10px] text-gray-500 group-hover:text-gray-400 transition-colors">Bulan Ini: <span class="text-white font-bold">Rp {{ number_format($pendapatanBulanIni/1000000, 0, ',', '.') }} Juta</span></p>
                </div>
            </a>

             {{-- Panggilan & Laporan Aktif (Link to Calls/Reports) --}}
             <a href="{{ route('admin.panggilan') }}" class="bg-[#080808] border border-white/5 rounded-2xl p-5 flex flex-col justify-between group hover:border-[#D0B75B]/50 hover:bg-white/[0.02] transition-all cursor-pointer">
                 <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold tracking-wider group-hover:text-gray-400 transition-colors">Perhatian Operasional</p>
                        <div class="flex items-baseline gap-3 mt-1">
                            <h3 class="text-2xl font-black text-white" style="font-family: 'Inter';">{{ count($panggilanRoom) }}</h3>
                            <span class="text-xs text-red-500 font-bold animate-pulse">Panggilan</span>
                            <div class="h-4 w-px bg-white/10"></div>
                             <h3 class="text-2xl font-black text-white" style="font-family: 'Inter';">{{ count($laporanPending) }}</h3>
                            <span class="text-xs text-orange-500 font-bold">Laporan</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all">
                        <i data-lucide="bell-ring" class="w-4 h-4"></i>
                    </div>
                </div>
                 <div class="mt-4">
                     <template x-if="{{ count($panggilanRoom) }} > 0">
                        <p class="text-[10px] text-red-400 font-medium truncate group-hover:text-red-300 transition-colors">Terbaru: {{ $panggilanRoom[0]['ruangan'] ?? 'Tidak ada' }} ({{ $panggilanRoom[0]['tipe'] ?? '-' }})</p>
                     </template>
                     <template x-if="{{ count($panggilanRoom) }} === 0">
                        <p class="text-[10px] text-green-500 font-medium group-hover:text-green-400 transition-colors">Aman, tidak ada panggilan aktif.</p>
                     </template>
                </div>
            </a>

             {{-- Kehadiran Staf (Link to Attendance) --}}
             <a href="{{ route('admin.manajemen-absensi') }}" class="bg-[#080808] border border-white/5 rounded-2xl p-5 flex flex-col justify-between group hover:border-[#D0B75B]/50 hover:bg-white/[0.02] transition-all cursor-pointer">
                 <div class="flex justify-between items-start">
                    <div>
                         <p class="text-gray-500 text-[10px] uppercase font-bold tracking-wider group-hover:text-gray-400 transition-colors">Kehadiran Staf</p>
                         <h3 class="text-2xl font-black text-white mt-1" style="font-family: 'Inter';">{{ $stafHadir }} <span class="text-sm text-gray-600 font-medium group-hover:text-gray-500 transition-colors">/ {{ $totalStaf }}</span></h3>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                 <div class="mt-4">
                    <p class="text-[10px] text-gray-500 group-hover:text-gray-400 transition-colors">Manajemen Absensi & Operator</p>
                </div>
            </a>
        </div>

        {{-- SECTION 2: OPERASIONAL & MONITORING (Grid Split) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto lg:h-[400px]">
            
            {{-- Monitoring Waktu Ruangan (Takes 2 Cols) --}}
            <div class="lg:col-span-2 bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                <div class="px-5 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                     <div class="flex items-center gap-2">
                        <i data-lucide="monitor-play" class="w-4 h-4 text-[#D0B75B]"></i>
                        <h3 class="text-xs font-bold text-gray-200 uppercase tracking-widest">Monitoring Ruangan Live</h3>
                    </div>
                    <a href="{{ route('admin.ruangan') }}" class="text-[10px] text-gray-500 hover:text-white flex items-center gap-1 transition-colors">Lihat Semua</a>
                </div>
                <div class="overflow-y-auto custom-scrollbar flex-1 relative">
                    <table class="w-full text-xs" style="font-family: 'Inter';">
                        <thead class="sticky top-0 bg-[#080808] z-10">
                            <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                                <th class="text-left px-5 py-3">Ruangan</th>
                                <th class="text-left px-5 py-3">Tamu</th>
                                <th class="text-center px-5 py-3">Sisa Waktu</th>
                                <th class="text-center px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5" x-data="{ 
                            rooms: {{ Js::from($monitoringWaktu) }},
                            formatTime(seconds) {
                                if (seconds < 0) seconds = 0;
                                const h = Math.floor(seconds / 3600);
                                const m = Math.floor((seconds % 3600) / 60);
                                const s = seconds % 60;
                                return [h, m, s].map(v => v < 10 ? '0' + v : v).join(':');
                            },
                            init() {
                                setInterval(() => {
                                    this.rooms.forEach(room => {
                                        if (room.billing_mode === 'paket') {
                                            if (room.sisa_detik > 0) {
                                                room.sisa_detik--;
                                                room.hampir_habis = room.sisa_detik < 300; // Alert if < 5 mins
                                            } else {
                                                room.hampir_habis = true;
                                            }
                                        } else if (room.billing_mode === 'open') {
                                            room.durasi_berjalan++;
                                        }
                                    });
                                }, 1000);
                            }
                        }">
                            <template x-for="room in rooms" :key="room.ruangan">
                                <tr class="hover:bg-white/[0.01]">
                                    <td class="px-5 py-3 font-bold text-white" x-text="room.ruangan"></td>
                                    <td class="px-5 py-3 text-gray-400" x-text="room.tamu"></td>
                                    <td class="px-5 py-3 text-center font-mono">
                                        <template x-if="room.billing_mode === 'paket'">
                                             <span :class="room.hampir_habis ? 'text-red-500 animate-pulse font-bold' : 'text-[#D0B75B]'" x-text="formatTime(room.sisa_detik)"></span>
                                        </template>
                                        <template x-if="room.billing_mode === 'open'">
                                             <span class="text-blue-400" x-text="formatTime(room.durasi_berjalan)"></span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                         <template x-if="room.sisa_detik <= 0 && room.billing_mode === 'paket'">
                                            <span class="text-[8px] bg-red-500/10 text-red-500 px-2 py-0.5 rounded border border-red-500/20 uppercase font-black">Habis</span>
                                         </template>
                                         <template x-if="room.sisa_detik > 0 || room.billing_mode === 'open'">
                                            <span class="text-[8px] bg-green-500/10 text-green-500 px-2 py-0.5 rounded border border-green-500/20 uppercase font-black">Aktif</span>
                                         </template>
                                    </td>
                                </tr>
                            </template>
                             @if(count($monitoringWaktu) === 0)
                                <tr><td colspan="4" class="text-center py-10 text-gray-600 italic">Tidak ada ruangan aktif</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Peringatan Operasional & Reservasi (Stacked) --}}
            <div class="grid grid-rows-2 gap-4 h-full">
                
                {{-- Reservasi Mendatang --}}
                <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                     <div class="px-4 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                             <i data-lucide="calendar-days" class="w-4 h-4 text-purple-400"></i>
                             <h3 class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">Reservasi Hari Ini</h3>
                        </div>
                         <a href="{{ route('admin.reservasi-room') }}" class="text-[10px] text-gray-500 hover:text-white">Lihat Semua</a>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto custom-scrollbar flex-1">
                        @foreach($reservasiHariIni as $booking)
                        <div class="flex items-center gap-3 bg-white/[0.02] p-2.5 rounded-lg border border-white/5 hover:bg-white/[0.05] transition-colors">
                            <div class="flex flex-col items-center bg-purple-500/10 text-purple-400 px-2 py-1 rounded border border-purple-500/20">
                                <span class="text-xs font-black">{{ $booking['jam'] }}</span>
                            </div>
                             <div class="flex-1 min-w-0">
                                <p class="text-xs text-white font-bold truncate">{{ $booking['tamu'] }}</p>
                                <p class="text-[9px] text-gray-500 truncate">{{ $booking['ruangan'] }} • {{ $booking['pax'] }} Pax</p>
                            </div>
                        </div>
                        @endforeach
                        @if(empty($reservasiHariIni))
                            <p class="text-center text-gray-600 text-[10px] py-4">Tidak ada reservasi hari ini</p>
                        @endif
                    </div>
                </div>

                {{-- Stok F&B Menipis --}}
                 <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                     <div class="px-4 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                             <i data-lucide="box" class="w-4 h-4 text-orange-400"></i>
                             <h3 class="text-[10px] font-bold text-gray-200 uppercase tracking-widest">Peringatan Stok</h3>
                        </div>
                         <a href="{{ route('admin.food-beverages') }}" class="text-[10px] text-gray-500 hover:text-white">Lihat Semua</a>
                    </div>
                    <div class="p-4 space-y-2 overflow-y-auto custom-scrollbar flex-1">
                         @foreach($stokMenipis as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-300">{{ $item['nama'] }}</span>
                            <span class="text-[10px] font-bold text-orange-500 bg-orange-500/10 px-1.5 py-0.5 rounded border border-orange-500/20">Sisa {{ $item['stok'] }}</span>
                        </div>
                        @endforeach
                         @if(empty($stokMenipis))
                            <p class="text-center text-gray-600 text-[10px] py-4">Stok aman</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- SECTION 3: RINGKASAN BAWAH (Tabel & Statistik) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Transaksi Terbaru --}}
            <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                     <h3 class="text-xs font-bold text-gray-200 uppercase tracking-widest">Transaksi Terakhir</h3>
                     <a href="{{ route('admin.billing') }}" class="text-[10px] text-gray-500 hover:text-white">Lihat Semua</a>
                </div>
                <table class="w-full text-xs">
                     <tbody class="divide-y divide-white/5">
                        @foreach(array_slice($transaksiHariIni, 0, 3) as $trx)
                        <tr class="hover:bg-white/[0.01]">
                            <td class="px-5 py-3 text-gray-400 font-mono text-[9px]">{{ $trx['waktu'] }}</td>
                            <td class="px-5 py-3">
                                <div class="text-white font-bold">{{ $trx['tamu'] }}</div>
                                <div class="text-[9px] text-gray-500">{{ $trx['ruangan'] }}</div>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="text-[#D0B75B] font-bold">Rp {{ number_format($trx['total'], 0, ',', '.') }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Statistik Lagu --}}
             <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                     <h3 class="text-xs font-bold text-gray-200 uppercase tracking-widest">Library Lagu</h3>
                     <a href="{{ route('admin.lagu') }}" class="text-[10px] text-gray-500 hover:text-white">Lihat Semua</a>
                </div>
                <div class="p-5 flex items-center gap-6 border-b border-white/5">
                    <div>
                        <h4 class="text-2xl font-black text-white">{{ number_format($totalLagu) }}</h4>
                        <p class="text-[9px] text-gray-500 uppercase font-bold tracking-wider">Total Lagu</p>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                     <div>
                        <h4 class="text-2xl font-black text-white">{{ $totalKategori }}</h4>
                        <p class="text-[9px] text-gray-500 uppercase font-bold tracking-wider">Kategori</p>
                    </div>
                </div>
                <div class="px-5 py-3 bg-white/[0.02]">
                    <p class="text-[9px] text-gray-500 uppercase font-bold tracking-widest mb-2">Paling Sering Diputar</p>
                     <div class="space-y-2">
                        @foreach($laguPopuler as $index => $lagu)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] text-gray-600 font-mono">#{{ $index+1 }}</span>
                                <span class="text-[10px] text-gray-300 truncate max-w-[120px]">{{ $lagu['judul'] }}</span>
                            </div>
                             <span class="text-[9px] text-gray-500">{{ $lagu['diputar'] }}x</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Membership --}}
             <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                <div class="px-5 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                     <h3 class="text-xs font-bold text-gray-200 uppercase tracking-widest">Membership</h3>
                     <a href="{{ route('admin.membership') }}" class="text-[10px] text-gray-500 hover:text-white">Lihat Semua</a>
                </div>
                 <div class="flex-1 p-5 flex flex-col justify-center">
                    <div class="flex items-center justify-between mb-4">
                         <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#D0B75B] text-black flex items-center justify-center font-black">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-white">{{ number_format($totalMember) }}</h4>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Member Terdaftar</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-green-500">+{{ $memberBaruBulanIni }} Baru</span>
                    </div>
                    <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden flex">
                        <div class="h-full bg-[#D0B75B] w-[60%]"></div>
                        <div class="h-full bg-gray-400 w-[30%]"></div>
                        <div class="h-full bg-orange-600 w-[10%]"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-[9px] text-gray-500 font-mono">
                        <span>Gold (60%)</span><span>Silver (30%)</span><span>Bronze (10%)</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- SECTION 4: LOG AKTIVITAS (Log Aktivitas) --}}
         <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
             <div class="px-5 py-3 border-b border-white/5 bg-black/20 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="list-checks" class="w-4 h-4 text-gray-400"></i>
                    <h3 class="text-xs font-bold text-gray-200 uppercase tracking-widest">Log Aktivitas Sistem</h3>
                </div>
                <a href="{{ route('admin.activity-log') }}" class="text-[9px] text-gray-500 hover:text-white">Lihat Log Lengkap</a>
            </div>
            <div class="p-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
                 @foreach($logAktivitas as $log)
                 <div class="bg-white/[0.02] p-2 rounded border border-white/5 flex items-center gap-2 hover:bg-white/[0.05] transition-colors">
                     <div class="w-1 h-8 bg-gray-600 rounded-full"></div>
                     <div class="flex-1 min-w-0">
                         <p class="text-[10px] text-white font-bold truncate">{{ $log['aksi'] }}</p>
                         <p class="text-[9px] text-gray-500 truncate">{{ $log['user'] }} • {{ $log['waktu'] }}</p>
                     </div>
                 </div>
                 @endforeach
            </div>
         </div>
    </div>
@endsection
