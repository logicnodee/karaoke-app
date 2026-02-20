@extends('layouts.dashboard')

@section('dashboard-role', 'Operator Panel')
@section('dashboard-role-icon', 'user')

@section('title', 'Manajemen Ruangan - Operator Dashboard')
@section('page-title', 'Manajemen Ruangan')

@section('sidebar-nav')
    <a href="{{ route('dashboard.operator') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#D0B75B]/10 text-[#D0B75B] text-sm font-bold">
        <i data-lucide="monitor" class="w-5 h-5"></i>
        Monitoring Ruangan
    </a>
@endsection

@section('dashboard-content')
    <div x-data="{
        rooms: {{ json_encode($daftarRuangan) }},
        currentTime: new Date().getTime(),
        showToast: false,
        toastMessage: '',
        
        // Modal State
        showCleanModal: false,
        cleaningRoomIndex: null,
        cleaningRoomName: '',
        
        init() {
            // First render
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
            
            // Watch for changes to re-render icons
            this.$watch('rooms', () => {
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            });
            
            setInterval(() => {
                this.currentTime = new Date().getTime();
                this.updateTimers();
            }, 1000);

            // Hydrate logic same as admin
            this.rooms.forEach(room => {
                if (room.status === 'Digunakan' && !room.booking_start) {
                    if (room.sisa_waktu) {
                         const seconds = this.parseTime(room.sisa_waktu);
                         if (seconds > 0) {
                            room.billing_mode = 'paket';
                            room.sisa_detik = seconds;
                            let estimatedDuration = Math.ceil(seconds / 3600);
                            if (estimatedDuration < 1) estimatedDuration = 1;
                            room.booking_duration = estimatedDuration;
                            const elapsed = (estimatedDuration * 3600) - seconds;
                            room.booking_start = new Date(Date.now() - (elapsed * 1000)).toISOString();
                         }
                    } else if (room.durasi_pakai) {
                         const elapsedSeconds = this.parseTime(room.durasi_pakai);
                         room.billing_mode = 'open';
                         room.durasi_berjalan = elapsedSeconds;
                         room.booking_start = new Date(Date.now() - (elapsedSeconds * 1000)).toISOString();
                    } else {
                         room.billing_mode = 'open';
                         room.booking_start = new Date().toISOString();
                         room.durasi_berjalan = 0;
                    }
                }
            });
        },

        playWarningBeeps(room) {
            // Encode filenames
            const audio1Url = '/assets/sound%20effect/' + encodeURIComponent('Announcement sound effect.mp3');
            const audio2Url = '/assets/sound%20effect/' + encodeURIComponent('waktu anda tersisa 10menit sound effect (warnet).mp3');

            const audio1 = new Audio(audio1Url);
            const audio2 = new Audio(audio2Url);
            
            audio1.play()
                .then(() => {
                    audio1.onended = () => {
                        audio2.play().catch(e => console.warn('Audio 2 play failed:', e));
                    };
                })
                .catch(e => {
                    console.warn('Audio Autoplay Blocked:', e);
                });
        },

        updateTimers() {
            this.rooms.forEach(room => {
                if (room.status === 'Digunakan' && room.booking_start) {
                    const start = new Date(room.booking_start).getTime();
                    const now = this.currentTime;
                    const elapsed = Math.floor((now - start) / 1000);
                    
                    if (room.billing_mode === 'open') {
                        room.durasi_berjalan = elapsed;
                    } else if (room.billing_mode === 'paket') {
                        const totalSeconds = (room.booking_duration || 0) * 3600;
                        let remaining = totalSeconds - elapsed;
                        
                        // WARNING LOGIC
                        if (remaining <= 18 && remaining >= 17) {
                            if (!room.hasPlayedWarning) {
                                this.playWarningBeeps(room);
                                this.toastMessage = `Waktu Sesi ${room.nama} Hampir Habis!`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 5000);
                                room.hasPlayedWarning = true;
                            }
                        } else {
                            if (remaining > 22) room.hasPlayedWarning = false;
                        }

                        if (remaining <= 0) {
                            remaining = 0;
                            if (room.status !== 'Cleaning') {
                                room.status = 'Cleaning'; // Auto switch to Cleaning
                                this.toastMessage = `Waktu ${room.nama} Habis. Status Cleaning.`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 5000);
                                // Force icon refresh
                                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                            }
                        }
                        room.sisa_detik = remaining;
                        room.hampir_habis = remaining < 900; 
                    }
                }
            });
        },

        parseTime(str) {
            if (!str) return 0;
            const parts = str.split(':').map(p => parseInt(p, 10));
            if (parts.length !== 3) return 0;
            return (parts[0] * 3600) + (parts[1] * 60) + parts[2];
        },

        formatTimeArray(seconds) {
            if (!seconds || isNaN(seconds)) seconds = 0;
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },

        openCleanModal(room) {
            if (room.status !== 'Cleaning') return;
            // Find index in master array
            this.cleaningRoomIndex = this.rooms.indexOf(room);
            this.cleaningRoomName = room.nama;
            this.showCleanModal = true;
        },

        confirmClean() {
            if (this.cleaningRoomIndex === null) return;
            
            const room = this.rooms[this.cleaningRoomIndex];
            room.status = 'Kosong';
            room.tamu = null;
            room.sisa_waktu = null;
            room.hampir_habis = false;
            
            this.toastMessage = 'Status ruangan berhasil diperbarui menjadi SIAP.';
            this.showToast = true;
            this.showCleanModal = false;
            setTimeout(() => this.showToast = false, 3000);
        }
    }" class="space-y-8">

    {{-- Clean Confirmation Modal --}}
    <template x-teleport="body">
        <div x-show="showCleanModal" 
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <div class="bg-[#18181b] border border-white/10 rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all"
                 @click.away="showCleanModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="sparkles" class="w-8 h-8 text-yellow-500"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-2" style="font-family: 'Inter';">
                        Konfirmasi Kebersihan
                    </h3>
                    
                    <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                        Apakah ruangan <span class="text-white font-bold" x-text="cleaningRoomName"></span> sudah selesai dibersihkan dan siap untuk tamu berikutnya?
                    </p>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showCleanModal = false" 
                                class="py-3 px-4 rounded-xl border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/5 transition-colors">
                            Batal
                        </button>
                        <button @click="confirmClean()" 
                                class="py-3 px-4 rounded-xl bg-yellow-500 text-black font-bold text-sm hover:bg-yellow-400 transition-colors shadow-lg shadow-yellow-500/20">
                            Ya, Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Toast --}}
    <template x-teleport="body">
        <div x-show="showToast" 
             x-transition 
             class="fixed top-6 right-6 bg-[#0A0A0A] border border-green-500/20 text-green-500 px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 z-50">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span x-text="toastMessage" class="font-bold text-sm"></span>
        </div>
    </template>

    {{-- Monitoring Grid --}}
    <div>
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-[#D0B75B]/10 rounded-lg">
                <i data-lucide="monitor" class="w-5 h-5 text-[#D0B75B]"></i> 
            </div>
            <h2 class="text-xl font-black text-white tracking-tight" style="font-family: 'Inter';">Monitoring Status Real-time</h2>
        </div>

        {{-- Lantai 1 --}}
        <div class="mb-8">
            <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 1</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                <template x-for="room in rooms.filter(r => r.lantai == 1)" :key="room.nama">
                    <div @click="openCleanModal(room)" 
                         class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 relative overflow-hidden flex flex-col h-full group"
                         :class="room.status === 'Cleaning' 
                            ? 'border-yellow-500/50 cursor-pointer hover:bg-yellow-500/10 hover:shadow-lg hover:shadow-yellow-500/10' 
                            : (room.status === 'Digunakan' ? 'border-[#3f3f46] opacity-75' : 'border-green-500/30 opacity-60')">
                        
                        <div class="relative z-10 flex flex-col h-full">
                            {{-- Icon --}}
                            <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center transition-all duration-300"
                                 :class="room.status === 'Cleaning' ? 'text-yellow-500' : (room.status === 'Digunakan' ? 'text-gray-400' : 'text-green-500')">
                                <template x-if="room.status === 'Digunakan'"><div><i data-lucide="mic-2" class="w-6 h-6"></i></div></template>
                                <template x-if="room.status === 'Kosong'"><div><i data-lucide="check-circle-2" class="w-6 h-6"></i></div></template>
                                <template x-if="room.status === 'Cleaning'"><div><i data-lucide="sparkles" class="w-6 h-6 animate-pulse"></i></div></template>
                            </div>
                            
                            <p class="text-white text-sm font-black mb-1" x-text="room.nama"></p>
                            
                            {{-- Key Display --}}
                            <div class="flex items-center justify-center gap-1.5 mb-2 opacity-60">
                                <i data-lucide="key" class="w-3 h-3 text-[#D0B75B]"></i>
                                <span class="text-[10px] text-gray-300 font-mono tracking-wider" x-text="room.key || '-'"></span>
                            </div>
                            
                            <template x-if="room.status === 'Digunakan'">
                                <div class="mt-2">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mb-0.5 truncate" x-text="room.tamu || 'Tamu'"></p>
                                    <div class="text-sm font-mono font-black text-gray-500">
                                         <span x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="room.status === 'Kosong'">
                                <div class="mt-2 text-green-500 text-[10px] font-bold uppercase tracking-widest">
                                    SIAP
                                </div>
                            </template>
                            
                             <template x-if="room.status === 'Cleaning'">
                                <div class="mt-2">
                                    <div class="text-[10px] font-black text-yellow-500 animate-pulse uppercase tracking-widest">PERLU DIBERSIHKAN</div>
                                    <div class="mt-3 py-1 bg-yellow-500 text-black text-[9px] font-bold rounded uppercase">
                                        Klik Selesai
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Lantai 2 --}}
        <div>
             <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 2</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                <template x-for="room in rooms.filter(r => r.lantai == 2)" :key="room.nama">
                    <div @click="openCleanModal(room)" 
                         class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 relative overflow-hidden flex flex-col h-full group"
                         :class="room.status === 'Cleaning' 
                            ? 'border-yellow-500/50 cursor-pointer hover:bg-yellow-500/10 hover:shadow-lg hover:shadow-yellow-500/10' 
                            : (room.status === 'Digunakan' ? 'border-[#3f3f46] opacity-75' : 'border-green-500/30 opacity-60')">
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center transition-all duration-300"
                                 :class="room.status === 'Cleaning' ? 'text-yellow-500' : (room.status === 'Digunakan' ? 'text-gray-400' : 'text-green-500')">
                                <template x-if="room.status === 'Digunakan'"><div><i data-lucide="mic-2" class="w-6 h-6"></i></div></template>
                                <template x-if="room.status === 'Kosong'"><div><i data-lucide="check-circle-2" class="w-6 h-6"></i></div></template>
                                <template x-if="room.status === 'Cleaning'"><div><i data-lucide="sparkles" class="w-6 h-6 animate-pulse"></i></div></template>
                            </div>
                            
                            <p class="text-white text-sm font-black mb-1" x-text="room.nama"></p>
                            
                            <template x-if="room.status === 'Digunakan'">
                                <div class="mt-2">
                                    <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mb-0.5 truncate" x-text="room.tamu || 'Tamu'"></p>
                                    <span x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)" class="text-sm font-mono font-black text-gray-500"></span>
                                </div>
                            </template>

                            <template x-if="room.status === 'Kosong'">
                                <div class="mt-2 text-green-500 text-[10px] font-bold uppercase tracking-widest">
                                    SIAP
                                </div>
                            </template>
                            
                             <template x-if="room.status === 'Cleaning'">
                                <div class="mt-2">
                                    <div class="text-[10px] font-black text-yellow-500 animate-pulse uppercase tracking-widest">PERLU DIBERSIHKAN</div>
                                    <div class="mt-3 py-1 bg-yellow-500 text-black text-[9px] font-bold rounded uppercase">
                                        Klik Selesai
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    </div>
@endsection
