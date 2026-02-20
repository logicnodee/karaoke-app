@extends('layouts.dashboard')

@section('dashboard-role', 'Kasir Panel')
@section('dashboard-role-icon', 'user-check')

@section('title', 'Pesanan Aktif - Kasir Dashboard')
@section('page-title', 'Kelola Pesanan Dapur')

@section('sidebar-nav')
    @include('dashboard.kasir._sidebar', ['active' => 'pesanan-aktif'])
@endsection

@section('dashboard-content')
    <style>
        @media print {
            body * { visibility: hidden; }
            .no-print, .no-print * { display: none !important; }
            #receipt-printable, #receipt-printable * { visibility: visible; }
            #receipt-printable {
                position: fixed !important; 
                left: 0 !important; top: 0 !important;
                width: 56mm !important; /* 58mm paper usually has ~56mm print area */
                margin: 0 !important; padding: 2mm !important;
                background: white; color: black; border: none; box-shadow: none;
                height: auto; overflow: visible; z-index: 9999;
                font-family: 'Courier New', Courier, monospace;
                font-size: 9px; /* Smaller font for 58mm */
            }
            @page { size: 58mm auto; margin: 0mm; }
            html, body { height: auto; overflow: hidden; background: white; margin: 0; }
        }
    </style>

    <div x-data="kitchenDisplay()" x-init="init()" class="h-full relative">
        
        {{-- MAIN LAYOUT --}}
        <div class="h-full flex flex-col gap-4 no-print">
            {{-- FILTERS & STATS --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-[#0A0A0A] border border-white/5 p-3 rounded-xl">
                <div class="flex gap-2 overflow-x-auto w-full md:w-auto no-scrollbar">
                    <button @click="filterStatus = 'Semua'" 
                            class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                            :class="filterStatus === 'Semua' ? 'bg-[#D0B75B] text-black border-[#D0B75B]' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-white'">
                        Semua <span class="ml-1 opacity-60" x-text="'(' + orders.length + ')'"></span>
                    </button>
                    <button @click="filterStatus = 'Baru'" 
                            class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                            :class="filterStatus === 'Baru' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-blue-500'">
                        Baru <span class="ml-1 opacity-60" x-text="'(' + orders.filter(o => o.status === 'Baru').length + ')'"></span>
                    </button>
                    <button @click="filterStatus = 'Diproses'" 
                            class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                            :class="filterStatus === 'Diproses' ? 'bg-yellow-600 text-white border-yellow-600 shadow-lg shadow-yellow-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-yellow-500'">
                        Diproses <span class="ml-1 opacity-60" x-text="'(' + orders.filter(o => o.status === 'Diproses').length + ')'"></span>
                    </button>
                    <button @click="filterStatus = 'Selesai'" 
                            class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                            :class="filterStatus === 'Selesai' ? 'bg-green-600 text-white border-green-600 shadow-lg shadow-green-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-green-500'">
                        Selesai <span class="ml-1 opacity-60" x-text="'(' + orders.filter(o => o.status === 'Selesai').length + ')'"></span>
                    </button>
                </div>
                
                <div class="relative w-full md:w-64">
                    <input type="text" x-model="searchQuery" placeholder="Cari Ruangan / Order ID..." 
                           class="w-full bg-zinc-900 border border-white/10 rounded-lg pl-9 pr-3 py-2 text-xs font-bold text-white focus:border-[#D0B75B] outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>

            {{-- ORDERS GRID --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    
                    <template x-for="order in filteredOrders" :key="order.id">
                        <div class="bg-[#0A0A0A] border border-white/5 rounded-xl overflow-hidden flex flex-col hover:shadow-xl transition-all group relative"
                             :class="{'border-blue-500/50': order.status === 'Baru', 'border-yellow-500/50': order.status === 'Diproses', 'opacity-60 hover:opacity-100': order.status === 'Selesai'}">
                            
                            {{-- Header Compact --}}
                            <div class="px-3 py-2 border-b border-white/5 bg-zinc-900/40 flex justify-between items-center">
                                <div class="min-w-0">
                                    <h4 class="text-sm font-black text-white truncate" x-text="order.room"></h4>
                                    <p class="text-[9px] text-gray-500 font-mono truncate" x-text="'#' + order.id"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase block mb-0.5 text-center"
                                          :class="{
                                              'bg-blue-500 text-black': order.status === 'Baru',
                                              'bg-yellow-500 text-black': order.status === 'Diproses',
                                              'bg-green-500 text-black': order.status === 'Selesai'
                                          }"
                                          x-text="order.status">
                                    </span>
                                    <span class="text-[9px] text-gray-400 block" x-text="order.time"></span>
                                </div>
                            </div>

                            {{-- Items List Compact --}}
                            <div class="p-3 flex-1 space-y-1.5 min-h-[80px]">
                                <template x-for="item in order.items" :key="item.name">
                                    <div class="flex justify-between items-start text-[10px] leading-tight">
                                        <div class="flex gap-1.5 overflow-hidden">
                                            <span class="font-bold text-[#D0B75B] shrink-0" x-text="item.qty + 'x'"></span>
                                            <span class="text-gray-300 truncate" x-text="item.name"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="order.items.length > 3">
                                    <p class="text-[9px] text-gray-500 italic text-center pt-1">+ item lainnya...</p>
                                </template>
                            </div>

                            {{-- Footer Compact --}}
                            <div class="p-2 bg-zinc-900/30 border-t border-white/5 space-y-2">
                                 <div class="flex justify-between items-center">
                                    <span class="text-[9px] text-gray-500 font-bold uppercase">Total</span>
                                    <span class="text-xs font-black text-[#D0B75B]" x-text="formatMoney(order.total)"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click.stop="showReceipt(order)" 
                                            class="relative z-10 py-1.5 rounded border border-white/10 hover:bg-white/5 text-gray-400 hover:text-white font-bold text-[9px] uppercase flex items-center justify-center gap-1 transition-colors">
                                        <i data-lucide="printer" class="w-3 h-3"></i> Struk
                                    </button>
                                    
                                    <template x-if="order.status === 'Baru'">
                                        <button @click.stop="updateStatus(order.id, 'Diproses')" 
                                                class="relative z-10 py-1.5 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-[9px] uppercase">
                                            Proses
                                        </button>
                                    </template>
                                    
                                    <template x-if="order.status === 'Diproses'">
                                        <button @click.stop="updateStatus(order.id, 'Selesai')" 
                                                class="relative z-10 py-1.5 rounded bg-green-600 hover:bg-green-500 text-white font-bold text-[9px] uppercase">
                                            Selesai
                                        </button>
                                    </template>

                                    <template x-if="order.status === 'Selesai'">
                                        <button disabled class="relative z-10 py-1.5 rounded bg-zinc-800 text-gray-600 font-bold text-[9px] uppercase cursor-not-allowed">
                                            Selesai
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- RECEIPT PREVIEW MODAL --}}
        <div x-show="showReceiptModal" 
             class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
             style="display: none;"
             x-transition.opacity>
            
            <div class="absolute inset-0" @click="showReceiptModal = false"></div>

            <template x-if="activeOrder">
                 <div class="bg-white w-full max-w-sm rounded-none shadow-2xl relative z-20 flex flex-col max-h-[90vh]">
                    {{-- Receipt Content (White Paper Style) --}}
                    <div id="receipt-printable" class="p-8 font-mono text-black text-xs leading-relaxed bg-white overflow-y-auto">
                        {{-- Logo/Header --}}
                        <div class="text-center mb-6">
                            <h2 class="text-xl font-black uppercase mb-1 tracking-widest">SGRT KARAOKE</h2>
                            <p class="text-[10px] text-gray-500">Jl. Hiburan Malam No. 99, Jakarta</p>
                            <p class="text-[10px] text-gray-500">Telp: (021) 555-9999</p>
                        </div>
                        
                        {{-- Meta --}}
                        <div class="border-b-2 border-dashed border-black/20 pb-4 mb-4 space-y-1">
                            <div class="flex justify-between">
                                <span>TANGGAL</span>
                                <span x-text="new Date().toLocaleDateString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>WAKTU</span>
                                <span x-text="activeOrder.time"></span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>ORDER ID</span>
                                <span x-text="'#' + activeOrder.id"></span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>RUANGAN</span>
                                <span x-text="activeOrder.room"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>TYPE</span>
                                <span x-text="activeOrder.type"></span>
                            </div>
                        </div>

                        {{-- Items --}}
                        <div class="mb-4">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-black">
                                        <th class="pb-1">ITEM</th>
                                        <th class="pb-1 text-center">QTY</th>
                                        <th class="pb-1 text-right">PRICE</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-dashed divide-black/10">
                                    <template x-for="item in activeOrder.items" :key="item.name">
                                        <tr>
                                            <td class="py-2 pr-2">
                                                <div class="font-bold" x-text="item.name"></div>
                                            </td>
                                            <td class="py-2 text-center align-top" x-text="item.qty"></td>
                                            <td class="py-2 text-right align-top" x-text="formatMoney(item.price * item.qty, true)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Totals --}}
                        <div class="border-t-2 border-dashed border-black/20 pt-4 space-y-1">
                            <div class="flex justify-between font-bold text-sm">
                                <span>TOTAL</span>
                                <span x-text="formatMoney(activeOrder.total, true)"></span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500 mt-2">
                                <span>TAX (10%)</span>
                                <span>INCLUDED</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500">
                                <span>SERVICE (5%)</span>
                                <span>INCLUDED</span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="text-center mt-8 pt-4 border-t border-black/10">
                            <p class="font-bold mb-1">TERIMA KASIH</p>
                            <p class="text-[10px] text-gray-500">Silakan datang kembali!</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div id="receipt-actions" class="p-4 bg-gray-100 border-t border-gray-200 flex gap-3">
                        <button @click="showReceiptModal = false" class="flex-1 py-3 text-gray-600 font-bold text-xs uppercase border border-gray-300 rounded hover:bg-gray-200">
                            Tutup
                        </button>
                        <button @click="printReceipt()" class="flex-[2] py-3 bg-black text-white font-bold text-xs uppercase rounded hover:bg-gray-800 flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Struk
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kitchenDisplay', () => ({
                filterStatus: 'Semua',
                searchQuery: '',
                orders: [
                    { id: 'OD-7782', room: 'Room 104', type: 'Reguler', time: 'Baru Saja', status: 'Baru', items: [{ name: 'Southwest Eggroll', qty: 2, price: 17000 }, { name: 'Chicken Wings - Small', qty: 2, price: 340000 }, { name: 'Seared Ahi Salad', qty: 2, price: 240000 }], total: 954000 },
                    { id: 'OD-7781', room: 'VVIP 01', type: 'Suite', time: '12 Menit Lalu', status: 'Diproses', items: [{ name: 'Bintang Radler', qty: 5, price: 45000 }, { name: 'French Fries', qty: 1, price: 85000 }], total: 310000 },
                    { id: 'OD-7780', room: 'Room 205', type: 'Reguler', time: '15 Menit Lalu', status: 'Diproses', items: [{ name: 'Nasi Goreng Kampung', qty: 3, price: 45000 }, { name: 'Es Teh Manis', qty: 3, price: 12000 }], total: 171000 },
                    { id: 'OD-7779', room: 'VIP 02', type: 'VIP', time: '20 Menit Lalu', status: 'Selesai', items: [{ name: 'Spaghetti Carbonara', qty: 2, price: 65000 }, { name: 'Cola Pitcher', qty: 1, price: 55000 }], total: 185000 },
                    { id: 'OD-7778', room: 'Room 101', type: 'Reguler', time: '25 Menit Lalu', status: 'Selesai', items: [{ name: 'Mineral Water', qty: 4, price: 10000 }], total: 40000 },
                    { id: 'OD-7777', room: 'Room 108', type: 'Reguler', time: '30 Menit Lalu', status: 'Selesai', items: [{ name: 'Chicken Popcorn', qty: 2, price: 35000 }], total: 70000 },
                    { id: 'OD-7776', room: 'Room 303', type: 'Party', time: '35 Menit Lalu', status: 'Baru', items: [{ name: 'Fruit Platter XL', qty: 1, price: 150000 }, { name: 'Cocktail Mix', qty: 2, price: 250000 }], total: 650000 },
                    { id: 'OD-7775', room: 'Room 201', type: 'Reguler', time: '40 Menit Lalu', status: 'Diproses', items: [{ name: 'Onion Rings', qty: 1, price: 25000 }], total: 25000 },
                    { id: 'OD-7774', room: 'VVIP 02', type: 'VVIP', time: '42 Menit Lalu', status: 'Diproses', items: [{ name: 'BBQ Ribs', qty: 2, price: 180000 }, { name: 'Red Wine', qty: 1, price: 1200000 }], total: 1560000 },
                    { id: 'OD-7773', room: 'Room 102', type: 'Reguler', time: '45 Menit Lalu', status: 'Selesai', items: [{ name: 'Pisang Goreng Keju', qty: 2, price: 25000 }], total: 50000 },
                    { id: 'OD-7772', room: 'Room 105', type: 'Reguler', time: '50 Menit Lalu', status: 'Baru', items: [{ name: 'Mie Goreng Spesial', qty: 4, price: 40000 }], total: 160000 },
                    { id: 'OD-7771', room: 'Room 206', type: 'Reguler', time: '55 Menit Lalu', status: 'Selesai', items: [{ name: 'Ice Cream Sundae', qty: 3, price: 30000 }], total: 90000 },
                    { id: 'OD-7770', room: 'VIP 01', type: 'VIP', time: '1 Jam Lalu', status: 'Selesai', items: [{ name: 'Pizza Meat Lover', qty: 2, price: 110000 }, { name: 'Cola', qty: 4, price: 15000 }], total: 280000 },
                    { id: 'OD-7769', room: 'Room 301', type: 'Party', time: '1 Jam Lalu', status: 'Diproses', items: [{ name: 'Buffet Set A', qty: 1, price: 1500000 }], total: 1500000 },
                    { id: 'OD-7768', room: 'Room 109', type: 'Reguler', time: '1.2 Jam Lalu', status: 'Selesai', items: [{ name: 'French Fries', qty: 1, price: 25000 }], total: 25000 },
                    { id: 'OD-7767', room: 'Room 110', type: 'Reguler', time: '1.3 Jam Lalu', status: 'Selesai', items: [{ name: 'Lemon Tea', qty: 2, price: 18000 }], total: 36000 },
                    { id: 'OD-7766', room: 'VVIP 03', type: 'Suite', time: '1.4 Jam Lalu', status: 'Baru', items: [{ name: 'Sushi Platter', qty: 2, price: 220000 }], total: 440000 },
                    { id: 'OD-7765', room: 'Room 202', type: 'Reguler', time: '1.5 Jam Lalu', status: 'Diproses', items: [{ name: 'Nachos Supreme', qty: 1, price: 45000 }], total: 45000 },
                    { id: 'OD-7764', room: 'Room 203', type: 'Reguler', time: '1.6 Jam Lalu', status: 'Selesai', items: [{ name: 'Cappuccino', qty: 2, price: 30000 }], total: 60000 },
                    { id: 'OD-7763', room: 'Room 204', type: 'Reguler', time: '1.8 Jam Lalu', status: 'Selesai', items: [{ name: 'Mineral Water', qty: 1, price: 10000 }], total: 10000 }
                ],
                showReceiptModal: false,
                activeOrder: null,

                init() {
                    this.$watch('filteredOrders', () => {
                        this.refreshIcons();
                    });
                    this.refreshIcons();
                },

                refreshIcons() {
                    this.$nextTick(() => {
                        if(window.lucide) window.lucide.createIcons();
                    });
                },

                get filteredOrders() {
                    return this.orders.filter(order => {
                        const statusMatch = this.filterStatus === 'Semua' || order.status === this.filterStatus;
                        const searchMatch = order.room.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                          order.id.toLowerCase().includes(this.searchQuery.toLowerCase());
                        return statusMatch && searchMatch;
                    });
                },

                formatMoney(value, clean = false) {
                    if(!value) return '0';
                    const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                    return clean ? formatted.replace('Rp', '').trim() : formatted;
                },

                updateStatus(id, status) {
                    const order = this.orders.find(o => o.id === id);
                    if(order) order.status = status;
                },

                showReceipt(order) {
                    this.activeOrder = order;
                    this.showReceiptModal = true;
                    this.refreshIcons(); // For modal icons
                },

                printReceipt() {
                    window.print();
                    // Don't close immediately to allow browser print dialog interaction
                }
            }));
        });
    </script>
@endsection
