@extends('layouts.room')

@section('content')
    {{-- Main View --}}
    <div x-data="{ 
        queryTitle: '',
        queryArtist: '',
        allSongs: [],
        get topSongs() {
            return this.allSongs ? this.allSongs.slice(0, 10) : [];
        },
        init() {
            try {
                this.allSongs = {{ Js::from($songs) }};
                if (!Array.isArray(this.allSongs)) {
                    console.error('Songs data is not an array');
                    this.allSongs = [];
                }
                

            } catch (e) {
                console.error('Error loading songs:', e);
                this.allSongs = [];
            }

            // Dispatch songs to parent for Mix Playlist feature
            this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('update-available-songs', { detail: this.allSongs }));
            });

            this.$watch('filteredSongs', () => {
                this.$nextTick(() => {
                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }
                });
            });
        },
        get filteredSongs() {
            if (!this.allSongs) return [];
            let songs = this.allSongs;
            
            const title = this.queryTitle ? this.queryTitle.toLowerCase() : '';
            const artist = this.queryArtist ? this.queryArtist.toLowerCase() : '';

            if (!title && !artist) return songs;

            return songs.filter(s => {
                const matchTitle = !title || (s.judul && s.judul.toLowerCase().includes(title));
                const matchArtist = !artist || (s.artis && s.artis.toLowerCase().includes(artist));
                return matchTitle && matchArtist;
            });
        }
    }" x-show="activePage === 'home'" x-transition.opacity.duration.500ms>
         
         {{-- Top Gradient Overlay --}}
         <div class="fixed top-0 left-0 right-[340px] h-48 bg-gradient-to-b from-black/80 to-transparent z-40 pointer-events-none"></div>

        {{-- Hero Section --}}
        <div class="pt-40 pb-12 pl-8 md:pl-12 pr-8 md:pr-12 relative z-10">
            
            {{-- Greeting & Search Section --}}
            <div class="flex flex-col md:flex-row items-end justify-between gap-8 mb-12">
                <div class="w-full">

                    {{-- Split Search Bars --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Search Title --}}
                        <div class="relative group/title">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <i data-lucide="music" class="w-6 h-6 text-gray-500 group-focus-within/title:text-[#D0B75B] transition-colors"></i>
                            </div>
                            <input type="text" 
                                   x-model.debounce.300ms="queryTitle"
                                   placeholder="Cari Judul Lagu..." 
                                   class="w-full bg-[#1a1a1a] border border-white/20 text-white text-lg rounded-2xl py-4 pl-16 pr-12 focus:outline-none focus:border-[#D0B75B] focus:ring-1 focus:ring-[#D0B75B] placeholder-gray-500 transition-all shadow-lg">
                            
                            <button x-show="queryTitle" 
                                    @click="queryTitle = ''" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors p-1"
                                    x-transition.opacity>
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        {{-- Search Artist --}}
                        <div class="relative group/artist">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <i data-lucide="mic-2" class="w-6 h-6 text-gray-500 group-focus-within/artist:text-[#D0B75B] transition-colors"></i>
                            </div>
                            <input type="text" 
                                   x-model.debounce.300ms="queryArtist"
                                   placeholder="Cari Nama Artis..." 
                                   class="w-full bg-[#1a1a1a] border border-white/20 text-white text-lg rounded-2xl py-4 pl-16 pr-12 focus:outline-none focus:border-[#D0B75B] focus:ring-1 focus:ring-[#D0B75B] placeholder-gray-500 transition-all shadow-lg">
                            
                            <button x-show="queryArtist" 
                                    @click="queryArtist = ''" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors p-1"
                                    x-transition.opacity>
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Room Info Card --}}

            </div>

            {{-- Top Chart Section --}}
            <div class="mb-4 mt-6" x-show="!queryTitle && !queryArtist">
                <h2 class="text-2xl font-bold text-white mb-4">Top Chart</h2>
                
               <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                   <template x-for="(song, index) in topSongs" :key="index">
                       <div class="flex items-center gap-2 p-2 bg-[#1a1a1a] rounded-lg hover:bg-[#252525] border border-white/5 hover:border-[#D0B75B]/30 transition-all group cursor-pointer shadow-sm"
                            @click="addToPlaylist(song, $event)">
                           
                           {{-- Rank --}}
                           <div class="w-6 h-6 rounded-full bg-black/30 flex items-center justify-center shrink-0 border border-white/5">
                               <span class="text-xs font-bold text-[#D0B75B]" x-text="index + 1"></span>
                           </div>
                           
                           {{-- Info --}}
                           <div class="flex-1 min-w-0">
                               <h4 class="font-bold text-white text-xs line-clamp-1 group-hover:text-[#D0B75B] transition-colors" x-text="song.judul"></h4>
                               <p class="text-[10px] text-gray-400 truncate" x-text="song.artis"></p>
                           </div>

                           {{-- Add Button --}}
                           <div class="w-6 h-6 rounded-full bg-white/5 group-hover:bg-[#D0B75B] group-hover:text-black flex items-center justify-center text-gray-400 transition-all shrink-0">
                               <i data-lucide="plus" class="w-3 h-3"></i>
                           </div>
                       </div>
                   </template>
               </div>
           </div>

           {{-- Koleksi Lagu Header --}}
           <div class="flex items-center gap-3 mb-2">
               <h2 class="text-xl font-bold text-white tracking-wide"></h2>
           </div>

           {{-- Song Lists (Grid) --}}
           
           <div class="space-y-4 pb-20">
               
              <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="(song, index) in filteredSongs" :key="song.id ?? index">
                        <div class="group relative bg-[#1a1a1a] rounded-lg border border-white/5 hover:border-[#D0B75B]/50 hover:bg-[#202020] transition-all duration-200 cursor-pointer p-2 flex items-center justify-between gap-3"
                             @click="addToPlaylist(song, $event)">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-white text-xs line-clamp-1 mb-0.5 group-hover:text-[#D0B75B] transition-colors" x-text="song.judul"></h4>
                                <p class="text-[10px] text-gray-400 truncate" x-text="song.artis"></p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="w-6 h-6 rounded-full bg-white/5 group-hover:bg-[#D0B75B] group-hover:text-black flex items-center justify-center text-gray-400 transition-all">
                                    <i data-lucide="plus" class="w-3 h-3"></i>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    {{-- Empty State --}}
                    <div x-show="filteredSongs.length === 0" class="col-span-full py-12 text-center text-gray-500">
                        <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                        <p>Tidak ditemukan lagu dengan kata kunci tersebut.</p>
                    </div>
                </div>

             </div>
         </div>
    </div>

    {{-- CATEGORY PAGES --}}


    @foreach($categories as $category)
        <div x-show="activePage === '{{ $category['slug'] }}'" x-transition.opacity.duration.300ms>
            <x-room.category-page :songs="$songs" :category="$category" />
        </div>
    @endforeach
@endsection
