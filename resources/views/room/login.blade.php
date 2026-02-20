<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Access Login - SGRT Karaoke</title>
    <link rel="icon" type="image/png" href="/assets/img/global/krls_logo.png">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #000; color: #fff; }

        /* Mock Receipt */
        .mock-receipt {
            background: #faf8f0;
            color: #222;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.5;
            padding: 14px 12px;
            border-radius: 4px;
            position: relative;
            max-width: 200px;
            margin: 0 auto;
            box-shadow: 0 6px 24px rgba(0,0,0,0.3);
        }
        .mock-receipt::before {
            content: '';
            position: absolute;
            top: -6px; left: 0; right: 0; height: 6px;
            background: linear-gradient(135deg, transparent 33.33%, #faf8f0 33.33%, #faf8f0 66.67%, transparent 66.67%),
                        linear-gradient(225deg, transparent 33.33%, #faf8f0 33.33%, #faf8f0 66.67%, transparent 66.67%);
            background-size: 10px 6px;
        }
        .mock-receipt::after {
            content: '';
            position: absolute;
            bottom: -6px; left: 0; right: 0; height: 6px;
            background: linear-gradient(45deg, transparent 33.33%, #faf8f0 33.33%, #faf8f0 66.67%, transparent 66.67%),
                        linear-gradient(315deg, transparent 33.33%, #faf8f0 33.33%, #faf8f0 66.67%, transparent 66.67%);
            background-size: 10px 6px;
        }
        .receipt-divider {
            border-top: 1px dashed #bbb;
            margin: 5px 0;
        }
        .key-highlight {
            background: linear-gradient(90deg, #D0B75B22, #D0B75B44, #D0B75B22);
            border: 2px solid #D0B75B;
            border-radius: 5px;
            padding: 4px 6px;
            animation: pulseGlow 2s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 6px rgba(208, 183, 91, 0.3); }
            50% { box-shadow: 0 0 16px rgba(208, 183, 91, 0.6); }
        }
        @keyframes arrowBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .arrow-bounce {
            animation: arrowBounce 1s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-black h-screen w-full flex items-center justify-center overflow-hidden relative">
    


    {{-- Main Container: Side by Side --}}
    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10 lg:gap-16 px-6 max-w-4xl w-full">

        {{-- LEFT: Login Form --}}
        <div class="w-full max-w-sm flex-shrink-0">
            {{-- Header --}}
            <div class="text-center mb-8">
                <img src="/assets/img/global/krls_logo.png" alt="SGRT Logo" class="w-16 h-16 mx-auto mb-3 object-contain">
                <h1 class="text-3xl font-black tracking-tighter text-white mb-1">SGRT <span class="text-[#D0B75B]">KARAOKE ROOM</span></h1>
                <p class="text-gray-500 text-xs font-medium tracking-wide">MASUKKAN KODE AKSES RUANGAN</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('room.login.post') }}" method="POST" class="space-y-4">
                @csrf
                
                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-500 px-3 py-2 rounded-xl text-center text-xs font-bold animate-pulse">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('expired'))
                    <div class="bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 px-3 py-2 rounded-xl text-center text-xs font-bold">
                        Sesi Telah Berakhir. Terima Kasih.
                    </div>
                @endif

                <div class="relative group">
                    <input type="text" name="key" 
                           class="w-full bg-[#111] border border-white/10 text-center text-2xl font-mono text-white tracking-[0.2em] font-bold py-5 rounded-2xl focus:outline-none focus:border-[#D0B75B] focus:ring-1 focus:ring-[#D0B75B] transition-all uppercase placeholder-gray-700"
                           placeholder="K-XXX" required autocomplete="off" autofocus>
                </div>

                <button type="submit" 
                        class="w-full bg-white text-black font-black text-base py-3.5 rounded-xl hover:bg-gray-200 hover:scale-[1.02] transition-all transform duration-200 shadow-xl shadow-white/5">
                    MULAI SESI
                </button>
            </form>
        </div>

        {{-- DIVIDER (desktop only) --}}
        <div class="hidden lg:block w-px h-80 bg-gradient-to-b from-transparent via-white/10 to-transparent flex-shrink-0"></div>

        {{-- RIGHT: Tutorial --}}
        <div class="w-full max-w-xs flex-shrink-0">
            <div class="bg-white/[0.03] backdrop-blur-sm border border-white/10 rounded-2xl p-5 space-y-4">
                
                {{-- Title --}}
                <div class="text-center">
                    <h2 class="text-sm font-bold text-white mb-0.5">Cara Menemukan Kode Akses</h2>
                    <p class="text-gray-500 text-[10px]">Lihat kode di nota/struk pembayaran Anda</p>
                </div>

                {{-- Steps (compact) --}}
                <div class="space-y-2.5">
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#D0B75B]/20 border border-[#D0B75B]/40 flex items-center justify-center mt-0.5">
                            <span class="text-[#D0B75B] text-[9px] font-bold">1</span>
                        </div>
                        <div>
                            <p class="text-white text-xs font-semibold leading-tight">Ambil nota pembayaran</p>
                            <p class="text-gray-500 text-[10px] leading-tight">Terima nota dari kasir setelah pembayaran.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#D0B75B]/20 border border-[#D0B75B]/40 flex items-center justify-center mt-0.5">
                            <span class="text-[#D0B75B] text-[9px] font-bold">2</span>
                        </div>
                        <div>
                            <p class="text-white text-xs font-semibold leading-tight">Cari "Kode Akses Room"</p>
                            <p class="text-gray-500 text-[10px] leading-tight">Di bagian bawah nota, format <span class="text-[#D0B75B] font-mono font-bold">K-XXX</span></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-[#D0B75B]/20 border border-[#D0B75B]/40 flex items-center justify-center mt-0.5">
                            <span class="text-[#D0B75B] text-[9px] font-bold">3</span>
                        </div>
                        <div>
                            <p class="text-white text-xs font-semibold leading-tight">Masukkan kode & mulai</p>
                            <p class="text-gray-500 text-[10px] leading-tight">Ketik kode lalu tekan <strong class="text-white">MULAI SESI</strong></p>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-white/5"></div>

                {{-- Mock Receipt --}}
                <div>
                    <p class="text-gray-400 text-[10px] font-semibold text-center mb-3 uppercase tracking-wider">Contoh Nota</p>
                    
                    <div class="mock-receipt">
                        <div class="text-center font-bold text-xs">SGRT KARAOKE</div>
                        <div class="text-center text-[8px] text-gray-500">Jl. Contoh Alamat No. 123</div>
                        
                        <div class="receipt-divider"></div>

                        <div class="flex justify-between text-[8px]">
                            <span>No. Trx</span>
                            <span>TRX-00412</span>
                        </div>
                        <div class="flex justify-between text-[8px]">
                            <span>Tanggal</span>
                            <span>16/02/2026</span>
                        </div>

                        <div class="receipt-divider"></div>

                        <div class="text-[8px] space-y-0.5">
                            <div class="flex justify-between">
                                <span>Room VIP (2 Jam)</span>
                                <span>150.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Snack Package</span>
                                <span>50.000</span>
                            </div>
                        </div>

                        <div class="receipt-divider"></div>

                        <div class="flex justify-between font-bold text-[9px]">
                            <span>TOTAL</span>
                            <span>Rp 200.000</span>
                        </div>

                        <div class="receipt-divider"></div>

                        {{-- KEY Highlighted --}}
                        <div class="key-highlight mt-1">
                            <div class="text-center text-[8px] text-gray-600 font-bold">KODE AKSES ROOM</div>
                            <div class="text-center text-base font-black text-[#7a6521] tracking-wider">K-412</div>
                        </div>

                        <div class="receipt-divider mt-2"></div>
                        <div class="text-center text-[7px] text-gray-400">
                            Terima kasih! Selamat bersenang-senang 🎤
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-3 gap-1.5">
                        <span class="arrow-bounce text-[#D0B75B] text-sm">👆</span>
                        <span class="text-[#D0B75B] text-[10px] font-bold">Masukkan kode ini di kolom input</span>
                    </div>
                </div>

                {{-- Tip --}}
                <div class="bg-[#D0B75B]/10 border border-[#D0B75B]/20 rounded-lg p-2 flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#D0B75B] flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[#D0B75B]/80 text-[10px] leading-relaxed">
                        <strong class="text-[#D0B75B]">Tips:</strong> Kode unik per sesi. Kehilangan nota? Hubungi kasir.
                    </p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

