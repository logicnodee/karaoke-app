<div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden mt-8">
    <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex items-center justify-between">
        <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest" style="font-family: 'Inter';">Log Aktivitas Terbaru</h2>
    </div>
    <div class="divide-y divide-white/5">
        @foreach($logAktivitas as $log)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-white/[0.01] transition-colors">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-xs text-gray-400" style="font-family: 'Inter';">
                        <span class="text-white font-bold">{{ $log['nama'] }}</span> {{ $log['aksi'] }}
                    </p>
                    <p class="text-[9px] text-gray-600 mt-0.5 font-bold uppercase tracking-wider" style="font-family: 'Inter';">{{ $log['waktu'] }}</p>
                </div>
            </div>
            <span class="text-[9px] text-gray-600 font-mono bg-white/5 px-2 py-1 rounded">{{ $log['ip'] }}</span>
        </div>
        @endforeach
    </div>
    <div class="px-6 py-3 bg-black/40 text-center border-t border-white/5">
        <button class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-[#D0B75B] transition-colors" style="font-family: 'Inter';">Lihat Semua Aktivitas</button>
    </div>
</div>
