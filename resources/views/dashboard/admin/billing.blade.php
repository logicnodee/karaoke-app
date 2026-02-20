@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Riwayat Billing - Admin Dashboard')
@section('page-title', 'Riwayat Billing')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'billing'])
@endsection

@section('dashboard-content')
    <div x-data="billingSystem()">
    {{-- Filter Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-[#D0B75B]"></i>
                Riwayat Billing
            </h2>
            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-widest mt-1">
                Detail Transaksi Billing & Pembayaran
            </p>
        </div>

        {{-- Filter Controls --}}
        <form action="{{ route('admin.billing') }}" method="GET" class="flex items-center gap-2 bg-[#0A0A0A] p-1.5 rounded-xl border border-white/10">
            <select name="filter_type" onchange="this.form.submit()" 
                    class="bg-transparent text-xs font-bold text-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:bg-white/5 cursor-pointer hover:text-white transition-colors border-none">
                <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }} class="bg-[#18181b] text-white">Bulanan</option>
                <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }} class="bg-[#18181b] text-white">Periode</option>
            </select>
            
            <div class="h-5 w-px bg-white/10"></div>
            
            @if($filterType == 'monthly')
            <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" style="color-scheme: dark;"
                   class="bg-transparent text-xs font-bold text-white px-3 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border border-white/10 cursor-pointer hover:bg-white/5 transition-colors">
            @else
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" style="color-scheme: dark;"
                       class="bg-transparent text-xs font-bold text-white px-2 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border border-white/10 cursor-pointer hover:bg-white/5 transition-colors">
                <span class="text-gray-500 text-[10px] uppercase font-bold">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" style="color-scheme: dark;"
                       class="bg-transparent text-xs font-bold text-white px-2 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border border-white/10 cursor-pointer hover:bg-white/5 transition-colors">
            </div>
            @endif
        </form>
    </div>

    {{-- Riwayat Billing Table --}}
    <div class="bg-black border border-white/5 rounded-2xl overflow-hidden shadow-2xl shadow-black relative group">
        <div class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent pointer-events-none"></div>
        <div class="px-4 py-4 border-b border-white/5 bg-zinc-900/20 flex items-center justify-between backdrop-blur-sm">
            <div class="flex items-center gap-3">
                 <div class="p-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-purple-400"></i>
                 </div>
                <h2 class="text-[10px] font-bold text-gray-200 uppercase tracking-[0.2em]" style="font-family: 'Inter';">Data Transaksi Billing</h2>
            </div>
             {{-- Search Filter --}}
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari No. Tagihan / Tamu..." 
                       class="bg-black/50 border border-white/10 text-[10px] text-white rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:border-purple-500/50 placeholder-gray-600 transition-colors w-48">
                <i data-lucide="search" class="w-3 h-3 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs" style="font-family: 'Inter';">
                <thead>
                    <tr class="text-gray-500 text-[9px] font-black uppercase tracking-[0.2em] bg-black/40 border-b border-white/5">
                        <th class="text-left px-4 py-3">No. Tagihan</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Ruangan</th>
                        <th class="text-left px-4 py-3">Tamu</th>
                        <th class="text-left px-4 py-3">Kasir</th>
                        <th class="text-center px-4 py-3">Durasi</th>
                        <th class="text-center px-4 py-3">Extend</th>
                        <th class="text-right px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <template x-for="(bill, index) in filteredBills" :key="index">
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-3 text-white font-mono text-[10px] opacity-70" x-text="bill.no_tagihan"></td>
                            <td class="px-4 py-3 text-gray-400 text-[10px] uppercase font-bold tracking-wider" x-text="bill.tanggal"></td>
                            <td class="px-4 py-3 text-white font-bold" x-text="bill.ruangan"></td>
                            <td class="px-4 py-3 text-gray-400 font-medium" x-text="bill.tamu"></td>
                            <td class="px-4 py-3 text-gray-500 font-medium text-[10px] uppercase" x-text="bill.kasir"></td>
                            <td class="px-4 py-3 text-center text-gray-400" x-text="bill.durasi"></td>
                            <td class="px-4 py-3 text-center">
                                <template x-if="bill.extend">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 font-bold" x-text="'+' + bill.extend"></span>
                                </template>
                                <template x-if="!bill.extend">
                                    <span class="text-gray-800 font-bold">-</span>
                                </template>
                            </td>
                            <td class="px-4 py-3 text-right text-[#D0B75B] font-bold" x-text="formatRupiah(bill.total)"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[8px] px-3 py-1 rounded-full bg-green-500/10 text-green-400 border border-green-500/20 font-black uppercase tracking-widest">Lunas</span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredBills.length === 0">
                        <tr>
                             <td colspan="9" class="px-4 py-6 text-center text-gray-500 italic">Tidak ada transaksi ditemukan</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('billingSystem', () => ({
                search: '',
                bills: @json($riwayatBilling),

                get filteredBills() {
                    if (this.search === '') {
                        return this.bills;
                    }
                    const lowerSearch = this.search.toLowerCase();
                    return this.bills.filter(bill => {
                        return (bill.no_tagihan && bill.no_tagihan.toLowerCase().includes(lowerSearch)) ||
                               (bill.tamu && bill.tamu.toLowerCase().includes(lowerSearch)) ||
                               (bill.ruangan && bill.ruangan.toLowerCase().includes(lowerSearch));
                    });
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                },
                
                init() {
                    this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
                    this.$watch('filteredBills', () => {
                        this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
                    });
                }
            }));
        });
    </script>
@endsection
