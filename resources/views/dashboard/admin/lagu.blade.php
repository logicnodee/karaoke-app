@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Lagu - Admin Dashboard')
@section('page-title', 'Manajemen Lagu')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'lagu'])
@endsection

@section('dashboard-content')
    <div x-data="{
        search: '',
        genre: 'Semua Genre',
        bahasa: 'Semua Bahasa',
        status: 'Semua Status',
        showModal: false,
        activeVideo: null,
        songs: {{ json_encode($katalogLagu) }}.map(s => ({...s, isTopChart: s.diputar > 3000})),
        newSong: { judul: '', artis: '', genre: 'Pop', bahasa: 'Indonesia', file: '', isTopChart: false },
        editMode: false,
        editIndex: null,
        genresByLanguage: {
            'Indonesia': ['Pop', 'Rock', 'Dangdut', 'Ballad', 'Jazz'],
            'Inggris': ['Pop', 'R&B', 'Rock', 'Electronic', 'Country']
        },
        availableGenres: ['Pop', 'Rock', 'Dangdut', 'Ballad', 'Jazz'],
        updateGenres() {
            this.availableGenres = this.genresByLanguage[this.newSong.bahasa] || [];
            // Reset genre to first available
            if (this.availableGenres.length > 0) {
                this.newSong.genre = this.availableGenres[0];
            } else {
                this.newSong.genre = '';
            }
        },
        get filteredSongs() {
            return this.songs.filter(song => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = song.judul.toLowerCase().includes(searchLower) || 
                                      song.artis.toLowerCase().includes(searchLower);
                const matchesGenre = this.genre === 'Semua Genre' || song.genre === this.genre;
                const matchesBahasa = this.bahasa === 'Semua Bahasa' || song.bahasa === this.bahasa;
                const matchesStatus = this.status === 'Semua Status' || (this.status === 'Top Chart' && song.isTopChart);
                return matchesSearch && matchesGenre && matchesBahasa && matchesStatus;
            });
        },
        init() {
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
            this.$watch('search', () => this.refreshIcons());
            this.$watch('genre', () => this.refreshIcons());
            this.$watch('bahasa', () => this.refreshIcons());
            this.$watch('status', () => this.refreshIcons());
            this.$watch('songs', () => this.refreshIcons());
        },
        refreshIcons() {
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },
        playVideo(file) {
            if(!file) {
                alert('ID YouTube tidak tersedia');
                return;
            }
            this.activeVideo = 'https://www.youtube.com/embed/' + encodeURIComponent(file) + '?autoplay=1';
        },
        closeVideo() {
            this.activeVideo = null;
        },
        extractYoutubeId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : url;
        },
        formatDuration(seconds) {
            if (!seconds) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },
        addSong() {
            if(!this.newSong.judul || !this.newSong.artis || !this.newSong.file) {
                 alert('Mohon lengkapi semua data termasuk Link/ID YouTube.');
                 return;
            }
            
            const ytId = this.extractYoutubeId(this.newSong.file);

            this.songs.unshift({
                judul: this.newSong.judul,
                artis: this.newSong.artis,
                genre: this.newSong.genre,
                bahasa: this.newSong.bahasa,
                durasi: '04:00',
                diputar: 0,
                file: ytId,
                isTopChart: false
            });
            
            this.showModal = false;
            this.resetForm();
            alert('Lagu berhasil ditambahkan ke katalog!');
            this.refreshIcons();
        },
        openEditModal(song, index) {
            this.editIndex = this.songs.indexOf(song);
            this.newSong = { ...song }; 
            this.editMode = true;
            this.showModal = true;
        },
        updateSong() {
            if(!this.newSong.judul || !this.newSong.artis || !this.newSong.file) {
                 alert('Mohon lengkapi data utama.');
                 return;
            }
            
            const ytId = this.extractYoutubeId(this.newSong.file);
            this.newSong.file = ytId;
            
            // Assign back to array
            this.songs[this.editIndex] = { ...this.newSong };
            this.showModal = false;
            this.resetForm();
            alert('Data lagu berhasil diperbarui!');
            this.refreshIcons();
        },
        async deleteSong(song) {
            if(confirm('Apakah Anda yakin ingin menghapus lagu ' + song.judul + '?')) {
                this.songs = this.songs.filter(s => s !== song);
                alert('Lagu berhasil dihapus.');
                this.refreshIcons();
            }
        },
        resetForm() {
            this.newSong = { judul: '', artis: '', genre: 'Pop', bahasa: 'Indonesia', file: '', isTopChart: false };
            this.editMode = false;
            this.editIndex = null;
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-white" style="font-family: 'Inter';">
                    Katalog Video Lagu
                </h2>
                <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Total: <span x-text="filteredSongs.length"></span> lagu tersedia</p>
            </div>
            <button @click="showModal = true" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2 self-start" style="font-family: 'Inter';">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Input Lagu Baru
            </button>
        </div>

        {{-- Search & Filter --}}
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <div class="flex-1 relative">
                <i data-lucide="search" class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" x-model="search" placeholder="Cari judul lagu, artis..."
                       class="w-full bg-[#0A0A0A] text-white border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 placeholder-gray-600 transition-all"
                       style="font-family: 'Inter';">
            </div>
            <select x-model="genre" class="bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all"
                    style="font-family: 'Inter';">
                <option>Semua Genre</option>
                <option>Pop</option>
                <option>Rock</option>
                <option>Dangdut</option>
                <option>R&B</option>
                <option>Jazz</option>
                <option>K-Pop</option>
            </select>
            <select x-model="bahasa" class="bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all"
                    style="font-family: 'Inter';">
                <option>Semua Bahasa</option>
                <option>Indonesia</option>
                <option>Inggris</option>
                <option>Mandarin</option>
            </select>
            <select x-model="status" class="bg-[#0A0A0A] text-gray-400 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/50 transition-all"
                    style="font-family: 'Inter';">
                <option>Semua Status</option>
                <option>Top Chart</option>
            </select>
        </div>

        {{-- Song Table --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden shadow-2xl shadow-black">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                        <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-4 py-3 w-10">NO</th>
                            <th class="text-left px-4 py-3">Judul Lagu</th>
                            <th class="text-left px-4 py-3">Artis</th>
                            <th class="text-left px-4 py-3">Genre</th>
                            <th class="text-left px-4 py-3">Bahasa</th>
                            <th class="text-center px-4 py-3">Durasi</th>
                            <th class="text-center px-4 py-3">Diputar</th>
                            <th class="text-center px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(lagu, index) in filteredSongs" :key="index">
                            <tr class="hover:bg-white/[0.01] transition-colors group">
                                <td class="px-4 py-3 text-gray-600 font-mono text-[10px]" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div @click="playVideo(lagu.file)" class="w-20 h-12 rounded-lg bg-zinc-800 overflow-hidden relative group/thumb cursor-pointer border border-white/10 shrink-0">
                                            <img :src="'https://img.youtube.com/vi/' + lagu.file + '/mqdefault.jpg'" 
                                                 class="w-full h-full object-cover opacity-80 group-hover/thumb:opacity-100 transition-opacity grayscale group-hover/thumb:grayscale-0"
                                                 alt="Thumbnail">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover/thumb:bg-black/20 transition-colors">
                                                 <div class="w-6 h-6 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center group-hover/thumb:bg-[#D0B75B] group-hover/thumb:text-black transition-all shadow-lg">
                                                    <i data-lucide="play" class="w-3 h-3 text-white group-hover/thumb:text-black fill-current ml-0.5"></i>
                                                 </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-white font-bold text-sm" x-text="lagu.judul"></span>
                                            <template x-if="lagu.isTopChart">
                                                <span class="bg-[#D0B75B] text-black text-[8px] font-bold px-1.5 py-0.5 rounded w-fit flex items-center gap-1 mt-0.5">
                                                    <i data-lucide="trophy" class="w-2 h-2"></i> TOP CHART
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-400 font-medium" x-text="lagu.artis"></td>
                                <td class="px-4 py-3">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-white/5 text-gray-400 border border-white/5 uppercase tracking-wide" x-text="lagu.genre"></span>
                                </td>
                                <td class="px-4 py-3 text-gray-400" x-text="lagu.bahasa"></td>
                                <td class="px-4 py-3 text-center text-gray-400 font-mono text-[10px]" x-text="lagu.realDuration || lagu.durasi"></td>
                                <td class="px-4 py-3 text-center text-gray-500 font-bold" x-text="new Intl.NumberFormat('en-US').format(lagu.diputar) + 'x'"></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal(lagu, index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center" title="Edit">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteSong(lagu)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSongs.length === 0">
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 italic">
                                    Tidak ada lagu yang ditemukan untuk pencarian ini.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add Song Modal --}}
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
             x-transition.opacity>
            <div @click.away="showModal = false" 
                 class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-zinc-900/50">
                    <h3 class="text-white font-bold text-lg" style="font-family: 'Inter';" x-text="editMode ? 'Edit Data Lagu' : 'Input Lagu Baru'"></h3>
                    <button @click="showModal = false; resetForm()" class="text-gray-400 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Judul Lagu</label>
                        <input type="text" x-model="newSong.judul" class="w-full bg-zinc-900/50 text-white border border-white/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 transition-all placeholder:text-zinc-700" placeholder="Contoh: Sial">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Nama Artis</label>
                        <input type="text" x-model="newSong.artis" class="w-full bg-zinc-900/50 text-white border border-white/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 transition-all placeholder:text-zinc-700" placeholder="Contoh: Mahalini">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Link Youtube Atau ID Video</label>
                        <input type="text" x-model="newSong.file" class="w-full bg-zinc-900/50 text-white border border-white/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 transition-all placeholder:text-zinc-700" placeholder="Contoh: https://www.youtube.com/watch?v=pKfXFHLeR-w">
                        <template x-if="newSong.file">
                            <div class="mt-2 w-full h-32 rounded bg-zinc-800 overflow-hidden relative border border-white/5">
                                <img :src="'https://img.youtube.com/vi/' + extractYoutubeId(newSong.file) + '/mqdefault.jpg'" class="w-full h-full object-cover opacity-80">
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Bahasa</label>
                            <select x-model="newSong.bahasa" @change="updateGenres()" class="w-full bg-zinc-900/50 text-white border border-white/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 transition-all">
                                <option value="Indonesia">Indonesia</option>
                                <option value="Inggris">Inggris</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Genre</label>
                            <select x-model="newSong.genre" class="w-full bg-zinc-900/50 text-white border border-white/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 transition-all">
                                <template x-for="g in availableGenres" :key="g">
                                    <option :value="g" x-text="g"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Top Chart Toggle --}}
                    <div class="flex items-center justify-between bg-zinc-900/50 p-3 rounded-lg border border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#D0B75B]/10 flex items-center justify-center text-[#D0B75B]">
                                <i data-lucide="trophy" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Top Chart</h4>
                                <p class="text-[10px] text-gray-400">Tandai lagu ini sebagai populer</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="newSong.isTopChart" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#D0B75B]/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D0B75B]"></div>
                        </label>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-white/5 bg-zinc-900/30 flex justify-end gap-3">
                    <button @click="showModal = false; resetForm()" class="text-xs font-bold text-gray-400 hover:text-white px-4 py-2.5 transition-colors">
                        Batal
                    </button>
                    <button @click="editMode ? updateSong() : addSong()" class="text-xs bg-[#D0B75B] text-black font-bold px-6 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors shadow-lg shadow-[#D0B75B]/10" x-text="editMode ? 'Simpan Perubahan' : 'Simpan Lagu'">
                    </button>
                </div>
            </div>
        </div>

        {{-- Video Player Modal --}}
        <div x-show="activeVideo" 
             style="display: none;"
             class="fixed inset-0 z-[70] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
             x-transition.opacity>
            <div @click.away="closeVideo()" class="relative w-full max-w-4xl bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10 aspect-video">
                <div class="absolute top-4 right-4 z-[80]">
                    <button @click="closeVideo()" class="bg-black/50 hover:bg-[#D0B75B] hover:text-black text-white p-2 rounded-full backdrop-blur-sm transition-all shadow-lg">
                        <i data-lucide="x" class="w-6 h-6 border-transparent"></i>
                    </button>
                </div>
                <template x-if="activeVideo">
                    <iframe :src="activeVideo" class="w-full h-full border-0 absolute inset-0 z-[75]" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </template>
            </div>
        </div>

    </div>
@endsection
