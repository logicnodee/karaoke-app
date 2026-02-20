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
                alert('File video tidak tersedia');
                return;
            }
            this.activeVideo = '/admin/video/' + encodeURIComponent(file);
        },
        closeVideo() {
            this.activeVideo = null;
        },
        formatDuration(seconds) {
            if (!seconds) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },
        addSong() {
            if(!this.newSong.judul || !this.newSong.artis || !this.newSong.file) {
                 alert('Mohon lengkapi semua data termasuk file video.');
                 return;
            }
            
            this.songs.unshift({
                judul: this.newSong.judul,
                artis: this.newSong.artis,
                genre: this.newSong.genre,
                bahasa: this.newSong.bahasa,
                durasi: '00:00',
                diputar: 0,
                file: this.newSong.file,
                isTopChart: false
            });
            
            this.showModal = false;
            this.resetForm();
            alert('Lagu berhasil ditambahkan ke katalog!');
            this.refreshIcons();
        },
        openEditModal(song, index) {
            // Find actual index in the main array if using filteredSongs
            // But here we can just map the index or pass the song object
            // To keep it simple for prototype, we'll find the index in main array
            this.editIndex = this.songs.indexOf(song);
            this.newSong = { ...song }; 
            this.editMode = true;
            this.showModal = true;
        },
        updateSong() {
            if(!this.newSong.judul || !this.newSong.artis) {
                 alert('Mohon lengkapi data utama.');
                 return;
            }
            
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
                                            <video :src="'/admin/video/' + encodeURIComponent(lagu.file)" 
                                                   class="w-full h-full object-cover opacity-80 group-hover/thumb:opacity-100 transition-opacity grayscale group-hover/thumb:grayscale-0"
                                                   muted 
                                                   loop
                                                   preload="metadata"
                                                   @loadedmetadata="if($event.target.duration > 1 && isFinite($event.target.duration)) lagu.realDuration = formatDuration($event.target.duration)"
                                                   @mouseenter="$el.play()" 
                                                   @mouseleave="$el.pause(); $el.currentTime = 0">
                                            </video>
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover/thumb:bg-black/20 transition-colors">
                                                 <div class="w-6 h-6 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center group-hover/thumb:bg-[#D0B75B] group-hover/thumb:text-black transition-all shadow-lg">
                                                    <i data-lucide="play" class="w-3 h-3 text-white group-hover/thumb:text-black fill-current ml-0.5"></i>
                                                 </div>
                                            </div>
                                            <div class="absolute bottom-1 right-1 bg-black/60 px-1 py-0.5 rounded text-[8px] text-white font-mono opacity-0 group-hover/thumb:opacity-100 transition-opacity" x-text="lagu.realDuration || '...'"></div>
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
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1.5 block">Upload Video (MP4)</label>
                        <div class="relative">
                            <input type="file" accept="video/mp4" @change="newSong.file = $event.target.files[0] ? $event.target.files[0].name : ''" 
                                   class="w-full text-sm text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-black file:font-semibold file:bg-[#D0B75B] hover:file:bg-[#e0c86b] file:cursor-pointer cursor-pointer border border-white/10 rounded-lg bg-zinc-900/50 focus:outline-none focus:border-[#D0B75B]/50 transition-all">
                        </div>
                        <p x-show="newSong.file" class="text-[10px] text-[#D0B75B] mt-1 italic flex items-center gap-1">
                            <i data-lucide="check" class="w-3 h-3"></i> <span x-text="newSong.file"></span>
                        </p>
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
            <div @click.away="closeVideo()" class="relative w-full max-w-4xl bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                <div class="absolute top-4 right-4 z-10">
                    <button @click="closeVideo()" class="bg-black/50 hover:bg-black/80 text-white p-2 rounded-full backdrop-blur-sm transition-all">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <video x-bind:src="activeVideo" controls autoplay class="w-full h-auto max-h-[80vh] aspect-video bg-black"></video>
            </div>
        </div>

    </div>
@endsection
