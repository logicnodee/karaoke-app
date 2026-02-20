@extends('layouts.dashboard')

@section('dashboard-role', 'Kasir Panel')
@section('dashboard-role-icon', 'user-check')

@section('title', 'Food & Beverages - Kasir Dashboard')
@section('page-title', 'Food & Beverages')

@section('sidebar-nav')
    @include('dashboard.kasir._sidebar', ['active' => 'fnb'])
@endsection

@section('dashboard-content')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('foodBeverages', (initialItems) => ({
                menuItems: initialItems,
                showModal: false,
                newItem: { category: 'Food', name: '', price: 0, description: '', status: 'Available', image: '', stock: 100, variations: [] },
                hasVariations: false,
                showToast: false,
                toastMessage: '',
                toastType: 'success',
                activeTab: 'Food',
                filterStatus: 'All',
                editingItem: null,

                showNotification(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 3000);
                },

                addVariation() {
                    if (!this.newItem.variations) this.newItem.variations = [];
                    this.newItem.variations.push({ name: '', qty: 1, price: '' });
                },

                removeVariation(index) {
                    this.newItem.variations.splice(index, 1);
                },

                openAddModal() {
                    this.editingItem = null;
                    this.newItem = { category: 'Food', name: '', price: 0, description: '', status: 'Available', image: '', stock: 100, variations: [] };
                    this.hasVariations = false;
                    this.showModal = true;
                },

                openEditModal(item) {
                    this.editingItem = item;
                    this.newItem = JSON.parse(JSON.stringify(item));
                    if (!this.newItem.variations) this.newItem.variations = [];
                    this.hasVariations = this.newItem.variations.length > 0;
                    if (!this.hasVariations && !this.newItem.price) {
                        this.newItem.price = 0;
                    }
                    this.showModal = true;
                },

                deleteItem(item) {
                    if(confirm('Apakah anda yakin ingin menghapus menu "' + item.name + '"?')) {
                        const index = this.menuItems.indexOf(item);
                        if (index > -1) {
                            this.menuItems.splice(index, 1);
                            this.showNotification('Menu berhasil dihapus!', 'success');
                        }
                    }
                },

                saveItem() {
                    if(!this.newItem.name) {
                        this.showNotification('Nama menu wajib diisi!', 'error');
                        return;
                    }

                    if (!this.hasVariations && this.newItem.price <= 0) {
                        this.showNotification('Harga wajib diisi!', 'error');
                        return;
                    }
                    
                    if (this.hasVariations && this.newItem.variations.length === 0) {
                        this.showNotification('Minimal satu variasi harga wajib diisi!', 'error');
                        return;
                    }

                    let finalPrice = this.newItem.price;
                    let finalVariations = [];

                    if (this.hasVariations) {
                        finalVariations = this.newItem.variations.map(v => ({ name: v.name, qty: parseInt(v.qty) || 1, price: parseFloat(v.price) }));
                        if (finalVariations.length > 0) {
                            finalPrice = Math.min(...finalVariations.map(v => v.price));
                        }
                    }

                    const itemData = {
                        category: this.newItem.category,
                        name: this.newItem.name,
                        price: finalPrice,
                        variations: finalVariations,
                        description: this.newItem.description,
                        status: this.newItem.status,
                        stock: this.newItem.stock,
                        image: this.newItem.image || 'assets/img/pages/food-beverages/placeholder.png'
                    };

                    if (this.editingItem) {
                        const index = this.menuItems.indexOf(this.editingItem);
                        if (index > -1) {
                            this.menuItems[index] = itemData;
                            this.showNotification('Menu berhasil diperbarui!', 'success');
                        }
                    } else {
                        this.menuItems.push(itemData);
                        this.showNotification('Menu berhasil ditambahkan!', 'success');
                    }

                    this.showModal = false;
                },

                formatPrice(item) {
                    const format = (price) => {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(price);
                    };

                    if (item.variations && item.variations.length > 0) {
                        return item.variations.map(v => `${v.name} ${format(v.price)}`).join(' | ');
                    }
                    if (typeof item.price === 'number') {
                        return format(item.price);
                    }
                    return item.price;
                },

                get filteredItems() {
                    return this.menuItems.filter(item => {
                        const categoryMatch = item.category === this.activeTab;
                        const statusMatch = this.filterStatus === 'All' || item.status === this.filterStatus;
                        return categoryMatch && statusMatch;
                    });
                }
            }));
        });
    </script>
    <div x-data="foodBeverages({{ json_encode($menuItems) }})">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Daftar Menu F&B</h2>
            <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Kelola daftar makanan, minuman, dan paket</p>
        </div>
        <button @click="openAddModal()" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2 self-start" style="font-family: 'Inter';">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Menu Baru
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-end border-b border-white/10 pb-1">
        <div class="flex gap-4">
            <button @click="activeTab = 'Food'" :class="activeTab === 'Food' ? 'border-b-2 border-[#D0B75B] text-[#D0B75B]' : 'text-gray-400 hover:text-white'" class="pb-2 px-4 text-sm font-bold uppercase transition-colors">Food</button>
            <button @click="activeTab = 'Beverages'" :class="activeTab === 'Beverages' ? 'border-b-2 border-[#D0B75B] text-[#D0B75B]' : 'text-gray-400 hover:text-white'" class="pb-2 px-4 text-sm font-bold uppercase transition-colors">Beverages</button>
        </div>
        
        <div class="relative w-full md:w-48 mb-2">
            <select x-model="filterStatus" class="w-full bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#D0B75B]/50 transition-all appearance-none cursor-pointer">
                <option value="All">Semua Status</option>
                <option value="Available">Available</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>
            <i data-lucide="filter" class="w-3 h-3 text-gray-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>
    </div>

    {{-- Menu Table --}}
    <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs" style="font-family: 'Inter';">
                <thead>
                    <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40 border-b border-white/5">
                        <th class="text-left px-6 py-4">Menu Item</th>
                        <th class="text-left px-6 py-4">Description</th>
                        <th class="text-right px-6 py-4">Price</th>
                        <th class="text-center px-6 py-4">Stock / Status</th>
                        <th class="text-center px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <template x-for="(item, index) in filteredItems" :key="index">
                        <tr class="hover:bg-white/[0.01] transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-zinc-900 border border-white/5 overflow-hidden flex-shrink-0">
                                        <template x-if="item.image">
                                            <img :src="'/' + item.image" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.image">
                                            <div class="w-full h-full flex items-center justify-center text-gray-600">
                                                <!-- Image Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold mb-0.5" x-text="item.name"></div>
                                        <div class="text-gray-500 text-[10px]" x-text="item.category"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-gray-500 line-clamp-2" x-text="item.description || '-'"></p>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-[#D0B75B] font-bold">
                                <span x-text="formatPrice(item)"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-[8px] px-3 py-1 rounded-full border font-black uppercase tracking-widest"
                                              :class="item.status === 'Available' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'"
                                              x-text="item.status">
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-medium">
                                            Sisa: <span class="text-white font-bold" x-text="item.stock !== undefined ? item.stock : 0"></span>
                                        </span>
                                    </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEditModal(item)" class="w-7 h-7 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center">
                                        <!-- Edit Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                    <button @click="deleteItem(item)" class="w-7 h-7 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                        <!-- Trash Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div x-show="filteredItems.length === 0" class="text-center py-12 text-gray-500">
            <p>Tidak ada menu yang ditemukan.</p>
        </div>
    </div>

    {{-- Add Menu Modal --}}
    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" 
                x-effect="if(showModal) $nextTick(() => lucide.createIcons())"
                class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
            {{-- Increased width to max-w-3xl for side-by-side layout --}}
            <div @click.away="showModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-4xl overflow-hidden relative flex flex-col max-h-[90vh]">
                    <div class="px-5 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center flex-shrink-0">
                        <h3 class="text-white font-bold" x-text="editingItem ? 'Edit Menu F&B' : 'Tambah Menu Baru'"></h3>
                        <button @click="showModal = false" type="button" class="w-8 h-8 rounded-lg bg-red-600 hover:bg-red-700 text-white inline-flex items-center justify-center transition-all shadow-lg shadow-red-900/20 focus:outline-none">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- LEFT COLUMN: Details & Price --}}
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Kategori</label>
                                        <div class="relative">
                                            <select x-model="newItem.category" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer text-xs">
                                                <option>Food</option>
                                                <option>Beverages</option>
                                            </select>
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="m6 9 6 6 6-6"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Status</label>
                                        <div class="relative">
                                            <select x-model="newItem.status" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer text-xs">
                                                <option>Available</option>
                                                <option>Out of Stock</option>
                                            </select>
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="m6 9 6 6 6-6"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Menu</label>
                                        <input type="text" x-model="newItem.name" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none text-xs" placeholder="Nasi Goreng">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Stok</label>
                                        <input type="number" x-model="newItem.stock" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#D0B75B] outline-none text-xs" placeholder="100">
                                    </div>
                                </div>
                                
                                {{-- Price Section --}}
                                <div class="bg-zinc-900/50 p-4 rounded-xl border border-white/5">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-xs uppercase font-bold text-gray-500">Harga</label>
                                        <div class="flex items-center gap-2">
                                             <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Variasi?</span>
                                             <button @click="hasVariations = !hasVariations" class="w-8 h-4 rounded-full relative transition-colors" :class="hasVariations ? 'bg-[#D0B75B]' : 'bg-zinc-700'">
                                                 <div class="w-2.5 h-2.5 bg-white rounded-full absolute top-0.5 transition-all shadow-sm" :class="hasVariations ? 'left-4.5' : 'left-0.5'"></div>
                                             </button>
                                        </div>
                                    </div>

                                    <div x-show="!hasVariations">
                                        <input type="number" x-model="newItem.price" class="w-full bg-black border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none text-sm font-mono" placeholder="Rp">
                                    </div>

                                    <div x-show="hasVariations" class="space-y-2">
                                        <template x-for="(variation, index) in newItem.variations" :key="index">
                                            <div class="flex gap-2 items-center">
                                                <div class="flex-1">
                                                    <input type="text" x-model="variation.name" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#D0B75B] outline-none text-xs" placeholder="Nama Varian (ex: Bucket)">
                                                </div>
                                                <div class="w-20">
                                                    <div class="relative">
                                                        <input type="number" x-model="variation.qty" class="w-full bg-black border border-white/10 rounded-lg pl-3 pr-6 py-2 text-white focus:border-[#D0B75B] outline-none text-xs text-center" placeholder="1">
                                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-500">pcs</span>
                                                    </div>
                                                </div>
                                                <div class="w-28">
                                                    <input type="number" x-model="variation.price" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:border-[#D0B75B] outline-none text-xs font-mono" placeholder="Rp">
                                                </div>
                                                <button @click="removeVariation(index)" class="p-2 text-red-500 hover:text-red-400 bg-white/5 hover:bg-white/10 rounded-lg transition-colors flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button @click="addVariation()" class="w-full py-2 text-[10px] uppercase tracking-wider font-bold text-[#D0B75B] border border-dashed border-[#D0B75B]/30 rounded-lg hover:bg-[#D0B75B]/10 transition-colors flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg> 
                                            Tambah Variasi
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT COLUMN: Image & Description --}}
                            <div class="flex flex-col h-full">
                                <div class="flex-1 flex flex-col">
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Upload Gambar</label>
                                    <div class="flex-1 border-2 border-dashed border-white/10 rounded-xl p-4 flex flex-col items-center justify-center text-center hover:border-[#D0B75B]/50 transition-colors cursor-pointer relative bg-zinc-900/50 min-h-[140px]">
                                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="newItem.image = $event.target.files[0] ? 'assets/img/pages/food-beverages/' + $event.target.files[0].name : ''">
                                        
                                        <template x-if="!newItem.image">
                                            <div class="flex flex-col items-center">
                                                <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center mb-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                                </div>
                                                <p class="text-xs font-bold text-white">Upload / Drop Image</p>
                                                <p class="text-[10px] text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                                            </div>
                                        </template>
                                        
                                        <template x-if="newItem.image">
                                            <div class="w-full h-full absolute inset-0 p-2">
                                                <div class="w-full h-full relative rounded-lg overflow-hidden group">
                                                    <img :src="'/' + newItem.image" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <p class="text-white text-xs font-bold">Ganti Gambar</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Deskripsi</label>
                                    <textarea x-model="newItem.description" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none text-xs resize-none h-24" placeholder="Keterangan bahan atau detail menu..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 mt-2 border-t border-white/5">
                            <button @click="saveItem()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3.5 rounded-xl transition-all h-full" x-text="editingItem ? 'Update Menu' : 'Simpan Menu'">
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </template>

    {{-- Toast Notification --}}
    <template x-teleport="body">
        <div x-show="showToast" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-4 right-4 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px]"
                :class="{
                'bg-[#0A0A0A] border-green-500/20 text-green-500': toastType === 'success',
                'bg-[#0A0A0A] border-red-500/20 text-red-500': toastType === 'error'
                }">
            <div class="p-2 rounded-full" 
                    :class="{
                    'bg-green-500/10': toastType === 'success',
                    'bg-red-500/10': toastType === 'error'
                    }">
                <template x-if="toastType === 'success'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </template>
                <template x-if="toastType === 'error'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                </template>
            </div>
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : 'Gagal'"></h4>
                <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>
    </template>
    </div>
@endsection
