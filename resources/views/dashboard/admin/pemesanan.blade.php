@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Reservasi Room - Admin Dashboard')
@section('page-title', 'Reservasi Room')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'pemesanan'])
@endsection

@section('dashboard-content')
    <div x-data="bookingSystem()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Reservasi Room</h2>
            <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Kelola reservasi ruangan dari telepon atau walk-in</p>
        </div>
        <button @click="showModal = true" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2 self-start" style="font-family: 'Inter';">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Reservasi Baru
        </button>
    </div>

    {{-- Filter --}}
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <div class="flex-1 relative">
            <input type="date" x-model="filters.date" class="w-full bg-[#0A0A0A] text-white border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all placeholder:text-zinc-600"
                   style="font-family: 'Inter';">
        </div>
        <select x-model="filters.room" class="bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all"
                style="font-family: 'Inter';">
            <option>Semua Ruangan</option>
            <option>VIP Suite</option>
            <option>Party Suite</option>
            <option>Regular K1</option>
            <option>VIP 01</option>
            <option>Room 02</option>
        </select>
        <select x-model="filters.status" class="bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all"
                style="font-family: 'Inter';">
            <option>Semua Status</option>
            <option>Terkonfirmasi</option>
            <option>Menunggu</option>
            <option>Dibatalkan</option>
        </select>
    </div>

    {{-- Booking Table --}}
    <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden shadow-2xl shadow-black">
        <div class="overflow-x-auto">
            <table class="w-full text-xs" style="font-family: 'Inter';">
                <thead>
                    <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                        <th class="text-left px-6 py-4">Tamu</th>
                        <th class="text-left px-6 py-4">Ruangan</th>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-center px-6 py-4">Jam</th>
                        <th class="text-center px-6 py-4">Durasi</th>
                        <th class="text-left px-6 py-4">Kontak</th>
                        <th class="text-center px-6 py-4">Status</th>
                        <th class="text-center px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <template x-for="(booking, index) in filteredBookings" :key="index">
                        <tr class="hover:bg-white/[0.01] transition-colors group">
                            <td class="px-6 py-4 text-white font-bold" x-text="booking.tamu"></td>
                            <td class="px-6 py-4 text-gray-400 font-medium" x-text="booking.ruangan"></td>
                            <td class="px-6 py-4 text-gray-400 text-[10px] uppercase tracking-wider" x-text="booking.tanggal"></td>
                            <td class="px-6 py-4 text-center text-gray-400 font-mono text-[10px]">
                                <span class="bg-white/5 rounded px-2 py-0.5" x-text="booking.jam"></span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-400"><span x-text="booking.durasi"></span> Jam</td>
                            <td class="px-6 py-4 text-gray-500 text-[10px] font-mono" x-text="booking.kontak"></td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="booking.status === 'Terkonfirmasi'">
                                    <span class="text-[8px] px-3 py-1 rounded-full bg-green-500/10 text-green-400 border border-green-500/20 font-black uppercase tracking-widest">Terkonfirmasi</span>
                                </template>
                                <template x-if="booking.status === 'Menunggu'">
                                    <span class="text-[8px] px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-black uppercase tracking-widest">Menunggu</span>
                                </template>
                                <template x-if="booking.status === 'Dibatalkan'">
                                    <span class="text-[8px] px-3 py-1 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 font-black uppercase tracking-widest">Dibatalkan</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="updateStatus(booking, 'Terkonfirmasi')" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-green-500/10 hover:text-green-500 transition-all flex items-center justify-center" title="Check-in / Konfirmasi">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="updateStatus(booking, 'Dibatalkan')" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500/10 hover:text-red-500 transition-all flex items-center justify-center" title="Batalkan">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Booking Modal --}}
    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" 
                class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
            <div @click.away="showModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                    <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                    <h3 class="text-white font-bold">Buat Reservasi Baru</h3>
                    <button @click="showModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Tamu</label>
                                <input type="text" x-model="newBooking.tamu" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Nama Pemesan">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Ruangan</label>
                                <div class="relative">
                                    <select x-model="newBooking.ruangan" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer">
                                        <option>VIP 01</option>
                                        <option>VIP 02</option>
                                        <option>Room 101</option>
                                        <option>Room 102</option>
                                        <option>Room 103</option>
                                        <option>VIP Suite</option>
                                        <option>Party Suite</option>
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Kontak (HP)</label>
                                <input type="text" x-model="newBooking.kontak" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="08xxx">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Tanggal</label>
                                <input type="date" x-model="newBooking.tanggal" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none [color-scheme:dark]">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Jam</label>
                                <input type="time" x-model="newBooking.jam" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none [color-scheme:dark]">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Durasi (Jam)</label>
                                <div class="flex items-center gap-0">
                                    <button @click="if(newBooking.durasi > 1) newBooking.durasi--" type="button" class="h-[42px] px-4 rounded-l-lg bg-zinc-800 border border-white/10 text-white hover:bg-zinc-700 transition-all flex items-center justify-center font-bold text-lg border-r-0">−</button>
                                    <input type="number" x-model.number="newBooking.durasi" min="1" class="flex-1 h-[42px] bg-zinc-900 border border-white/10 px-3 py-2 text-white text-center font-bold focus:border-[#D0B75B] outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button @click="newBooking.durasi++" type="button" class="h-[42px] px-4 rounded-r-lg bg-zinc-800 border border-white/10 text-white hover:bg-zinc-700 transition-all flex items-center justify-center font-bold text-lg border-l-0">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                                <button @click="prepareBooking()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3 rounded-xl transition-all">
                                Preview & Simpan
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
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </template>
                <template x-if="toastType === 'error'">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </template>
            </div>
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : 'Gagal'"></h4>
                <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>
    </template>

    {{-- Receipt Preview Modal --}}
    <template x-teleport="body">
        <div x-show="showReceiptPreview" style="display: none;" 
             class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
            <div class="bg-white text-black p-6 rounded-sm w-full max-w-[320px] shadow-2xl relative">
                <div class="text-center font-mono text-sm leading-tight border-b border-dashed border-gray-400 pb-4 mb-4">
                    <h3 class="font-bold text-lg mb-1">SGRT KARAOKE</h3>
                    <p class="text-xs">Jl. Hiburan Malam No. 99</p>
                    <p class="text-xs">Telp: 021-555-999</p>
                </div>
                
                <template x-if="receiptData">
                    <div class="space-y-2 font-mono text-xs">
                        <div class="text-center font-bold uppercase mb-2">Bukti Reservasi</div>
                        <div class="flex justify-between"><span>Tanggal:</span> <span x-text="receiptData.tanggal"></span></div>
                        <div class="flex justify-between"><span>Ruangan:</span> <span x-text="receiptData.ruangan"></span></div>
                        
                        <div class="border-t border-dashed border-gray-400 my-2"></div>
                        <div class="flex justify-between"><span>Tamu:</span> <span x-text="receiptData.tamu"></span></div>
                        <div class="flex justify-between"><span>Kontak:</span> <span x-text="receiptData.kontak"></span></div>
                        
                        <div class="border-t border-dashed border-gray-400 my-2"></div>
                        <div class="text-center font-bold text-lg my-2" x-text="receiptData.jam"></div>
                        <div class="text-center text-[10px]">(Durasi: <span x-text="receiptData.durasi"></span>)</div>
                    </div>
                </template>

                <div class="text-center font-mono text-[10px] mt-6 border-t border-dashed border-gray-400 pt-4 opacity-70">
                    <p>Harap datang 10 menit sebelum jadwal</p>
                    <p>--- Tunjukkan struk ini saat check-in ---</p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <button @click="showReceiptPreview = false" class="bg-gray-200 text-gray-800 text-xs font-bold py-2 rounded hover:bg-gray-300">Batal</button>
                    <button @click="processBooking(true)" class="bg-[#D0B75B] text-black text-xs font-bold py-2 rounded hover:bg-[#e0c86b] flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-3 h-3"></i> Cetak & Simpan
                    </button>
                    <button @click="processBooking(false)" class="col-span-2 bg-transparent border border-black/20 text-black text-[10px] font-bold py-2 rounded hover:bg-black/5">
                        Simpan Tanpa Cetak
                    </button>
                </div>
            </div>
        </div>
    </template>
    </div>
    @include('dashboard.admin.pemesanan-script')
@endsection
