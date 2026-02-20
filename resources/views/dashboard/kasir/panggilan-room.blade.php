@extends('layouts.dashboard')

@section('dashboard-role', 'Kasir Panel')
@section('dashboard-role-icon', 'user-check')

@section('title', 'Panggilan Room - Kasir Dashboard')
@section('page-title', 'Panggilan / Bantuan Room')

@section('sidebar-nav')
    @include('dashboard.kasir._sidebar', ['active' => 'panggilan'])
@endsection

@section('dashboard-content')
    <div x-data="roomCallsData()" x-effect="updateBadge()" class="h-full relative flex flex-col gap-4">
        
        {{-- Filters & Stats --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-[#0A0A0A] border border-white/5 p-3 rounded-xl">
            <div class="flex gap-2 overflow-x-auto w-full md:w-auto no-scrollbar">
                <button @click="filterStatus = 'Semua'" 
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                        :class="filterStatus === 'Semua' ? 'bg-[#D0B75B] text-black border-[#D0B75B]' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-white'">
                    Semua <span class="ml-1 opacity-60" x-text="'(' + helpRequests.length + ')'"></span>
                </button>
                <button @click="filterStatus = 'Baru'" 
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                        :class="filterStatus === 'Baru' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-blue-500'">
                    Baru <span class="ml-1 opacity-60" x-text="'(' + helpRequests.filter(r => r.status === 'Baru').length + ')'"></span>
                </button>
                <button @click="filterStatus = 'Diproses'" 
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                        :class="filterStatus === 'Diproses' ? 'bg-yellow-600 text-white border-yellow-600 shadow-lg shadow-yellow-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-yellow-500'">
                    Diproses <span class="ml-1 opacity-60" x-text="'(' + helpRequests.filter(r => r.status === 'Diproses').length + ')'"></span>
                </button>
                <button @click="filterStatus = 'Selesai'" 
                        class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border transition-all whitespace-nowrap"
                        :class="filterStatus === 'Selesai' ? 'bg-green-600 text-white border-green-600 shadow-lg shadow-green-600/20' : 'bg-zinc-900 text-gray-400 border-white/10 hover:text-green-500'">
                    Selesai <span class="ml-1 opacity-60" x-text="'(' + helpRequests.filter(r => r.status === 'Selesai').length + ')'"></span>
                </button>
            </div>
        </div>

        {{-- Calls Grid --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                <template x-for="req in filteredRequests" :key="req.id">
                    <div class="bg-[#0A0A0A] border border-white/5 rounded-xl overflow-hidden flex flex-col hover:shadow-xl transition-all group relative"
                            :class="{'border-blue-500/50': req.status === 'Baru', 'border-yellow-500/50': req.status === 'Diproses', 'opacity-60': req.status === 'Selesai'}">
                        
                        {{-- Header --}}
                        <div class="px-3 py-2 border-b border-white/5 bg-zinc-900/40 flex justify-between items-center">
                            <h4 class="text-sm font-black text-white truncate" x-text="req.room"></h4>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase"
                                    :class="{
                                        'bg-blue-500 text-black': req.status === 'Baru',
                                        'bg-yellow-500 text-black': req.status === 'Diproses',
                                        'bg-green-500 text-black': req.status === 'Selesai'
                                    }"
                                    x-text="req.status"></span>
                        </div>

                        {{-- Content --}}
                        <div class="p-3 flex-1 flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-gray-300">
                                <template x-if="req.type.includes('Panggil')"><i data-lucide="user" class="w-4 h-4 text-blue-400"></i></template>
                                <template x-if="req.type.includes('Audio')"><i data-lucide="mic" class="w-4 h-4 text-yellow-400"></i></template>
                                <template x-if="req.type.includes('AC') || req.type.includes('Ruangan')"><i data-lucide="thermometer" class="w-4 h-4 text-cyan-400"></i></template>
                                <template x-if="req.type.includes('Lainnya')"><i data-lucide="help-circle" class="w-4 h-4 text-gray-400"></i></template>
                                <span class="text-[10px] font-bold uppercase" x-text="req.type"></span>
                            </div>
                            <p class="text-[10px] text-gray-500 italic" x-text="req.note || 'Tidak ada catatan'"></p>
                            <div class="mt-auto pt-2 text-[9px] text-gray-600 font-mono flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <span x-text="req.time"></span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="p-2 bg-zinc-900/30 border-t border-white/5 grid grid-cols-1 gap-1">
                            <template x-if="req.status === 'Baru'">
                                <button @click="req.status = 'Diproses'" class="w-full py-1.5 rounded bg-blue-600 hover:bg-blue-500 text-white font-bold text-[9px] uppercase transition-colors">
                                    Respon
                                </button>
                            </template>
                            <template x-if="req.status === 'Diproses'">
                                <button @click="req.status = 'Selesai'" class="w-full py-1.5 rounded bg-green-600 hover:bg-green-500 text-white font-bold text-[9px] uppercase transition-colors">
                                    Selesai
                                </button>
                            </template>
                            <template x-if="req.status === 'Selesai'">
                                <button disabled class="w-full py-1.5 rounded bg-zinc-800 text-gray-600 font-bold text-[9px] uppercase cursor-not-allowed">
                                    Selesai
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('roomCallsData', () => ({
                filterStatus: 'Semua',
                helpRequests: [
                    { id: 1, room: 'Room 205', type: 'Panggil Staff', time: 'Baru Saja', status: 'Baru', note: '' },
                    { id: 2, room: 'VVIP 01', type: 'Masalah Audio', time: '5 Menit Lalu', status: 'Diproses', note: 'Mic 2 tidak bunyi' },
                    { id: 3, room: 'Room 102', type: 'Masalah AC', time: '12 Menit Lalu', status: 'Selesai', note: 'Suhu terlalu panas' },
                    { id: 4, room: 'Room 303', type: 'Lainnya', time: '15 Menit Lalu', status: 'Diproses', note: 'Minta tisu tambahan' },
                    { id: 5, room: 'Room 105', type: 'Panggil Staff', time: '20 Menit Lalu', status: 'Selesai', note: 'Minta bantuan teknis' }
                ],
                
                init() {
                    this.updateBadge(); // Initial check
                    this.$watch('filteredRequests', () => {
                        this.$nextTick(() => {
                            if(window.lucide) window.lucide.createIcons();
                        });
                    });
                     this.$nextTick(() => {
                        if(window.lucide) window.lucide.createIcons();
                    });
                },

                updateBadge() {
                    const activeCount = this.helpRequests.filter(req => req.status !== 'Selesai').length;
                    
                    // Sidebar Elements
                    const countEl = document.getElementById('call-notification-count');
                    const dotEl = document.getElementById('call-notification-dot');

                    if(countEl) {
                        countEl.innerText = activeCount;
                        // Hide if 0
                        if(activeCount === 0) {
                            countEl.style.cssText = 'display: none !important';
                        } else {
                            countEl.style.cssText = ''; // Revert to default class styles (or block)
                            countEl.style.display = 'inline-block';
                        }
                    }
                    if(dotEl) {
                        if(activeCount === 0) {
                            dotEl.style.cssText = 'display: none !important';
                        } else {
                            dotEl.style.cssText = '';
                            dotEl.style.display = 'block';
                        }
                    }
                },

                get filteredRequests() {
                    // Trigger badge update whenever dependency changes (side effect in getter is hacky but handy here if x-effect fails)
                    // But better to use x-effect on root.
                    if (this.filterStatus === 'Semua') return this.helpRequests;
                    return this.helpRequests.filter(req => req.status === this.filterStatus);
                }
            }));
        });
    </script>
@endsection
