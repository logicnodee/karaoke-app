<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'What are the differences between the rooms?',
                'answer' => '<p>Choosing the right suite at MICS Karaoke depends on the size of your party, the vibe you\'re going for, and the kind of celebration you\'re planning. Here\'s a quick guide:</p>
                <ul class="list-disc pl-5 mt-3 space-y-2">
                    <li><strong>Small Suites (K1, K2, K3):</strong> Perfect for intimate gatherings of 2–6 guests. Great for a casual night out or a cozy birthday with your closest friends.</li>
                    <li><strong>Medium Suites (777, 888, 999, V1, V2):</strong> Ideal for larger groups of 8–15. These suites are perfect for milestone birthdays, corporate events, or a full-out girls\' night.</li>
                    <li><strong>MICS VIP Suite:</strong> Our crown jewel, this luxury suite fits up from 16–40 guests and is ideal for major celebrations. It includes elevated design, a private bathroom, premium audio, and exclusive add-on options.</li>
                </ul>
                <p class="mt-3">Still not sure? Our team is happy to help recommend the best option based on your guest count, event type, and preferred time.</p>
                <p class="mt-2 text-[#D0B75B] italic">Tip: If you\'re booking for a birthday or other special event, consider reserving early and asking about our decor add-ons and food/beverage packages!</p>',
            ],
            [
                'question' => 'Is a deposit required?',
                'answer' => '<p>Yes — a deposit is required to secure your suite and goes toward your final bill. The amount depends on the day and suite type:</p>
                <ul class="list-disc pl-5 mt-3 space-y-2">
                    <li><strong>Monday–Thursday & Sunday:</strong> $25 deposit for all suites (unless booking a decor package then it\'s $100)</li>
                    <li><strong>Friday & Saturday:</strong> $100 minimum deposit for all suites</li>
                    <li><strong>MICS VIP Suite:</strong> Always requires a $200 deposit</li>
                </ul>
                <p class="mt-3">Your deposit goes toward your final bill and helps us prepare for your visit. All deposits are <strong>non-refundable</strong> and must be paid within 24 hours of booking to hold your reservation.</p>',
            ],
            [
                'question' => 'What is a minimum menu spend?',
                'answer' => '<p>A minimum menu spend is the required amount your group must spend on food and beverages during your suite reservation. It ensures that every guest contributes to the experience and allows us to provide high-quality service and offerings.</p>
                <p class="mt-3 font-semibold">Weekdays (Monday–Thursday):</p>
                <ul class="list-disc pl-5 mt-1 space-y-1">
                    <li>Each room must order at least 2 items (any combination of food, drinks, hookah or bottles).</li>
                    <li>This applies to all suites except the MICS VIP Suite, which always requires a $500 minimum spend.</li>
                </ul>
                <p class="mt-3 font-semibold">Friday & Saturday:</p>
                <ul class="list-disc pl-5 mt-1 space-y-1">
                    <li><strong>Standard Suites (2–6 guests):</strong> $75 minimum spend</li>
                    <li><strong>Medium/Party Suites (7–15 guests):</strong> $200 minimum spend</li>
                    <li><strong>MICS VIP Suite:</strong> $500 minimum spend</li>
                    <li>These minimums average to about $30–40 per person.</li>
                </ul>
                <p class="mt-3 text-gray-400 text-sm">Note: The minimum must be met with food and beverage purchases made inside your suite. Drinks purchased at the bar do not count toward your suite minimum. Tax and service fees are also not included in the minimum. If your party doesn\'t meet the spend requirement, the remaining balance will be added to your final bill.</p>',
            ],
            [
                'question' => 'When is karaoke half off?',
                'answer' => '<p>½ Off Karaoke is <strong>Monday, Tuesday, and Wednesday</strong>, in addition to <strong>Thursdays from 6 PM - 9 PM</strong>. Please note that ½ Off Karaoke does not apply to holidays or VIP rooms.</p>',
            ],
            [
                'question' => 'Is there an age limit?',
                'answer' => '<p>We are <strong>21+ on Fridays and Saturdays</strong> (after 7PM). However, <strong>all ages are welcome Sunday-Thursday</strong>.</p>',
            ],
            [
                'question' => 'What types of songs do you have available?',
                'answer' => '<p>We have over <strong>72,000 songs</strong> and can be updated with your songs of choice if you fill out our form ahead of time.</p>',
            ],
            [
                'question' => 'Can we bring outside food and beverages?',
                'answer' => '<p><strong>No outside food or drinks</strong>, except birthday cakes/cupcakes with our cake fee or as part of a décor package.</p>',
            ],
            [
                'question' => 'Is the VIP room half off during half off karaoke days?',
                'answer' => '<p>The VIP room is <strong>not included in any promotions or discounts</strong>.</p>',
            ],
            [
                'question' => 'What do I do if I need to cancel/reschedule a reservation?',
                'answer' => '<p>If you need to cancel/reschedule you can send us a message on the smiley text platform, or give us a call on site at <a href="tel:7704628888" class="text-[#D0B75B] hover:underline font-semibold">(770) 462-8888</a>. Rescheduling must be done at least <strong>24 hours before your reservation time</strong> in order to avoid forfeiting your deposit.</p>',
            ],
            [
                'question' => 'Do I pay a second deposit when rescheduling?',
                'answer' => '<p>If you are rescheduling within the 24 hour time window that is allotted, you will <strong>not</strong> need to pay a secondary deposit. If you reschedule outside of that window, you will forfeit your deposit.</p>',
            ],
            [
                'question' => 'When are special event hours?',
                'answer' => '<p>Special event hours are <strong>12pm-6pm each day</strong>.</p>',
            ],
            [
                'question' => 'How do I book a special event?',
                'answer' => '<p>To book a special event please email <a href="mailto:micsktv8888@gmail.com" class="text-[#D0B75B] hover:underline font-semibold">micsktv8888@gmail.com</a>. You will receive a response within 72 hours.</p>',
            ],
            [
                'question' => 'Do you have vegetarian options?',
                'answer' => '<p><strong>Yes.</strong></p>',
            ],
            [
                'question' => 'Is there a public bar?',
                'answer' => '<p>Yes, the bar is located in the lobby and <strong>open every day</strong>!</p>',
            ],
            [
                'question' => 'Can we split checks?',
                'answer' => '<p>Yes, just let us know! We can split the bill evenly <strong>up to four ways</strong> so your group can focus on the fun, not the math.</p>',
            ],
            [
                'question' => 'Is there an additional guest fee?',
                'answer' => '<p>Yes. A fee of <strong>$50</strong> will incur for every two additional guests. Please note that we cannot accommodate additional seating when guest count is exceeded.</p>',
            ],
        ];

        return view('pages.faq', compact('faqs'));
    }
}
