@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Operator - Admin Dashboard')
@section('page-title', 'Manajemen Operator')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'operator'])
@endsection

@section('dashboard-content')
    <div x-data="{
        rooms: {{ json_encode($daftarRuangan) }},
        selectedRoom: null,
        showConfirmModal: false,

        openConfirm(room) {
            this.selectedRoom = room;
            this.showConfirmModal = true;
        },

        completeCleaning() {
            if (!this.selectedRoom) return;

            // Update status locally
            this.selectedRoom.status = 'Kosong';
            
            // Show notification
            this.showNotification(`Ruangan ${this.selectedRoom.nama} siap digunakan!`, 'success');
            
            this.showConfirmModal = false;
            this.selectedRoom = null;
        },

        // Toast Notification State
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        
        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        formatTimeArray(seconds) {
            if (!seconds || isNaN(seconds)) seconds = 0;
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },

        // Timer initialization (optional if needed for active rooms)
        init() {
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });

            setInterval(() => {
                this.rooms.forEach(room => {
                    if (room.status === 'Digunakan' && room.sisa_detik > 0) {
                        room.sisa_detik--;
                    } else if (room.status === 'Digunakan' && room.durasi_berjalan >= 0) {
                        room.durasi_berjalan++;
                    }
                });
            }, 1000);
        }
    }" class="space-y-8">

    {{-- Toast Notification --}}
    <template x-teleport="body">
        <div x-show="showToast" 
             class="fixed top-4 right-4 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px]"
             :class="{
                'bg-[#0A0A0A] border-green-500/20 text-green-500': toastType === 'success',
                'bg-[#0A0A0A] border-red-500/20 text-red-500': toastType === 'error'
             }">
             <div class="p-2 rounded-full" :class="toastType === 'success' ? 'bg-green-500/10' : 'bg-red-500/10'">
                <i :data-lucide="toastType === 'success' ? 'check' : 'alert-circle'" class="w-5 h-5"></i>
             </div>
             <div>
                <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : 'Gagal'"></h4>
                <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
             </div>
        </div>
    </template>

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-[#D0B75B]/10 rounded-lg">
            <i data-lucide="hard-hat" class="w-5 h-5 text-[#D0B75B]"></i> 
        </div>
        <div>
            <h2 class="text-xl font-black text-white tracking-tight" style="font-family: 'Inter';">Monitoring Operator</h2>
            <p class="text-xs text-gray-400 font-medium">Pantau status kebersihan ruangan real-time.</p>
        </div>
    </div>

    {{-- Lantai 1 --}}
    <div class="mb-8">
        <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 1</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
            <template x-for="room in rooms.filter(r => r.lantai == 1)" :key="room.nama">
                <div class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 relative overflow-hidden flex flex-col h-full min-h-[140px]"
                     :class="{
                        'border-[#D0B75B] shadow-[0_0_15px_-3px_rgba(208,183,91,0.3)]': room.status === 'Cleaning',
                        'border-green-500/30 opacity-60': room.status === 'Kosong' || room.status === 'Siap',
                        'border-white/10 opacity-60': room.status === 'Digunakan'
                     }">
                    
                    {{-- Icon --}}
                    <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center"
                         :class="{
                            'text-[#D0B75B]': room.status === 'Cleaning',
                            'text-green-500': room.status === 'Kosong' || room.status === 'Siap',
                            'text-gray-500': room.status === 'Digunakan'
                         }">
                        <template x-if="room.status === 'Cleaning'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles animate-pulse"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275Z"/></svg>
                        </template>
                        <template x-if="room.status === 'Kosong' || room.status === 'Siap'">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </template>
                        <template x-if="room.status === 'Digunakan'">
                            <i data-lucide="mic-2" class="w-6 h-6"></i>
                        </template>
                    </div>

                    <p class="text-white text-sm font-black mb-1" style="font-family: 'Inter';" x-text="room.nama"></p>
                    
                    {{-- Status Text / Timer --}}
                    <div class="mt-1 mb-2">
                        <template x-if="room.status === 'Cleaning'">
                            <p class="text-[10px] font-bold text-[#D0B75B] uppercase tracking-widest animate-pulse">PERLU DIBERSIHKAN</p>
                        </template>
                         <template x-if="room.status === 'Kosong' || room.status === 'Siap'">
                            <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest">SIAP</p>
                        </template>
                        <template x-if="room.status === 'Digunakan'">
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase truncate" x-text="room.tamu || 'Tamu'"></p>
                                <p class="text-[10px] text-gray-500 font-mono" x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)"></p>
                            </div>
                        </template>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-auto">
                        <template x-if="room.status === 'Cleaning'">
                            <button @click="openConfirm(room)" 
                                    class="w-full py-2 rounded-lg bg-[#D0B75B] text-black font-bold text-[10px] uppercase tracking-wider hover:bg-[#bfa64d] transition-colors shadow-lg shadow-[#D0B75B]/20">
                                KLIK SELESAI
                            </button>
                        </template>
                         <template x-if="room.status !== 'Cleaning'">
                             <div class="w-full py-2 border border-white/5 rounded-lg text-[10px] font-bold text-gray-600 uppercase flex items-center justify-center gap-1 opacity-50 cursor-not-allowed">
                                 <i data-lucide="lock" class="w-3 h-3"></i> Kelola
                             </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Lantai 2 --}}
    <div class="mb-8">
        <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 2</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
             <template x-for="room in rooms.filter(r => r.lantai == 2)" :key="room.nama">
                <div class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 relative overflow-hidden flex flex-col h-full min-h-[140px]"
                     :class="{
                        'border-[#D0B75B] shadow-[0_0_15px_-3px_rgba(208,183,91,0.3)]': room.status === 'Cleaning',
                        'border-green-500/30 opacity-60': room.status === 'Kosong' || room.status === 'Siap',
                        'border-white/10 opacity-60': room.status === 'Digunakan'
                     }">
                    
                    {{-- Icon --}}
                    <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center"
                         :class="{
                            'text-[#D0B75B]': room.status === 'Cleaning',
                            'text-green-500': room.status === 'Kosong' || room.status === 'Siap',
                            'text-gray-500': room.status === 'Digunakan'
                         }">
                        <template x-if="room.status === 'Cleaning'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles animate-pulse"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275Z"/></svg>
                        </template>
                        <template x-if="room.status === 'Kosong' || room.status === 'Siap'">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </template>
                        <template x-if="room.status === 'Digunakan'">
                            <i data-lucide="mic-2" class="w-6 h-6"></i>
                        </template>
                    </div>

                    <p class="text-white text-sm font-black mb-1" style="font-family: 'Inter';" x-text="room.nama"></p>
                    
                    {{-- Status Text / Timer --}}
                    <div class="mt-1 mb-2">
                        <template x-if="room.status === 'Cleaning'">
                            <p class="text-[10px] font-bold text-[#D0B75B] uppercase tracking-widest animate-pulse">PERLU DIBERSIHKAN</p>
                        </template>
                         <template x-if="room.status === 'Kosong' || room.status === 'Siap'">
                            <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest">SIAP</p>
                        </template>
                        <template x-if="room.status === 'Digunakan'">
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase truncate" x-text="room.tamu || 'Tamu'"></p>
                                <p class="text-[10px] text-gray-500 font-mono" x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)"></p>
                            </div>
                        </template>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-auto">
                        <template x-if="room.status === 'Cleaning'">
                            <button @click="openConfirm(room)" 
                                    class="w-full py-2 rounded-lg bg-[#D0B75B] text-black font-bold text-[10px] uppercase tracking-wider hover:bg-[#bfa64d] transition-colors shadow-lg shadow-[#D0B75B]/20">
                                KLIK SELESAI
                            </button>
                        </template>
                         <template x-if="room.status !== 'Cleaning'">
                             <div class="w-full py-2 border border-white/5 rounded-lg text-[10px] font-bold text-gray-600 uppercase flex items-center justify-center gap-1 opacity-50 cursor-not-allowed">
                                 <i data-lucide="lock" class="w-3 h-3"></i> Kelola
                             </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-transition.opacity>
        <div class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-md p-6 relative overflow-hidden" 
             @click.outside="showConfirmModal = false">
             
             {{-- Modal Glow --}}
             <div class="absolute top-0 right-0 w-32 h-32 bg-[#D0B75B]/10 rounded-full blur-3xl -z-10 translate-x-10 -translate-y-10"></div>
             
             <div class="flex flex-col items-center text-center">
                 <div class="w-16 h-16 bg-[#D0B75B]/10 rounded-full flex items-center justify-center mb-4">
                     <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles text-[#D0B75B]"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275Z"/></svg>
                 </div>
                 
                 <h2 class="text-xl font-bold text-white mb-2">Konfirmasi Kebersihan</h2>
                 <p class="text-gray-400 text-sm mb-6">Apakah ruangan <span class="font-bold text-white" x-text="selectedRoom?.nama"></span> sudah selesai dibersihkan dan siap untuk tamu berikutnya?</p>
                 
                 <div class="flex gap-3 w-full">
                     <button @click="showConfirmModal = false" class="flex-1 py-3 rounded-xl border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/5 transition-colors">
                         Batal
                     </button>
                     <button @click="completeCleaning()" class="flex-1 py-3 rounded-xl bg-[#D0B75B] text-black font-bold text-sm hover:bg-[#bfa64d] transition-colors shadow-lg shadow-[#D0B75B]/20">
                         Ya, Selesai
                     </button>
                 </div>
             </div>
        </div>
    </div>

    </div>
@endsection
