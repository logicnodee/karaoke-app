@extends('layouts.app')

@section('title', 'KRLS Karaoke - Premium Private Luxury Karaoke')

@section('content')
<!-- Hero Section -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/pages/home/cd297f_0e8b3223f5a54660ba69e9779fa66f32~mv2.jpg') }}" alt="KRLS Karaoke Background" class="w-full h-full object-cover">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/80"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full flex flex-col items-center justify-center gap-10 md:gap-14 mt-8">
        <!-- Main Title -->
        <h1 class="w-full text-center text-[12vw] md:text-[8rem] lg:text-[10rem] text-white font-customHeader tracking-tighter leading-none drop-shadow-2xl select-none" 
            style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400; text-shadow: 0 4px 30px rgba(0,0,0,0.5);">
            <span class="uppercase block">KRLS KARAOKE</span>
        </h1>
        
        <!-- Premium Book Now Button -->
        <div class="flex justify-center z-20">
            <a href="{{ route('rooms') }}" class="hero-book-btn group relative">
                <!-- Animated gradient border -->
                <span class="hero-book-btn-border"></span>
                <!-- Glass inner -->
                <span class="hero-book-btn-inner">
                    <span class="hero-book-btn-text">BOOK NOW</span>
                    <svg class="hero-book-btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </span>
                <!-- Shimmer sweep -->
                <span class="hero-book-btn-shimmer"></span>
            </a>
        </div>
    </div>
</section>

<style>
    /* Premium Book Now Button */
    .hero-book-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #d4a843, #f0d48a, #c8953e, #e8c86e, #d4a843);
        background-size: 300% 300%;
        animation: hero-gradient-spin 4s ease-in-out infinite;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        cursor: pointer;
        text-decoration: none;
        overflow: hidden;
    }
    .hero-book-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 40px rgba(212, 168, 67, 0.5), 0 0 80px rgba(240, 212, 138, 0.3);
    }

    /* Animated gradient border */
    .hero-book-btn-border {
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        background: linear-gradient(135deg, #d4a843, #f0d48a, #c8953e, #e8c86e, #d4a843);
        background-size: 300% 300%;
        animation: hero-gradient-spin 4s ease-in-out infinite;
        z-index: 0;
    }

    /* Glass inner background */
    .hero-book-btn-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 48px;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(212, 168, 67, 0.15);
        transition: background 0.3s ease;
    }
    .hero-book-btn:hover .hero-book-btn-inner {
        background: rgba(0, 0, 0, 0.4);
    }

    /* Button text */
    .hero-book-btn-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.875rem;
        font-weight: 300;
        letter-spacing: 0.3em;
        color: #fff;
        transition: letter-spacing 0.4s ease;
    }
    .hero-book-btn:hover .hero-book-btn-text {
        letter-spacing: 0.4em;
    }

    /* Arrow icon */
    .hero-book-btn-arrow {
        color: rgba(255, 255, 255, 0.6);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
    }
    .hero-book-btn:hover .hero-book-btn-arrow {
        transform: translateX(4px);
        color: #f0d48a;
    }

    /* Shimmer sweep effect */
    .hero-book-btn-shimmer {
        position: absolute;
        inset: 0;
        z-index: 2;
        border-radius: 9999px;
        background: linear-gradient(
            105deg,
            transparent 35%,
            rgba(255, 255, 255, 0.15) 45%,
            rgba(255, 255, 255, 0.25) 50%,
            rgba(255, 255, 255, 0.15) 55%,
            transparent 65%
        );
        background-size: 250% 100%;
        animation: hero-shimmer-sweep 3s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes hero-gradient-spin {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes hero-shimmer-sweep {
        0% { background-position: 200% center; }
        100% { background-position: -200% center; }
    }
</style>
@endsection
