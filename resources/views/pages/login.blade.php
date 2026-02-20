@extends('layouts.app')

@section('title', 'Login - SGRT Karaoke')

@section('content')
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-black pt-24">


    {{-- Login Card --}}
    <div class="relative z-10 w-full max-w-sm mx-4"
         x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 100)"
         x-show="show"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100">

        <div class="bg-zinc-900/80 backdrop-blur-xl border border-white/10 rounded-2xl p-6 md:p-8 shadow-2xl shadow-black/50">
            
            {{-- Logo / Brand --}}
            <div class="text-center mb-6">
                <h1 class="text-xl text-[#D0B75B] tracking-wide"
                    style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400;">
                    Welcome Back
                </h1>
                <p class="text-gray-500 text-xs mt-1" style="font-family: 'madefor-display';">
                    Sign in to your account
                </p>
            </div>

            {{-- Error Message --}}
            @if($errors->has('login'))
            <div class="mb-4 bg-red-500/10 border border-red-500/30 rounded-xl px-3 py-2.5 flex items-center gap-2"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition.opacity.duration.300ms>
                <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-400 text-sm" style="font-family: 'madefor-display';">{{ $errors->first('login') }}</p>
                <button @click="show = false" class="ml-auto text-red-400/60 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4"
                  x-data="{ loading: false }"
                  @submit="loading = true">
                @csrf

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-xs text-gray-400 mb-2 tracking-wide uppercase"
                           style="font-family: 'madefor-display';">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" id="email" name="email" value="{{ old('email') }}"
                               class="w-full bg-black/50 text-white border border-white/10 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/30 transition-all placeholder-gray-600"
                               style="font-family: 'madefor-display';"
                               placeholder="Enter your email"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Password Field --}}
                <div x-data="{ showPass: false }">
                    <label for="password" class="block text-xs text-gray-400 mb-2 tracking-wide uppercase"
                           style="font-family: 'madefor-display';">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password"
                               class="w-full bg-black/50 text-white border border-white/10 rounded-xl pl-11 pr-11 py-2.5 text-sm focus:outline-none focus:border-[#D0B75B]/50 focus:ring-1 focus:ring-[#D0B75B]/30 transition-all placeholder-gray-600"
                               style="font-family: 'madefor-display';"
                               placeholder="Enter your password">
                        {{-- Toggle Password Visibility --}}
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition-colors">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-1">
                    <button type="submit"
                            class="w-full relative overflow-hidden rounded-xl py-2.5 text-sm font-bold tracking-[0.2em] uppercase transition-all duration-500 group"
                            :class="loading ? 'opacity-70 pointer-events-none' : ''"
                            style="font-family: 'madefor-display';">
                        {{-- Gradient background --}}
                        <span class="absolute inset-0 bg-gradient-to-r from-[#D0B75B] via-[#f0d48a] to-[#D0B75B] group-hover:from-[#f0d48a] group-hover:via-[#D0B75B] group-hover:to-[#f0d48a] transition-all duration-500"></span>
                        {{-- Shimmer Effect --}}
                        <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                        <span class="relative z-10 text-black flex items-center justify-center gap-2">
                            <template x-if="!loading">
                                <span>Sign In</span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing in...
                                </span>
                            </template>
                        </span>
                    </button>
                </div>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-4">
                <div class="flex-1 h-px bg-white/10"></div>
                <span class="text-gray-600 text-[10px] uppercase tracking-wider" style="font-family: 'madefor-display';">Demo Accounts</span>
                <div class="flex-1 h-px bg-white/10"></div>
            </div>

            {{-- Quick Login Hints --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between bg-black/30 border border-white/5 rounded-lg px-3 py-2 group hover:border-[#D0B75B]/20 transition-colors cursor-default">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-red-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white text-[11px] font-medium" style="font-family: 'madefor-display';">Admin</p>
                            <p class="text-gray-600 text-[9px]" style="font-family: 'madefor-display';">admin / admin</p>
                        </div>
                    </div>
                    <span class="text-[9px] text-gray-600 bg-white/5 px-1.5 py-0.5 rounded-full" style="font-family: 'madefor-display';">Full Access</span>
                </div>

                <div class="flex items-center justify-between bg-black/30 border border-white/5 rounded-lg px-3 py-2 group hover:border-[#D0B75B]/20 transition-colors cursor-default">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-green-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.1-5.1m0 0L11.42 4.97m-5.1 5.1H20"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white text-[11px] font-medium" style="font-family: 'madefor-display';">Operator</p>
                            <p class="text-gray-600 text-[9px]" style="font-family: 'madefor-display';">operator / operator</p>
                        </div>
                    </div>
                    <span class="text-[9px] text-gray-600 bg-white/5 px-1.5 py-0.5 rounded-full" style="font-family: 'madefor-display';">Operations</span>
                </div>
            </div>
                <div class="flex items-center justify-between bg-black/30 border border-white/5 rounded-lg px-3 py-2 group hover:border-[#D0B75B]/20 transition-colors cursor-default">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-purple-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white text-[11px] font-medium" style="font-family: 'madefor-display';">Kasir</p>
                            <p class="text-gray-600 text-[9px]" style="font-family: 'madefor-display';">kasir / kasir</p>
                        </div>
                    </div>
                    <span class="text-[9px] text-gray-600 bg-white/5 px-1.5 py-0.5 rounded-full" style="font-family: 'madefor-display';">POS / Billing</span>
                </div>
            </div>

    </div>
</section>
@endsection
