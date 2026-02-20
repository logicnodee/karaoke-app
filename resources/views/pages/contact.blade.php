@extends('layouts.app')

@section('title', 'Contact Us - SGRT Karaoke')

@section('content')
{{-- Hero Section --}}
<div class="relative w-full overflow-hidden" style="height: 45vh;">
    <img src="{{ asset('assets/img/pages/contact/cd297f_f12169c4de2f421dbea23f49f15a7bb6~mv2.jpg') }}" 
         alt="Contact Us - Luxury Karaoke in Atlanta GA" 
         class="absolute inset-0 w-full h-full object-cover" style="opacity: 0.4;">
    {{-- Full black overlay for navbar visibility --}}
    <div class="absolute inset-0 bg-black/50"></div>
    {{-- Gradient overlay at bottom --}}
    <div class="absolute bottom-0 left-0 right-0 h-60 bg-gradient-to-t from-black to-transparent"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-5xl md:text-7xl tracking-widest uppercase text-center px-4" 
            style="font-family: 'wf_a339f259334e44ff9a746f30d';">CONTACT US</h1>
    </div>
</div>

{{-- Form Section --}}
<div class="bg-black text-white py-10 relative" style="font-family: 'madefor-display', sans-serif;">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Title --}}
        <div class="text-center mb-8">
            <h2 class="text-xl md:text-3xl tracking-wide uppercase" 
                style="font-family: 'wf_a339f259334e44ff9a746f30d'; color: #F2E3BC;">HAVE A QUESTION? SEND US A MESSAGE</h2>
        </div>

        {{-- Contact Form — sends to WhatsApp --}}
        <form class="space-y-4"
              x-data="{
                  firstName: '',
                  lastName: '',
                  email: '',
                  phone: '',
                  inquiry: '',
                  comments: '',
                  errors: {},
                  inquiryLabels: {
                      general: 'General Inquiry',
                      booking: 'Booking Request',
                      event: 'Private Event',
                      feedback: 'Feedback',
                      other: 'Other'
                  },
                  submitToWhatsApp() {
                      this.errors = {};
                      if (!this.firstName.trim()) {
                          this.errors.firstName = 'Please enter your first name.';
                          this.$refs.firstNameInput.focus();
                          return;
                      }
                      const name = (this.firstName.trim() + ' ' + this.lastName.trim()).trim();
                      let msg = 'Hi, my name is *' + name + '*.';
                      if (this.email.trim()) msg += '\n📧 Email: ' + this.email.trim();
                      if (this.phone.trim()) msg += '\n📱 Phone: ' + this.phone.trim();
                      if (this.inquiry) msg += '\n📋 Inquiry: *' + this.inquiryLabels[this.inquiry] + '*';
                      if (this.comments.trim()) msg += '\n💬 Message: ' + this.comments.trim();
                      msg += '\n\nI would appreciate your help. Thank you!';
                      const url = 'https://wa.me/17704628888?text=' + encodeURIComponent(msg);
                      window.open(url, '_blank');
                  }
              }"
              @submit.prevent="submitToWhatsApp()">
            
            {{-- First Name & Last Name --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-xs text-white mb-1 tracking-wide" 
                           style="font-family: 'madefor-display', sans-serif;">First name</label>
                    <input type="text" id="first_name" name="first_name" x-model="firstName" x-ref="firstNameInput"
                           @input="errors.firstName = ''"
                           class="w-full bg-white text-black border-2 rounded-full px-4 py-2 text-sm focus:outline-none transition-colors"
                           :class="errors.firstName ? 'border-red-500 ring-1 ring-red-500' : 'border-white/60 focus:border-yellow-500'"
                           style="font-family: 'madefor-display', sans-serif;">
                    {{-- Inline error message --}}
                    <p x-show="errors.firstName" x-transition.opacity.duration.300ms
                       class="text-red-400 text-xs mt-1.5 flex items-center gap-1"
                       style="font-family: 'madefor-display', sans-serif;">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="errors.firstName"></span>
                    </p>
                </div>
                <div>
                    <label for="last_name" class="block text-xs text-white mb-1 tracking-wide" 
                           style="font-family: 'madefor-display', sans-serif;">Last name</label>
                    <input type="text" id="last_name" name="last_name" x-model="lastName"
                           class="w-full bg-white text-black border-2 border-white/60 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-yellow-500 transition-colors"
                           style="font-family: 'madefor-display', sans-serif;">
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs text-white mb-1 tracking-wide" 
                       style="font-family: 'madefor-display', sans-serif;">Email</label>
                <input type="email" id="email" name="email" x-model="email"
                       class="w-full bg-white text-black border-2 border-white/60 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-yellow-500 transition-colors"
                       style="font-family: 'madefor-display', sans-serif;">
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-xs text-white mb-1 tracking-wide" 
                       style="font-family: 'madefor-display', sans-serif;">Phone</label>
                <input type="text" id="phone" name="phone" x-model="phone"
                       class="w-full bg-white text-black border-2 border-white/60 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-yellow-500 transition-colors"
                       style="font-family: 'madefor-display', sans-serif;">
            </div>

            {{-- Choose your inquiry --}}
            <div>
                <label for="inquiry" class="block text-xs text-white mb-1 tracking-wide" 
                       style="font-family: 'madefor-display', sans-serif;">Choose your inquiry</label>
                <div class="relative">
                    <select id="inquiry" name="inquiry" x-model="inquiry"
                            class="w-full bg-white text-black border-2 border-white/60 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-yellow-500 transition-colors appearance-none cursor-pointer"
                            style="font-family: 'madefor-display', sans-serif;">
                        <option value="" disabled selected></option>
                        <option value="general">General Inquiry</option>
                        <option value="booking">Booking Request</option>
                        <option value="event">Private Event</option>
                        <option value="feedback">Feedback</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-600">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M18.255 8.182L18.962 8.889 12.595 15.254 12.598 15.256 11.891 15.963 11.888 15.961 11.887 15.962 11.18 15.255 11.181 15.253 4.818 8.889 5.525 8.182 11.888 14.546 18.255 8.182Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Additional Comments --}}
            <div>
                <label for="comments" class="block text-xs text-white mb-1 tracking-wide" 
                       style="font-family: 'madefor-display', sans-serif;">Additional Comments</label>
                <textarea id="comments" name="comments" rows="3" x-model="comments"
                          class="w-full bg-white text-black border-2 border-white/60 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-500 transition-colors resize-none"
                          style="font-family: 'madefor-display', sans-serif;"></textarea>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        class="w-full text-white font-normal py-2.5 rounded-full transition-all duration-300 uppercase tracking-widest text-sm hover:opacity-80"
                        style="background-color: #D0B75B; font-family: 'madefor-display', sans-serif;">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
