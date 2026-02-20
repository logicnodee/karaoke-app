@extends('layouts.app')

@section('title', 'Frequently Asked Questions - SGRT Karaoke')

@section('content')

{{-- ═══════════════════════════════════════════════
    HERO SECTION — Matching the reference design
═══════════════════════════════════════════════ --}}
<section class="relative h-[50vh] md:h-[60vh] flex items-center justify-center overflow-hidden">
    {{-- Background Image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/pages/faq/cd297f_a4355409f9124ffa81bedac9ac5e368d~mv2.jpg') }}"
             alt="SGRT Karaoke FAQ"
             class="w-full h-full object-cover">
        {{-- Dark overlay for readability --}}
        <div class="absolute inset-0 bg-black/50"></div>
        {{-- Bottom gradient fade into content --}}
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black to-transparent"></div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 text-center px-4"
         x-data
         x-intersect="$el.classList.add('opacity-100', 'translate-y-0')"
         class="opacity-0 translate-y-6 transition-all duration-1000 ease-out">
        <h1 class="text-5xl md:text-7xl lg:text-[5rem] text-[#D0B75B] tracking-tight leading-none drop-shadow-lg"
            style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400;">
            FAQ
        </h1>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    FAQ CONTENT SECTION
═══════════════════════════════════════════════ --}}
<section class="bg-black text-white py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center mb-12 md:mb-16"
             x-data
             x-intersect="$el.classList.add('opacity-100', 'translate-y-0')"
             class="opacity-0 translate-y-6 transition-all duration-700 ease-out">
            <h2 class="text-3xl md:text-4xl lg:text-[2.5rem] text-[#F2E3BC] tracking-wide"
                style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400;">
                Frequently asked questions
            </h2>
        </div>

        {{-- FAQ Accordion --}}
        <div class="space-y-0" x-data="{ active: 0 }">
            @foreach($faqs as $index => $faq)
            <div x-data
                 x-intersect.once="$el.classList.add('opacity-100', 'translate-y-0')"
                 class="opacity-0 translate-y-4 transition-all duration-500 ease-out border-b border-white/10"
                 style="transition-delay: {{ min($index * 50, 400) }}ms;">

                {{-- Question Button --}}
                <button @click="active = active === {{ $index }} ? null : {{ $index }}"
                        class="w-full text-left py-6 md:py-7 flex justify-between items-center group focus:outline-none transition-colors duration-300"
                        :class="{ 'text-[#D0B75B]': active === {{ $index }} }">

                    <span class="text-base md:text-lg font-medium pr-8 transition-colors duration-300 group-hover:text-[#D0B75B]"
                          :class="active === {{ $index }} ? 'text-[#D0B75B]' : 'text-white'"
                          style="font-family: 'madefor-display';">
                        {{ $faq['question'] }}
                    </span>

                    {{-- Chevron Icon --}}
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center transition-transform duration-300"
                         :class="{ 'rotate-180': active === {{ $index }} }">
                        <svg class="w-5 h-5 transition-colors duration-300"
                             :class="active === {{ $index }} ? 'text-[#D0B75B]' : 'text-gray-500'"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Answer Panel --}}
                <div x-show="active === {{ $index }}"
                     x-collapse
                     x-cloak>
                    <div class="pb-6 md:pb-8 pr-12 text-gray-300 text-sm md:text-base leading-relaxed"
                         style="font-family: 'madefor-display';">
                        {!! $faq['answer'] !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Contact CTA --}}
        <div class="text-center mt-16 md:mt-20"
             x-data
             x-intersect="$el.classList.add('opacity-100', 'translate-y-0')"
             class="opacity-0 translate-y-6 transition-all duration-700 ease-out">
            <p class="text-gray-400 text-lg mb-6" style="font-family: 'madefor-display';">
                Still have questions?
            </p>
            <a href="{{ route('contact') }}"
               class="group relative inline-flex items-center justify-center px-10 py-4 overflow-hidden rounded-full transition-all duration-500">
                {{-- Button border gradient --}}
                <span class="absolute inset-0 rounded-full bg-gradient-to-r from-[#D0B75B] via-[#f0d48a] to-[#D0B75B] p-[2px]">
                    <span class="absolute inset-[2px] rounded-full bg-black/80 backdrop-blur-sm"></span>
                </span>
                <span class="relative z-10 text-[#D0B75B] font-bold text-sm tracking-[0.2em] uppercase group-hover:text-white transition-colors duration-300"
                      style="font-family: 'madefor-display';">
                    CONTACT US
                </span>
            </a>
        </div>

    </div>
</section>

@endsection
