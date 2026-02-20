@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Laporan Kendala - Admin Dashboard')
@section('page-title', 'Laporan Kendala')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'laporan'])
@endsection

@section('dashboard-content')
    <div class="flex flex-col h-[calc(100vh-5rem)]" 
         x-data="{ 
            showToast: false, 
            toastMessage: '', 
            toastType: 'success',
            reports: {{ Js::from($laporan) }},
            showNotification(message, type = 'success') {
                this.toastMessage = message;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            },
            processReport(id) {
                const report = this.reports.find(r => r.id === id);
                if (report) {
                    report.status = 'In Progress';
                    this.showNotification('Laporan ' + id + ' sedang diproses', 'info');
                    this.$nextTick(() => lucide.createIcons());
                }
            },
            resolveReport(id) {
                const report = this.reports.find(r => r.id === id);
                if (report) {
                    report.status = 'Resolved';
                    this.showNotification('Laporan ' + id + ' telah diselesaikan', 'success');
                    this.$nextTick(() => lucide.createIcons());
                }
            },
            deleteReport(id) {
                this.reports = this.reports.filter(r => r.id !== id);
                this.showNotification('Laporan ' + id + ' dihapus', 'error');
                this.$nextTick(() => lucide.createIcons());
            }
         }">
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex-1 flex flex-col">
             <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="flag" class="w-5 h-5 text-[#D0B75B]"></i> 
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest" style="font-family: 'Inter';">Daftar Laporan Masuk</h2>
                </div>
                <span class="text-[10px] text-gray-500 font-mono" x-text="reports.length + ' laporan'"></span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                        <tr class="text-gray-600 text-[8px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-4 py-3">ID Laporan</th>
                            <th class="text-left px-4 py-3">Ruangan</th>
                            <th class="text-left px-4 py-3">Jenis Kendala</th>
                            <th class="text-left px-4 py-3">Keterangan</th>
                            <th class="text-left px-4 py-3">Pelapor</th>
                            <th class="text-center px-4 py-3">Waktu</th>
                            <th class="text-center px-4 py-3">Status</th>
                            <th class="text-center px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" x-init="$nextTick(() => lucide.createIcons())" x-effect="reports; $nextTick(() => lucide.createIcons())">
                        <template x-for="row in reports" :key="row.id">
                            <tr class="hover:bg-white/[0.01] transition-colors group text-[10px]">
                                <td class="px-4 py-3 text-gray-500 font-mono" x-text="row.id"></td>
                                <td class="px-4 py-3 text-white font-bold" x-text="row.room"></td>
                                <td class="px-4 py-3 text-[#D0B75B] font-bold" x-text="row.issue"></td>
                                <td class="px-4 py-3 text-gray-400 max-w-[200px] truncate" :title="row.description" x-text="row.description"></td>
                                <td class="px-4 py-3 text-gray-500" x-text="row.reporter"></td>
                                <td class="px-4 py-3 text-center text-gray-500 font-mono" x-text="row.time"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-widest transition-all duration-300"
                                          :class="{
                                              'bg-red-500/10 text-red-500 border border-red-500/20': row.status === 'Pending',
                                              'bg-blue-500/10 text-blue-500 border border-blue-500/20': row.status === 'In Progress',
                                              'bg-green-500/10 text-green-500 border border-green-500/20': row.status === 'Resolved'
                                          }"
                                          x-text="row.status"></span>
                                </td>
                                <td class="px-4 py-3 text-center" x-data="{ open: false }">
                                    <div class="relative">
                                        <button @click="open = !open" @click.outside="open = false" class="text-gray-500 hover:text-white transition-colors p-1 rounded hover:bg-white/5">
                                            <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                        </button>
                                        
                                        {{-- Dropdown Menu --}}
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 top-full mt-1 w-32 bg-[#1a1a1a] border border-white/10 rounded-lg shadow-xl z-50 text-left overflow-hidden origin-top-right"
                                             style="display: none;">
                                            
                                            <button @click="open = false; processReport(row.id)" class="w-full px-3 py-2 text-[10px] text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-2 transition-colors">
                                                <i data-lucide="loader-2" class="w-3 h-3 text-blue-400"></i>
                                                Proses
                                            </button>
                                            
                                            <button @click="open = false; resolveReport(row.id)" class="w-full px-3 py-2 text-[10px] text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-2 transition-colors">
                                                <i data-lucide="check-circle" class="w-3 h-3 text-green-400"></i>
                                                Selesai
                                            </button>
                                            
                                            <div class="h-px bg-white/5 my-0.5"></div>
                                            
                                            <button @click="open = false; deleteReport(row.id)" class="w-full px-3 py-2 text-[10px] text-red-400 hover:bg-red-500/10 flex items-center gap-2 transition-colors">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="reports.length === 0">
                            <td colspan="8" class="text-center py-8 text-gray-500">Belum ada laporan masuk</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Toast Notification --}}
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-4 right-4 z-[200] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px] bg-[#0A0A0A]"
             :class="{
                'border-green-500/20 text-green-500': toastType === 'success',
                'border-blue-500/20 text-blue-500': toastType === 'info',
                'border-red-500/20 text-red-500': toastType === 'error'
             }"
             style="display: none;">
            
            <div class="p-2 rounded-full" 
                 :class="{
                    'bg-green-500/10': toastType === 'success',
                    'bg-blue-500/10': toastType === 'info',
                    'bg-red-500/10': toastType === 'error'
                 }">
                <svg x-show="toastType === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg x-show="toastType === 'info'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <svg x-show="toastType === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : (toastType === 'info' ? 'Info' : 'Dihapus')"></h4>
                <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
            </div>

            <button @click="showToast = false" class="ml-auto text-gray-500 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>
@endsection
