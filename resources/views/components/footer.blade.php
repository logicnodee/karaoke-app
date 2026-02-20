<footer class="bg-black text-white pt-20 pb-0 border-t border-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
            
            <!-- LEFT COLUMN: Contact Information -->
            <div class="space-y-6">
                <h3 class="text-xl md:text-2xl font-customHeader tracking-wider mb-6" style="font-family: 'wf_a339f259334e44ff9a746f30d';">CONTACT INFORMATION</h3>
                
                <div class="space-y-4 font-light text-sm md:text-base">
                    <!-- Email -->
                    <div class="flex items-center space-x-3">
                        <i data-lucide="mail" class="w-5 h-5 text-white"></i>
                        <a href="mailto:karangploso@gmail.com" class="hover:text-[#D0B75B] transition-colors">karangploso@gmail.com</a>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-center space-x-3">
                        <i data-lucide="phone" class="w-5 h-5 text-white"></i>
                        <span>+6285783839562</span>
                    </div>

                    <!-- Address -->
                    <div class="mt-4">
                        <p>Malang, Jawa Timur</p>
                        <p>Indonesia</p>
                    </div>

                    <!-- Hours -->
                    <div class="mt-6 space-y-1">
                        <p>Mon-Thur: 6pm-2am</p>
                        <p>Fri: 6pm-3am</p>
                        <p>Sat-Sun: 2pm-3am</p>
                        <p>Special Event Hours: 12pm - 6pm</p>
                    </div>
                </div>
            </div>

            <!-- CENTER COLUMN: Logo & Socials -->
            <div class="flex flex-col items-center justify-center space-y-6 mt-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ asset('assets/img/global/krls_logo.png') }}" alt="KRLS Karaoke" class="h-12 md:h-16 w-auto transition-transform duration-300 hover:scale-105">
                </a>

                <!-- Social Icons -->
                <div class="flex space-x-6">
                    <a href="#" class="bg-white text-black p-2 rounded-full hover:bg-[#D0B75B] transition-colors duration-300">
                        <i data-lucide="facebook" class="w-6 h-6 fill-current"></i>
                    </a>
                    <a href="#" class="bg-white text-black p-2 rounded-full hover:bg-[#D0B75B] transition-colors duration-300">
                        <i data-lucide="instagram" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="bg-white text-black p-2 rounded-full hover:bg-[#D0B75B] transition-colors duration-300">
                        <!-- TikTok Icon replacement (since lucide might generally support standard ones, ensuring fallback or specific svg if needed) -->
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                            <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: Quick Links -->
            <div class="flex flex-col md:items-end">
                <h3 class="text-xl md:text-2xl font-customHeader tracking-wider mb-6 text-right" style="font-family: 'wf_a339f259334e44ff9a746f30d';">QUICK LINKS</h3>
                <nav class="flex flex-col space-y-3 text-right text-sm md:text-base font-light underline decoration-transparent hover:decoration-[#D0B75B] transition-all">
                    <a href="{{ route('home') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">HOME</a>
                    <a href="{{ route('about') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">ABOUT US</a>
                    <a href="{{ route('rooms') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">ROOMS & PACKAGES</a>
                    <a href="{{ route('food-beverages') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">FOOD & BEVERAGE</a>
                    <a href="{{ route('faq') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">OUR FAQ</a>
                    <a href="{{ route('contact') }}" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">CONTACT US</a>
                    <a href="#" class="hover:text-[#D0B75B] transition-colors uppercase border-b border-transparent hover:border-[#D0B75B] w-fit self-end">PRIVACY POLICY</a>
                </nav>
            </div>

        </div>
    </div>

    <!-- Copyright Bar -->
    <div class="bg-[#D0B75B] py-4 text-center">
        <p class="text-black text-sm font-medium">
            &copy; 2024 KRLS Karaoke | Website Designed by ayathusaini
        </p>
    </div>
</footer>
