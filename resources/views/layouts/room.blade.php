<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Dashboard - {{ $room['nama'] ?? 'Mics Karaoke' }}</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.8.49/Tone.js"></script>
    <link rel="icon" type="image/png" href="/assets/img/global/krls_logo.png">
    <style>
        @font-face {
            font-family: 'wf_header';
            src: url('/assets/fonts/wf_header.woff2') format('woff2');
            font-display: swap;
        }
        @font-face {
            font-family: 'madefor-display';
            src: url('/assets/fonts/madefor_display.woff2') format('woff2');
            font-display: swap;
        }
        body { font-family: 'madefor-display', sans-serif; background-color: #000; color: #fff; }
        .font-main-header { font-family: 'wf_header', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#141414] overflow-hidden h-screen w-screen text-gray-100 flex flex-col" x-data="roomDashboard" :class="{ 'overflow-hidden': isPlayerOpen }">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-[340px] z-[150] bg-gradient-to-b from-black/80 to-transparent px-8 py-6 flex items-center justify-between transition-all duration-300 pointer-events-none"
         :class="{ 'bg-black shadow-lg': scrolled, '-translate-y-full': isPlayerOpen }">
        
        <div class="flex items-center gap-8 pointer-events-auto">
            <a href="#" @click.prevent="activePage = 'home'">
                <img src="/assets/img/global/krls_logo.png" alt="SGRT" class="h-16 w-auto object-contain">
            </a>
            <ul class="flex gap-8 text-xl font-medium text-gray-400 font-['madefor-display'] tracking-wide">
                <li @click="activePage = 'home'" class="cursor-pointer transition-all hover:text-white hover:scale-105" :class="activePage === 'home' ? 'text-white font-bold scale-105 shadow-white drop-shadow-md' : ''">Home</li>
                @foreach($categories as $category)
                    <li @click="activePage = '{{ $category['slug'] }}'" class="cursor-pointer transition-all hover:text-white hover:scale-105" :class="activePage === '{{ $category['slug'] }}' ? 'text-white font-bold scale-105 shadow-white drop-shadow-md' : ''">{{ $category['name'] }}</li>
                @endforeach
            </ul>
        </div>

        <div class="flex items-center gap-4 relative z-[160] pointer-events-auto">

            {{-- F&B Toggle --}}
            <button class="relative group outline-none z-10" @click="isFnbOpen = !isFnbOpen">
                <div class="bg-gray-800/80 rounded-xl border border-gray-700 hover:bg-gray-700 hover:border-[#D0B75B] hover:text-[#D0B75B] transition-all flex items-center justify-center w-14 h-14 backdrop-blur-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-white group-hover:text-[#D0B75B]"><path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/><path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.7.7 2 .7 2.8 0L15 15Zm0 0 7 7"/><path d="m2.1 21.8 6.4-6.3"/><path d="m19 5-7 7"/></svg>
                </div>
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full min-w-[20px] h-[20px] flex items-center justify-center border border-[#141414] shadow-sm transform transition-transform" 
                      :class="(fnbOrder.length + totalActiveOrders) > 0 ? 'scale-100' : 'scale-0'"
                      x-text="fnbOrder.length > 0 ? fnbOrder.length : totalActiveOrders"></span>
            </button>

            {{-- Timer --}}
            <div x-data="{ 
                expanded: false, 
                timeout: null,
                toggle() {
                    this.expanded = !this.expanded;
                    if (this.timeout) clearTimeout(this.timeout);
                    if (this.expanded) {
                        this.timeout = setTimeout(() => {
                            this.expanded = false;
                        }, 3000);
                    }
                }
            }" class="relative z-50">
                <button @click="toggle()" 
                        class="bg-gray-800/80 border border-gray-700 hover:bg-gray-700 hover:border-[#D0B75B] rounded-xl flex items-center backdrop-blur-md h-14 transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden group"
                        :class="expanded ? 'gap-4 pl-4 pr-6' : 'gap-0 w-14 pl-0 pr-0 justify-center'">
                     <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 shrink-0 transition-colors group-hover:text-[#D0B75B]" :class="expanded ? 'text-[#D0B75B]' : 'text-white'"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                     <div class="flex items-center gap-4 whitespace-nowrap overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                          :class="expanded ? 'max-w-[400px] opacity-100' : 'max-w-0 opacity-0'">
                        <span class="text-white font-bold text-lg font-main-header">{{ $room['nama'] ?? 'Room' }}</span>
                        <span class="text-gray-500">•</span>
                        <span class="text-2xl font-mono font-black text-white tracking-widest tabular-nums font-main-header" 
                              :class="timerMode === 'countup' ? 'text-blue-400' : 'text-white'"
                              x-text="formattedTime"></span>
                        <div class="w-px h-5 bg-white/20 mx-2"></div>
                        <div class="cursor-pointer group/close p-1" @click.stop="expanded = false; if(timeout) clearTimeout(timeout)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-400 group-hover/close:text-white transition-colors"><path d="M18 6 6 18"/><path d="m6 6 18 18"/></svg>
                        </div>
                     </div>
                </button>
            </div>

            {{-- Help Button --}}
            <button @click="isHelpOpen = true; $nextTick(() => refreshIcons())" class="relative group outline-none z-10">
                <div class="bg-red-900/20 border border-red-900/50 hover:bg-red-900/60 hover:border-red-500 transition-all rounded-xl flex items-center gap-3 px-6 h-14 backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-red-500 group-hover:text-white transition-colors"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span class="text-sm font-bold text-red-400 group-hover:text-white uppercase tracking-wider transition-colors whitespace-nowrap hidden md:inline">Butuh Bantuan?</span>
                </div>
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full min-w-[20px] h-[20px] flex items-center justify-center border border-[#141414] shadow-sm transform transition-transform" 
                      :class="helpHistory.filter(h => h.status !== 'Selesai').length > 0 ? 'scale-100' : 'scale-0'"
                      x-text="helpHistory.filter(h => h.status !== 'Selesai').length"></span>
            </button>
            
            {{-- Logout --}}

        </div>
    </nav>

    <main class="absolute inset-0 right-[340px] overflow-y-auto custom-scrollbar no-scrollbar" @scroll="scrolled = $el.scrollTop > 50">
        @yield('content')
    </main>

    {{-- VIDEO PLAYER OVERLAY --}}
    {{-- VIDEO PLAYER OVERLAY --}}
    <div class="fixed inset-0 z-[100] bg-black group/player"
         x-ref="playerContainer"
         x-show="isPlayerOpen"
         x-transition.opacity.duration.300ms
         :class="showControls ? '' : 'cursor-none'"
         style="display: none;"
         @mousemove="handleActivity"
         @click="handleActivity">
         
         <div class="relative w-full h-full bg-black overflow-hidden group">
             {{-- Video Player --}}
             <video x-ref="mainVideo" crossorigin="anonymous" class="w-full h-full object-contain transition-transform duration-500" 
                @ended="handleEnded" 
                @click="togglePlay"
                @timeupdate="updateProgress"
                @loadedmetadata="updateProgress"
                @waiting="playerState.loading = true"
                @playing="playerState.loading = false; playerState.playing = true"
                @pause="playerState.playing = false"
                @seeked="playerState.isSeeking = false"
                @progress="updateProgress"></video>

         {{-- Top Bar --}}
         <div class="absolute top-0 left-0 right-0 p-8 flex justify-between items-start z-[110] bg-gradient-to-b from-black/90 via-black/40 to-transparent transition-opacity duration-300"
              :class="showControls ? 'opacity-100' : 'opacity-0 pointer-events-none'">
             <button @click="closePlayer()" class="text-white hover:text-[#D0B75B] transition-colors p-2 rounded-full hover:bg-white/10">
                 <i data-lucide="arrow-left" class="w-8 h-8"></i>
             </button>
              <button @click="showReportModal = true" class="text-white hover:text-red-500 transition-colors p-2 rounded-full hover:bg-white/10">
                  <i data-lucide="flag" class="w-6 h-6"></i>
              </button>
         </div>


         
         {{-- Loading Spinner --}}


         <div x-show="playerState.loading" class="absolute inset-0 flex items-center justify-center pointer-events-none z-[105]">
             <div class="w-16 h-16 border-4 border-[#D0B75B] border-t-transparent rounded-full animate-spin"></div>
         </div>

         {{-- Report Modal --}}
         <div class="fixed inset-0 z-[130] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
              x-show="showReportModal"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0"
              x-transition:enter-end="opacity-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0"
              style="display: none;">
              
             <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl w-full max-w-md shadow-2xl p-6 relative"
                  @click.outside="showReportModal = false; reportType = null; reportNote = ''">
                 
                 <button @click="showReportModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                     <i data-lucide="x" class="w-5 h-5"></i>
                 </button>

                 <h3 class="text-xl font-bold text-white mb-2">Laporkan Masalah</h3>
                 <p class="text-sm text-gray-400 mb-6">Apa masalah pada lagu <span class="text-[#D0B75B]" x-text="currentSong?.judul"></span>?</p>

                 <div class="space-y-3">
                     {{-- Quick Options --}}
                     <div class="grid grid-cols-2 gap-3">
                         <button @click="submitReport('Audio Bermasalah')" class="p-3 border border-white/10 rounded-xl hover:bg-red-500/10 hover:border-red-500/50 hover:text-red-400 text-gray-300 transition-all flex flex-col items-center justify-center gap-2 group">
                             <i data-lucide="volume-x" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                             <span class="text-xs font-bold">Audio Masalah</span>
                         </button>
                         <button @click="submitReport('Video Error')" class="p-3 border border-white/10 rounded-xl hover:bg-red-500/10 hover:border-red-500/50 hover:text-red-400 text-gray-300 transition-all flex flex-col items-center justify-center gap-2 group">
                             <i data-lucide="monitor-x" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                             <span class="text-xs font-bold">Video Error</span>
                         </button>
                         <button @click="submitReport('Lirik Salah')" class="p-3 border border-white/10 rounded-xl hover:bg-red-500/10 hover:border-red-500/50 hover:text-red-400 text-gray-300 transition-all flex flex-col items-center justify-center gap-2 group">
                             <i data-lucide="align-left" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                             <span class="text-xs font-bold">Lirik Salah</span>
                         </button>
                         <button @click="reportType = 'Lainnya'" 
                                 class="p-3 border rounded-xl transition-all flex flex-col items-center justify-center gap-2 group"
                                 :class="reportType === 'Lainnya' ? 'bg-[#D0B75B] border-[#D0B75B] text-black' : 'border-white/10 hover:bg-white/5 text-gray-300'">
                             <i data-lucide="edit-3" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                             <span class="text-xs font-bold">Lainnya</span>
                         </button>
                     </div>

                     {{-- Manual Input for 'Lainnya' --}}
                     <div x-show="reportType === 'Lainnya'" class="pt-2">
                         <textarea x-model="reportNote" 
                                   class="w-full bg-black/50 border border-white/10 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-[#D0B75B] placeholder-gray-600 resize-none"
                                   rows="3"
                                   placeholder="Jelaskan masalahnya secara detail..."></textarea>
                         <button @click="submitReport('Lainnya', reportNote)" 
                                 class="w-full mt-3 bg-[#D0B75B] text-black font-bold py-2 rounded-lg hover:bg-[#e1c564] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                 :disabled="!reportNote.trim()">
                             Kirim Laporan
                         </button>
                     </div>
                 </div>
             </div>
         </div>

         {{-- Mini Playlist Overlay --}}
         <div class="absolute bottom-32 right-8 w-96 bg-[#1a1a1a]/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl z-[120] overflow-hidden flex flex-col max-h-[60vh] transition-opacity duration-300"
              x-show="showMiniPlaylist"
              :class="!showControls ? 'opacity-0 pointer-events-none' : ''"
              x-transition:enter="transition ease-out duration-300 transform"
              x-transition:enter-start="opacity-0 translate-y-4 scale-95"
              x-transition:enter-end="opacity-100 translate-y-0 scale-100"
              x-transition:leave="transition ease-[cubic-bezier(0.4,0,0.2,1)] duration-300 transform"
              x-transition:leave-start="opacity-100 translate-y-0 scale-100"
              x-transition:leave-end="opacity-0 translate-y-4 scale-95">
              
              <div class="p-4 border-b border-white/10 flex items-center justify-between bg-[#121212]/50">
                  <div class="flex items-center gap-2">
                       <i data-lucide="arrow-left" class="w-4 h-4 text-gray-400"></i>
                       <h3 class="font-bold text-white text-sm">Playlist Anda</h3>
                  </div>
                  <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded text-gray-400" x-text="playlist.length + ' Lagu'"></span>
              </div>

              <div class="overflow-y-auto custom-scrollbar p-2 space-y-1">
                  <template x-if="playlist.length === 0">
                      <div class="py-10 text-center text-gray-500 text-xs">
                          Playlist kosong
                      </div>
                  </template>
                  
                  <template x-for="(item, index) in playlist" :key="item.id || item.judul">
                      <div class="group flex items-center gap-3 p-2 rounded-lg transition-colors cursor-move active:cursor-grabbing hover:bg-white/5 relative"
                           :class="[(currentSong && currentSong.judul === item.judul) ? 'bg-[#D0B75B]/10 border border-[#D0B75B]/20' : 'border border-transparent', draggingIndex === index ? 'opacity-50 bg-white/5' : '']"
                           draggable="true"
                           @dragstart="handleDragStart($event, index)"
                           @dragenter="handleDragEnter(index)"
                           @dragover.prevent="handleDragOver($event)"
                           @drop="handleDrop($event)"
                           @dragend="handleDragEnd()"
                           @click="playSong(item, 'playlist')">
                           
                           <div class="w-6 text-center text-xs font-bold text-gray-500" 
                                :class="(currentSong && currentSong.judul === item.judul) ? 'text-[#D0B75B]' : ''"
                                x-text="index + 1"></div>
                           
                           <div class="flex-1 min-w-0">
                               <h4 class="text-xs font-bold text-white truncate" 
                                   :class="(currentSong && currentSong.judul === item.judul) ? 'text-[#D0B75B]' : ''"
                                   x-text="item.judul"></h4>
                               <p class="text-[10px] text-gray-500 truncate" x-text="item.artis"></p>
                           </div>

                           <div class="flex items-center gap-1">
                               {{-- Move Controls --}}
                               <template x-if="!currentSong || currentSong.judul !== item.judul">
                                   <div class="flex items-center gap-1 mr-1">
                                       <button @click.stop="pinToTop(index)" 
                                               class="text-gray-500 hover:text-[#D0B75B] disabled:opacity-30 disabled:hover:text-gray-500"
                                               :disabled="index === 0"
                                               title="Pin to Top">
                                           <i data-lucide="pin" class="w-3 h-3"></i>
                                       </button>
                                       <div class="flex flex-col gap-0.5">
                                           <button @click.stop="moveInPlaylist(index, -1)" 
                                                   class="text-gray-500 hover:text-[#D0B75B] disabled:opacity-30 disabled:hover:text-gray-500"
                                                   :disabled="index === 0">
                                               <i data-lucide="chevron-up" class="w-3 h-3"></i>
                                           </button>
                                           <button @click.stop="moveInPlaylist(index, 1)" 
                                                   class="text-gray-500 hover:text-[#D0B75B] disabled:opacity-30 disabled:hover:text-gray-500"
                                                   :disabled="index === playlist.length - 1">
                                               <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                           </button>
                                       </div>
                                   </div>
                               </template>

                               <div class="text-[10px] font-bold tracking-wider" 
                                    :class="(currentSong && currentSong.judul === item.judul) ? 'text-[#D0B75B]' : 'text-gray-600'">
                                   <template x-if="currentSong && currentSong.judul === item.judul">
                                       <span>PLAYING</span>
                                   </template>
                                   <template x-if="!currentSong || currentSong.judul !== item.judul">
                                       <button @click.stop="removeFromPlaylist(index)" class="hover:text-red-500 transition-colors p-1">
                                           <i data-lucide="trash-2" class="w-3 h-3"></i>
                                       </button>
                                   </template>
                               </div>
                           </div>
                      </div>
                  </template>
              </div>
         </div>



         {{-- Bottom Controls --}}
         <div class="absolute bottom-0 left-0 right-0 px-8 pb-8 pt-32 z-[110] bg-gradient-to-t from-black/95 via-black/80 to-transparent flex flex-col gap-6 transition-opacity duration-300"
              :class="showControls ? 'opacity-100' : 'opacity-0 pointer-events-none'">
             
             {{-- Progress Bar --}}
             <div class="w-full relative group/progress cursor-pointer h-4 flex items-center z-50"
                  @click="handleProgressClick($event)"
                  @mousedown="handleProgressMouseDown($event)"
                  @mousemove="handleProgressMove($event)"
                  @mouseup="handleProgressMouseUp($event)"
                  @mouseleave="handleProgressLeave($event)">
                  {{-- Background Track --}}
                  <div class="absolute inset-x-0 h-1 bg-white/20 rounded-full overflow-hidden pointer-events-none">
                      {{-- Buffered --}}
                      <div class="absolute top-0 left-0 h-full bg-white/30 transition-all duration-300 ease-out" 
                           :style="`width: ${(playerState.buffered / (playerState.duration || 1)) * 100}%`"></div>
                      {{-- Played --}}
                      <div class="h-full bg-red-600 relative" :style="`width: ${(playerState.currentTime / (playerState.duration || 1)) * 100}%`"></div>
                  </div>
                  {{-- Thumb (Dot) --}}
                  <div class="absolute h-2.5 w-2.5 bg-red-600 rounded-full shadow-md pointer-events-none -translate-x-1/2 top-1/2 -translate-y-1/2 transition-all duration-150 group-hover/progress:h-3 group-hover/progress:w-3"
                       :style="`left: ${(playerState.currentTime / (playerState.duration || 1)) * 100}%`"></div>
                  
                   {{-- Hover Tooltip --}}
                   <div class="absolute -top-10 -translate-x-1/2 bg-black border border-white/20 rounded-lg shadow-xl overflow-hidden flex flex-col items-center pointer-events-none z-30"
                        x-show="playerState.hoverTime !== null"
                        style="display: none;" 
                        :style="`left: ${playerState.hoverX}px`">
                       
                       {{-- Time Text Only --}}
                       <div class="bg-black/90 w-full text-center px-3 py-1.5 rounded min-w-[60px]">
                           <span class="text-white text-xs font-bold font-mono" x-text="formatSeconds(playerState.hoverTime)"></span>
                       </div>
                   </div>
             </div>

             <div class="flex items-center justify-between">
                 {{-- Left Controls --}}
                 <div class="flex items-center gap-6">
                     {{-- Play/Pause --}}
                     <button @click="togglePlay" class="text-white hover:text-[#D0B75B] transition-colors relative w-8 h-8 flex items-center justify-center">
                         <span x-show="playerState.playing" style="display: none;">
                             <i data-lucide="pause" class="w-8 h-8 fill-white"></i>
                         </span>
                         <span x-show="!playerState.playing">
                             <i data-lucide="play" class="w-8 h-8 fill-white"></i>
                         </span>
                     </button>
                     
                     {{-- Rewind --}}
                     {{-- Rewind --}}
                     <button @click="skipBackward()" class="text-gray-400 hover:text-white transition-colors relative group/rewind p-2">
                         <i data-lucide="rotate-ccw" class="w-6 h-6"></i>
                         <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] font-bold pt-1 select-none">10</span>
                     </button>
                     
                     {{-- Forward --}}
                     <button @click="skipForward()" class="text-gray-400 hover:text-white transition-colors relative group/ff p-2">
                         <i data-lucide="rotate-cw" class="w-6 h-6"></i>
                         <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] font-bold pt-1 select-none">10</span>
                     </button>
                     
                     {{-- Volume --}}
                     <div x-data="{ vol: 1, muted: false, showVol: false }" 
                          class="relative flex items-center justify-center p-2"
                          @mouseenter="showVol = true" 
                          @mouseleave="showVol = false">
                         
                         <button @click="muted = !muted; $refs.mainVideo.muted = muted" class="text-gray-400 hover:text-white transition-colors">
                             <i x-show="muted || vol == 0" data-lucide="volume-x" class="w-6 h-6"></i>
                             <i x-show="!muted && vol > 0 && vol < 0.5" data-lucide="volume-1" class="w-6 h-6"></i>
                             <i x-show="!muted && vol >= 0.5" data-lucide="volume-2" class="w-6 h-6"></i>
                         </button>

                         {{-- Vertical Slider Popup --}}
                         <div class="absolute bottom-full mb-4 left-1/2 -translate-x-1/2 w-10 h-32 bg-[#1a1a1a]/95 backdrop-blur-xl border border-white/10 rounded-2xl flex flex-col items-center justify-center shadow-2xl z-[120]"
                              x-show="showVol"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                              x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                              x-transition:leave="transition ease-in duration-150"
                              x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                              x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                              style="display: none;">
                              
                              <div class="h-24 w-1.5 relative bg-white/20 rounded-full">
                                   {{-- Red Fill --}}
                                   <div class="absolute bottom-0 left-0 w-full bg-red-600 rounded-full pointer-events-none transition-all duration-75"
                                        :style="`height: ${muted ? 0 : vol * 100}%`"></div>
                                   
                                   {{-- Thumb --}}
                                   <div class="absolute left-1/2 -translate-x-1/2 w-3 h-3 bg-white rounded-full shadow-lg pointer-events-none transition-all duration-75"
                                         :style="`bottom: calc(${muted ? 0 : vol * 100}% - 6px)`"></div>
                                   
                                   {{-- Input (Rotated) --}}
                                   <input type="range" min="0" max="1" step="0.05" x-model.number="vol"
                                          @input="$refs.mainVideo.volume = vol; muted = false"
                                          class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-24 h-10 opacity-0 cursor-pointer -rotate-90 origin-center">
                              </div>
                         </div>
                     </div>

                     {{-- Skip Back --}}
                     <button @click="playPrevious()" class="text-white hover:text-[#D0B75B] transition-colors">
                         <i data-lucide="skip-back" class="w-6 h-6 fill-white"></i>
                     </button>
                 </div>

                 {{-- Now Playing Info (Center) --}}
                 <div class="flex-1 flex items-center justify-center min-w-0 px-4" x-show="currentSong">
                     <div class="text-center min-w-0 max-w-xs">
                         <h3 class="text-white font-bold text-sm truncate leading-tight" x-text="currentSong?.judul"></h3>
                         <p class="text-gray-400 text-[11px] truncate mt-0.5" x-text="currentSong?.artis || 'Unknown Artist'"></p>
                     </div>
                 </div>

                 {{-- Right Controls --}}
                 <div class="flex items-center gap-6">
                     <span class="text-sm font-mono font-bold text-gray-400">
                         <span x-text="formatSeconds(playerState.currentTime)">0:00</span> / 
                         <span x-text="formatSeconds(playerState.duration)">0:00</span>
                     </span>
                     
                     {{-- Skip --}}
                     <button @click="playNext()" class="text-white hover:text-[#D0B75B] transition-colors">
                         <i data-lucide="skip-forward" class="w-6 h-6 fill-white"></i>
                     </button>
                     
                     {{-- Playlist --}}
                     <button @click="showMiniPlaylist = !showMiniPlaylist" 
                             class="transition-colors relative"
                             :class="showMiniPlaylist ? 'text-[#D0B75B]' : 'text-gray-400 hover:text-white'">
                         <i data-lucide="list-music" class="w-6 h-6"></i>
                         <span class="absolute -top-1 -right-1 w-2 h-2 bg-[#D0B75B] rounded-full" x-show="playlist.length > 0"></span>
                     </button>
                     
                      {{-- Settings / Gear --}}
                      <div class="relative" @click.outside="showSpeedModal = false">
                          <button @click="showSpeedModal = !showSpeedModal; if(showSpeedModal) showMiniPlaylist = false" 
                                  class="text-gray-400 hover:text-white transition-colors relative flex items-center justify-center p-2">
                              <i data-lucide="settings" class="w-6 h-6"></i>
                          </button>

                          {{-- Audio Settings Popover --}}
                          <div class="absolute bottom-full right-0 -mr-12 mb-6 w-80 bg-[#121212] border border-white/10 rounded-2xl shadow-2xl p-4 z-[9999]"
                               x-show="showSpeedModal"
                               x-transition:enter="transition ease-out duration-200"
                               x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                               x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                               x-transition:leave="transition ease-in duration-150"
                               x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                               x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                               style="display: none;">
                               
                               <div class="flex justify-between items-center mb-4 pb-2 border-b border-white/5">
                                   <h3 class="text-xs font-bold text-white tracking-widest uppercase">Audio Settings</h3>
                                   <button @click="playerState.transpose = 0; playerState.pitch = 0; setSpeed(1)" 
                                           x-show="playerState.transpose !== 0 || playerState.pitch !== 0 || playerState.playbackRate !== 1"
                                           class="text-[10px] text-[#D0B75B] hover:text-white transition-colors font-bold">RESET SEMUA</button>
                               </div>

                               <div class="grid gap-4">
                                   {{-- Transpose --}}
                                   <div class="bg-[#1a1a1a] rounded-lg p-3 border border-white/5">
                                       <div class="flex justify-between items-center mb-2">
                                           <span class="text-xs font-bold text-white">Transpose</span>
                                           <div class="flex items-center gap-2">
                                                <button @click="playerState.transpose = 0" 
                                                        x-show="playerState.transpose !== 0"
                                                        class="text-gray-500 hover:text-white transition-colors"
                                                        title="Reset Transpose">
                                                    <i data-lucide="refresh-ccw" class="w-3 h-3"></i>
                                                </button>
                                                <span class="text-xs font-mono font-bold text-[#D0B75B]" x-text="playerState.transpose > 0 ? '+' + playerState.transpose : playerState.transpose"></span>
                                           </div>
                                       </div>
                                       <div class="flex items-center gap-3">
                                           <button @click="playerState.transpose > -12 ? playerState.transpose-- : null" class="text-gray-400 hover:text-white"><i data-lucide="minus" class="w-4 h-4"></i></button>
                                           <input type="range" min="-12" max="12" step="1" x-model.number="playerState.transpose" class="flex-1 h-1 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[#D0B75B]">
                                           <button @click="playerState.transpose < 12 ? playerState.transpose++ : null" class="text-gray-400 hover:text-white"><i data-lucide="plus" class="w-4 h-4"></i></button>
                                       </div>
                                   </div>

                                   {{-- Pitch --}}
                                   <div class="bg-[#1a1a1a] rounded-lg p-3 border border-white/5">
                                       <div class="flex justify-between items-center mb-2">
                                           <span class="text-xs font-bold text-white">Pitch</span>
                                           <div class="flex items-center gap-2">
                                                <button @click="playerState.pitch = 0" 
                                                        x-show="playerState.pitch != 0"
                                                        class="text-gray-500 hover:text-white transition-colors"
                                                        title="Reset Pitch">
                                                    <i data-lucide="refresh-ccw" class="w-3 h-3"></i>
                                                </button>
                                                <span class="text-xs font-mono font-bold text-[#D0B75B]" x-text="Number(playerState.pitch).toFixed(2) + ' Hz'"></span>
                                           </div>
                                       </div>
                                       <div class="flex items-center gap-3">
                                           <button @click="(playerState.pitch = Math.max(-1, parseFloat(playerState.pitch) - 0.05)).toFixed(2)" class="text-gray-400 hover:text-white"><i data-lucide="minus" class="w-4 h-4"></i></button>
                                           <input type="range" min="-1" max="1" step="0.05" x-model.number="playerState.pitch" class="flex-1 h-1 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[#D0B75B]">
                                           <button @click="(playerState.pitch = Math.min(1, parseFloat(playerState.pitch) + 0.05)).toFixed(2)" class="text-gray-400 hover:text-white"><i data-lucide="plus" class="w-4 h-4"></i></button>
                                       </div>
                                   </div>

                                   {{-- Speed --}}
                                   <div class="bg-[#1a1a1a] rounded-lg p-3 border border-white/5">
                                       <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-white">Speed</span>
                                            <div class="flex items-center gap-2">
                                                <button @click="setSpeed(1)" 
                                                        x-show="playerState.playbackRate !== 1"
                                                        class="text-gray-500 hover:text-white transition-colors"
                                                        title="Reset Speed">
                                                    <i data-lucide="refresh-ccw" class="w-3 h-3"></i>
                                                </button>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-xs font-mono font-bold text-[#D0B75B]" x-text="Math.round(playerState.playbackRate * 100) + '%'"></span>
                                                    <span class="text-[10px] text-gray-500 font-mono">Tempo bpm</span>
                                                </div>
                                            </div>
                                       </div>
                                       <div class="flex items-center gap-3">
                                           <button @click="setSpeed(Number(Math.max(0.2, playerState.playbackRate - 0.1).toFixed(1)))" class="text-gray-400 hover:text-white"><i data-lucide="minus" class="w-4 h-4"></i></button>
                                           <input type="range" min="0.2" max="4.0" step="0.1" 
                                                  :value="playerState.playbackRate" 
                                                  @input="setSpeed($event.target.value)"
                                                  class="flex-1 h-1 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[#D0B75B]">
                                           <button @click="setSpeed(Number(Math.min(4.0, playerState.playbackRate + 0.1).toFixed(1)))" class="text-gray-400 hover:text-white"><i data-lucide="plus" class="w-4 h-4"></i></button>
                                       </div>
                                   </div>
                               </div>
                          </div>
                      </div>
                     
                     {{-- Fullscreen --}}
                     <button @click="toggleFullscreen" class="text-gray-400 hover:text-white transition-colors">
                         <i data-lucide="scan" class="w-6 h-6"></i>
                     </button>
                 </div>
             </div>
         </div>
         
         {{-- Watermark Background --}}

    </div>
    </div>

    {{-- Playlist Sidebar --}}
    <div class="fixed inset-y-0 right-0 w-[340px] bg-[#121212] border-l border-white/10 shadow-2xl flex flex-col z-[140] transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
         :class="isPlayerOpen ? 'translate-x-full' : 'translate-x-0'">
         
         {{-- Header --}}
         <div class="h-14 flex items-center justify-between px-4 border-b border-white/5 bg-[#121212] shrink-0">
             <div class="flex items-center gap-3">
                 <i data-lucide="list-music" class="w-4 h-4 text-[#D0B75B]"></i>
                 <h3 class="font-bold text-white text-sm tracking-wide">Playlist Anda</h3>
             </div>
             <div class="flex items-center gap-2">
                 <button @click="clearPlaylist()" 
                         x-show="playlist.length > 0" 
                         class="bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/50 text-[9px] font-bold text-red-500 px-3 py-1 rounded-md transition-all uppercase tracking-wider active:scale-95"
                         title="Kosongkan Playlist">
                     HAPUS SEMUA
                 </button>
                 <span class="text-xs bg-white/10 px-2 py-1 rounded text-gray-400 font-bold min-w-[50px] text-center" x-text="playlist.length + ' Lagu'"></span>
             </div>
         </div>

         {{-- Content --}}
         <div class="flex-1 overflow-y-auto px-3 py-2 custom-scrollbar space-y-1">
             <template x-if="playlist.length === 0">
                 <div class="h-full flex flex-col items-center justify-center text-gray-600 space-y-4">
                     <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                         <i data-lucide="music-2" class="w-10 h-10 opacity-30"></i>
                     </div>
                      <p class="text-sm font-medium opacity-50 mb-4">Belum ada lagu di playlist</p>
                      
                      <button @click="mixPlaylist()" 
                              class="px-4 py-2 bg-[#D0B75B]/10 hover:bg-[#D0B75B]/20 border border-[#D0B75B]/50 hover:border-[#D0B75B] text-[#D0B75B] rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 group">
                          <i data-lucide="shuffle" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i>
                          <span>Mix Playlist</span>
                      </button>
                  </div>
             </template>
             
              <template x-for="(item, index) in playlist" :key="item.id || item.judul">
                  <div class="rounded-lg border px-2.5 py-2 flex items-center gap-2.5 group transition-all relative overflow-hidden cursor-move active:cursor-grabbing"
                        draggable="true"
                        @dragstart="handleDragStart($event, index)"
                        @dragenter="handleDragEnter(index)"
                        @dragover.prevent="handleDragOver($event)"
                        @drop="handleDrop($event)"
                        @dragend="handleDragEnd()"
                        :class="[(currentSong && currentSong.judul === item.judul) ? 'bg-[#D0B75B]/20 border-[#D0B75B]/50' : 'bg-[#1a1a1a] border-white/5 hover:border-[#D0B75B]/30', draggingIndex === index ? 'bg-[#D0B75B]/10 border-[#D0B75B]/50 z-10' : '']">
                     
                     {{-- Number Box --}}
                     <div class="w-6 h-6 bg-white/5 rounded flex items-center justify-center shrink-0 border border-white/5 font-mono text-[10px] font-bold"
                           :class="(currentSong && currentSong.judul === item.judul) ? 'text-[#D0B75B]' : 'text-gray-500'">
                          <span x-text="index + 1"></span>
                     </div>

                     {{-- Song Info --}}
                     <div class="flex-1 min-w-0 cursor-pointer" @click="playSong(item, 'playlist')">
                         <h4 class="font-bold text-xs text-white truncate" x-text="item.judul"></h4>
                          <p class="text-[9px] text-gray-400 truncate" x-text="(item.artis || 'Unknown') + ' • Pop'"></p>    
                     </div>

                     {{-- Actions --}}
                     <div class="flex items-center gap-1.5 pl-1">
                         {{-- Active State: NOW PLAYING --}}
                         <template x-if="currentSong && currentSong.judul === item.judul">
                             <span class="text-[10px] font-bold text-[#D0B75B] tracking-wider whitespace-nowrap">NOW PLAYING</span>
                         </template>

                         {{-- Inactive State: Vertical Move Controls --}}
                         <template x-if="!currentSong || currentSong.judul !== item.judul">
                             <div class="flex items-center gap-1">
                                 {{-- Pin to Top --}}
                                 <button class="w-6 h-6 rounded hover:bg-white/10 flex items-center justify-center text-gray-500 hover:text-[#D0B75B] transition-colors" 
                                         @click.stop="pinToTop(index)" 
                                         :class="index === 0 ? 'invisible pointer-events-none' : ''"
                                         title="Pin to Top">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><line x1="12" y1="17" x2="12" y2="22"></line><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path></svg>
                                 </button>

                                 <div class="flex flex-col items-center justify-center gap-0.5">
                                     {{-- Move Up --}}
                                     <button class="w-6 h-4 rounded hover:bg-white/10 flex items-center justify-center text-gray-500 hover:text-[#D0B75B] transition-colors" 
                                             @click.stop="moveInPlaylist(index, -1)" 
                                             :class="index === 0 ? 'invisible pointer-events-none' : ''">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="m18 15-6-6-6 6"/></svg>
                                     </button>
                                     
                                     {{-- Move Down --}}
                                     <button class="w-6 h-4 rounded hover:bg-white/10 flex items-center justify-center text-gray-500 hover:text-[#D0B75B] transition-colors" 
                                             @click.stop="moveInPlaylist(index, 1)" 
                                             :class="index === playlist.length - 1 ? 'invisible pointer-events-none' : ''">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="m6 9 6 6 6-6"/></svg>
                                     </button>
                                 </div>
                             </div>
                         </template>

                         {{-- Delete --}}
                         <button @click.stop="removeFromPlaylist(index)" 
                                  class="w-6 h-6 rounded hover:bg-red-500/10 flex items-center justify-center text-gray-500 hover:text-red-500 transition-colors">
                             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                         </button>
                     </div>
                 </div>
             </template>
         </div>

         {{-- Footer with Play All Button --}}
          <div class="px-3 py-3 border-t border-white/10 bg-[#121212] z-10 shrink-0">
              <button @click="if(playlist.length > 0) { playSong(playlist[0], 'playlist'); }" 
                      :disabled="playlist.length === 0"
                      class="w-full bg-[#D0B75B] disabled:opacity-50 disabled:cursor-not-allowed text-black font-black py-2.5 rounded-lg hover:bg-[#e1ca72] transition-colors flex items-center justify-center gap-2 shadow-lg shadow-[#D0B75B]/10 active:scale-[0.98] transform duration-100 uppercase tracking-widest text-xs">
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                 <span>Putar Semua</span>
                 <span class="bg-black/10 px-2 py-0.5 rounded text-xs font-bold" x-text="'( ' + playlist.length + ' )'"></span>
             </button>
         </div>
    </div>

    {{-- F&B Modal (Redesign) --}}
    <div x-show="isFnbOpen" 
         @click.outside="isFnbOpen = false"
         x-transition:enter="transition transform ease-out duration-700"
         x-transition:enter-start="-translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition transform ease-in duration-500"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="-translate-y-full"
         class="fixed inset-0 z-[200] flex items-center justify-center bg-[#0a0a0a]"
         style="display: none;">
         
         <div class="bg-[#0a0a0a] w-full h-full flex overflow-hidden relative">
             
             {{-- Left: Main Content (Header + Tabs + Grid) --}}
             <div class="flex-1 flex flex-col min-w-0 bg-[#0a0a0a] h-full relative border-r border-white/5">
                 
                 {{-- Fixed Header Section --}}
                 <div class="shrink-0 bg-[#0a0a0a] z-10 border-b border-white/5">
                     <div class="flex justify-between items-center px-8 h-20 border-b border-white/5">
                         
                         <div class="flex items-center gap-6">
                             {{-- Title --}}
                             <div class="flex items-center gap-3">
                                 <i data-lucide="utensils-crossed" class="w-6 h-6 text-[#D0B75B]"></i>
                                 <div>
                                     <h2 class="text-xl font-black text-white tracking-tight leading-none">Food & Beverages</h2>
                                     <p class="text-[10px] text-gray-400 font-medium tracking-wide">Pesan langsung dari ruangan Anda</p>
                                 </div>
                             </div>

                             {{-- Vertical Divider --}}
                             <div class="h-8 w-px bg-white/10"></div>

                             {{-- Category Tabs (Moved Up) --}}
                             <div class="flex gap-2">
                                 <button @click="activeCategory = 'All'" 
                                         class="px-6 py-2 rounded-full text-[10px] font-bold transition-all uppercase tracking-wide border"
                                         :class="activeCategory === 'All' ? 'bg-[#D0B75B] border-[#D0B75B] text-black' : 'bg-transparent border-white/10 text-gray-400 hover:text-white hover:border-white/30'">
                                     SEMUA
                                 </button>
                                 <button @click="activeCategory = 'Beverages'" 
                                         class="px-6 py-2 rounded-full text-[10px] font-bold transition-all uppercase tracking-wide border"
                                         :class="activeCategory === 'Beverages' ? 'bg-[#D0B75B] border-[#D0B75B] text-black' : 'bg-transparent border-white/10 text-gray-400 hover:text-white hover:border-white/30'">
                                     BEVERAGES
                                 </button>
                                 <button @click="activeCategory = 'Food'" 
                                         class="px-6 py-2 rounded-full text-[10px] font-bold transition-all uppercase tracking-wide border"
                                         :class="activeCategory === 'Food' ? 'bg-[#D0B75B] border-[#D0B75B] text-black' : 'bg-transparent border-white/10 text-gray-400 hover:text-white hover:border-white/30'">
                                     FOOD
                                 </button>
                             </div>
                         </div>

                         {{-- Close Button --}}
                         <button @click="isFnbOpen = false" class="w-10 h-10 rounded-full hover:bg-red-600 flex items-center justify-center transition-colors group">
                             <i data-lucide="x" class="w-6 h-6 text-red-500 group-hover:text-white transition-colors"></i>
                         </button>
                     </div>
                 </div>

                 {{-- Scrollable Grid Section --}}
                 <div class="flex-1 overflow-y-auto px-8 pb-20 custom-scrollbar">
                     <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-4 gap-y-6 content-start pt-6">
                         <template x-for="item in fnbMenu.filter(i => activeCategory === 'All' || i.category === activeCategory)" :key="item.name">
                             <div @click="addToOrder(item)" class="group relative bg-transparent flex flex-col cursor-pointer hover:opacity-90 transition-opacity">
                                 
                                 {{-- Image Container (Compact) --}}
                                 <div class="relative mb-2">
                                     <div class="aspect-square relative overflow-hidden bg-[#121212]">
                                         {{-- Gold Corners (Smaller) --}}
                                         <div class="absolute top-0 left-0 w-6 h-6 border-t-[1.5px] border-l-[1.5px] border-[#D0B75B] z-20"></div>
                                         <div class="absolute bottom-0 right-0 w-6 h-6 border-b-[1.5px] border-r-[1.5px] border-[#D0B75B] z-20"></div>
                                         
                                         <img :src="'/' + item.image" class="w-full h-full object-cover">
                                         
                                         <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                                         
                                         {{-- Add Button (Compact) --}}
                                         <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[1px] z-10">
                                             <button class="w-10 h-10 rounded-full bg-[#D0B75B] text-black flex items-center justify-center shadow-xl hover:bg-white transition-colors duration-300">
                                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                     <path d="M5 12h14" />
                                                     <path d="M12 5v14" />
                                                 </svg>
                                             </button>
                                         </div>
                                     </div>
                                     
                                     {{-- Badge (Compact) --}}
                                     <div class="absolute top-2 right-2 z-20">
                                         <span class="text-[9px] font-black px-1.5 py-0.5 rounded-sm uppercase tracking-wider shadow-lg" 
                                               :class="item.category === 'Beverages' ? 'bg-blue-500 text-white' : 'bg-[#D0B75B] text-black'" 
                                               x-text="item.category"></span>
                                     </div>
                                 </div>

                                 {{-- Content (Compact) --}}
                                 <div class="relative">
                                     <h3 class="font-bold text-white text-xs mb-0.5 leading-tight" x-text="item.name"></h3>
                                     <p class="text-[10px] text-gray-400 line-clamp-2 h-6 leading-snug" x-text="item.description"></p>
                                     <div class="flex items-center justify-between mt-1">
                                         <span class="text-[#D0B75B] font-bold font-mono text-xs" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price)"></span>
                                     </div>
                                 </div>
                             </div>
                         </template>
                     </div>
                 </div>
             </div>
             
             {{-- Right: Cart Sidebar (Full Height) --}}
             <div class="w-[400px] bg-[#121212] border-l border-white/5 flex flex-col shadow-2xl z-20 relative h-full">
                 {{-- Sidebar Tabs --}}
                 <div class="flex border-b border-white/5 shrink-0 h-20">
                     <button @click="sidebarTab = 'cart'" 
                             class="flex-1 h-full text-xs font-bold tracking-widest uppercase border-b-2 transition-all flex items-center justify-center gap-2 relative bg-[#121212] hover:bg-[#181818]"
                             :class="sidebarTab === 'cart' ? 'border-[#D0B75B] text-[#D0B75B]' : 'border-transparent text-gray-500 hover:text-white'">
                         KERANJANG
                         <span class="absolute top-6 right-8 bg-red-600 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full" x-show="fnbOrder.length > 0" x-text="fnbOrder.length"></span>
                     </button>
                     <button @click="sidebarTab = 'history'" 
                             class="flex-1 h-full text-xs font-bold tracking-widest uppercase border-b-2 transition-all flex items-center justify-center gap-2 relative bg-[#121212] hover:bg-[#181818]"
                             :class="sidebarTab === 'history' ? 'border-[#D0B75B] text-[#D0B75B]' : 'border-transparent text-gray-500 hover:text-white'">
                         RIWAYAT
                         <span class="absolute top-6 right-8 bg-blue-600 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full" x-show="totalActiveOrders > 0" x-text="totalActiveOrders"></span>
                     </button>
                 </div>
                 
                 {{-- Fixed Subheader for Cart --}}
                 <div class="px-6 pt-6 pb-2 shrink-0 flex justify-between items-center border-b border-white/5 pb-4 mb-2" x-show="sidebarTab === 'cart' && fnbOrder.length > 0">
                     <span class="text-xs font-bold text-[#D0B75B] tracking-wider">ITEM DIPILIH</span>
                     <span class="text-xs text-gray-500 font-mono" x-text="fnbOrder.length + ' ITEM'"></span>
                 </div>
                 
                 {{-- Cart Content --}}
                 <div class="flex-1 overflow-y-auto px-6 pb-6 pt-2 custom-scrollbar flex flex-col gap-4" x-show="sidebarTab === 'cart'">
                     
                     <template x-if="fnbOrder.length === 0">
                         <div class="h-full flex flex-col items-center justify-center text-gray-600 space-y-4">
                             <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                                 <i data-lucide="shopping-basket" class="w-10 h-10 opacity-30"></i>
                             </div>
                             <p class="text-sm font-medium opacity-50">Keranjang Anda kosong</p>
                         </div>
                     </template>
                     
                     <template x-for="(item, index) in fnbOrder" :key="item.name">
                         <div class="bg-[#1a1a1a] p-2 rounded-lg border border-white/5 flex gap-3 animate-in fade-in slide-in-from-right-4 duration-300 group hover:border-white/10 transition-colors items-center">
                             <div class="w-10 h-10 bg-black/50 rounded overflow-hidden shrink-0 border border-white/5 relative">
                                 <img :src="'/' + item.image" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                             </div>
                             <div class="flex-1 min-w-0">
                                 <div class="flex justify-between items-start mb-0.5">
                                     <h4 class="font-bold text-xs text-white truncate pr-2" x-text="item.name"></h4>
                                     <p class="text-[10px] font-bold text-[#D0B75B] font-mono whitespace-nowrap" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price * item.qty)"></p>
                                 </div>
                                 
                                 <div class="flex justify-between items-center">
                                     <p class="text-[9px] text-gray-500 truncate max-w-[80px]" x-text="item.variant ? item.variant.name : item.category"></p>
                                     
                                     <div class="flex items-center gap-1.5 bg-black/40 rounded px-1 py-0.5 border border-white/5">
                                         <button @click="updateQty(item, -1)" class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-white transition-colors text-[10px] font-bold hover:bg-white/10 rounded">-</button>
                                         <span class="text-[10px] font-bold w-4 text-center text-white" x-text="item.qty"></span>
                                         <button @click="updateQty(item, 1)" class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-white transition-colors text-[10px] font-bold hover:bg-white/10 rounded">+</button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </template>
                 </div>

                 {{-- History Content --}}
                 <div class="flex-1 overflow-y-auto min-h-0 custom-scrollbar relative" x-show="sidebarTab === 'history'">
                     <div class="p-6 flex flex-col gap-4 pb-24">
                         <template x-if="orderHistory.length === 0">
                             <div class="h-full flex flex-col items-center justify-center text-gray-600 space-y-4 py-20">
                                 <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                                     <i data-lucide="history" class="w-10 h-10 opacity-30"></i>
                                 </div>
                                 <p class="text-sm font-medium opacity-50">Belum ada riwayat pesanan</p>
                             </div>
                         </template>

                         <template x-for="order in orderHistory" :key="order.id">
                             <div class="bg-[#1a1a1a] rounded-xl border border-white/5 overflow-hidden animate-in fade-in slide-in-from-right-4 duration-300 shrink-0">
                                 <div class="px-4 py-3 bg-white/5 flex justify-between items-center border-b border-white/5">
                                     <div class="flex items-center gap-2">
                                         <span class="text-[10px] bg-white/10 px-1.5 py-0.5 rounded text-gray-400 font-mono" x-text="'#' + order.id"></span>
                                         <span class="text-[10px] text-gray-500" x-text="order.time"></span>
                                     </div>
                                     <div class="flex items-center gap-2">
                                         <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-white/5 text-gray-400 border border-white/10" x-text="order.paymentMethod" x-show="order.paymentMethod"></span>
                                         <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#D0B75B]/10 text-[#D0B75B] border border-[#D0B75B]/20" x-text="order.status"></span>
                                     </div>
                                 </div>
                                 <div class="p-4 space-y-3">
                                     <template x-for="item in order.items" :key="item.name">
                                         <div class="flex gap-3 items-start">
                                              <div class="w-10 h-10 rounded-md bg-black/50 overflow-hidden shrink-0 border border-white/5">
                                                  <img :src="'/' + item.image" class="w-full h-full object-cover opacity-80">
                                              </div>
                                              <div class="flex-1 min-w-0">
                                                  <h4 class="text-xs font-bold text-gray-300 truncate" x-text="item.name"></h4>
                                                  <div class="flex justify-between items-center mt-1">
                                                      <span class="text-[10px] text-[#D0B75B] font-bold" x-text="item.qty + 'x'"></span>
                                                      <span class="text-[10px] text-gray-500 font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price * item.qty)"></span>
                                                  </div>
                                              </div>
                                         </div>
                                     </template>
                                 </div>
                                 <div class="px-4 py-3 border-t border-white/5 flex justify-between items-center bg-black/20">
                                     <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total</span>
                                     <span class="text-sm font-bold text-white font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(order.total)"></span>
                                 </div>
                             </div>
                         </template>
                     </div>
                 </div>
                 
                 {{-- Footer --}}
                 <div class="p-6 bg-[#121212] border-t border-white/5 shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-30" x-show="sidebarTab === 'cart'">
                     <div class="flex justify-between items-end mb-6">
                         <span class="text-gray-400 text-xs font-bold tracking-widest uppercase">Grand Total</span>
                         <span class="text-2xl font-black text-[#D0B75B] font-mono tracking-wide" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalOrderPrice)"></span>
                     </div>
                     
                     <button @click="showConfirmation = true" 
                             :disabled="fnbOrder.length === 0"
                             class="w-full bg-[#D0B75B] disabled:opacity-50 disabled:cursor-not-allowed text-black font-black py-4 rounded-xl hover:bg-[#e1ca72] transition-colors flex items-center justify-center gap-2 shadow-lg shadow-[#D0B75B]/10 active:scale-[0.98] transform duration-100 uppercase tracking-widest text-sm">
                         <i data-lucide="check-circle" class="w-5 h-5"></i>
                         Pesan Sekarang
                     </button>
                 </div>
             </div>
             
             {{-- Confirmation Modal --}}
             <div class="absolute inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
                  x-show="showConfirmation && !showPaymentGateway"
                  x-transition.opacity
                  style="display: none;">
                  <div class="bg-[#181818] w-full max-w-4xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden transform transition-all"
                       @click.outside="showConfirmation = false; paymentMethod = null;">
                      <div class="p-6 text-center border-b border-white/5">
                          <h3 class="text-2xl font-bold text-white mb-1">KONFIRMASI PESANAN</h3>
                          <p class="text-base text-gray-400">Mohon periksa kembali pesanan Anda</p>
                      </div>
                      
                      <div class="max-h-[50vh] overflow-y-auto p-6 custom-scrollbar">
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                               <template x-for="item in fnbOrder" :key="item.name">
                                   <div class="flex items-center gap-3 p-2.5 border border-white/5 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                       <div class="w-10 h-10 rounded bg-black/50 overflow-hidden shrink-0 border border-white/5">
                                           <img :src="'/' + item.image" class="w-full h-full object-cover">
                                       </div>
                                       <div class="flex-1 text-left min-w-0">
                                           <h4 class="font-bold text-xs text-white truncate" x-text="item.name"></h4>
                                           <div class="flex justify-between items-center mt-1">
                                                <p class="text-[10px] text-gray-400 font-bold" x-text="'Qty: ' + item.qty"></p>
                                                <span class="font-bold text-xs text-[#D0B75B] font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price * item.qty)"></span>
                                           </div>
                                       </div>
                                   </div>
                               </template>
                           </div>
                      </div>
                      
                      <div class="p-6 bg-[#121212] border-t border-white/10">
                          {{-- Total --}}
                          <div class="flex justify-between items-center mb-6 px-4">
                              <span class="text-lg text-gray-400 font-bold">Total Pembayaran</span>
                              <span class="text-3xl font-black text-[#D0B75B] font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalOrderPrice)"></span>
                          </div>

                          {{-- Payment Method Selection --}}
                          <div class="mb-6 px-4">
                              <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Metode Pembayaran</p>
                              <div class="grid grid-cols-2 gap-3">
                                  {{-- Open Billing Option --}}
                                  <button @click="if(isOpenBilling) paymentMethod = 'open_billing'"
                                          class="relative p-4 rounded-xl border-2 transition-all duration-300 text-left group overflow-hidden"
                                          :class="paymentMethod === 'open_billing' 
                                              ? 'border-[#D0B75B] bg-[#D0B75B]/10' 
                                              : (isOpenBilling ? 'border-white/10 bg-white/5 hover:border-white/30 hover:bg-white/10' : 'border-white/5 bg-white/[0.02] opacity-40 cursor-not-allowed')"
                                          :disabled="!isOpenBilling">
                                      <div class="flex items-center gap-3 mb-2">
                                          <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-colors"
                                               :class="paymentMethod === 'open_billing' ? 'bg-[#D0B75B]/20' : 'bg-white/10'">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                   :class="paymentMethod === 'open_billing' ? 'text-[#D0B75B]' : 'text-gray-400'">
                                                  <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                                              </svg>
                                          </div>
                                          <div>
                                              <h4 class="font-bold text-sm" :class="paymentMethod === 'open_billing' ? 'text-[#D0B75B]' : 'text-white'">OPEN BILLING</h4>
                                              <p class="text-[10px] text-gray-500">Bayar di kasir saat checkout</p>
                                          </div>
                                      </div>
                                      <template x-if="!isOpenBilling">
                                          <div class="flex items-center gap-1.5 mt-1">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                                                  <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>
                                              </svg>
                                              <span class="text-[9px] text-red-400 font-medium">Tidak tersedia (billing paket)</span>
                                          </div>
                                      </template>
                                      <template x-if="isOpenBilling">
                                          <div class="flex items-center gap-1.5 mt-1">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                              </svg>
                                              <span class="text-[9px] text-green-400 font-medium">Tersedia untuk ruangan ini</span>
                                          </div>
                                      </template>
                                      {{-- Selected Indicator --}}
                                      <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                           :class="paymentMethod === 'open_billing' ? 'border-[#D0B75B] bg-[#D0B75B]' : 'border-gray-600'">
                                          <svg x-show="paymentMethod === 'open_billing'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                                              <path d="M20 6 9 17l-5-5"/>
                                          </svg>
                                      </div>
                                  </button>

                                  {{-- Payment Gateway Option --}}
                                  <button @click="paymentMethod = 'payment_gateway'"
                                          class="relative p-4 rounded-xl border-2 transition-all duration-300 text-left group overflow-hidden"
                                          :class="paymentMethod === 'payment_gateway' 
                                              ? 'border-[#D0B75B] bg-[#D0B75B]/10' 
                                              : 'border-white/10 bg-white/5 hover:border-white/30 hover:bg-white/10'">
                                      <div class="flex items-center gap-3 mb-2">
                                          <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-colors"
                                               :class="paymentMethod === 'payment_gateway' ? 'bg-[#D0B75B]/20' : 'bg-white/10'">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                   :class="paymentMethod === 'payment_gateway' ? 'text-[#D0B75B]' : 'text-gray-400'">
                                                  <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                              </svg>
                                          </div>
                                          <div>
                                              <h4 class="font-bold text-sm" :class="paymentMethod === 'payment_gateway' ? 'text-[#D0B75B]' : 'text-white'">PAYMENT GATEWAY</h4>
                                              <p class="text-[10px] text-gray-500">Bayar sekarang via digital</p>
                                          </div>
                                      </div>
                                      <div class="flex items-center gap-2 mt-1">
                                          <span class="text-[8px] font-bold px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/20">QRIS</span>
                                          <span class="text-[8px] font-bold px-1.5 py-0.5 rounded bg-green-500/20 text-green-400 border border-green-500/20">TRANSFER</span>
                                          <span class="text-[8px] font-bold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-400 border border-purple-500/20">E-WALLET</span>
                                      </div>
                                      {{-- Selected Indicator --}}
                                      <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                           :class="paymentMethod === 'payment_gateway' ? 'border-[#D0B75B] bg-[#D0B75B]' : 'border-gray-600'">
                                          <svg x-show="paymentMethod === 'payment_gateway'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                                              <path d="M20 6 9 17l-5-5"/>
                                          </svg>
                                      </div>
                                  </button>
                              </div>
                          </div>

                          {{-- Action Buttons --}}
                          <div class="flex gap-4 px-4">
                              <button @click="showConfirmation = false; paymentMethod = null;" class="flex-1 py-4 rounded-xl font-bold text-gray-400 bg-white/5 hover:bg-white/10 transition-colors uppercase tracking-wide text-sm">
                                  BATAL
                              </button>
                              <button @click="confirmSubmit()" 
                                      :disabled="!paymentMethod"
                                      class="flex-1 py-4 rounded-xl font-bold text-black bg-[#D0B75B] hover:bg-[#e1ca72] transition-colors uppercase tracking-wide text-sm disabled:opacity-30 disabled:cursor-not-allowed">
                                  <span x-text="paymentMethod === 'payment_gateway' ? 'LANJUT BAYAR' : 'KONFIRMASI PESANAN'"></span>
                              </button>
                          </div>
                      </div>
                  </div>
             </div>

             {{-- Payment Gateway Modal --}}
             <div class="absolute inset-0 z-[55] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
                  x-show="showPaymentGateway"
                  x-transition.opacity
                  style="display: none;">
                  <div class="bg-[#181818] w-full max-w-md rounded-2xl border border-white/10 shadow-2xl overflow-hidden"
                       @click.outside="if(!paymentProcessing) { showPaymentGateway = false; showConfirmation = true; }">
                      
                      {{-- Header --}}
                      <div class="p-5 text-center border-b border-white/5 relative">
                          <button @click="if(!paymentProcessing) { showPaymentGateway = false; showConfirmation = true; selectedGateway = null; }"
                                  class="absolute left-5 top-1/2 -translate-y-1/2 p-2 rounded-full hover:bg-white/10 text-gray-400 hover:text-white transition-colors"
                                  :class="paymentProcessing ? 'opacity-0 pointer-events-none' : ''">
                              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                          </button>
                          <h3 class="text-lg font-bold text-white">PAYMENT GATEWAY</h3>
                          <p class="text-xs text-gray-500 mt-0.5">Pilih metode pembayaran digital</p>
                      </div>

                      {{-- Payment Success State --}}
                      <template x-if="paymentSuccess">
                          <div class="p-10 text-center">
                              <div class="w-20 h-20 rounded-full bg-green-500/20 flex items-center justify-center mx-auto mb-6 animate-bounce">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-green-400">
                                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                  </svg>
                              </div>
                              <h3 class="text-2xl font-black text-green-400 mb-2">PEMBAYARAN BERHASIL</h3>
                              <p class="text-gray-400 text-sm mb-2">Pesanan Anda sedang diproses</p>
                              <p class="text-[#D0B75B] font-mono font-bold text-xl" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalOrderPrice)"></p>
                          </div>
                      </template>

                      {{-- Payment Processing State --}}
                      <template x-if="paymentProcessing && !paymentSuccess">
                          <div class="p-10 text-center">
                              <div class="relative mx-auto mb-6 w-20 h-20">
                                  <div class="w-20 h-20 border-4 border-[#D0B75B] border-t-transparent rounded-full animate-spin"></div>
                              </div>
                              <h3 class="text-lg font-bold text-white mb-2">Memproses Pembayaran...</h3>
                              <p class="text-gray-500 text-sm">Mohon tunggu sebentar</p>
                          </div>
                      </template>

                      {{-- Gateway Selection State --}}
                      <template x-if="!paymentProcessing && !paymentSuccess">
                          <div>
                              {{-- Amount --}}
                              <div class="px-6 pt-5 pb-4 text-center border-b border-white/5 bg-white/[0.02]">
                                  <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Pembayaran</p>
                                  <p class="text-3xl font-black text-[#D0B75B] font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalOrderPrice)"></p>
                              </div>

                              {{-- Gateway Options --}}
                              <div class="p-5 space-y-3">
                                  {{-- QRIS --}}
                                  <button @click="selectedGateway = 'qris'"
                                          class="w-full p-4 rounded-xl border-2 transition-all duration-300 flex items-center gap-4"
                                          :class="selectedGateway === 'qris' ? 'border-[#D0B75B] bg-[#D0B75B]/10' : 'border-white/10 bg-white/5 hover:border-white/20'">
                                      <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                                           :class="selectedGateway === 'qris' ? 'bg-blue-500/20' : 'bg-blue-500/10'">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400">
                                              <rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/>
                                              <path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>
                                          </svg>
                                      </div>
                                      <div class="flex-1 text-left">
                                          <h4 class="font-bold text-sm" :class="selectedGateway === 'qris' ? 'text-[#D0B75B]' : 'text-white'">QRIS</h4>
                                          <p class="text-[10px] text-gray-500">Scan QR untuk pembayaran instan</p>
                                      </div>
                                      <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                           :class="selectedGateway === 'qris' ? 'border-[#D0B75B] bg-[#D0B75B]' : 'border-gray-600'">
                                          <svg x-show="selectedGateway === 'qris'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-black"><path d="M20 6 9 17l-5-5"/></svg>
                                      </div>
                                  </button>

                                  {{-- Bank Transfer --}}
                                  <button @click="selectedGateway = 'bank_transfer'"
                                          class="w-full p-4 rounded-xl border-2 transition-all duration-300 flex items-center gap-4"
                                          :class="selectedGateway === 'bank_transfer' ? 'border-[#D0B75B] bg-[#D0B75B]/10' : 'border-white/10 bg-white/5 hover:border-white/20'">
                                      <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                                           :class="selectedGateway === 'bank_transfer' ? 'bg-green-500/20' : 'bg-green-500/10'">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-400">
                                              <line x1="3" x2="21" y1="22" y2="22"/><line x1="6" x2="6" y1="18" y2="11"/><line x1="10" x2="10" y1="18" y2="11"/><line x1="14" x2="14" y1="18" y2="11"/><line x1="18" x2="18" y1="18" y2="11"/>
                                              <polygon points="12 2 20 7 4 7"/>
                                          </svg>
                                      </div>
                                      <div class="flex-1 text-left">
                                          <h4 class="font-bold text-sm" :class="selectedGateway === 'bank_transfer' ? 'text-[#D0B75B]' : 'text-white'">VIRTUAL ACCOUNT</h4>
                                          <p class="text-[10px] text-gray-500">Transfer via BCA, Mandiri, BNI, BRI</p>
                                      </div>
                                      <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                           :class="selectedGateway === 'bank_transfer' ? 'border-[#D0B75B] bg-[#D0B75B]' : 'border-gray-600'">
                                          <svg x-show="selectedGateway === 'bank_transfer'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-black"><path d="M20 6 9 17l-5-5"/></svg>
                                      </div>
                                  </button>

                                  {{-- E-Wallet --}}
                                  <button @click="selectedGateway = 'ewallet'"
                                          class="w-full p-4 rounded-xl border-2 transition-all duration-300 flex items-center gap-4"
                                          :class="selectedGateway === 'ewallet' ? 'border-[#D0B75B] bg-[#D0B75B]/10' : 'border-white/10 bg-white/5 hover:border-white/20'">
                                      <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                                           :class="selectedGateway === 'ewallet' ? 'bg-purple-500/20' : 'bg-purple-500/10'">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-400">
                                              <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                                          </svg>
                                      </div>
                                      <div class="flex-1 text-left">
                                          <h4 class="font-bold text-sm" :class="selectedGateway === 'ewallet' ? 'text-[#D0B75B]' : 'text-white'">E-WALLET</h4>
                                          <p class="text-[10px] text-gray-500">GoPay, OVO, Dana, ShopeePay</p>
                                      </div>
                                      <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                           :class="selectedGateway === 'ewallet' ? 'border-[#D0B75B] bg-[#D0B75B]' : 'border-gray-600'">
                                          <svg x-show="selectedGateway === 'ewallet'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-black"><path d="M20 6 9 17l-5-5"/></svg>
                                      </div>
                                  </button>
                              </div>

                              {{-- Pay Button --}}
                              <div class="px-5 pb-5">
                                  <button @click="processPaymentGateway()"
                                          :disabled="!selectedGateway"
                                          class="w-full py-4 rounded-xl font-bold text-black bg-[#D0B75B] hover:bg-[#e1ca72] transition-colors flex items-center justify-center gap-2 uppercase tracking-widest text-sm disabled:opacity-30 disabled:cursor-not-allowed shadow-lg shadow-[#D0B75B]/10">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                      BAYAR SEKARANG
                                  </button>
                              </div>
                          </div>
                      </template>
                  </div>
             </div>

             {{-- Bottom Toast Notification --}}
             <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
                  x-show="toast.show"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                  x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                  style="display: none;">
                  <div class="bg-[#D0B75B] text-black px-6 py-3 rounded-xl shadow-2xl flex items-center gap-4 min-w-[300px]">
                      <div class="w-8 h-8 rounded-full border-2 border-black/20 flex items-center justify-center shrink-0">
                          <i data-lucide="check" class="w-5 h-5 stroke-[3]"></i>
                      </div>
                      <div>
                          <h4 class="font-black text-xs uppercase tracking-widest mb-0.5">SUKSES</h4>
                          <p class="text-xs font-medium leading-tight opacity-90">
                              <span x-text="toast.message" class="font-bold"></span>
                          </p>
                      </div>
                  </div>
             </div>

             {{-- Product Detail Modal --}}
             <div class="absolute inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                  x-show="isDetailOpen"
                  x-transition.opacity
                  style="display: none;">
                 
                 <div class="relative bg-[#121212] w-full max-w-sm rounded-2xl border border-white/10 shadow-2xl overflow-hidden" 
                      @click.away="closeDetail()">
                     
                     {{-- Image Header --}}
                     <div class="relative h-48">
                         <img :src="detailItem ? '/' + detailItem.image : ''" class="w-full h-full object-cover">
                         <div class="absolute inset-0 bg-gradient-to-t from-[#121212] to-transparent"></div>
                         
                         <button @click="closeDetail()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center hover:bg-red-600 transition-colors group">
                             <i data-lucide="x" class="w-4 h-4 text-red-500 group-hover:text-white transition-colors"></i>
                         </button>
                     </div>

                     {{-- Content --}}
                     <div class="p-6 pt-2">
                         <div class="flex justify-between items-start mb-2">
                             <h3 class="text-xl font-bold text-white leading-tight" x-text="detailItem?.name"></h3>
                         </div>
                         <p class="text-xs text-gray-400 mb-6 leading-relaxed" x-text="detailItem?.description"></p>

                         {{-- Variations --}}
                         <template x-if="detailItem?.variations && detailItem.variations.length > 0">
                             <div class="mb-6">
                                 <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 block">Pilih Varian</label>
                                 <div class="space-y-2">
                                     <template x-for="variant in detailItem.variations" :key="variant.name">
                                         <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                                                :class="detailVariant?.name === variant.name ? 'border-[#D0B75B] bg-[#D0B75B]/10' : 'border-white/10 hover:bg-white/5'">
                                             <div class="flex items-center gap-3">
                                                 <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                      :class="detailVariant?.name === variant.name ? 'border-[#D0B75B]' : 'border-gray-500'">
                                                     <div class="w-2 h-2 rounded-full bg-[#D0B75B]" x-show="detailVariant?.name === variant.name"></div>
                                                 </div>
                                                 <span class="text-sm font-medium" :class="detailVariant?.name === variant.name ? 'text-[#D0B75B]' : 'text-gray-300'" x-text="variant.name"></span>
                                             </div>
                                             <span class="text-xs font-mono" :class="detailVariant?.name === variant.name ? 'text-[#D0B75B]' : 'text-gray-500'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(variant.price)"></span>
                                             
                                             <input type="radio" name="variant" :value="variant" 
                                                    @change="detailVariant = variant" 
                                                    class="hidden">
                                         </label>
                                     </template>
                                 </div>
                             </template>

                         {{-- Footer Actions --}}
                         <div class="flex items-center justify-between pt-4 border-t border-white/10">
                             <div class="flex items-center gap-4 bg-black/30 rounded-full px-1 py-1 border border-white/10">
                                 <button @click="detailQuantity > 1 ? detailQuantity-- : null" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 text-white transition-colors">
                                     <i data-lucide="minus" class="w-4 h-4"></i>
                                 </button>
                                 <span class="font-bold text-white w-4 text-center" x-text="detailQuantity"></span>
                                 <button @click="detailQuantity++" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 text-white transition-colors">
                                     <i data-lucide="plus" class="w-4 h-4"></i>
                                 </button>
                             </div>

                             <button @click="confirmDetailOrder()" class="flex-1 ml-4 py-3 rounded-full bg-[#D0B75B] hover:bg-white text-black font-bold uppercase text-xs tracking-wide transition-colors shadow-lg flex items-center justify-center gap-2">
                                 <span>Tambah</span>
                                 <span class="font-mono text-[10px] opacity-80" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(currentDetailPrice * detailQuantity)"></span>
                             </button>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
    </div>

    {{-- Help Modal --}}
    {{-- Help Modal --}}
    <div class="fixed inset-0 z-[400] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-show="isHelpOpen"
         x-cloak
         x-transition.opacity>
         
        <div class="bg-[#121212] w-full max-w-lg rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden flex flex-col max-h-[80vh]" 
             @click.outside="isHelpOpen = false">
             
             {{-- Header --}}
             <div class="flex items-center justify-center py-6 border-b border-white/5 relative bg-[#121212] z-10 shrink-0">
                 <div class="flex items-center gap-3">
                     <i data-lucide="bell-ring" class="w-6 h-6 text-red-500 fill-red-500/20"></i>
                     <h3 class="text-xl font-bold text-white tracking-tight">Butuh Bantuan?</h3>
                 </div>
                 
                 <button @click="isHelpOpen = false" class="absolute right-6 top-1/2 -translate-y-1/2 p-2 rounded-full hover:bg-white/10 text-gray-400 hover:text-white transition-colors">
                     <i data-lucide="x" class="w-5 h-5"></i>
                 </button>
             </div>

             {{-- Content --}}
             <div class="p-6 bg-[#0a0a0a] overflow-y-auto custom-scrollbar">
                 
                 {{-- Button Grid --}}
                 <div x-show="!helpInputMode" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                     <div class="grid grid-cols-2 gap-4">
                         {{-- Panggil Staff --}}
                         <button @click="sendHelp('Panggil Staff')" 
                                 class="bg-[#1a1a1a] hover:bg-[#222] border border-white/5 hover:border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center gap-4 transition-all group active:scale-[0.98]">
                             <div class="w-14 h-14 rounded-full bg-[#1e293b] flex items-center justify-center group-hover:bg-[#1e293b]/80 transition-colors">
                                 <i data-lucide="user" class="w-7 h-7 text-blue-500"></i>
                             </div>
                             <span class="text-white font-bold text-sm tracking-wide">Panggil Staff</span>
                         </button>

                         {{-- Masalah Audio --}}
                         <button @click="sendHelp('Masalah Audio')" 
                                 class="bg-[#1a1a1a] hover:bg-[#222] border border-white/5 hover:border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center gap-4 transition-all group active:scale-[0.98]">
                             <div class="w-14 h-14 rounded-full bg-[#3f2f11] flex items-center justify-center group-hover:bg-[#3f2f11]/80 transition-colors">
                                 <i data-lucide="mic" class="w-7 h-7 text-[#D0B75B]"></i>
                             </div>
                             <span class="text-white font-bold text-sm tracking-wide">Masalah Audio</span>
                         </button>

                         {{-- Masalah Ruangan --}}
                         <button @click="sendHelp('Masalah Ruangan')" 
                                 class="bg-[#1a1a1a] hover:bg-[#222] border border-white/5 hover:border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center gap-4 transition-all group active:scale-[0.98]">
                             <div class="w-14 h-14 rounded-full bg-[#0e3a3e] flex items-center justify-center group-hover:bg-[#0e3a3e]/80 transition-colors">
                                 <i data-lucide="thermometer" class="w-7 h-7 text-cyan-400"></i>
                             </div>
                             <span class="text-white font-bold text-sm tracking-wide">Masalah Ruangan</span>
                         </button>

                         {{-- Lainnya --}}
                         <button @click="helpInputMode = true" 
                                 class="bg-[#1a1a1a] hover:bg-[#222] border border-white/5 hover:border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center gap-4 transition-all group active:scale-[0.98]">
                             <div class="w-14 h-14 rounded-full bg-[#27272a] flex items-center justify-center group-hover:bg-[#27272a]/80 transition-colors">
                                 <i data-lucide="message-square" class="w-7 h-7 text-gray-400"></i>
                             </div>
                             <span class="text-white font-bold text-sm tracking-wide">Lainnya...</span>
                         </button>
                     </div>
                 </div>

                 {{-- Input Form --}}
                 <div x-show="helpInputMode" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                     <div class="space-y-4">
                         <div class="flex items-center gap-3 mb-2">
                             <button @click="helpInputMode = false" class="p-2 rounded-full hover:bg-white/10 -ml-2 text-gray-400 hover:text-white transition-colors">
                                 <i data-lucide="arrow-left" class="w-5 h-5"></i>
                             </button>
                             <h4 class="font-bold text-white">Jelaskan Masalah Anda</h4>
                         </div>
                         
                         <textarea x-model="helpNote" 
                                   class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl p-4 text-white placeholder-gray-500 focus:outline-none focus:border-[#D0B75B] transition-colors resize-none h-32"
                                   placeholder="Contoh: Mic nomor 2 tidak berbunyi..."></textarea>
                         
                         <button @click="sendHelp('Lainnya')" 
                                 class="w-full bg-[#D0B75B] text-black font-black py-4 rounded-xl hover:bg-[#e1ca72] transition-colors uppercase tracking-widest shadow-lg shadow-[#D0B75B]/10">
                             KIRIM PERMINTAAN
                         </button>
                     </div>
                 </div>

                 {{-- Active Status List --}}
                 <div class="mt-8 pt-6 border-t border-white/5" x-show="helpHistory.length > 0">
                     <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Status Panggilan</h4>
                     <div class="space-y-3">
                         <template x-for="req in helpHistory" :key="req.id">
                             <div class="bg-[#1a1a1a] rounded-xl border border-white/5 p-4 flex items-center justify-between animate-in fade-in slide-in-from-bottom-2">
                                 <div class="flex items-center gap-3">
                                     <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                          :class="{
                                              'bg-blue-900/20 text-blue-500': req.type === 'Panggil Staff',
                                              'bg-yellow-900/20 text-[#D0B75B]': req.type === 'Masalah Audio',
                                              'bg-cyan-900/20 text-cyan-500': req.type === 'Masalah Ruangan',
                                              'bg-gray-800 text-gray-400': req.type === 'Lainnya'
                                          }">
                                          <i :data-lucide="req.icon" class="w-5 h-5"></i>
                                     </div>
                                     <div>
                                         <h5 class="font-bold text-white text-sm" x-text="req.type"></h5>
                                         <p class="text-[10px] text-gray-500" x-text="req.note || req.time"></p>
                                     </div>
                                 </div>
                                 <span class="text-[10px] font-bold px-2 py-1 rounded text-black uppercase tracking-wider"
                                       :class="{
                                           'bg-blue-500': req.status === 'Baru',
                                           'bg-yellow-500': req.status === 'Diproses',
                                           'bg-green-500': req.status === 'Selesai'
                                       }"
                                       x-text="req.status"></span>
                             </div>
                         </template>
                     </div>
                 </div>
             </div>
        </div>
    </div>



    {{-- Score Overlay --}}

    <div class="fixed inset-0 z-[500] flex items-center justify-center bg-black/95 backdrop-blur-sm"
         x-show="showScoreOverlay"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         style="display: none;">
         
         {{-- Party Confetti --}}
         <div class="absolute inset-0 overflow-hidden pointer-events-none">
             <template x-for="i in 50">
                 <div class="confetti absolute top-[-20px]"
                      :style="`
                        left: ${Math.random() * 100}%;
                        animation-delay: ${Math.random() * 2}s;
                        background-color: ${['#D0B75B', '#ffffff', '#FCD34D'][Math.floor(Math.random() * 3)]};
                      `"></div>
             </template>
         </div>
         
         <div class="relative flex flex-col items-center p-12 z-10">
             {{-- Trophy Icon --}}
             <div class="mb-8 relative animate-bounce">
                 <i data-lucide="trophy" class="w-28 h-28 text-[#D0B75B] drop-shadow-2xl"></i>

             </div>

             {{-- Text --}}
             <h2 class="text-4xl font-black text-white mb-2 uppercase tracking-widest animate-pulse">Luar Biasa!</h2>
             <p class="text-gray-400 text-lg mb-8 font-medium">Skor Kamu</p>

             {{-- Score Number --}}
             <div class="relative flex items-center justify-center scale-110">
                 <span class="text-[140px] font-black leading-none text-[#D0B75B] drop-shadow-sm"
                       x-text="score">
                     100
                 </span>
                 
                 {{-- Decorative Stars --}}
                 <div class="absolute -top-6 -right-16 text-[#D0B75B] animate-spin-slow">
                     <i data-lucide="sparkles" class="w-16 h-16 fill-current opacity-80"></i>
                 </div>
                 <div class="absolute -bottom-6 -left-16 text-yellow-200 animate-bounce" style="animation-duration: 2s;">
                     <i data-lucide="star" class="w-10 h-10 fill-current"></i>
                 </div>
                 <div class="absolute top-1/2 -right-20 text-white animate-pulse">
                     <i data-lucide="star" class="w-6 h-6 fill-current opacity-50"></i>
                 </div>
                 <div class="absolute top-0 -left-12 text-[#D0B75B] animate-ping" style="animation-duration: 3s;">
                     <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                 </div>
             </div>
             
             <div class="mt-12 flex items-center gap-3 bg-white/5 px-6 py-3 rounded-full border border-white/10 backdrop-blur-md">
                 <i data-lucide="timer" class="w-5 h-5 text-[#D0B75B] animate-pulse"></i>
                 <p class="text-gray-300 text-sm font-bold font-mono tracking-wide">
                     <span x-show="hasNextSong">Lagu berikutnya dalam</span>
                     <span x-show="!hasNextSong">Kembali ke Beranda dalam</span>
                     <span class="text-[#D0B75B] text-base" x-text="nextSongCountdown">5</span> detik...
                 </p>
             </div>
         </div>
    </div>

    {{-- Logic --}}
    <style>
        .confetti {
            width: 8px;
            height: 16px;
            position: absolute;
            animation: confetti-fall 4s linear infinite;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
    </style>
    <script>
        lucide.createIcons();
        document.addEventListener('alpine:init', () => {
            Alpine.data('roomDashboard', () => {
                let pitchShiftNode = null;
                let toneSource = null;

                return {
                activePage: 'home',
                activeCategory: 'All', // Added activeCategory to fix ReferenceError
                scrolled: false,
                displaySeconds: 0,
                timerMode: 'countdown',
                checkPlayed: false,
                initialData: {!! json_encode($room ?? [], 15, 512) !!},
                
                // State
                isPlaylistOpen: true,
                isFnbOpen: false,
                playlist: [],
                fnbMenu: {{ Js::from($menuItems ?? []) }},
                fnbOrder: [],
                orderHistory: [],
                isPlaying: false,
                currentSong: null,
                isHelpOpen: false,
                helpInputMode: false,
                helpNote: '',
                helpHistory: [],
                
                // F&B New State
                toast: { show: false, message: '' },
                sidebarTab: 'cart', // 'cart' or 'history'
                showConfirmation: false,
                showMiniPlaylist: false,
                isPlayerOpen: false, // New state for player visibility

                // Payment Method State
                paymentMethod: null, // 'open_billing' or 'payment_gateway'
                selectedGateway: null, // 'qris', 'bank_transfer', 'ewallet'
                showPaymentGateway: false,
                paymentProcessing: false,
                paymentSuccess: false,
                paymentCountdown: 0,
                paymentTimer: null,

                get isOpenBilling() {
                    return this.initialData && this.initialData.billing_mode === 'open';
                },

                get hasNextSong() {
                    if (!this.currentSong || this.playlist.length === 0) return false;
                    const index = this.playlist.findIndex(s => s.judul === this.currentSong.judul);
                    return index !== -1 && index < this.playlist.length - 1;
                },

                // Product Detail Modal State
                isDetailOpen: false,
                detailItem: null,
                detailQuantity: 1,
                detailVariant: null,

                refreshIcons() {
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                },
                
                playerState: {
                    playing: false,
                    currentTime: 0,
                    duration: 0,
                    buffered: 0,
                    loading: false,
                    playbackRate: 1,
                    transpose: 0,
                    pitch: 0,
                    hoverTime: null,
                    hoverX: 0,
                    lastHoverTime: 0,
                    spriteX: 0,
                    spriteY: 0,
                    spriteUrl: null,
                    spriteLoaded: false,
                    previewReady: false,
                    isSeeking: false
                },
                
                showControls: true,
                controlsTimeout: null,
                previewHoverTimeout: null,
                showReportModal: false,
                // showSpeedModal: false, // Local state used instead or reused appropriately
                showSpeedModal: false, // Keeping it for compatibility
                reportType: null,
                reportNote: '',
                
                // Score System
                showScoreOverlay: false,
                score: 0,
                scoreTimeout: null,
                nextSongCountdown: 5,
                reportNote: '',
                
                availableSongs: [], // Store all songs for mixing

                init() {
                    this.$watch('activeCategory', () => this.refreshIcons());
                    this.$watch('sidebarTab', () => this.refreshIcons());
                    
                    // Listen for songs update from child components
                    window.addEventListener('update-available-songs', (e) => {
                         console.log('Songs updated for mixing:', e.detail.length);
                         this.availableSongs = e.detail;
                    });
                    this.$watch('sidebarTab', () => this.refreshIcons());
                    this.$watch('isFnbOpen', () => this.refreshIcons());
                    this.$watch('fnbOrder', () => this.refreshIcons());
                    this.$watch('orderHistory', () => this.refreshIcons());
                    this.$watch('playlist', () => this.refreshIcons());
                    this.$watch('showMiniPlaylist', () => this.refreshIcons());
                    this.$watch('currentSong', () => this.refreshIcons());
                    this.$watch('isHelpOpen', () => this.refreshIcons());
                    
                    // Audio Params Watchers
                    this.$watch('playerState.transpose', () => this.updateAudioParams());
                    this.$watch('playerState.pitch', () => this.updateAudioParams());
                    
                    setTimeout(() => this.refreshIcons(), 500);

                    document.addEventListener('fullscreenchange', () => {
                         this.isFullscreen = !!document.fullscreenElement;
                    });
                    
                    window.addEventListener('keydown', (e) => {
                        if (e.code === 'Space' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                            e.preventDefault();
                            this.togglePlay();
                        }
                    });



                    // Listen for Putar Mix from category pages
                    window.addEventListener('play-mix', (e) => {
                        const songs = e.detail.songs;
                        if (songs && songs.length > 0) {
                            this.isPlaylistOpen = false;
                            this.playlist = songs;
                            this.playSong(songs[0], 'playlist');
                        }
                    });

                    if(this.initialData) this.updateTimeFromData(this.initialData);

                    setInterval(() => {
                        if (this.timerMode === 'countdown') {
                            if (this.displaySeconds > 0) {
                                this.displaySeconds--;
                                this.checkWarning();
                            }
                        } else {
                            this.displaySeconds++;
                        }
                    }, 1000);
                },

                // ... (existing time methods)
                updateTimeFromData(room) {
                    if (room.billing_mode === 'paket' || room.billing_mode === 'khusus') {
                        this.timerMode = 'countdown';
                         const start = new Date(room.booking_start).getTime();
                         const now = new Date().getTime();
                         const elapsed = Math.floor((now - start) / 1000);
                         const duration = (room.booking_duration || 0) * 3600;
                         let remaining = duration - elapsed;
                         if (remaining < 0) remaining = 0;
                         this.displaySeconds = remaining;
                    } else {
                        this.timerMode = 'countup';
                        const start = new Date(room.booking_start).getTime();
                        const now = new Date().getTime();
                        const elapsed = Math.floor((now - start) / 1000);
                        this.displaySeconds = elapsed;
                    }
                },

                checkWarning() {
                    if (this.displaySeconds <= 10 && this.displaySeconds > 0) {
                        this.playWarningBeep();
                    }
                },

                playWarningBeep() {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.5);
                    
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    
                    osc.start();
                    osc.stop(ctx.currentTime + 0.5);
                },

                get formattedTime() {
                    let sec = this.displaySeconds;
                    if (sec < 0) sec = 0;
                    const h = Math.floor(sec / 3600);
                    const m = Math.floor((sec % 3600) / 60);
                    const s = sec % 60;
                    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },
                
                playSong(song, mode = 'single') {
                    // Stop current HLS if any
                    if (this.hlsInstance) {
                        this.hlsInstance.destroy();
                        this.hlsInstance = null;
                    }
                    if (this.previewHls) {
                        this.previewHls.destroy();
                        this.previewHls = null;
                    }

                    console.log('Playing:', song);
                    if(mode === 'playlist') {
                        this.currentSong = song;
                        this.showMiniPlaylist = true; // Auto-show playlist
                    } else {
                        // Check if exists
                        const exists = this.playlist.find(s => s.judul === song.judul);
                        if (!exists) {
                            this.playlist.push(song);
                        }
                        this.currentSong = song;
                        this.showMiniPlaylist = false;
                    }

                    this.playerState.loading = true;
                    // Set duration instantly from metadata if available
                    this.playerState.duration = song.durasi || 0;
                    this.handleActivity(); // Init controls visibility

                    this.playerState.playing = true;
                    this.isPlaying = true;
                    this.isPlayerOpen = true; // Open player overlay
                    this.setSpeed(1); // Reset speed to normal

                    // Init Audio Context for Pitch Shift
                    this.initAudio();

                    this.$nextTick(() => {
                        const video = this.$refs.mainVideo;
                        const preview = this.$refs.previewVideo;
                        if (!video) return;
                        
                        // Reset preview src to avoid lingering frames
                        if(preview) {
                            preview.removeAttribute('src'); 
                            preview.load();
                        }

                        // URL Construction logic - Use streaming route for Range request support (seeking)
                        let url = song.url || song.video_preview;
                        
                        if (!url) {
                            console.error('No URL for song:', song);
                            this.playerState.loading = false;
                            return;
                        }

                        // Extract just the filename from the path for streaming route
                        let filename = url;
                        // Remove /assets/lagu/ prefix if present
                        if (filename.startsWith('/assets/lagu/')) {
                            filename = filename.substring('/assets/lagu/'.length);
                        }
                        
                        // Use streaming route for proper Range request support (required for seeking)
                        url = '/stream/video/' + encodeURIComponent(filename);

                        // Helper to setup fallback video
                        const setupFallbackPreview = () => {
                             console.log('Setting up fallback video preview...');
                             this.playerState.spriteLoaded = false;
                             
                             this.$nextTick(() => {
                                 const preview = this.$refs.previewVideo;
                                 if(!preview) return;

                                 // Reset
                                 preview.innerHTML = '';
                                 preview.removeAttribute('src');
                                 
                                 // HLS for Preview?
                                 if (Hls.isSupported() && (url.includes('.m3u8') || song.is_hls)) {
                                     if(this.previewHls) this.previewHls.destroy();
                                     this.previewHls = new Hls();
                                     this.previewHls.loadSource(url);
                                     this.previewHls.attachMedia(preview);
                                 } else if (preview.canPlayType('application/vnd.apple.mpegurl') && (url.includes('.m3u8') || song.is_hls)) {
                                     preview.src = url;
                                 } else {
                                     preview.src = url;
                                 }
                                 
                                 // Check readiness for fallback
                                 preview.oncanplay = () => {
                                     this.playerState.previewReady = true;
                                 };
                                 // If already ready (cached)
                                 if (preview.readyState >= 2) {
                                     this.playerState.previewReady = true;
                                 }
                                 
                                 preview.load();
                             });
                        };

                        // Determine Sprite URL locally first (don't set state yet to avoid 404s)
                        let potentialSpriteUrl = null;
                        try {
                            const filename = url.substring(url.lastIndexOf('/') + 1);
                            const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
                            const baseUrl = url.substring(0, url.lastIndexOf('/'));
                            potentialSpriteUrl = `${baseUrl}/${nameWithoutExt}_sprite.jpg`;
                        } catch (e) {
                            console.error('Error parsing sprite url', e);
                        }
                        
                        // Reset state
                        this.playerState.spriteUrl = null;
                        this.playerState.spriteLoaded = false;
                        this.playerState.previewReady = false; // Reset ready state

                        // Feature detection: Try to load sprite
                        if(potentialSpriteUrl) {
                            const img = new Image();
                            img.onload = () => {
                                console.log('Sprite loaded successfully');
                                this.playerState.spriteUrl = potentialSpriteUrl; // Only set if valid
                                this.playerState.spriteLoaded = true;
                                this.playerState.previewReady = true; // Sprite is ready!
                            };
                            img.onerror = () => {
                                console.warn('Sprite failed/missing. Using video fallback.');
                                this.playerState.spriteUrl = null;
                                this.playerState.spriteLoaded = false;
                                setupFallbackPreview();
                            };
                            img.src = potentialSpriteUrl;
                        } else {
                             setupFallbackPreview();
                        }

                        // Debug log
                        console.log('Loading video from:', url);
                        console.log('Attempting to load sprite from:', potentialSpriteUrl);
                        
                        // Main Video Setup
                        if (Hls.isSupported() && (url.includes('.m3u8') || song.is_hls)) {
                            // Main Video HLS
                            this.hlsInstance = new Hls();
                            this.hlsInstance.loadSource(url);
                            this.hlsInstance.attachMedia(video);
                            this.hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
                                video.play()
                                    .then(() => {
                                        this.playerState.loading = false;
                                    })
                                    .catch(e => {
                                        if (e.name !== 'AbortError') console.error("Auto-play blocked", e);
                                    });
                            });
                            this.hlsInstance.on(Hls.Events.ERROR, (event, data) => {
                                console.error('HLS Error:', data);
                                if(data.fatal) {
                                   this.playerState.loading = false;
                                }
                            });

                        } else if (video.canPlayType('application/vnd.apple.mpegurl') && (url.includes('.m3u8') || song.is_hls)) {
                            // Native HLS (Safari)
                            video.src = url;
                            video.addEventListener('loadedmetadata', () => {
                                video.play()
                                    .then(() => {
                                        this.playerState.loading = false;
                                    })
                                    .catch(e => {
                                         if (e.name !== 'AbortError') console.error("Auto-play blocked", e);
                                    });
                            }, { once: true });
                        } else {
                            // Standard MP4
                            video.src = url;
                            
                            // Important: Wait for main video to be ready
                            video.play()
                                .then(() => {
                                     this.playerState.loading = false;
                                })
                                .catch(e => {
                                    if (e.name !== 'AbortError') console.error("Auto-play blocked", e);
                                });
                        }
                    });
                },

                togglePlay() {
                    const video = this.$refs.mainVideo;
                    if(!video) return;
                    if(video.paused) {
                        video.play();
                        this.playerState.playing = true;
                    } else {
                        video.pause();
                        this.playerState.playing = false;
                    }
                },

                closePlayer() {
                    const video = this.$refs.mainVideo;
                    if(video) video.pause();
                    if(this.hlsInstance) {
                        this.hlsInstance.destroy();
                        this.hlsInstance = null;
                    }
                    if (this.previewHls) {
                        this.previewHls.destroy();
                        this.previewHls = null;
                    }
                    setTimeout(() => {
                        const video = this.$refs.mainVideo;
                        if(video) video.pause();
                        if(this.hlsInstance) {
                            this.hlsInstance.destroy();
                            this.hlsInstance = null;
                        }
                        if (this.previewHls) {
                             this.previewHls.destroy();
                             this.previewHls = null;
                        }
                    }, 100);
                    
                    this.playerState.playing = false;
                    this.isPlaying = false;
                    this.currentSong = null;
                    this.showMiniPlaylist = false;
                    this.isPlayerOpen = false; // Close player overlay
                },

                handleActivity() {
                    this.showControls = true;
                    if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
                    
                    // Jangan auto-close jika sedang drag & drop
                    if (this.draggingIndex !== null) return;

                    this.controlsTimeout = setTimeout(() => {
                        if (this.playerState.playing && this.draggingIndex === null) {
                            this.showControls = false;
                        }
                    }, 3000); // Sedikit diperlama biar nyaman
                },
                
                addToPlaylist(song, event) {
                    const exists = this.playlist.find(s => s.judul === song.judul);
                    if (exists) {
                        this.showToastNotification(`Lagu sudah ada di playlist`);
                        return;
                    }

                    this.playlist.push(song);
                    // Animation
                    if(event) {
                        const startX = event.clientX;
                        const startY = event.clientY;
                        this.animateFlyToPlaylist(startX, startY);
                    }
                    this.showToastNotification(`Berhasil ditambahkan: ${song.judul}`);
                },

                animateFlyToPlaylist(startX, startY) {
                    const targetBtn = document.getElementById('playlist-toggle');
                    if(!targetBtn) return;
                    
                    const rect = targetBtn.getBoundingClientRect();
                    const targetX = rect.left + rect.width / 2;
                    const targetY = rect.top + rect.height / 2;
                    
                    const el = document.createElement('div');
                    el.className = 'fixed z-[9999] w-8 h-8 rounded-full bg-[#D0B75B] flex items-center justify-center text-black pointer-events-none shadow-[0_0_15px_rgba(208,183,91,0.5)]';
                    el.style.left = `${startX}px`;
                    el.style.top = `${startY}px`;
                    el.style.transform = 'translate(-50%, -50%) scale(1)';
                    el.style.transition = 'all 0.7s cubic-bezier(0.2, 0.8, 0.2, 1)';
                    
                    // Icon
                    el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>';
                    
                    document.body.appendChild(el);
                    
                    // Trigger animation
                    requestAnimationFrame(() => {
                        el.style.left = `${targetX}px`;
                        el.style.top = `${targetY}px`;
                        el.style.transform = 'translate(-50%, -50%) scale(0.2)';
                        el.style.opacity = '0.5';
                    });
                    
                    setTimeout(() => {
                        el.remove();
                        // Pulse effect
                        if(targetBtn) {
                            targetBtn.style.transform = 'scale(1.2)';
                            targetBtn.style.filter = 'brightness(1.5)';
                            setTimeout(() => {
                                targetBtn.style.transform = 'scale(1)';
                                targetBtn.style.filter = 'none';
                            }, 200);
                        }
                    }, 700);
                },

                removeFromPlaylist(index) {
                    this.playlist.splice(index, 1);
                    this.refreshIcons();
                },

                clearPlaylist() {
                    if (this.playlist.length === 0) return;
                    
                    // Langsung hapus (Direct delete without confirmation)
                    this.playlist = [];
                    this.refreshIcons();
                    this.showToastNotification('Playlist dikosongkan');
                },

                moveInPlaylist(index, direction) {
                    const newIndex = index + direction;
                    if (newIndex >= 0 && newIndex < this.playlist.length) {
                         const item = this.playlist.splice(index, 1)[0];
                         this.playlist.splice(newIndex, 0, item);
                         this.refreshIcons();
                    }
                },

                pinToTop(index) {
                    if (index > 0 && index < this.playlist.length) {
                         const item = this.playlist.splice(index, 1)[0];
                         this.playlist.unshift(item);
                         this.refreshIcons();
                         this.showToastNotification('Lagu dipin ke atas');
                    }
                },

                // Drag and Drop Logic
                draggingIndex: null,

                handleDragStart(e, index) {
                    this.draggingIndex = index;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.dropEffect = 'move';
                    
                    // Stop auto-hide timer saat mulai drag
                    if (this.controlsTimeout) clearTimeout(this.controlsTimeout);
                    this.showControls = true;
                },

                handleDragEnd() {
                    this.draggingIndex = null;
                    this.handleActivity(); // Resume auto-hide logic
                },

                handleDragEnter(index) {
                    if (this.draggingIndex !== null && this.draggingIndex !== index) {
                        // Live Sort: Swap items immediately
                        const item = this.playlist.splice(this.draggingIndex, 1)[0];
                        this.playlist.splice(index, 0, item);
                        this.draggingIndex = index;
                    }
                },

                handleDragOver(e) {
                    e.preventDefault(); 
                },

                handleDrop(e) {
                    this.draggingIndex = null;
                    this.refreshIcons();
                    this.handleActivity();
                },

                mixPlaylist() {
                    if (this.availableSongs.length === 0) {
                        this.showToastNotification('Sedang memuat lagu, coba sesaat lagi...');
                        return;
                    }

                    let candidates = this.availableSongs;
                    
                    // Filter based on activePage (Category) if applicable
                    if (this.activePage !== 'home') {
                        // Attempt to filter by category slug matching activePage
                        // We check common fields: 'category', 'kategori', 'category_slug'
                        const filtered = candidates.filter(s => {
                            const cat = (s.category || s.kategori || '').toLowerCase();
                            const slug = (s.category_slug || '').toLowerCase();
                            return cat === this.activePage || slug === this.activePage || cat.includes(this.activePage);
                        });
                        
                        // If filtering found results, use them. Otherwise fallback to all (or show empty?)
                        // User request: "harus sesuai". So if no match, maybe notification?
                        // But data might not be perfect. Let's fallback to candidates if filtered is empty implies mapped wrongly, 
                        // but safer to try filtered first.
                        if (filtered.length > 0) {
                            candidates = filtered;
                        } else {
                             // Fallback or Notify? Let's notify for clarity
                             // this.showToastNotification('Tidak ada lagu khusus kategori ini, mengambil acak...');
                             // Actually, let's just mix all if 0 found, to be helpful.
                        }
                    }

                    // Shuffle
                    const shuffled = [...candidates].sort(() => 0.5 - Math.random());
                    
                    // Pick 10 (or less)
                    const count = Math.min(10, shuffled.length);
                    const selected = shuffled.slice(0, count);
                    
                    if (selected.length === 0) {
                        this.showToastNotification('Tidak ada lagu tersedia');
                        return;
                    }

                    // Add to playlist
                    selected.forEach(song => {
                        // Check duplicates
                        if (!this.playlist.find(p => p.judul === song.judul)) {
                            this.playlist.push(song);
                        }
                    });

                    this.showToastNotification(`${count} lagu ditambahkan ke Mix`);
                    this.refreshIcons();
                },

                sendHelp(type) {
                    if (type === 'Lainnya' && !this.helpInputMode) {
                        this.helpInputMode = true;
                        this.refreshIcons();
                        return;
                    }

                    // Map types to icons
                    const icons = {
                        'Panggil Staff': 'user',
                        'Masalah Audio': 'mic',
                        'Masalah Ruangan': 'thermometer',
                        'Lainnya': 'message-square'
                    };

                    const note = type === 'Lainnya' ? this.helpNote : '';
                    if (type === 'Lainnya' && !note.trim()) {
                        this.showToastNotification('Mohon isi pesan Anda');
                        return;
                    }

                    const newRequest = {
                        id: Date.now(),
                        type: type,
                        icon: icons[type] || 'bell',
                        note: note,
                        status: 'Baru',
                        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                    };

                    this.helpHistory.unshift(newRequest);
                    this.showToastNotification('Permintaan bantuan dikirim');
                    
                    // Reset
                    this.helpInputMode = false;
                    this.helpNote = '';
                    // this.isHelpOpen = false; // Keep open to show status? User said "toast aja", but logic implies seeing status.
                    // If I keep it open, they see the new status immediately.
                    
                    this.refreshIcons();
                },
                
                handleEnded() {
                    // Generate Random Score 80-100
                    this.score = Math.floor(Math.random() * (100 - 80 + 1)) + 80;
                    this.showScoreOverlay = true;
                    this.showControls = false; // Hide controls to focus on score
                    this.nextSongCountdown = 5;
                    
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });

                    if (this.scoreTimeout) clearInterval(this.scoreTimeout);
                    this.scoreTimeout = setInterval(() => {
                        this.nextSongCountdown--;
                        if (this.nextSongCountdown <= 0) {
                            clearInterval(this.scoreTimeout);
                            this.showScoreOverlay = false;
                            this.playNext();
                        }
                    }, 1000);
                },

                playNext() {
                    if (this.playlist.length === 0) return;
                    
                    // Logic to find next index
                    let currentIndex = -1;
                    if (this.currentSong) {
                        currentIndex = this.playlist.findIndex(s => s.judul === this.currentSong.judul);
                    }
                    
                    if (currentIndex !== -1 && currentIndex < this.playlist.length - 1) {
                        this.playSong(this.playlist[currentIndex + 1], 'playlist');
                        // Ensure we remain in playlist mode view if needed
                    } else {
                        // End of playlist or single song
                        this.closePlayer();
                    }
                },

                playPrevious() {
                    if (this.playlist.length === 0) return;
                    
                    let currentIndex = -1;
                    if (this.currentSong) {
                        currentIndex = this.playlist.findIndex(s => s.judul === this.currentSong.judul);
                    }

                    if (currentIndex > 0) {
                        this.playSong(this.playlist[currentIndex - 1], 'playlist');
                    } else {
                        // Restart current song
                        const video = this.$refs.mainVideo;
                        if(video) video.currentTime = 0;
                    }
                },

                setSpeed(rate) {
                    this.playerState.playbackRate = rate;
                    if(this.$refs.mainVideo) {
                        this.$refs.mainVideo.playbackRate = rate;
                    }
                },    

                skipForward() {
                    const video = this.$refs.mainVideo;
                    if (video) {
                        video.currentTime = Math.min(video.currentTime + 10, video.duration);
                        this.playerState.currentTime = video.currentTime; // Instant update UI
                    }
                },

                skipBackward() {
                     const video = this.$refs.mainVideo;
                     if (video) {
                         video.currentTime = Math.max(video.currentTime - 10, 0);
                         this.playerState.currentTime = video.currentTime; // Instant update UI
                     }
                },
                
                updateProgress() {
                    const video = this.$refs.mainVideo;
                    if(video) {
                        // Don't overwrite currentTime while user is seeking or video is mid-seek
                        if (!this.playerState.isSeeking && !video.seeking) {
                            this.playerState.currentTime = video.currentTime;
                        }
                        // Use actual video duration for progress bar (metadata durasi may differ from actual file)
                        this.playerState.duration = (video.duration && isFinite(video.duration)) ? video.duration : (this.currentSong && this.currentSong.durasi ? this.currentSong.durasi : 0);
                        if (!video.paused) this.playerState.playing = true;

                        // Calculate Buffered
                        if (video.buffered.length > 0) {
                            for (let i = 0; i < video.buffered.length; i++) {
                                if (video.buffered.start(i) <= video.currentTime && video.buffered.end(i) >= video.currentTime) {
                                    this.playerState.buffered = video.buffered.end(i);
                                    break;
                                }
                            }
                            // Fallback if current time not in buffered range (e.g. seeking ahead)
                             if (video.currentTime > this.playerState.buffered) {
                                 this.playerState.buffered = video.buffered.end(video.buffered.length - 1);
                             }
                        }
                    }
                },

                formatSeconds(seconds) {
                    if (!seconds && seconds !== 0) return '0:00';
                    const h = Math.floor(seconds / 3600);
                    const m = Math.floor((seconds % 3600) / 60);
                    const s = Math.floor(seconds % 60);
                    if (h > 0) {
                        return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    }
                    return `${m}:${s.toString().padStart(2, '0')}`;
                },

                handleProgressHover(e) {
                    const rect = e.currentTarget.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const width = rect.width;
                    const percent = Math.max(0, Math.min(1, x / width));
                    
                    this.playerState.hoverX = x;
                    const hoverTime = percent * (this.playerState.duration || 0);
                    this.playerState.hoverTime = hoverTime;
                    this.playerState.lastHoverTime = hoverTime;
                },

                _getTimeFromEvent(e) {
                    const rect = e.currentTarget.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const width = rect.width;
                    const percent = Math.max(0, Math.min(1, x / width));
                    return percent * (this.playerState.duration || 0);
                },

                handleProgressClick(e) {
                    // Only handle click if NOT from a drag operation
                    if (this._wasDragging) {
                        this._wasDragging = false;
                        return;
                    }
                    
                    const time = this._getTimeFromEvent(e);
                    const video = this.$refs.mainVideo;
                    if (!video) return;

                    console.log('Seek click:', time.toFixed(1) + 's', 
                        '| duration:', video.duration.toFixed(1) + 's',
                        '| seekable:', video.seekable.length > 0 ? video.seekable.end(0).toFixed(1) : 'none');
                    
                    if (!isNaN(time) && Number.isFinite(time)) {
                        this.playerState.isSeeking = true;
                        this.playerState.currentTime = time;
                        video.currentTime = time;
                        
                        if(video.paused) {
                            video.play().catch(err => console.log('Play prevented', err));
                        }
                        this.playerState.playing = true;
                        this.playerState.hoverTime = null;
                    }
                },

                handleProgressMouseDown(e) {
                    e.preventDefault();
                    this.playerState.isSeeking = true;
                    this._wasDragging = false;
                    this._progressBarEl = e.currentTarget;
                    
                    // Attach global mousemove/mouseup so dragging works even outside the bar
                    const onMouseMove = (ev) => {
                        this._wasDragging = true;
                        const rect = this._progressBarEl.getBoundingClientRect();
                        const x = ev.clientX - rect.left;
                        const width = rect.width;
                        const percent = Math.max(0, Math.min(1, x / width));
                        const time = percent * (this.playerState.duration || 0);
                        
                        this.playerState.currentTime = time;
                        this.playerState.hoverX = Math.max(0, Math.min(width, x));
                        this.playerState.hoverTime = time;
                    };
                    
                    const onMouseUp = (ev) => {
                        document.removeEventListener('mousemove', onMouseMove);
                        document.removeEventListener('mouseup', onMouseUp);
                        
                        const rect = this._progressBarEl.getBoundingClientRect();
                        const x = ev.clientX - rect.left;
                        const width = rect.width;
                        const percent = Math.max(0, Math.min(1, x / width));
                        const time = percent * (this.playerState.duration || 0);
                        
                        console.log('Progress bar seek to:', time, 'seconds');
                        
                        const video = this.$refs.mainVideo;
                        if (video && !isNaN(time) && Number.isFinite(time)) {
                            this.playerState.currentTime = time;
                            video.currentTime = time;
                            // isSeeking stays true until @seeked fires on the video element
                        }
                        
                        this.playerState.hoverTime = null;
                    };
                    
                    document.addEventListener('mousemove', onMouseMove);
                    document.addEventListener('mouseup', onMouseUp);
                },

                handleProgressMove(e) {
                    // Show hover tooltip (only when NOT dragging, drag uses global listeners)
                    if (!this.playerState.isSeeking) {
                        this.handleProgressHover(e);
                    }
                },

                handleProgressMouseUp(e) {
                    // Handled by global listener from mousedown
                },

                handleProgressLeave(e) {
                    if (!this.playerState.isSeeking) {
                        this.playerState.hoverTime = null;
                    }
                },

                seekToPreview(e) {
                    let time = this.playerState.hoverTime;
                    
                    if (e) {
                        const rect = e.currentTarget.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const width = rect.width;
                        const percent = Math.max(0, Math.min(1, x / width));
                        time = percent * (this.playerState.duration || 0);
                    }

                    console.log('Seeking to time:', time);
                    
                    if (time !== null && time !== undefined && !isNaN(time)) {
                        const video = this.$refs.mainVideo;
                        if(video) {
                            this.playerState.isSeeking = true;
                            this.playerState.currentTime = time;
                            video.currentTime = time;
                            if(video.paused) {
                                video.play().catch(e => console.log('Play prevented', e));
                            }
                            this.playerState.playing = true;
                            this.playerState.hoverTime = null;
                        }
                    }
                },

                submitReport(issue, detail = '') {
                    if (!this.currentSong) return;
                    
                    const songTitle = this.currentSong.judul || 'Unknown Song';
                    let reportDetail = issue;
                    if(issue === 'Lainnya' && detail) {
                        reportDetail = `Lainnya: ${detail}`;
                    }

                    const reportNote = `Lapor Lagu: ${songTitle} - ${reportDetail}`;
                    
                    const newRequest = {
                        id: Date.now(),
                        type: 'Masalah Audio', 
                        icon: 'alert-triangle',
                        note: reportNote,
                        status: 'Baru',
                        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                    };

                    this.helpHistory.unshift(newRequest);
                    this.showToastNotification(`Laporan dikirim: ${issue}`);
                    
                    // Reset Modal
                    this.showReportModal = false;
                    this.reportType = null;
                    this.reportNote = '';
                },

                toggleFullscreen() {
                    const container = this.$refs.playerContainer;
                    if (!document.fullscreenElement) {
                        if(container.requestFullscreen) container.requestFullscreen();
                        else if(container.webkitRequestFullscreen) container.webkitRequestFullscreen();
                    } else {
                        if(document.exitFullscreen) document.exitFullscreen();
                    }
                },

                addToOrder(item) {
                    const existing = this.fnbOrder.find(i => i.name === item.name);
                    if(existing) {
                        existing.qty++;
                    } else {
                        this.fnbOrder.push({ ...item, qty: 1 });
                    }
                    this.showToastNotification(`${item.name} ditambahkan`);
                },

                showToastNotification(message) {
                    this.toast.message = message;
                    this.toast.show = true;
                    setTimeout(() => this.toast.show = false, 3000);
                },

                updateQty(target, change) {
                    let index = -1;
                    if (typeof target === 'number') {
                        index = target;
                    } else {
                        // Find by object matching
                        index = this.fnbOrder.findIndex(i => i.name === target.name && (i.variant?.name === target.variant?.name));
                    }

                    if (index === -1 || !this.fnbOrder[index]) return;

                    const item = this.fnbOrder[index];
                    item.qty += change;
                    
                    if(item.qty <= 0) {
                        this.fnbOrder.splice(index, 1);
                    }
                },

                get totalOrderPrice() {
                    return this.fnbOrder.reduce((acc, item) => acc + (item.price * item.qty), 0);
                },
                
                get totalActiveOrders() {
                    return this.orderHistory.length; // Count total submitted order batches
                },

                // Product Detail Methods
                openDetail(item) {
                     this.detailItem = item;
                     this.detailQuantity = 1;
                     // Set initial variant if available
                     if(this.detailItem.variations && this.detailItem.variations.length > 0) {
                         this.detailVariant = this.detailItem.variations[0];
                     } else {
                         this.detailVariant = null;
                     }
                     this.isDetailOpen = true;
                },
                
                closeDetail() {
                    this.isDetailOpen = false;
                    setTimeout(() => {
                        this.detailItem = null;
                        this.detailQuantity = 1;
                        this.detailVariant = null;
                    }, 300);
                },

                // Audio Logic (Tone.js)
                audioInitialized: false,

                async initAudio() {
                    const video = this.$refs.mainVideo;
                    if (!video) return;

                    // 1. Resume Context (User Interaction)
                    if (Tone.context.state !== 'running') {
                        try { await Tone.context.resume(); } catch(e) { }
                    }

                    // 2. Resolve Source
                    let source = video._toneMediaSource;
                    let ctx = Tone.context.rawContext;

                    if (source) {
                         ctx = source.context;
                         if (ctx.state === 'suspended') {
                            try { ctx.resume(); } catch(e) {}
                         }
                    } else {
                        try {
                            source = ctx.createMediaElementSource(video);
                            video._toneMediaSource = source; 
                        } catch (e) {
                            console.warn('Source creation error', e); 
                            return; 
                        }
                    }

                    // 3. Sync Tone Context
                    // Important: Set Tone context to match the source's native context
                    if (Tone.context.rawContext !== ctx) {
                         Tone.setContext(ctx);
                    }

                    toneSource = source;

                    // 4. Create PitchShift Effect
                    // Dispose old if context differs
                    if (pitchShiftNode && pitchShiftNode.context.rawContext !== ctx) {
                        pitchShiftNode.dispose();
                        pitchShiftNode = null;
                    }

                    if (!pitchShiftNode) {
                         pitchShiftNode = new Tone.PitchShift({
                            pitch: 0,
                            windowSize: 0.1,
                            delayTime: 0,
                            feedback: 0
                        }).toDestination();
                    }

                    // 5. Connect Source -> Effect (Pure Native Connection)
                    try {
                        try { source.disconnect(); } catch(e) {}

                        // Driller function to find the first Native AudioNode
                        const getNativeNode = (obj, depth=0) => {
                             if (!obj || depth > 4) return null;
                             // Check for native AudioNode traits
                             if (obj instanceof AudioNode || (obj.context && obj.connect && obj.numberOfInputs > 0 && !obj.dispose)) {
                                 return obj;
                             }
                             // Drill down common Tone properties
                             return getNativeNode(obj.input, depth+1) || getNativeNode(obj._gainNode, depth+1);
                        };

                        const validNativeInput = getNativeNode(pitchShiftNode);

                        if (validNativeInput) {
                             source.connect(validNativeInput);
                             console.log('Connected Native Source to:', validNativeInput);
                             this.audioInitialized = true;
                             this.updateAudioParams();
                        } else {
                             // Absolute fallback: direct to speakers
                             console.warn('Could not find native input node. Bypassing effects.');
                             source.connect(ctx.destination);
                        }

                    } catch (e) {
                        console.error('Audio Connect Error:', e);
                        try { source.connect(ctx.destination); } catch(x) {}
                    }
                },

                updateAudioParams() {
                    // 1. Speed (Native Video Feature)
                    const video = this.$refs.mainVideo;
                    if (video) {
                        if (video.playbackRate !== this.playerState.playbackRate) {
                            video.playbackRate = this.playerState.playbackRate;
                        }
                        if (video.preservesPitch === undefined || video.preservesPitch === false) {
                            video.preservesPitch = true; 
                        }
                    }

                    // 2. Pitch/Transpose (Tone.js)
                    if (pitchShiftNode) {
                        const totalShift = this.playerState.transpose + this.playerState.pitch;
                         if (pitchShiftNode.pitch !== totalShift) {
                            pitchShiftNode.pitch = totalShift;
                        }
                    }
                },

                get currentDetailPrice() {
                    if(!this.detailItem) return 0;
                    if(this.detailVariant) return this.detailVariant.price;
                    return this.detailItem.price;
                },

                addToOrder(item) {
                    this.openDetail(item);
                },

                confirmDetailOrder() {
                    const price = this.currentDetailPrice;
                    let selectedName = this.detailItem.name;
                    // Append variant name if selected
                    if (this.detailVariant) {
                        selectedName += ` (${this.detailVariant.name})`;
                    }

                    // Check if item already exists in cart with same variant
                    const existingItem = this.fnbOrder.find(i => 
                        i.name === selectedName && 
                        (i.variant?.name === this.detailVariant?.name)
                    );

                    if (existingItem) {
                        existingItem.qty += this.detailQuantity; // Update quantity
                    } else {
                        this.fnbOrder.push({
                            ...this.detailItem,
                            name: selectedName,
                            originalName: this.detailItem.name,
                            variant: this.detailVariant,
                            price: price,
                            qty: this.detailQuantity
                        });
                    }
                    
                    this.closeDetail();
                    this.sidebarTab = 'cart';
                    this.showToastNotification('Item berhasil ditambahkan');
                },

                submitOrder() {
                    if(this.fnbOrder.length === 0) return;
                    this.showConfirmation = true;
                },
                
                confirmSubmit() {
                    if (!this.paymentMethod) return;
                    
                    if (this.paymentMethod === 'open_billing') {
                        // Open Billing: langsung masuk ke tagihan kamar
                        this.finalizeOrder('Diproses • Open Billing');
                        this.showToastNotification('Pesanan ditambahkan ke tagihan kamar.');
                    } else if (this.paymentMethod === 'payment_gateway') {
                        // Payment Gateway: buka modal pilih gateway
                        this.showConfirmation = true; // keep it for reference
                        this.showPaymentGateway = true;
                    }
                },

                processPaymentGateway() {
                    if (!this.selectedGateway) return;
                    
                    this.paymentProcessing = true;
                    
                    // Simulate payment processing (prototype)
                    setTimeout(() => {
                        this.paymentProcessing = false;
                        this.paymentSuccess = true;
                        
                        // Auto finalize after showing success
                        setTimeout(() => {
                            const gatewayLabel = {
                                'qris': 'QRIS',
                                'bank_transfer': 'Virtual Account',
                                'ewallet': 'E-Wallet'
                            };
                            this.finalizeOrder('Lunas • ' + (gatewayLabel[this.selectedGateway] || 'Online'));
                            this.showToastNotification('Pembayaran berhasil! Pesanan sedang diproses.');
                            
                            // Reset payment states
                            this.showPaymentGateway = false;
                            this.paymentSuccess = false;
                            this.selectedGateway = null;
                        }, 2000);
                    }, 2500);
                },

                finalizeOrder(status) {
                    const gatewayLabel = {
                        'qris': 'QRIS',
                        'bank_transfer': 'Virtual Account', 
                        'ewallet': 'E-Wallet'
                    };
                    
                    const batchOrder = {
                        id: Math.floor(1000 + Math.random() * 9000),
                        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                        status: status,
                        paymentMethod: this.paymentMethod === 'open_billing' ? 'Open Billing' : (gatewayLabel[this.selectedGateway] || 'Online'),
                        items: [...this.fnbOrder],
                        total: this.totalOrderPrice
                    };
                    
                    this.orderHistory.unshift(batchOrder);
                    this.fnbOrder = [];
                    this.showConfirmation = false;
                    this.sidebarTab = 'history';
                    this.paymentMethod = null;
                }
            };
            });

            Alpine.data('netflixCarousel', (items) => ({
                items: items,
                scrollProgress: 0,
                showLeftArrow: false,
                showRightArrow: true,
                hoveredIndex: null,
                hoverTimeout: null,
                
                // Pagination Config
                itemsPerPage: 5,
                itemWidth: 228, // 220px + 8px gap
                activePage: 0,
                
                get totalPages() {
                    return Math.ceil(this.items.length / this.itemsPerPage);
                },

                init() {
                    const container = this.$refs.container;
                    if(!container) return;
                    
                    container.addEventListener('scroll', () => {
                        this.updateState();
                    });
                    
                    // Initial check
                    setTimeout(() => this.updateState(), 100);
                },
                
                updateState() {
                    const el = this.$refs.container;
                    if(!el) return;
                    
                    // Update Arrows
                    this.showLeftArrow = el.scrollLeft > 20;
                    this.showRightArrow = el.scrollLeft < (el.scrollWidth - el.clientWidth - 20);
                    
                    // Update Active Page
                    // We add a small buffer (0.5 of a page) to switch highlight when halfway through
                    const scrollPageWidth = this.itemWidth * this.itemsPerPage;
                    this.activePage = Math.round(el.scrollLeft / scrollPageWidth);
                },
                
                scroll(direction) {
                    const el = this.$refs.container;
                    const scrollAmount = this.itemWidth * this.itemsPerPage;
                    const target = el.scrollLeft + (direction * scrollAmount);
                    el.scrollTo({ left: target, behavior: 'smooth' });
                },
                
                handleMouseEnter(index) {
                    if (this.hoverTimeout) clearTimeout(this.hoverTimeout);
                    this.hoverTimeout = setTimeout(() => {
                        this.hoveredIndex = index;
                        this.$nextTick(() => {
                            const video = this.$refs[`video_${index}`];
                            if (video) {
                                video.currentTime = 0;
                                video.play().catch(e => console.log('Autoplay prevented', e));
                            }
                        });
                    }, 500); // 500ms delay before expanding/playing
                },
                
                handleMouseLeave() {
                    if (this.hoverTimeout) clearTimeout(this.hoverTimeout);
                    
                    if (this.hoveredIndex !== null) {
                       const video = this.$refs[`video_${this.hoveredIndex}`];
                       if (video) {
                           video.pause();
                           video.currentTime = 0;
                       }
                    }
                    this.hoveredIndex = null;
                }
            }));
        });
    </script>
    @stack('scripts')
</body>
</html>
