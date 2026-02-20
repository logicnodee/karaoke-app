@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Log Aktivitas - Admin Dashboard')
@section('page-title', 'Log Aktivitas')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'activity-log'])
@endsection

@section('dashboard-content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    function activityLogComponent() {
        return {
            allLogs: @json($logAktivitas),
            searchQuery: '',
            filterUser: '',
            showFilterModal: false,
            filterDateFrom: '',
            filterDateTo: '',
            filterApplied: false,
            currentPage: 1,
            perPage: 10,
            openDropdown: null,
            showDetailModal: false,
            detailLog: null,
            showDeleteModal: false,
            deleteIndex: null,
            showToast: false,
            toastMessage: '',
            toastType: 'success',

            get uniqueUsers() {
                return [...new Set(this.allLogs.map(l => l.nama))];
            },

            get filteredLogs() {
                let result = this.allLogs;
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase();
                    result = result.filter(log =>
                        log.nama.toLowerCase().includes(q) ||
                        log.aksi.toLowerCase().includes(q) ||
                        log.ip.toLowerCase().includes(q)
                    );
                }
                if (this.filterUser) {
                    result = result.filter(log => log.nama === this.filterUser);
                }
                return result;
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredLogs.length / this.perPage));
            },

            get paginatedLogs() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredLogs.slice(start, start + this.perPage);
            },

            get pageNumbers() {
                const pages = [];
                const total = this.totalPages;
                const current = this.currentPage;
                if (total <= 5) {
                    for (let i = 1; i <= total; i++) pages.push(i);
                } else {
                    pages.push(1);
                    if (current > 3) pages.push('...');
                    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                        pages.push(i);
                    }
                    if (current < total - 2) pages.push('...');
                    pages.push(total);
                }
                return pages;
            },

            goToPage(page) {
                if (page === '...' || page < 1 || page > this.totalPages) return;
                this.currentPage = page;
                this.openDropdown = null;
            },

            resetFilters() {
                this.searchQuery = '';
                this.filterUser = '';
                this.filterDateFrom = '';
                this.filterDateTo = '';
                this.filterApplied = false;
                this.currentPage = 1;
            },

            applyDateFilter() {
                this.filterApplied = !!(this.filterDateFrom || this.filterDateTo);
                this.showFilterModal = false;
                this.currentPage = 1;
                this.notify('Filter tanggal diterapkan', 'success');
            },

            clearDateFilter() {
                this.filterDateFrom = '';
                this.filterDateTo = '';
                this.filterApplied = false;
                this.showFilterModal = false;
                this.currentPage = 1;
                this.notify('Filter tanggal dihapus', 'success');
            },

            toggleDropdown(index) {
                this.openDropdown = this.openDropdown === index ? null : index;
            },

            viewDetail(log) {
                this.detailLog = JSON.parse(JSON.stringify(log));
                this.showDetailModal = true;
                this.openDropdown = null;
            },

            confirmDelete(globalIndex) {
                this.deleteIndex = globalIndex;
                this.showDeleteModal = true;
                this.openDropdown = null;
            },

            executeDelete() {
                if (this.deleteIndex !== null) {
                    const deleted = this.allLogs.splice(this.deleteIndex, 1);
                    this.notify('Log aktivitas dihapus', 'success');
                    this.showDeleteModal = false;
                    this.deleteIndex = null;
                    if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
                }
            },

            getGlobalIndex(log) {
                return this.allLogs.indexOf(log);
            },

            exportLog() {
                const data = this.filteredLogs;
                if (data.length === 0) {
                    this.notify('Tidak ada data untuk di-export', 'error');
                    return;
                }

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                const today = new Date();
                const dateStr = today.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                const timeStr = today.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                // Header background
                doc.setFillColor(10, 10, 10);
                doc.rect(0, 0, pageWidth, 40, 'F');

                // Gold accent line
                doc.setFillColor(208, 183, 91);
                doc.rect(0, 40, pageWidth, 1.5, 'F');

                // Logo text
                doc.setTextColor(208, 183, 91);
                doc.setFontSize(18);
                doc.setFont('helvetica', 'bold');
                doc.text('SGRT KARAOKE', 15, 18);

                // Subtitle
                doc.setTextColor(180, 180, 180);
                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                doc.text('Sing & Joy Premium Karaoke', 15, 25);

                // Report title on right
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('LOG AKTIVITAS', pageWidth - 15, 18, { align: 'right' });

                // Date on right
                doc.setTextColor(150, 150, 150);
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.text('Dicetak: ' + dateStr + ' ' + timeStr, pageWidth - 15, 25, { align: 'right' });
                doc.text('Total: ' + data.length + ' aktivitas', pageWidth - 15, 31, { align: 'right' });

                // Table data
                const tableData = data.map((log, i) => [
                    (i + 1).toString(),
                    log.nama,
                    log.aksi,
                    log.waktu,
                    log.ip
                ]);

                // Generate table
                doc.autoTable({
                    head: [['No', 'Pengguna', 'Aktivitas', 'Waktu', 'IP Address']],
                    body: tableData,
                    startY: 48,
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 3,
                        textColor: [50, 50, 50],
                        lineColor: [220, 220, 220],
                        lineWidth: 0.3,
                    },
                    headStyles: {
                        fillColor: [30, 30, 30],
                        textColor: [208, 183, 91],
                        fontStyle: 'bold',
                        fontSize: 8,
                        halign: 'center',
                    },
                    alternateRowStyles: {
                        fillColor: [248, 248, 248],
                    },
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 12 },
                        1: { cellWidth: 30, fontStyle: 'bold' },
                        2: { cellWidth: 'auto' },
                        3: { cellWidth: 32, fontSize: 7 },
                        4: { cellWidth: 28, halign: 'center', fontSize: 7, font: 'courier' },
                    },
                    didDrawPage: function(data) {
                        // Footer on every page
                        doc.setFillColor(245, 245, 245);
                        doc.rect(0, pageHeight - 15, pageWidth, 15, 'F');
                        doc.setFillColor(208, 183, 91);
                        doc.rect(0, pageHeight - 15, pageWidth, 0.5, 'F');
                        doc.setTextColor(130, 130, 130);
                        doc.setFontSize(7);
                        doc.setFont('helvetica', 'normal');
                        doc.text('SGRT Karaoke - Sing & Joy | Dokumen ini digenerate secara otomatis oleh sistem', 15, pageHeight - 7);
                        doc.text('Halaman ' + data.pageNumber, pageWidth - 15, pageHeight - 7, { align: 'right' });
                    }
                });

                // Save
                doc.save('log_aktivitas_' + today.toISOString().slice(0,10) + '.pdf');
                this.notify('PDF berhasil di-download (' + data.length + ' data)', 'success');
            },

            notify(message, type) {
                this.toastMessage = message;
                this.toastType = type || 'success';
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3000);
            },

            init() {
                this.$watch('searchQuery', () => { this.currentPage = 1; });
                this.$watch('filterUser', () => { this.currentPage = 1; });
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            }
        };
    }
</script>

<div x-data="activityLogComponent()" @click="openDropdown = null">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Log Aktivitas Sistem</h2>
            <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Pantau semua aktivitas pengguna dalam sistem</p>
        </div>
        <div class="flex gap-3">
             <button @click="showFilterModal = true" 
                     class="text-xs font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 border"
                     :class="filterApplied ? 'bg-[#D0B75B]/10 text-[#D0B75B] border-[#D0B75B]/30' : 'bg-white/5 text-white border-white/10 hover:bg-white/10'">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span x-text="filterApplied ? 'Filter Aktif' : 'Filter Tanggal'"></span>
            </button>
            <button @click="exportLog()" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Log
            </button>
        </div>
    </div>

    <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex flex-col" style="min-height: calc(100vh - 180px);">
        {{-- Table Header --}}
        <div class="px-5 py-3 border-b border-white/5 bg-zinc-900/20 flex items-center justify-between gap-3">
            <h2 class="text-xs font-bold text-gray-200 uppercase tracking-widest flex-shrink-0" style="font-family: 'Inter';">Riwayat Aktivitas</h2>
            <div class="flex items-center gap-2">
                {{-- User Filter --}}
                <div class="relative">
                    <select x-model="filterUser" 
                            class="bg-black/50 border border-white/10 rounded-lg px-3 py-1 text-[11px] text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer pr-7 transition-all">
                        <option value="">Semua User</option>
                        <template x-for="user in uniqueUsers" :key="user">
                            <option :value="user" x-text="user"></option>
                        </template>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 text-gray-500 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                {{-- Search --}}
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari aktivitas..." 
                           class="bg-black/50 border border-white/10 rounded-lg pl-3 pr-7 py-1 text-[11px] text-white focus:border-[#D0B75B] outline-none w-44 transition-all">
                    <template x-if="searchQuery">
                        <button @click="searchQuery = ''" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        </div>
        
        {{-- Table Content --}}
        <div class="divide-y divide-white/5 flex-1">
            <template x-for="(log, idx) in paginatedLogs" :key="idx">
                <div class="px-5 py-2.5 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                    <div class="flex items-center gap-3">
                        <div>
                            <p class="text-xs text-gray-300" style="font-family: 'Inter';">
                                <span class="text-white font-bold" x-text="log.nama"></span> <span x-text="log.aksi"></span>
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                 <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wider" x-text="log.waktu"></p>
                                 <span class="text-[9px] text-gray-600 font-mono bg-white/5 px-1.5 py-0.5 rounded border border-white/5" x-text="log.ip"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Action Dropdown --}}
                    <div class="relative" @click.stop>
                        <button @click="toggleDropdown(idx)" 
                                class="p-1.5 hover:bg-white/10 rounded-lg transition-all text-gray-400 hover:text-white"
                                :class="openDropdown === idx ? 'opacity-100 bg-white/10 text-white' : 'opacity-0 group-hover:opacity-100'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg>
                        </button>
                        
                        <div x-show="openDropdown === idx" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-1 w-40 bg-[#0A0A0A] border border-white/10 rounded-xl shadow-2xl z-20 overflow-hidden py-1"
                             style="display: none;">
                            <button @click="viewDetail(log)" class="w-full text-left px-3 py-2 text-[11px] text-gray-300 hover:bg-white/5 hover:text-white transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Lihat Detail
                            </button>
                            <button @click="confirmDelete(getGlobalIndex(log))" class="w-full text-left px-3 py-2 text-[11px] text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Hapus Log
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredLogs.length === 0">
                <div class="px-5 py-10 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-10 h-10 text-gray-700 mb-3 mx-auto">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <p class="text-sm font-medium">Tidak ada aktivitas ditemukan</p>
                    <p class="text-[10px] text-gray-600 mt-1">Coba ubah kata kunci pencarian atau filter</p>
                    <button @click="resetFilters()" class="mt-3 text-[10px] text-[#D0B75B] hover:underline font-semibold">Reset Filter</button>
                </div>
            </template>
        </div>
        
        {{-- Pagination --}}
        <div class="px-5 py-3 bg-black/40 text-center border-t border-white/5 flex items-center justify-between mt-auto">
            <span class="text-[9px] text-gray-500" x-text="'Menampilkan ' + paginatedLogs.length + ' dari ' + filteredLogs.length + ' aktivitas'"></span>
            <div class="flex gap-1" x-show="totalPages > 1">
                <button @click="goToPage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                
                <div class="flex items-center gap-1 px-1">
                    <template x-for="(page, pi) in pageNumbers" :key="'p'+pi">
                        <button @click="goToPage(page)" 
                                class="w-7 h-7 rounded-lg font-bold text-[10px] transition-colors"
                                :class="page === '...' ? 'cursor-default text-gray-600' : (currentPage === page ? 'bg-[#D0B75B] text-black' : 'bg-transparent text-gray-400 hover:text-white')"
                                :disabled="page === '...'"
                                x-text="page"></button>
                    </template>
                </div>
                
                <button @click="goToPage(currentPage + 1)" 
                        :disabled="currentPage === totalPages"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Tanggal Modal --}}
    <template x-teleport="body">
        <div x-show="showFilterModal" style="display: none;" 
             class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
            <div @click.away="showFilterModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-sm overflow-hidden">
                 <div class="px-5 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                    <h3 class="text-white font-bold text-sm">Filter Tanggal</h3>
                    <button @click="showFilterModal = false" class="text-gray-500 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                 </div>
                 <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1.5 tracking-wider">Dari Tanggal</label>
                        <input type="date" x-model="filterDateFrom" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:border-[#D0B75B] outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1.5 tracking-wider">Sampai Tanggal</label>
                        <input type="date" x-model="filterDateTo" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:border-[#D0B75B] outline-none">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button @click="clearDateFilter()" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-bold uppercase tracking-widest py-2.5 rounded-xl transition-all text-[10px]">Reset</button>
                        <button @click="applyDateFilter()" class="flex-1 bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-2.5 rounded-xl transition-all text-[10px]">Terapkan</button>
                    </div>
                 </div>
            </div>
        </div>
    </template>

    {{-- Detail Modal --}}
    <template x-teleport="body">
        <div x-show="showDetailModal" style="display: none;" 
             class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
            <div @click.away="showDetailModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-sm overflow-hidden">
                 <div class="px-5 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                    <h3 class="text-white font-bold text-sm">Detail Aktivitas</h3>
                    <button @click="showDetailModal = false" class="text-gray-500 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                 </div>
                 <div class="p-5 space-y-3" x-show="detailLog">
                    <div class="flex justify-between items-start">
                        <span class="text-[9px] uppercase font-bold text-gray-600 tracking-wider">Pengguna</span>
                        <span class="text-xs text-white font-bold" x-text="detailLog ? detailLog.nama : ''"></span>
                    </div>
                    <div class="border-t border-white/5"></div>
                    <div>
                        <span class="text-[9px] uppercase font-bold text-gray-600 tracking-wider block mb-1">Aktivitas</span>
                        <p class="text-xs text-gray-300" x-text="detailLog ? detailLog.aksi : ''"></p>
                    </div>
                    <div class="border-t border-white/5"></div>
                    <div class="flex justify-between items-start">
                        <span class="text-[9px] uppercase font-bold text-gray-600 tracking-wider">Waktu</span>
                        <span class="text-xs text-gray-400" x-text="detailLog ? detailLog.waktu : ''"></span>
                    </div>
                    <div class="border-t border-white/5"></div>
                    <div class="flex justify-between items-start">
                        <span class="text-[9px] uppercase font-bold text-gray-600 tracking-wider">IP Address</span>
                        <span class="text-[10px] text-gray-400 font-mono bg-white/5 px-2 py-0.5 rounded border border-white/5" x-text="detailLog ? detailLog.ip : ''"></span>
                    </div>
                    <div class="pt-3">
                        <button @click="showDetailModal = false" class="w-full bg-white/5 hover:bg-white/10 text-white font-bold uppercase tracking-widest py-2.5 rounded-xl transition-all text-[10px]">Tutup</button>
                    </div>
                 </div>
            </div>
        </div>
    </template>

    {{-- Delete Confirmation Modal --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" style="display: none;" 
             class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
            <div @click.away="showDeleteModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-xs overflow-hidden text-center">
                 <div class="p-6">
                    <div class="w-14 h-14 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-3 border border-red-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-7 h-7 text-red-500">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm mb-1">Hapus Log?</h3>
                    <p class="text-gray-400 text-[10px] mb-5">Apakah Anda yakin ingin menghapus log aktivitas ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-2">
                        <button @click="showDeleteModal = false" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-bold py-2.5 rounded-xl transition-all uppercase tracking-widest text-[10px]">Batal</button>
                        <button @click="executeDelete()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl transition-all uppercase tracking-widest text-[10px] shadow-lg shadow-red-500/20">Ya, Hapus</button>
                    </div>
                 </div>
            </div>
        </div>
    </template>

    {{-- Toast --}}
    <template x-teleport="body">
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed top-4 right-4 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[280px]"
             :class="toastType === 'success' ? 'bg-[#0A0A0A] border-green-500/20 text-green-500' : 'bg-[#0A0A0A] border-red-500/20 text-red-500'">
            <div class="p-1.5 rounded-full" :class="toastType === 'success' ? 'bg-green-500/10' : 'bg-red-500/10'">
                <template x-if="toastType === 'success'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </template>
                <template x-if="toastType === 'error'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </template>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : 'Gagal'"></h4>
                <p class="text-[10px] text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>
    </template>

</div>
@endsection
