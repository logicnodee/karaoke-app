@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Buat Pesanan - Admin Dashboard')
@section('page-title', 'Buat Pesanan Langsung')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'pemesanan'])
@endsection

@section('dashboard-content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-printable, #receipt-printable * {
                visibility: visible;
            }
            #receipt-printable {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white;
                color: black;
                border: none;
                box-shadow: none;
                transform: none !important; /* Remove rotation */
            }
            
            /* Hide Browser Header/Footer (Date, URL, Title) */
            @page {
                size: auto;
                margin: 0mm;
            }
            body {
                margin: 0px;
            }

            /* Hide Buttons */
            #receipt-actions {
                display: none !important;
            }
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', (rooms, menuItems, packages) => ({
                rooms: rooms,
                menuItems: menuItems,
                packages: packages,
                
                // State
                activeTab: 'ruangan', 
                selectedRoom: null,
                cart: [],
                searchQuery: '',
                filterCategory: 'All',
                activeMenuTab: 'Food',
                filterLantai: 'All',
                filterTipe: 'All',

                // Booking State
                showBookingModal: false,
                pendingRoom: null, 
                bookingMode: 'regular',
                inputDuration: 1,
                selectedPackageIndex: null,
                
                // Item Details Modal State
                showMenuItemModal: false,
                selectedMenuItem: null,
                selectedVariation: null,
                itemQuantity: 1,

                // Transaction State
                customerName: '',
                customerPhone: '',
                customerDomicile: '',
                showReceiptPreview: false,
                generatedAccessCode: '',
                
                // Variation Modal State (Legacy - kept for safety but unused in new flow)
                showVariationModal: false,
                pendingItem: null, 
                
                // Toast State
                toasts: [],
                
                showToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 3000);
                },
                
                getRoomColor(type) {
                     const colors = {
                        'Regular': 'border-white/10 group-hover:border-white/20',
                        'VIP': 'border-yellow-500/50 bg-yellow-500/5 group-hover:border-yellow-500',
                        'VVIP': 'border-purple-500/50 bg-purple-500/5 group-hover:border-purple-500',
                        'Suite': 'border-blue-500/50 bg-blue-500/5 group-hover:border-blue-500',
                        'Party': 'border-pink-500/50 bg-pink-500/5 group-hover:border-pink-500',
                     };
                     return colors[type] || colors['Regular'];
                },
                getBadgeColor(type) {
                     const colors = {
                        'Regular': 'bg-white/10 text-gray-400',
                        'VIP': 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20',
                        'VVIP': 'bg-purple-500/10 text-purple-500 border border-purple-500/20',
                        'Suite': 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
                        'Party': 'bg-pink-500/10 text-pink-500 border border-pink-500/20',
                     };
                     return colors[type] || colors['Regular'];
                },
                init() {
                    this.refreshIcons();
                    this.$watch('activeTab', () => this.refreshIcons());
                    this.$watch('activeMenuTab', () => this.refreshIcons());
                    this.$watch('searchQuery', () => this.refreshIcons());
                    this.$watch('cart', () => this.refreshIcons());
                    this.$watch('selectedRoom', () => this.refreshIcons());
                    this.$watch('bookingMode', () => this.refreshIcons());
                    this.$watch('showBookingModal', () => this.refreshIcons());
                    this.$watch('selectedPackageIndex', () => this.refreshIcons());
                    this.$watch('showMenuItemModal', () => this.refreshIcons());
                },

                refreshIcons() {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },
                
                // Room Selection Logic
                selectRoom(room) {
                    if (room.status !== 'Kosong') return;
                    
                    this.pendingRoom = room;
                    // Reset Booking State
                    this.bookingMode = 'regular';
                    this.inputDuration = 1;
                    this.selectedPackageIndex = null;
                    
                    this.showBookingModal = true;
                },

                confirmBooking() {
                    // Validation
                    if (this.bookingMode === 'paket' && this.selectedPackageIndex === null) {
                        this.showToast('Silahkan pilih paket terlebih dahulu!', 'error');
                        return;
                    }

                    // Set Selected Room with Booking Details
                    this.selectedRoom = {
                        ...this.pendingRoom,
                        bookingRaw: {
                            mode: this.bookingMode,
                            duration: this.inputDuration,
                            packageIdx: this.selectedPackageIndex
                        }
                    };

                    this.showBookingModal = false;
                    this.activeTab = 'menu';
                    this.refreshIcons();
                },

                // Cart Logic
                addToCart(item) {
                     this.selectedMenuItem = item;
                     this.itemQuantity = 1;
                     // Auto-select first variation if exists
                     this.selectedVariation = (item.variations && item.variations.length > 0) ? item.variations[0] : null;
                     this.showMenuItemModal = true;
                },

                confirmAddItem() {
                    const item = this.selectedMenuItem;
                    if(!item) return;

                    let finalItem = item;

                    // Handle Variation
                    if (item.variations && item.variations.length > 0) {
                        if (!this.selectedVariation) {
                             this.showToast('Silahkan pilih variasi menu!', 'error');
                             return;
                        }
                        finalItem = {
                            ...item,
                            price: this.selectedVariation.price,
                            name: `${item.name} (${this.selectedVariation.name})`
                        };
                    }

                    this.addItemToCart(finalItem, this.itemQuantity);
                    this.showMenuItemModal = false;
                    this.showToast('Item berhasil ditambahkan', 'success');
                },
                
                // Legacy support (safe to keep) or remove
                selectVariation(variation) {
                     // ... logic moved to confirmAddItem
                },

                addItemToCart(item, quantity = 1) {
                    const existingItem = this.cart.find(i => i.name === item.name);
                    if (existingItem) {
                        existingItem.qty += quantity;
                    } else {
                        this.cart.push({
                            name: item.name,
                            price: item.price,
                            qty: quantity,
                            image: item.image
                        });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateQty(index, change) {
                    const item = this.cart[index];
                    item.qty += change;
                    if (item.qty <= 0) {
                        this.removeFromCart(index);
                    }
                },

                // Calculations
                get roomTotal() {
                    if (!this.selectedRoom) return 0;
                    
                    const b = this.selectedRoom.bookingRaw;
                    if (!b) return 0; // Fallback

                    if (b.mode === 'open') return 0;
                    
                    if (b.mode === 'paket') {
                        return this.packages[b.packageIdx].harga_weekday;
                    }
                    
                    // Regular
                    return this.selectedRoom.harga_weekday * b.duration; 
                },

                get fnbTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get grandTotal() {
                    return this.roomTotal + this.fnbTotal;
                },

                // Formatting
                formatMoney(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                },

                 get filteredRooms() {
                    return this.rooms.filter(r => {
                        const statusMatch = r.status === 'Kosong';
                        const lantaiMatch = this.filterLantai === 'All' || r.lantai == this.filterLantai;
                        const tipeMatch = this.filterTipe === 'All' || r.tipe == this.filterTipe;
                        return statusMatch && lantaiMatch && tipeMatch;
                    });
                 },
                 get uniqueLantais() {
                     return [...new Set(this.rooms.map(r => r.lantai))].sort();
                 },
                 get uniqueTipes() {
                     return ['Regular', 'VIP', 'VVIP', 'Suite', 'Party'];
                 },

                 get filteredMenu() {
                    return this.menuItems.filter(item => {
                        const catMatch = item.category === this.activeMenuTab;
                        const searchMatch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const statusMatch = item.status === 'Available';
                        return catMatch && searchMatch && statusMatch;
                    });
                 },
                 
                 processOrder() {
                    if(!this.selectedRoom) {
                        this.showToast('Pilih ruangan terlebih dahulu!', 'error');
                        return;
                    }
                    if(!this.customerName.trim()) {
                        this.showToast('Masukkan nama pelanggan terlebih dahulu!', 'error');
                        return;
                    }
                    this.generatedAccessCode = 'K-' + Math.floor(100 + Math.random() * 899);
                    this.showReceiptPreview = true;
                 },

                 printOrder() {
                    // Logic to print and submit
                    // Delay slightly to ensure modal render if needed, then print
                    setTimeout(() => {
                        window.print();
                        
                        // After print dialog closes (or immediately in some browsers), show toast and redirect
                        this.showToast('Struk berhasil dicetak! Pesanan sedang diproses.', 'success');
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.ruangan') }}";
                        }, 1000);
                    }, 100);
                 }
            }));
        });
    </script>
    
    <div x-data="posSystem({{ json_encode($daftarRuangan) }}, {{ json_encode($menuItems) }}, {{ json_encode($paketHarga) }})" class="h-[calc(100vh-2rem)] md:h-[calc(100vh-3rem)] lg:h-[calc(100vh-4rem)] flex flex-col md:flex-row gap-6">
        
        {{-- LEFT PANEL: SELECTIONS --}}
        <div class="flex-1 flex flex-col min-h-0 bg-[#0A0A0A] border border-white/5 rounded-2xl overflow-hidden relative">
            {{-- Tabs --}}
            <div class="flex border-b border-white/5 bg-zinc-900/20">
                <button @click="activeTab = 'ruangan'" 
                        class="flex-1 py-3 text-xs md:text-sm font-black uppercase tracking-widest transition-all hover:bg-white/5 relative"
                        :class="activeTab === 'ruangan' ? 'text-[#D0B75B] bg-white/5' : 'text-gray-500'">
                    <span class="z-10 relative">1. Pilih Ruangan</span>
                    <div x-show="activeTab === 'ruangan'" class="absolute bottom-0 left-0 w-full h-0.5 bg-[#D0B75B]" layoutId="activeTabIndicator"></div>
                </button>
                <button @click="activeTab = 'menu'" 
                        class="flex-1 py-3 text-xs md:text-sm font-black uppercase tracking-widest transition-all hover:bg-white/5 relative"
                        :class="activeTab === 'menu' ? 'text-[#D0B75B] bg-white/5' : 'text-gray-500'">
                    <span class="z-10 relative">2. Pilih Menu F&B</span>
                    <div x-show="activeTab === 'menu'" class="absolute bottom-0 left-0 w-full h-0.5 bg-[#D0B75B]" layoutId="activeTabIndicator"></div>
                </button>
            </div>

            {{-- CONTENT AREA --}}
            <div class="flex-1 overflow-y-auto bg-[#080808] custom-scrollbar relative">
                
                {{-- RUANGAN VIEW --}}
                <div x-show="activeTab === 'ruangan'" class="p-6 space-y-4 min-h-full" 
                     x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 -translate-x-4" 
                     x-transition:enter-end="opacity-100 translate-x-0">
                    <div>
                        {{-- Header & Filters --}}
                        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-4">
                            <h3 class="text-white font-bold flex items-center gap-3 text-base">
                                 <div class="w-8 h-8 rounded-lg bg-[#D0B75B]/20 flex items-center justify-center text-[#D0B75B]">
                                    <i data-lucide="door-open" class="w-4 h-4"></i>
                                 </div>
                                 <span x-text="filteredRooms.length + ' Ruangan Tersedia'"></span>
                            </h3>
                            
                            <div class="flex gap-2 w-full xl:w-auto overflow-x-auto pb-2 xl:pb-0">
                                {{-- Filter Lantai --}}
                                <select x-model="filterLantai" class="bg-zinc-900 border border-white/10 text-white text-[10px] font-bold rounded-lg px-3 py-1.5 outline-none focus:border-[#D0B75B] uppercase cursor-pointer">
                                    <option value="All">All Lantai</option>
                                    <template x-for="lantai in uniqueLantais" :key="lantai">
                                        <option :value="lantai" x-text="'Lantai ' + lantai"></option>
                                    </template>
                                </select>

                                {{-- Filter Tipe --}}
                                <select x-model="filterTipe" class="bg-zinc-900 border border-white/10 text-white text-[10px] font-bold rounded-lg px-3 py-1.5 outline-none focus:border-[#D0B75B] uppercase cursor-pointer">
                                    <option value="All">All Tipe</option>
                                    <template x-for="tipe in uniqueTipes" :key="tipe">
                                        <option :value="tipe" x-text="tipe"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Grid --}}
                        <div class="grid grid-cols-4 md:grid-cols-5 xl:grid-cols-6 gap-2">
                            <template x-for="room in filteredRooms" :key="room.nama">
                                <button @click="selectRoom(room)" 
                                        class="text-left rounded-xl border transition-all duration-300 relative overflow-hidden group flex flex-col justify-between"
                                        :class="selectedRoom && selectedRoom.nama === room.nama ? 'bg-[#D0B75B] border-[#D0B75B] shadow-lg shadow-[#D0B75B]/20 scale-[1.02]' : 'bg-zinc-900/80 hover:shadow-lg ' + getRoomColor(room.tipe)">
                                    
                                    {{-- Selected Background --}}
                                    <template x-if="selectedRoom && selectedRoom.nama === room.nama">
                                        <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-30 pointer-events-none"></div>
                                    </template>

                                    <div class="p-2.5 flex flex-col h-full relative z-10">
                                        {{-- Header: Type & Floor --}}
                                        <div class="flex justify-between items-start mb-1.5">
                                            <span class="text-[8px] font-black px-1 py-0.5 rounded-sm uppercase tracking-wider border backdrop-blur-sm transition-colors"
                                                  :class="selectedRoom && selectedRoom.nama === room.nama ? 'bg-black/10 border-black/10 text-black/80' : getBadgeColor(room.tipe)">
                                                <span x-text="room.tipe"></span>
                                            </span>
                                            <div class="flex items-center gap-1" :class="selectedRoom && selectedRoom.nama === room.nama ? 'text-black/60' : 'text-gray-500'">
                                                <i data-lucide="layers" class="w-2.5 h-2.5"></i>
                                                <span class="text-[9px] font-bold" x-text="room.lantai"></span>
                                            </div>
                                        </div>

                                        {{-- Body: Name & Capacity --}}
                                        <div class="flex-1 mb-1.5">
                                            <h4 class="font-black text-xs tracking-tight leading-3 mb-1 transition-colors line-clamp-1"
                                                :class="selectedRoom && selectedRoom.nama === room.nama ? 'text-black' : 'text-white'"
                                                x-text="room.nama"></h4>
                                            
                                            <div class="flex items-center gap-2" :class="selectedRoom && selectedRoom.nama === room.nama ? 'text-black/70' : 'text-gray-500'">
                                                <div class="flex items-center gap-1">
                                                    <i data-lucide="users" class="w-2.5 h-2.5"></i>
                                                    <span class="text-[9px] font-medium" x-text="room.kapasitas + ' Org'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer: Price --}}
                                        <div class="pt-1.5 border-t" 
                                             :class="selectedRoom && selectedRoom.nama === room.nama ? 'border-black/10' : 'border-white/5'">
                                            <div class="flex justify-between items-end">
                                                <div class="flex flex-col">
                                                    <span class="font-mono text-[9px] font-black transition-colors"
                                                          :class="selectedRoom && selectedRoom.nama === room.nama ? 'text-black' : (room.tipe === 'Regular' ? 'text-[#D0B75B]' : 'text-white')"
                                                          x-text="formatMoney(room.harga_weekday)"></span>
                                                </div>
                                                
                                                {{-- Check Icon --}}
                                                <div class="w-4 h-4 rounded-full flex items-center justify-center transition-all duration-300 transform"
                                                     :class="selectedRoom && selectedRoom.nama === room.nama ? 'bg-black text-[#D0B75B] scale-100 rotate-0' : 'bg-white/5 text-gray-500 scale-90 opacity-0 group-hover:opacity-100 group-hover:bg-[#D0B75B] group-hover:text-black group-hover:scale-100'">
                                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                            <div x-show="filteredRooms.length === 0" class="col-span-full text-center py-20">
                                <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-600">
                                    <i data-lucide="door-closed" class="w-8 h-8"></i>
                                </div>
                                <p class="text-gray-500 font-bold">Tidak ada ruangan kosong saat ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- F&B VIEW --}}
                <div x-show="activeTab === 'menu'" class="flex flex-col h-full" 
                     x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 translate-x-4" 
                     x-transition:enter-end="opacity-100 translate-x-0">
                    
                    {{-- Sticky Header --}}
                    <div class="sticky top-0 z-20 bg-[#080808]/95 backdrop-blur-xl border-b border-white/5 px-4 py-2.5">
                        <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                            <div class="flex bg-zinc-900 rounded-lg p-1 border border-white/5">
                                <button @click="activeMenuTab = 'Food'" 
                                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all"
                                        :class="activeMenuTab === 'Food' ? 'bg-[#D0B75B] text-black' : 'text-gray-500 hover:text-white'">
                                    Food
                                </button>
                                <button @click="activeMenuTab = 'Beverages'" 
                                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all"
                                        :class="activeMenuTab === 'Beverages' ? 'bg-[#D0B75B] text-black' : 'text-gray-500 hover:text-white'">
                                    Beverages
                                </button>
                            </div>
                            <div class="relative w-full md:w-60">
                                <input type="text" x-model="searchQuery" placeholder="Cari menu" class="w-full bg-zinc-900 border border-white/10 rounded-lg pl-8 pr-3 py-1.5 text-[10px] text-white focus:border-[#D0B75B] outline-none transition-colors placeholder:text-gray-600 font-bold uppercase tracking-wide">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable Grid --}}
                    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            <template x-for="item in filteredMenu" :key="item.name">
                                <button @click="addToCart(item)" class="bg-zinc-900 border border-white/5 rounded-xl overflow-hidden hover:border-[#D0B75B] transition-all group text-left flex flex-col h-full hover:bg-zinc-800">
                                    <div class="h-20 bg-black/50 relative overflow-hidden">
                                        <template x-if="item.image">
                                            <img :src="'/' + item.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        </template>
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="w-8 h-8 rounded-full bg-[#D0B75B] text-black flex items-center justify-center shadow-lg">
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 flex-1 flex flex-col">
                                        <h4 class="text-white font-bold text-[10px] mb-0.5 leading-tight h-6 overflow-hidden line-clamp-2" x-text="item.name" :title="item.name"></h4>
                                        {{-- Removed Description for Compactness --}}
                                        <div class="flex justify-between items-end mt-auto pt-1">
                                            <span class="text-[#D0B75B] font-mono font-bold text-[10px]" x-text="formatMoney(item.price)"></span>
                                            <span class="text-[8px] text-gray-500" x-text="'Stok: ' + item.stock"></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                            <div x-show="filteredMenu.length === 0" class="col-span-full py-12 text-center">
                                <p class="text-gray-500 font-bold text-sm">Menu tidak ditemukan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: SUMMARY & CART --}}
        <div class="w-full md:w-[300px] bg-[#0A0A0A] border border-white/5 rounded-2xl flex flex-col h-full shadow-2xl shadow-black relative z-20 overflow-hidden">
            <div class="px-4 py-3 border-b border-white/5 bg-zinc-900/20 backdrop-blur-sm">
                <h3 class="text-white font-black text-xs flex items-center gap-2 uppercase tracking-wide">
                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-[#D0B75B]"></i> Ringkasan Pesanan
                </h3>
            </div>

            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 custom-scrollbar">
                
                {{-- Customer Info --}}
                <div class="bg-zinc-900/50 p-2.5 rounded-xl border border-white/5 space-y-2">
                    <div>
                        <label class="text-[9px] uppercase font-bold text-gray-500 mb-0.5 block">Nama Pelanggan</label>
                        <div class="relative">
                            <input type="text" x-model="customerName" placeholder="Masukkan Nama..." 
                                   class="w-full bg-black border border-white/10 rounded-lg px-2.5 py-1.5 text-[10px] font-bold text-white focus:border-[#D0B75B] outline-none transition-colors">
                            <i data-lucide="user" class="w-3 h-3 text-gray-500 absolute right-2.5 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[9px] uppercase font-bold text-gray-500 mb-0.5 block">No. Telepon</label>
                            <div class="relative">
                                <input type="text" x-model="customerPhone" placeholder="08..." 
                                       class="w-full bg-black border border-white/10 rounded-lg px-2.5 py-1.5 text-[10px] font-bold text-white focus:border-[#D0B75B] outline-none transition-colors font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] uppercase font-bold text-gray-500 mb-0.5 block">Domisili</label>
                            <div class="relative">
                                <input type="text" x-model="customerDomicile" placeholder="Kecamatan..." 
                                       class="w-full bg-black border border-white/10 rounded-lg px-2.5 py-1.5 text-[10px] font-bold text-white focus:border-[#D0B75B] outline-none transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Room Section --}}
                <div class="bg-zinc-900/30 rounded-xl p-2.5 border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-[#D0B75B]/5 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
                    
                    <p class="text-[9px] text-gray-500 uppercase font-black mb-1.5 tracking-widest flex items-center gap-1.5">
                        <i data-lucide="key" class="w-3 h-3"></i> Ruangan
                    </p>
                    
                    <template x-if="selectedRoom">
                        <div class="flex justify-between items-center relative z-10">
                            <div>
                                <h4 class="text-white font-black text-sm leading-none mb-1" x-text="selectedRoom.nama"></h4>
                                <p class="text-[9px] text-[#D0B75B] font-bold uppercase tracking-widest mb-1" x-text="'Lantai ' + selectedRoom.lantai"></p>
                                
                                {{-- Dynamic Badge based on Mode --}}
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/10 text-gray-300 uppercase tracking-wider" x-text="selectedRoom.tipe"></span>
                                    
                                    <template x-if="selectedRoom.bookingRaw.mode === 'regular'">
                                        <span class="text-[9px] text-[#D0B75B] font-bold border border-[#D0B75B]/30 px-1.5 py-0.5 rounded" x-text="selectedRoom.bookingRaw.duration + ' Jam'"></span>
                                    </template>
                                    <template x-if="selectedRoom.bookingRaw.mode === 'paket'">
                                        <span class="text-[9px] text-blue-400 font-bold border border-blue-400/30 px-1.5 py-0.5 rounded">PAKET</span>
                                    </template>
                                    <template x-if="selectedRoom.bookingRaw.mode === 'open'">
                                        <span class="text-[9px] text-purple-400 font-bold border border-purple-400/30 px-1.5 py-0.5 rounded">OPEN BILL</span>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <template x-if="selectedRoom.bookingRaw.mode !== 'open'">
                                    <div>
                                        <p class="text-[#D0B75B] font-mono font-black text-xs" x-text="formatMoney(roomTotal)"></p>
                                        <template x-if="selectedRoom.bookingRaw.mode === 'regular'">
                                            <p class="text-[8px] text-gray-500 font-bold uppercase">Total Sewa</p>
                                        </template>
                                        <template x-if="selectedRoom.bookingRaw.mode === 'paket'">
                                            <p class="text-[8px] text-gray-500 font-bold uppercase">Harga Paket</p>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="selectedRoom.bookingRaw.mode === 'open'">
                                    <div>
                                        <p class="text-white font-mono font-black text-[10px] tracking-widest">---</p>
                                        <p class="text-[8px] text-gray-500 font-bold uppercase">Pay Later</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!selectedRoom">
                        <div class="py-2.5 text-center border border-dashed border-white/10 rounded-lg">
                            <p class="text-gray-600 text-[9px] font-bold italic">Belum ada ruangan dipilih</p>
                            <button @click="activeTab = 'ruangan'" class="text-[#D0B75B] text-[9px] font-bold uppercase mt-0.5 hover:underline">Pilih Sekarang</button>
                        </div>
                    </template>
                </div>

                {{-- F&B Section --}}
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-1.5">
                         <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest flex items-center gap-1.5">
                            <i data-lucide="utensils" class="w-3 h-3"></i> Menu Terpilih
                        </p>
                        <span class="text-[9px] font-mono text-gray-600" x-text="cart.length + ' Item'"></span>
                    </div>
                   
                    <div class="space-y-1.5">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex items-center gap-2 bg-zinc-900/30 p-1.5 rounded-lg border border-white/5 hover:bg-zinc-900/50 transition-colors group">
                                <template x-if="item.image">
                                    <div class="w-8 h-8 rounded-md bg-black flex-shrink-0 overflow-hidden relative">
                                        <img :src="'/' + item.image" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white text-[10px] font-bold truncate mb-0.5" x-text="item.name"></h4>
                                    <p class="text-[#D0B75B] text-[9px] font-mono font-bold" x-text="formatMoney(item.price * item.qty)"></p>
                                </div>
                                <div class="flex items-center bg-black rounded-md p-0.5 border border-white/5">
                                    <button @click="updateQty(index, -1)" class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded transition-colors font-bold text-[10px]">-</button>
                                    <span class="text-[9px] font-bold text-white w-4 text-center font-mono" x-text="item.qty"></span>
                                    <button @click="updateQty(index, 1)" class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded transition-colors font-bold text-[10px]">+</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="cart.length === 0">
                            <div class="py-4 text-center border border-dashed border-white/10 rounded-lg flex flex-col items-center">
                                <div class="w-6 h-6 bg-zinc-900 rounded-full flex items-center justify-center text-gray-600 mb-1.5">
                                    <i data-lucide="shopping-basket" class="w-3 h-3"></i>
                                </div>
                                <p class="text-gray-600 text-[9px] font-bold italic">Keranjang kosong</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer Summary --}}
            <div class="p-3 bg-[#080808] border-t border-white/5 relative z-20">
                <div class="space-y-1 mb-3">
                    <div class="flex justify-between text-[10px] font-medium text-gray-400">
                        <span>Total Ruangan</span>
                        <span class="font-mono text-white" x-text="formatMoney(roomTotal)"></span>
                    </div>
                    <div class="flex justify-between text-[10px] font-medium text-gray-400">
                        <span>Total F&B</span>
                        <span class="font-mono text-white" x-text="formatMoney(fnbTotal)"></span>
                    </div>
                    {{-- Dashed Separator --}}
                    <div class="border-t border-dashed border-white/10 my-1.5"></div>
                    
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Grand Total</span>
                        <span class="text-lg font-black text-[#D0B75B]" x-text="formatMoney(grandTotal)"></span>
                    </div>
                </div>
                <button @click="processOrder()" 
                        class="w-full py-2.5 rounded-lg font-black uppercase tracking-[0.2em] transition-all text-[9px] flex items-center justify-center gap-2 shadow-lg hover:shadow-[#D0B75B]/20 hover:-translate-y-0.5 active:translate-y-0"
                        :class="selectedRoom ? 'bg-[#D0B75B] text-black hover:bg-[#e0c86b]' : 'bg-zinc-800 text-gray-500 cursor-not-allowed'"
                        :disabled="!selectedRoom">
                    <span x-text="selectedRoom ? 'PROSES PESANAN' : 'PILIH RUANGAN DULU'"></span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </button>
            </div>
        </div>
    
    {{-- BOOKING MODAL --}}
    <div x-show="showBookingModal" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
             @click="showBookingModal = false"></div>

        {{-- Modal Content --}}
        <div class="bg-[#0A0A0A] border border-white/10 w-full max-w-lg rounded-2xl shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            
            {{-- Header --}}
            <div class="p-5 border-b border-white/5 bg-zinc-900/50 flex justify-between items-center">
                <div>
                    <h3 class="text-white font-black text-lg">Setup Pemesanan</h3>
                    <template x-if="pendingRoom">
                        <p class="text-[#D0B75B] text-xs font-bold mt-1" x-text="pendingRoom.nama + ' - ' + pendingRoom.tipe"></p>
                    </template>
                </div>
                <button @click="showBookingModal = false" class="text-gray-500 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Mode Tabs --}}
            <div class="p-5 overflow-y-auto custom-scrollbar">
                <div class="flex bg-black p-1 rounded-xl mb-6 border border-white/10">
                    <button @click="bookingMode = 'regular'" 
                            class="flex-1 py-2.5 text-xs font-bold uppercase rounded-lg transition-all"
                            :class="bookingMode === 'regular' ? 'bg-zinc-800 text-[#D0B75B] shadow-lg shadow-black/50' : 'text-gray-500 hover:text-white'">
                        Regular / Jam
                    </button>
                    <button @click="bookingMode = 'paket'" 
                            class="flex-1 py-2.5 text-xs font-bold uppercase rounded-lg transition-all"
                            :class="bookingMode === 'paket' ? 'bg-zinc-800 text-blue-400 shadow-lg shadow-black/50' : 'text-gray-500 hover:text-white'">
                        Paket Hemat
                    </button>
                    <button @click="bookingMode = 'open'" 
                            class="flex-1 py-2.5 text-xs font-bold uppercase rounded-lg transition-all"
                            :class="bookingMode === 'open' ? 'bg-zinc-800 text-purple-400 shadow-lg shadow-black/50' : 'text-gray-500 hover:text-white'">
                        Open Billing
                    </button>
                </div>

                {{-- Content: REGULAR --}}
                <div x-show="bookingMode === 'regular'" class="space-y-6 text-center py-4">
                    <div class="flex items-center justify-center gap-6">
                        <button @click="inputDuration > 1 ? inputDuration-- : null" 
                                class="w-14 h-14 rounded-2xl bg-zinc-900 border border-white/10 hover:border-[#D0B75B] hover:text-[#D0B75B] text-white flex items-center justify-center transition-all active:scale-95">
                            <i data-lucide="minus" class="w-6 h-6"></i>
                        </button>
                        <div class="text-center w-32">
                            <span class="block text-5xl font-black text-white mb-2" x-text="inputDuration"></span>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Jam</span>
                        </div>
                        <button @click="inputDuration++" 
                                class="w-14 h-14 rounded-2xl bg-zinc-900 border border-white/10 hover:border-[#D0B75B] hover:text-[#D0B75B] text-white flex items-center justify-center transition-all active:scale-95">
                            <i data-lucide="plus" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="bg-zinc-900/50 p-4 rounded-xl border border-white/5 inline-block">
                        <p class="text-gray-500 text-[10px] font-bold uppercase mb-1">Estimasi Harga Ruangan</p>
                        <template x-if="pendingRoom">
                            <p class="text-[#D0B75B] font-mono text-xl font-black" x-text="formatMoney(pendingRoom.harga_weekday * inputDuration)"></p>
                        </template>
                    </div>
                </div>

                {{-- Content: PAKET --}}
                <div x-show="bookingMode === 'paket'" class="space-y-3">
                    <template x-for="(paket, index) in packages" :key="index">
                        <button @click="selectedPackageIndex = index"
                                class="w-full text-left p-4 rounded-xl border transition-all flex justify-between items-center group"
                                :class="selectedPackageIndex === index ? 'bg-blue-500/10 border-blue-500' : 'bg-zinc-900 border-white/5 hover:border-white/20 hover:bg-zinc-800'">
                            <div>
                                <h4 class="font-bold text-white text-sm mb-1" x-text="paket.nama"></h4>
                                <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded text-gray-300 font-bold" x-text="paket.durasi"></span>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-black text-[#D0B75B]" x-text="formatMoney(paket.harga_weekday)"></p>
                                <div class="mt-2 flex justify-end">
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center transition-colors"
                                         :class="selectedPackageIndex === index ? 'bg-blue-500 border-blue-500 text-black' : 'border-white/20'">
                                         <template x-if="selectedPackageIndex === index">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                         </template>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Content: OPEN BILLING --}}
                <div x-show="bookingMode === 'open'" class="py-12 text-center">
                    <div class="w-20 h-20 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-purple-500/20 text-purple-500 animate-pulse">
                        <i data-lucide="infinity" class="w-10 h-10"></i>
                    </div>
                    <h4 class="text-white font-bold text-lg mb-2">Open Billing Mode</h4>
                    <p class="text-gray-400 text-xs px-12 leading-relaxed">
                        Durasi fleksibel. Tagihan akan dihitung berdasarkan waktu check-out.
                    </p>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="p-5 border-t border-white/5 bg-[#080808] flex gap-3">
                <button @click="showBookingModal = false" class="flex-1 py-3.5 rounded-xl border border-white/10 text-white font-bold uppercase text-xs hover:bg-white/5 transition-colors">
                    Batal
                </button>
                <button @click="confirmBooking()" class="flex-[2] py-3.5 rounded-xl bg-[#D0B75B] text-black font-black uppercase text-xs hover:bg-[#e0c86b] transition-colors shadow-lg shadow-[#D0B75B]/20">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    {{-- VARIATION MODAL --}}
    <div x-show="showVariationModal" 
         class="fixed inset-0 z-[65] flex items-center justify-center p-4"
         style="display: none;">
         
         <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
              @click="showVariationModal = false"></div>

         <div class="bg-[#18181b] w-full max-w-md rounded-2xl border border-white/10 shadow-2xl relative z-10 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-white/5 bg-zinc-900/50">
                <h3 class="text-white font-black text-lg tracking-wide uppercase">Pilih Variasi</h3>
                <p class="text-xs text-gray-500 mt-1" x-text="pendingItem?.name"></p>
            </div>
            
            <div class="p-5 space-y-3">
                <template x-if="pendingItem">
                    <template x-for="variant in pendingItem.variations" :key="variant.name">
                        <button @click="selectVariation(variant)" 
                                class="w-full p-4 rounded-xl border border-white/5 bg-zinc-900 hover:bg-zinc-800 hover:border-[#D0B75B]/50 transition-all flex justify-between items-center group">
                            <span class="text-white font-bold text-sm" x-text="variant.name"></span>
                            <span class="text-[#D0B75B] font-mono font-black" x-text="formatMoney(variant.price)"></span>
                        </button>
                    </template>
                </template>
            </div>
            
            <div class="p-4 bg-black/20 border-t border-white/5 text-center">
                <button @click="showVariationModal = false" class="text-xs text-gray-500 font-bold hover:text-white uppercase tracking-wider">Batal</button>
            </div>
         </div>
    </div>

    {{-- MENU DETAIL MODAL --}}
    <div x-show="showMenuItemModal" 
         class="fixed inset-0 z-[65] flex items-center justify-center p-4"
         style="display: none;">
         
         <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
              @click="showMenuItemModal = false"></div>

         <div class="bg-[#18181b] w-full max-w-[320px] rounded-xl border border-white/10 relative z-10 overflow-hidden flex flex-col max-h-[85vh]">
            
            <template x-if="selectedMenuItem">
                <div class="flex flex-col h-full bg-[#18181b]">
                    {{-- Image Header --}}
                    <div class="h-24 w-full bg-zinc-900 relative shrink-0 overflow-hidden">
                        <template x-if="selectedMenuItem.image">
                            <img :src="'/' + selectedMenuItem.image" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!selectedMenuItem.image">
                            <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-700"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#18181b] via-transparent to-transparent"></div>
                        
                        <button @click="showMenuItemModal = false" class="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black transition-colors border border-white/10 z-20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>

                        <div class="absolute bottom-0 left-0 right-0 p-3 pt-8 bg-gradient-to-t from-[#18181b] via-[#18181b]/80 to-transparent">
                             <h3 class="text-white font-black text-base leading-none mb-0.5" x-text="selectedMenuItem.name"></h3>
                             <p class="text-[#D0B75B] font-mono font-bold text-sm" x-text="formatMoney(selectedVariation ? selectedVariation.price : selectedMenuItem.price)"></p>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-3">
                        {{-- Description --}}
                        <div>
                            <h4 class="text-gray-500 text-[8px] font-bold uppercase tracking-widest mb-1 flex items-center gap-1 pl-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg> 
                                Deskripsi
                            </h4>
                            <p class="text-gray-300 text-[10px] leading-relaxed" x-text="selectedMenuItem.description || 'Tidak ada deskripsi tersedia.'"></p>
                        </div>

                        {{-- Variations --}}
                        <template x-if="selectedMenuItem.variations && selectedMenuItem.variations.length > 0">
                            <div>
                                <h4 class="text-gray-500 text-[8px] font-bold uppercase tracking-widest mb-1.5 flex items-center gap-1 pl-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                    Pilih Variasi
                                </h4>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <template x-for="variant in selectedMenuItem.variations" :key="variant.name">
                                        <button @click="selectedVariation = variant"
                                                class="p-1.5 rounded border text-left transition-all relative overflow-hidden flex flex-col justify-between h-full group"
                                                :class="selectedVariation && selectedVariation.name === variant.name ? 'bg-[#D0B75B] border-[#D0B75B]' : 'bg-zinc-900 border-white/5 text-gray-400 hover:border-white/20 hover:text-white hover:bg-zinc-800'">
                                            <span class="block text-[9px] font-bold uppercase mb-0.5 leading-tight" 
                                                  :class="selectedVariation && selectedVariation.name === variant.name ? 'text-black' : 'text-gray-300 group-hover:text-white'"
                                                  x-text="variant.name"></span>
                                            <span class="block font-mono font-black text-[9px]" 
                                                  :class="selectedVariation && selectedVariation.name === variant.name ? 'text-black' : 'text-[#D0B75B]'"
                                                  x-text="formatMoney(variant.price)"></span>
                                            
                                            <template x-if="selectedVariation && selectedVariation.name === variant.name">
                                                <div class="absolute top-1 right-1">
                                                    <div class="bg-black/20 rounded-full p-px">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-black"><polyline points="20 6 9 17 4 12"/></svg>
                                                    </div>
                                                </div>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Quantity --}}
                        <div>
                            <h4 class="text-gray-500 text-[8px] font-bold uppercase tracking-widest mb-1.5 flex items-center gap-1 pl-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                                Jumlah Pesanan
                            </h4>
                            <div class="flex items-center gap-2 bg-zinc-900/50 p-1 rounded-lg border border-white/5">
                                <button @click="itemQuantity > 1 ? itemQuantity-- : null" 
                                        class="w-7 h-7 rounded bg-zinc-800 border border-white/5 hover:bg-zinc-700 hover:border-[#D0B75B] hover:text-[#D0B75B] text-white flex items-center justify-center transition-all active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                </button>
                                <div class="flex-1 text-center">
                                    <span class="font-mono text-lg font-black text-white" x-text="itemQuantity"></span>
                                    <p class="text-[7px] text-gray-500 font-bold uppercase leading-none">Porsi</p>
                                </div>
                                <button @click="itemQuantity++" 
                                        class="w-7 h-7 rounded bg-zinc-800 border border-white/5 hover:bg-zinc-700 hover:border-[#D0B75B] hover:text-[#D0B75B] text-white flex items-center justify-center transition-all active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer --}}
                    <div class="p-3 border-t border-white/5 bg-zinc-900 flex gap-2 shrink-0 z-20 relative">
                         <div class="flex-1 flex flex-col justify-center">
                             <p class="text-[8px] text-gray-500 font-bold uppercase mb-px">Total Harga</p>
                             <p class="text-white font-mono font-black text-base" x-text="formatMoney((selectedVariation ? selectedVariation.price : selectedMenuItem.price) * itemQuantity)"></p>
                         </div>
                         <button @click="confirmAddItem()" class="flex-[1.5] py-2 rounded-lg bg-[#D0B75B] text-black font-black uppercase text-[10px] hover:bg-[#e0c86b] transition-all hover:scale-[1.02] flex items-center justify-center gap-1.5 group">
                            <span>Tambah</span>
                            <div class="bg-black/10 rounded-full p-0.5 group-hover:bg-black/20 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </div>
                         </button>
                    </div>
                </div>
            </template>
         </div>
    </div>
    <div class="fixed top-24 right-6 z-[100] flex flex-col gap-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-init="$nextTick(() => window.lucide && window.lucide.createIcons())"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto min-w-[300px] max-w-sm bg-[#0A0A0A] border border-white/10 shadow-2xl rounded-xl p-4 flex items-center gap-3 relative overflow-hidden group">
                <div class="absolute left-0 top-0 bottom-0 w-1" 
                     :class="toast.type === 'error' ? 'bg-red-500' : (toast.type === 'success' ? 'bg-[#D0B75B]' : 'bg-blue-500')"></div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                     :class="toast.type === 'error' ? 'bg-red-500/10 text-red-500' : (toast.type === 'success' ? 'bg-[#D0B75B]/10 text-[#D0B75B]' : 'bg-blue-500/10 text-blue-500')">
                    <i :data-lucide="toast.type === 'error' ? 'alert-circle' : 'check-circle'" class="w-4 h-4"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-white font-bold text-xs mb-0.5 uppercase tracking-wider" x-text="toast.type === 'error' ? 'Error' : 'Sukses'"></h5>
                    <p class="text-gray-400 text-[10px] font-medium leading-relaxed" x-text="toast.message"></p>
                </div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-gray-600 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    {{-- RECEIPT PREVIEW MODAL --}}
    <div x-show="showReceiptPreview" 
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="absolute inset-0 bg-black/90 backdrop-blur-sm transition-opacity" 
             @click="showReceiptPreview = false"></div>

        <div id="receipt-printable" class="bg-white w-full max-w-sm rounded-none shadow-2xl relative z-10 overflow-hidden flex flex-col font-mono text-black">
            
            {{-- Receipt Paper Effect --}}
            <div class="h-2 bg-gray-200 border-b border-dashed border-gray-400"></div>

            <div class="p-6 space-y-4" id="receipt-content">
                {{-- Receipt Header --}}
                <div class="text-center space-y-1 border-b-2 border-dashed border-black/10 pb-4">
                    <h2 class="text-xl font-black uppercase tracking-wider">SGRT KARAOKE</h2>
                    <p class="text-[10px] text-gray-600">Jl. Hiburan No. 123, Jakarta Public</p>
                    <p class="text-[10px] text-gray-600">Telp: 021-555-0199</p>
                </div>

                {{-- Order Info --}}
                <div class="text-[10px] space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal:</span>
                        <span class="font-bold">{{ date('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kasir:</span>
                        <span class="font-bold">{{ auth()->user()->nama ?? 'Kasir' }}</span>
                    </div>
                    <div class="border-t border-dashed border-black/10 my-1"></div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pelanggan:</span>
                        <span class="font-bold uppercase" x-text="customerName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. HP:</span>
                        <span class="font-bold font-mono" x-text="customerPhone || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Domisili:</span>
                        <span class="font-bold uppercase" x-text="customerDomicile || '-'"></span>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-black/10"></div>

                {{-- Room Details --}}
                <div class="space-y-2">
                    <template x-if="selectedRoom">
                        <div>
                            <p class="text-xs font-black uppercase mb-1" x-text="selectedRoom.nama + ' (' + selectedRoom.tipe + ')'"></p>
                            <p class="text-[10px] text-gray-600 mb-1" x-text="'Lantai ' + selectedRoom.lantai"></p>
                            <div class="flex justify-between text-[10px]">
                                <span x-text="selectedRoom.bookingRaw.mode === 'regular' ? selectedRoom.bookingRaw.duration + ' Jam' : (selectedRoom.bookingRaw.mode === 'paket' ? 'Paket Hemat' : 'Open Billing')"></span>
                                <span class="font-bold" x-text="selectedRoom.bookingRaw.mode === 'open' ? '---' : formatMoney(roomTotal)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Items --}}
                <template x-if="cart.length > 0">
                    <div class="pt-2 border-t border-dashed border-black/10 space-y-2">
                        <template x-for="item in cart" :key="item.name">
                            <div class="text-[10px] flex justify-between items-start">
                                <div class="flex-1">
                                    <span x-text="item.qty + 'x ' + item.name"></span>
                                </div>
                                <span class="font-bold" x-text="formatMoney(item.price * item.qty)"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="border-t-2 border-dashed border-black/10"></div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-bold">
                        <span>TOTAL</span>
                        <span class="text-lg" x-text="selectedRoom && selectedRoom.bookingRaw.mode === 'open' ? '---' : formatMoney(grandTotal)"></span>
                    </div>
                    <template x-if="selectedRoom && selectedRoom.bookingRaw.mode === 'open'">
                         <p class="text-[9px] text-center text-gray-500 italic mt-2">*Harga final dihitung saat checkout</p>
                    </template>
                </div>

                {{-- Access Code Section (If Regular/Paket) --}}
                <template x-if="selectedRoom && selectedRoom.bookingRaw.mode !== 'open'">
                    <div class="border-2 border-[#D0B75B] rounded-lg p-2 text-center mt-4 bg-[#D0B75B]/10">
                        <p class="text-[8px] font-bold text-[#D0B75B] uppercase tracking-widest mb-1">Kode Akses Room</p>
                        <p class="text-xl font-black text-[#D0B75B] tracking-widest" x-text="generatedAccessCode"></p>
                    </div>
                </template>

                <div class="border-t-2 border-dashed border-black/10 pt-4 text-center">
                    <p class="text-[10px] font-bold">TERIMA KASIH</p>
                    <p class="text-[8px] text-gray-500">Simpan struk ini sebagai bukti pembayaran</p>
                </div>
            </div>

            {{-- Receipt Actions --}}
            <div id="receipt-actions" class="p-4 bg-gray-50 flex gap-2">
                <button @click="showReceiptPreview = false" class="flex-1 py-3 text-xs font-bold uppercase border border-gray-300 rounded hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button @click="printOrder()" class="flex-[2] py-3 text-xs font-black uppercase bg-black text-white rounded hover:bg-gray-800 transition-colors shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak & Mulai
                </button>
            </div>
            
            {{-- Paper Bottom --}}
            <div class="h-2 bg-gray-200 border-t border-dashed border-gray-400"></div>
        </div>
    </div>
</div>
@endsection
