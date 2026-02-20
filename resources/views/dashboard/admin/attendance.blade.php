@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Absensi Karyawan - Admin Dashboard')
@section('page-title', 'Absensi Karyawan')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'absensi'])
@endsection

@section('dashboard-content')
    <div x-data="{
        todayStats: {
            total: 12,
            present: 8,
            absent: 4,
            late: 1
        },
        attendanceList: [
            {
                nama: 'Admin Utama',
                role: 'Admin',
                date: '17 Feb 2026',
                check_in: '08:55',
                check_out: '-',
                ip: '192.168.1.10',
                status: 'Online',
                status_color: 'text-green-500',
                status_bg: 'bg-green-500/10 border-green-500/20'
            },
            {
                nama: 'Sari Putri',
                role: 'Kasir',
                date: '17 Feb 2026',
                check_in: '09:10',
                check_out: '-',
                ip: '192.168.1.15',
                status: 'Terlambat',
                status_color: 'text-yellow-500',
                status_bg: 'bg-yellow-500/10 border-yellow-500/20'
            },
            {
                nama: 'Budi Santoso',
                role: 'Operator',
                date: '17 Feb 2026',
                check_in: '08:45',
                check_out: '16:00',
                ip: '192.168.1.22',
                status: 'Offline',
                status_color: 'text-gray-500',
                status_bg: 'bg-white/5 border-white/10'
            },
            {
                nama: 'Rahmawati',
                role: 'Kasir',
                date: '17 Feb 2026',
                check_in: '-',
                check_out: '-',
                ip: '-',
                status: 'Absen',
                status_color: 'text-red-500',
                status_bg: 'bg-red-500/10 border-red-500/20'
            }
        ],
        search: ''
    }">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-[#080808] border border-white/5 rounded-2xl p-5">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-2">Total Karyawan</div>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-white" x-text="todayStats.total"></h3>
                    <span class="text-xs text-gray-400 mb-1">orang</span>
                </div>
            </div>
            <div class="bg-[#080808] border border-white/5 rounded-2xl p-5">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-2">Hadir Hari Ini</div>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-green-500" x-text="todayStats.present"></h3>
                    <span class="text-xs text-gray-400 mb-1">orang</span>
                </div>
            </div>
            <div class="bg-[#080808] border border-white/5 rounded-2xl p-5">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-2">Tidak Hadir</div>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-red-500" x-text="todayStats.absent"></h3>
                    <span class="text-xs text-gray-400 mb-1">orang</span>
                </div>
            </div>
             <div class="bg-[#080808] border border-white/5 rounded-2xl p-5">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-2">Terlambat</div>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-black text-yellow-500" x-text="todayStats.late"></h3>
                    <span class="text-xs text-gray-400 mb-1">orang</span>
                </div>
            </div>
        </div>

        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Log Absensi Harian</h2>
                <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Pantau kehadiran dan aktivitas login karyawan hari ini</p>
            </div>
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari nama karyawan..." 
                       class="bg-[#080808] border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white focus:border-[#D0B75B] outline-none w-full md:w-64 transition-all">
                <i data-lucide="search" class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                        <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-5 py-4">Karyawan</th>
                            <th class="text-left px-5 py-4">Tanggal</th>
                            <th class="text-left px-5 py-4">Jam Masuk (Login)</th>
                            <th class="text-left px-5 py-4">Jam Keluar (Logout)</th>
                            <th class="text-left px-5 py-4">IP Address</th>
                            <th class="text-center px-5 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(log, index) in attendanceList.filter(l => l.nama.toLowerCase().includes(search.toLowerCase()))" :key="index">
                            <tr class="hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 font-bold text-[10px]" x-text="log.nama.charAt(0)"></div>
                                        <div>
                                            <div class="text-white font-bold" x-text="log.nama"></div>
                                            <div class="text-[9px] text-gray-500 uppercase tracking-wider mt-0.5" x-text="log.role"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-400 font-medium" x-text="log.date"></td>
                                <td class="px-5 py-4 text-white font-mono" x-text="log.check_in"></td>
                                <td class="px-5 py-4 text-white font-mono" x-text="log.check_out"></td>
                                <td class="px-5 py-4">
                                     <div class="flex items-center gap-1 opacity-70">
                                        <i data-lucide="globe" class="w-3 h-3 text-gray-500"></i>
                                        <span class="text-[10px] font-mono text-gray-300" x-text="log.ip"></span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded text-[9px] font-black uppercase tracking-widest border"
                                          :class="`${log.status_bg} ${log.status_color}`"
                                          x-text="log.status">
                                    </span>
                                </td>
                            </tr>
                        </template>
                         <template x-if="attendanceList.length === 0">
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500 italic">Belum ada data absensi hari ini.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('attendance', () => ({
                // Logic can be moved here if needed
            }))
        })
    </script>
@endsection
