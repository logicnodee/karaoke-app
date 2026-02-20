@extends('layouts.app')

@section('title', 'About Us - KRLS Karaoke')

@section('content')
<!-- Hero Section (Previously Home Hero) -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/pages/home/cd297f_304c9ad34e1149038bea2ac6dc0418eb~mv2.jpg') }}" alt="KRLS Karaoke Background" class="w-full h-full object-cover">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center px-4 max-w-6xl mx-auto flex flex-col items-center justify-center h-full pt-20">
        <h1 class="text-5xl md:text-7xl lg:text-[5.5rem] text-white font-customHeader tracking-tight leading-none mb-8 drop-shadow-lg" 
            style="font-family: 'wf_a339f259334e44ff9a746f30d'; font-weight: 400;">
            <span class="italic">WELCOME TO</span> <span class="uppercase">KRLS KARAOKE</span>
        </h1>

        <div class="w-full max-w-3xl mx-auto bg-black/0 backdrop-blur-none p-6 rounded-xl">
             <p class="text-base md:text-xl text-white font-customBody font-light leading-relaxed drop-shadow-md text-center" style="font-family: 'madefor-display';">
                KRLS Karaoke is Atlanta’s premier destination for private luxury karaoke. With beautifully designed suites, elevated food and cocktail menus, curated bottle service, and immersive sound systems, we offer the ultimate nightlife experience—perfect for celebrations, corporate gatherings, or spontaneous fun.
            </p>
        </div>
    </div>
</section>

<!-- Experience Luxury Section -->
<section class="relative py-24 bg-black overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Left Image -->
            <div x-data="{ shown: false }" x-intersect.once="shown = true" 
                 :class="{ 'opacity-100 translate-y-0': shown, 'opacity-0 translate-y-10': !shown }"
                 class="relative h-[643px] w-full overflow-hidden transition-all duration-1000 ease-out">
                <img src="{{ asset('assets/img/pages/home/cd297f_1dcefb2f2ba54b23861c5cb0100ed665~mv2.jpg') }}" alt="Luxury Karaoke Experience" class="w-full h-full object-cover">
            </div>

            <!-- Right Text -->
            <div x-data="{ shown: false }" x-intersect.once="shown = true" 
                 :class="{ 'opacity-100 translate-x-0': shown, 'opacity-0 translate-x-10': !shown }"
                 class="text-center md:text-left transition-all duration-1000 ease-out delay-300">
                <h2 class="text-4xl md:text-5xl text-yellow-500 font-customHeader mb-6" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                    EXPERIENCE LUXURY<br>AT KRLS KARAOKE
                </h2>
                <p class="text-white text-lg font-customBody font-light leading-relaxed mb-6" style="font-family: 'madefor-display';">
                    Step beyond the ordinary and into a space where every detail is designed for impact. From plush seating and atmospheric lighting to top-tier audio and attentive service, each of our 11 private suites offers a curated environment for connection, celebration, and escape.
                </p>
                <p class="text-white text-lg font-customBody font-light leading-relaxed" style="font-family: 'madefor-display';">
                    Whether you're hosting a birthday, planning a team event, or indulging in a spontaneous night out, KRLS transforms your moments into standout memories.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- What We Offer Section -->
<section class="py-24 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-6xl text-[#D0B75B] font-customHeader mb-4 tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">WHAT WE OFFER</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 px-4">
            <!-- Item 1: Luxury Karaoke -->
            <div class="relative group cursor-pointer">
                <!-- Gold Border Offset Background -->
                <div class="absolute top-4 left-4 w-full h-full border-2 border-[#D0B75B] z-0 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"></div>
                
                <!-- Image Container -->
                <div class="relative h-[500px] w-full overflow-hidden z-10">
                    <img src="{{ asset('assets/img/pages/home/999 new.jpg') }}" alt="Luxury Karaoke" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 brightness-75 group-hover:brightness-50">
                    
                    <!-- Centered Text Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <h3 class="text-4xl md:text-5xl text-[#D0B75B] font-customHeader text-center leading-tight drop-shadow-lg" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                            LUXURY<br>KARAOKE
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Item 2: Decor Packages -->
            <div class="relative group cursor-pointer mt-8 md:mt-0">
                <!-- Gold Border Offset Background -->
                <div class="absolute top-4 left-4 w-full h-full border-2 border-[#D0B75B] z-0 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"></div>
                
                <!-- Image Container -->
                <div class="relative h-[500px] w-full overflow-hidden z-10">
                    <img src="{{ asset('assets/img/pages/home/guest1.jpg') }}" alt="Decor Packages" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 brightness-75 group-hover:brightness-50">
                    
                    <!-- Centered Text Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <h3 class="text-4xl md:text-5xl text-[#D0B75B] font-customHeader text-center leading-tight drop-shadow-lg" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                            DECOR<br>PACKAGES
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Item 3: Private Watch Parties -->
            <div class="relative group cursor-pointer mt-8 md:mt-0">
                <!-- Gold Border Offset Background -->
                <div class="absolute top-4 left-4 w-full h-full border-2 border-[#D0B75B] z-0 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"></div>
                
                <!-- Image Container -->
                <div class="relative h-[500px] w-full overflow-hidden z-10">
                    <img src="{{ asset('assets/img/pages/home/mics.jpg') }}" alt="Private Watch Parties" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 brightness-75 group-hover:brightness-50">
                    
                    <!-- Centered Text Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <h3 class="text-4xl md:text-5xl text-[#D0B75B] font-customHeader text-center leading-tight drop-shadow-lg" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                            PRIVATE WATCH<br>PARTIES
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-20">
            <a href="{{ route('rooms') }}" class="inline-block px-10 py-4 border border-[#D0B75B] text-[#D0B75B] font-bold tracking-widest hover:bg-[#D0B75B] hover:text-black transition-all duration-300 rounded-none text-sm uppercase">
                LEARN MORE
            </a>
        </div>
    </div>
</section>

<!-- Customer Reviews Section (Slider) -->
<section class="py-24 bg-black text-white relative overflow-hidden" x-data="{ activeSlide: 0, slides: [
    { quote: 'Listen to me! This karaoke experience is top notch seriously. All the employees are a whole vibe themselves. Our server with glasses and long hair was so attentive and reliable. I normally dont do karaoke at all...however, KRLS Karaoke makes you want to sing your heart in front of your friends. All the rooms are V.I.P. designed with an exclusive vibe. Forget reserving a V.I.P. section at the club...COME HERE! Period (finger claps)', author: '-Kelli Rae (Google Reviews)', rating: 5 },
    { quote: 'Absolutely amazing experience! The sound system is incredible and the private rooms are so spacious. The food was delicious too!', author: '-Sarah J. (Google Reviews)', rating: 5 },
    { quote: 'Best place for a birthday party. The staff went above and beyond to make our night special. Highly recommend the VIP packages.', author: '-Michael T. (Google Reviews)', rating: 5 }
] }">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl md:text-5xl text-white font-customHeader mb-16" style="font-family: 'wf_a339f259334e44ff9a746f30d';">CUSTOMER REVIEWS</h2>
        
        <div class="relative min-h-[300px]">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform translate-x-10"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-10"
                     class="absolute top-0 left-0 w-full"
                >
                    <div class="flex justify-center mb-6 space-x-2">
                        <template x-for="i in 5">
                            <svg class="w-6 h-6 text-yellow-500 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </template>
                    </div>
                    <p class="text-lg md:text-xl font-light italic mb-8 font-customBody leading-relaxed" x-text="slide.quote"></p>
                    <p class="text-yellow-500 font-bold tracking-wide" x-text="slide.author"></p>
                </div>
            </template>
        </div>

        <div class="flex justify-center space-x-2 mt-8">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index" 
                        class="w-3 h-3 rounded-full transition-all duration-300"
                        :class="activeSlide === index ? 'bg-yellow-500 w-6' : 'bg-gray-600 hover:bg-gray-400'">
                </button>
            </template>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="py-24 bg-black border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
             <!-- Left Image -->
             <div class="relative h-[600px] w-full overflow-hidden order-last md:order-first">
                <img src="{{ asset('assets/img/pages/home/cd297f_1ed9445c69944d57ac68567718aad102~mv2.jpg') }}" alt="Bar and Karaoke" class="w-full h-full object-cover">
            </div>

            <!-- Right Text -->
            <div class="text-center md:text-left">
                <h2 class="text-4xl md:text-5xl text-white font-customHeader mb-6" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
                    ABOUT US
                </h2>
                <p class="text-white text-lg font-customBody font-light leading-relaxed" style="font-family: 'madefor-display';">
                    At KRLS Karaoke, we provide the ideal backdrop for a range of celebrations. We aim to make each experience special. Whether you're marking a marriage anniversary, proposing to your special someone, hosting an annual corporate event, throwing a birthday party, reuniting with family, or enjoying a night out with friends, our luxurious private rooms and opulent setting create the perfect atmosphere for unforgettable moments. Join us and make your celebration an extraordinary experience at KRLS Karaoke.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Luxury Private Karaoke Experience Section (CTA) -->
<section class="relative py-32 flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/pages/home/cd297f_0e8b3223f5a54660ba69e9779fa66f32~mv2.jpg') }}" alt="Luxury Private Karaoke Experience" class="w-full h-full object-cover">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/60"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-customHeader mb-8 tracking-wide drop-shadow-lg" style="font-family: 'wf_a339f259334e44ff9a746f30d';">
            LUXURY PRIVATE KARAOKE EXPERIENCE
        </h2>
        
        <p class="text-white text-lg md:text-xl font-customBody font-light leading-relaxed mb-10 drop-shadow-md" style="font-family: 'madefor-display';">
            Whether you're celebrating a birthday, toasting a bride-to-be, or curating an unforgettable night with friends, KRLS delivers the kind of energy, elegance, and experience that leaves a lasting impression. When you're searching for that "wow" factor — this is where you'll find it.
        </p>

        <a href="{{ route('rooms') }}" class="inline-block px-10 py-4 bg-[#D0B75B] text-black font-bold tracking-widest hover:bg-[#c0a74b] transition-all duration-300 rounded-full text-sm shadow-lg transform hover:scale-105">
            LEARN MORE
        </a>
    </div>
</section>
@endsection
