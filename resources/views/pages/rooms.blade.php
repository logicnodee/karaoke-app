@extends('layouts.app')

@section('title', 'Rooms & Packages - SGRT Karaoke')

@section('content')

{{-- ═══════════════════════════════════════════════
    HERO SECTION — Full-screen with background image
═══════════════════════════════════════════════ --}}
<section class="relative h-[85vh] flex items-center justify-center overflow-hidden">
    {{-- Background Image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/pages/rooms/cd297f_b6d74574ad904eba8b84c6ed46f402a0~mv2.jpg') }}"
             alt="SGRT Karaoke Rooms"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center justify-center">
        <h1 class="text-5xl md:text-7xl lg:text-[5rem] text-[#D0B75B] tracking-tight leading-none mb-6 drop-shadow-lg"
            style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400;">
            BOOK A RESERVATION
        </h1>
        <p class="text-lg md:text-xl text-white font-bold tracking-wide max-w-2xl mx-auto mb-10 drop-shadow-md"
           style="font-family: 'madefor-display'; letter-spacing: 0.03em;">
            Private watch parties, karaoke, and decor options can be explored here.
        </p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    OUR ROOMS — Section Title
═══════════════════════════════════════════════ --}}
<section class="bg-black py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl md:text-[35px] text-[#D0B75B] text-center tracking-tight"
            style="font-family: 'wf_a339f259334e44ff9a746f30d';">
            OUR ROOMS
        </h2>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    ROOM CARDS — Alternating layout (image/text)
═══════════════════════════════════════════════ --}}
@foreach($rooms as $index => $room)
<section class="bg-black py-4"
         x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0 items-center"
             :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
             style="transition: all 0.8s ease-out;">

            {{-- Image Column --}}
            <div class="{{ $room['image_position'] === 'left' ? 'md:order-1' : 'md:order-2' }}">
                <div class="relative overflow-hidden aspect-square max-w-[456px] mx-auto group">
                    <img src="{{ asset($room['image']) }}"
                         alt="SGRT Karaoke {{ $room['name'] }} Room"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    {{-- Subtle hover overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
            </div>

            {{-- Text Column --}}
            <div class="{{ $room['image_position'] === 'left' ? 'md:order-2' : 'md:order-1' }} flex flex-col items-center justify-center text-center px-6 py-10 md:py-0">
                {{-- Suite Type Badge --}}
                <h4 class="text-[#D0B75B] font-bold text-sm tracking-wider uppercase mb-2"
                    style="font-family: 'madefor-display'; line-height: 1.7em;">
                    {{ $room['suite_type'] }}
                </h4>

                {{-- Room Name --}}
                <h3 class="text-[#D0B75B] text-4xl md:text-[35px] mb-3"
                    style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                    {{ $room['name'] }}
                </h3>

                {{-- Divider --}}
                <div class="w-24 h-[1px] bg-[#D0B75B]/40 mb-6"></div>

                {{-- Description --}}
                <p class="text-white text-base leading-[1.7em] max-w-md mb-2 uppercase"
                   style="font-family: 'madefor-display';">
                    {!! $room['description'] !!}
                </p>

                {{-- Capacity --}}
                <p class="text-white text-base mt-2 mb-6 uppercase"
                   style="font-family: 'madefor-display';">
                    This room holds up to <span class="text-[#D0B75B] font-semibold">{{ $room['capacity'] }}</span>
                </p>

                {{-- Book Now Button --}}
                @php
                    $waMessage = "Hi, I'd like to make a reservation for the *" . $room['suite_type'] . " " . $room['name'] . " — " . $room['theme'] . "* (up to " . $room['capacity'] . ").\n\nCould you please help me with the availability and booking details? Thank you!";
                    $waLink = "https://wa.me/17704628888?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="group/btn relative inline-flex items-center justify-center px-8 py-3 overflow-hidden rounded-full transition-all duration-500 border border-[#D0B75B]/60 hover:border-[#D0B75B] hover:bg-[#D0B75B]/10">
                    <span class="relative z-10 text-[#D0B75B] font-bold text-sm tracking-[0.15em] uppercase group-hover/btn:text-white transition-colors duration-300"
                          style="font-family: 'madefor-display';">
                        BOOK NOW
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>
@endforeach


{{-- ═══════════════════════════════════════════════
    OUR RATES — Pricing Cards
═══════════════════════════════════════════════ --}}
<section class="bg-black py-20"
         x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4">
        {{-- Section Title --}}
        <h2 class="text-4xl md:text-[35px] text-[#D0B75B] text-center tracking-tight mb-16"
            style="font-family: 'wf_a339f259334e44ff9a746f30d';">
            OUR RATES
        </h2>

        {{-- Rates Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8"
             :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
             style="transition: all 0.8s ease-out;">
            @foreach($rates as $key => $rate)
            <div class="relative group">
                {{-- Card --}}
                <div class="bg-zinc-900/50 border border-[#D0B75B]/20 rounded-2xl p-8 text-center h-full transition-all duration-500 group-hover:border-[#D0B75B]/60 group-hover:shadow-[0_0_40px_rgba(208,183,91,0.1)]">
                    {{-- Title --}}
                    <h3 class="text-white text-2xl font-bold mb-3 tracking-wide"
                        style="font-family: 'madefor-display';">
                        {{ $rate['title'] }}
                    </h3>

                    {{-- Divider --}}
                    <div class="w-16 h-[1px] bg-[#D0B75B]/40 mx-auto mb-5"></div>

                    {{-- Description --}}
                    <p class="text-gray-400 text-base mb-8 leading-relaxed"
                       style="font-family: 'madefor-display';">
                        {{ $rate['description'] }}
                    </p>

                    {{-- Prices --}}
                    <div class="space-y-4">
                        @foreach($rate['prices'] as $price)
                        <div class="flex items-center justify-start text-left gap-3">
                            {{-- Microphone Icon --}}
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#AE8F1F]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5z"/>
                                    <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                                </svg>
                            </div>
                            <span class="text-white text-base" style="font-family: 'madefor-display';">
                                {{ $price }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Book Now Button --}}
        <div class="text-center mt-12">
            <a href="https://wa.me/17704628888?text={{ urlencode("Hi, I'd like to make a reservation at SGRT Karaoke. Could you please help me with the available rooms and booking details? Thank you!") }}" target="_blank" rel="noopener noreferrer"
               class="group relative inline-flex items-center justify-center px-10 py-4 overflow-hidden rounded-full transition-all duration-500">
                <span class="absolute inset-0 rounded-full bg-gradient-to-r from-[#D0B75B] via-[#f0d48a] to-[#D0B75B] p-[2px]">
                    <span class="absolute inset-[2px] rounded-full bg-black/80 backdrop-blur-sm"></span>
                </span>
                <span class="relative z-10 text-[#D0B75B] font-bold text-lg tracking-[0.2em] uppercase group-hover:text-white transition-colors duration-300"
                      style="font-family: 'madefor-display';">
                    BOOK NOW
                </span>
            </a>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    WHY CHOOSE US — Two-column with image
═══════════════════════════════════════════════ --}}
<section class="bg-black py-20"
         x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0 items-stretch"
             :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
             style="transition: all 0.8s ease-out;">
            {{-- Image --}}
            <div class="relative h-[500px] md:h-auto overflow-hidden">
                <img src="{{ asset('assets/img/pages/rooms/cd297f_00d407e007cb4fc7908a3697ab76d592~mv2.jpg') }}"
                     alt="SGRT Karaoke Bar"
                     class="w-full h-full object-cover">
            </div>

            {{-- Text --}}
            <div class="flex flex-col items-center justify-center text-center p-10 md:p-16 bg-zinc-950">
                <h2 class="text-4xl md:text-[35px] text-[#D0B75B] tracking-tight mb-8"
                    style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                    WHY CHOOSE US?
                </h2>
                <p class="text-white text-lg leading-[1.7em] max-w-lg"
                   style="font-family: 'madefor-display';">
                    At SGRT Karaoke, we provide the ideal backdrop for a range of celebrations. We aim to make each experience special. Whether you're marking a marriage anniversary, proposing to your special someone, hosting an annual corporate event, throwing a birthday party, reuniting with family, or enjoying a night out with friends, our luxurious private rooms and opulent setting create the perfect atmosphere for unforgettable moments. Join us and make your celebration an extraordinary experience at SGRT Karaoke.
                </p>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    CUSTOMER REVIEWS — Testimonials Slider
═══════════════════════════════════════════════ --}}
<section class="bg-black py-20"
         x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-5xl mx-auto px-4 text-center"
         :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
         style="transition: all 0.8s ease-out;">
        {{-- Section Title --}}
        <h2 class="text-4xl md:text-[35px] text-[#D0B75B] tracking-tight mb-12"
            style="font-family: 'wf_a339f259334e44ff9a746f30d';">
            CUSTOMER REVIEWS
        </h2>

        @foreach($reviews as $review)
        <div class="mb-8">
            {{-- Stars --}}
            <div class="flex items-center justify-center gap-1 mb-8">
                @for($i = 0; $i < 5; $i++)
                <svg class="w-6 h-6 text-[#D0B75B]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                @endfor
            </div>

            {{-- Review Text --}}
            <blockquote class="text-[#D0B75B] text-lg md:text-xl font-bold leading-relaxed mb-6 max-w-3xl mx-auto"
                        style="font-family: 'madefor-display';">
                {{ $review['text'] }} <span class="text-white">— {{ $review['author'] }}</span>
            </blockquote>

            {{-- Rating Breakdown --}}
            <p class="text-[#D0B75B] font-bold text-base"
               style="font-family: 'madefor-display';">
                {{ $review['rating'] }}
            </p>
        </div>
        @endforeach
    </div>
</section>


{{-- ═══════════════════════════════════════════════
    CTA SECTION — Full-width with background
═══════════════════════════════════════════════ --}}
<section class="relative py-0 overflow-hidden">
    <div class="relative h-[600px] md:h-[650px] flex items-center justify-center">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('assets/img/pages/rooms/cd297f_6750e8b8cde94ed5b2f299bc6e25826b~mv2.jpg') }}"
                 alt="SGRT Karaoke Luxury Experience"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto"
             x-data="{ shown: false }" x-intersect.once="shown = true"
             :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
             style="transition: all 0.8s ease-out;">
            <h2 class="text-4xl md:text-5xl text-[#D0B75B] tracking-tight mb-6"
                style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                LUXURY PRIVATE KARAOKE EXPERIENCE
            </h2>
            <p class="text-white text-lg md:text-xl font-bold tracking-wide max-w-2xl mx-auto mb-10 drop-shadow-md"
               style="font-family: 'madefor-display'; letter-spacing: 0.03em;">
                When looking for that wow factor, we are here to provide it. From birthdays, to bachelorettes, and girls night out, we have everything you need to make your night unforgettable.
            </p>

            {{-- BOOK NOW Button --}}
            <a href="https://wa.me/17704628888?text={{ urlencode("Hi, I'd like to book a luxury private karaoke experience at SGRT Karaoke. Could you please help me with the availability and booking details? Thank you!") }}" target="_blank" rel="noopener noreferrer"
               class="group relative inline-flex items-center justify-center px-10 py-4 overflow-hidden rounded-full transition-all duration-500">
                <span class="absolute inset-0 rounded-full bg-gradient-to-r from-[#D0B75B] via-[#f0d48a] to-[#D0B75B] p-[2px]">
                    <span class="absolute inset-[2px] rounded-full bg-black/80 backdrop-blur-sm"></span>
                </span>
                <span class="relative z-10 text-[#D0B75B] font-bold text-lg tracking-[0.2em] uppercase group-hover:text-white transition-colors duration-300"
                      style="font-family: 'madefor-display';">
                    BOOK NOW
                </span>
                <span class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-gradient-to-r from-[#D0B75B]/20 via-[#f0d48a]/30 to-[#D0B75B]/20"></span>
            </a>
        </div>
    </div>
</section>

@endsection
