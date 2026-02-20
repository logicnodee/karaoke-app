<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomsController extends Controller
{
    public function index()
    {
        $rooms = [
            [
                'id' => 'v1',
                'suite_type' => 'PARTY SUITE',
                'name' => 'V1',
                'theme' => 'BIG GAME SUITE',
                'description' => "If you are on the hunt for a unique room, you've found it with our <span class=\"text-[#D0B75B]\">Big Game Suite</span>. With an imprint of Bigfoot on the ceiling, deer head accessories, and a fireplace you will feel like you are away at the cabins. This room also has its own private restroom.",
                'capacity' => '15 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-1 (36).png',
                'image_position' => 'left',
            ],
            [
                'id' => 'v2',
                'suite_type' => 'PARTY SUITE',
                'name' => 'V2',
                'theme' => 'TECH SUITE',
                'description' => "Step into another dimension in our <span class=\"text-[#D0B75B]\">Tech Suite</span>. With a starry ceiling, 360 projector screen with interchangeable backdrops, and illuminated tables, you will feel like you are out of this world.",
                'capacity' => '15 GUESTS',
                'image' => 'assets/img/pages/rooms/V2.png',
                'image_position' => 'right',
            ],
            [
                'id' => 'v3',
                'suite_type' => 'STANDARD SUITE',
                'name' => 'V3',
                'theme' => 'BIG CITY',
                'description' => "Looking for something moody? Step into our <span class=\"text-[#D0B75B]\">Big City</span> room. With a royal purple couch, incorporated art, intimate seating and moody lights you're in for a smooth night.",
                'capacity' => '6 GUESTS',
                'image' => 'assets/img/pages/rooms/V3.png',
                'image_position' => 'left',
            ],
            [
                'id' => 'kt',
                'suite_type' => 'STANDARD SUITE',
                'name' => 'KT',
                'theme' => 'FANTASY ROOM',
                'description' => "Enhance your karaoke experience with our <span class=\"text-[#D0B75B]\">Fantasy Room</span>. A fun vibrant way to capture your fun side! This room will feel like the pink fantasy it is.",
                'capacity' => '6 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-2 (67).png',
                'image_position' => 'right',
            ],
            [
                'id' => 'k1',
                'suite_type' => 'STANDARD SUITE',
                'name' => 'K1',
                'theme' => 'RACETRACK SUITE',
                'description' => "Stepping into our <span class=\"text-[#D0B75B]\">Racetrack Suite</span> built to feels like you and your party are moving 100 miles an hour. Feel the rush of adrenaline and comfort as you whip through your favorite songs.",
                'capacity' => '6 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-1 (35).png',
                'image_position' => 'left',
            ],
            [
                'id' => 'k2',
                'suite_type' => 'STANDARD SUITE',
                'name' => 'K2',
                'theme' => 'CHROME HEARTS SUITE',
                'description' => "Our <span class=\"text-[#D0B75B]\">Chrome Hearts Suite</span> is for the true high fashion lovers. Step into this luxurious room where every detail makes you feel like you're on a runway. If you have good taste and an eye for design, this is where you want to be.",
                'capacity' => '6 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-2 (66).png',
                'image_position' => 'right',
            ],
            [
                'id' => 'k3',
                'suite_type' => 'PARTY SUITE',
                'name' => 'K3',
                'theme' => 'DIGITAL SUITE',
                'description' => "Our <span class=\"text-[#D0B75B]\">Digital Suite</span> has a 360 projector screen, bright yellow couch, illuminated table, and interchangeable backdrops. Get ready for a night of unpredictable fun as you move from a rainforest to outer space to a world you've never seen.",
                'capacity' => '12 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-1 (34).png',
                'image_position' => 'left',
            ],
            [
                'id' => '777',
                'suite_type' => 'PARTY SUITE',
                'name' => '777',
                'theme' => 'BLESSED SUITE',
                'description' => "Step into divine elegance with our <span class=\"text-[#D0B75B]\">Blessed Suite</span> — where luck meets luxury. Designed with a radiant stained glass window, glowing red accents, and an aura that feels both sacred and celebratory, this room evokes the thrill of a Vegas jackpot and the warmth of a Sunday sanctuary.",
                'capacity' => '15 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-2 (64).png',
                'image_position' => 'right',
            ],
            [
                'id' => '888',
                'suite_type' => 'PARTY SUITE',
                'name' => '888',
                'theme' => 'CHAMPAGNE SUITE',
                'description' => "Welcome to the <span class=\"text-[#D0B75B]\">Champagne Suite</span>. With champagne colored couches, and a glittered arch to accent your room, you will feel the luxury as soon as you walk in.",
                'capacity' => '15 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-1 (30).png',
                'image_position' => 'left',
            ],
            [
                'id' => '999',
                'suite_type' => 'PARTY SUITE',
                'name' => '999',
                'theme' => 'RETRO SUITE',
                'description' => "Step into our <span class=\"text-[#D0B75B]\">Retro Suite</span> and be transported back in time. Featuring black & white striped design, extended seating, a mirror backdrop and its own private restroom.",
                'capacity' => '15 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-2 (65).png',
                'image_position' => 'right',
            ],
            [
                'id' => 'mics',
                'suite_type' => 'VIP SUITE',
                'name' => 'KRLS',
                'theme' => 'KRLS VIP LOUNGE SUITE',
                'description' => "Step into our <span class=\"text-[#D0B75B]\">KRLS VIP Lounge Suite</span> where the most luxurious experiences happen. Enjoy your three illuminated tables, light show arrangements, mirrored backdrop, spacious seating, and private restroom.",
                'capacity' => '35 GUESTS',
                'image' => 'assets/img/pages/rooms/Untitled-1 (33).png',
                'image_position' => 'left',
            ],
        ];

        $rates = [
            'karaoke' => [
                'title' => 'LUXURY KARAOKE',
                'description' => 'Experience a private luxurious karaoke experience with personal service at every turn.',
                'prices' => [
                    '$45/hr for 2-6 pax',
                    '$85/hr for 12-15 pax',
                    '$125/hr for 20+ pax',
                ],
            ],
            'decor' => [
                'title' => 'DECOR PACKAGES',
                'description' => 'This service can provide peace of mind for you, let us take your special event to the next level.',
                'prices' => [
                    '$35 for standard room',
                    '$65 for party room',
                    '$110 for VIP',
                    '$135 for custom requested decor',
                ],
            ],
            'watch_party' => [
                'title' => 'PRIVATE WATCH PARTY',
                'description' => 'Enjoy the privacy of your upscale watch party, with a personal server on call and added option to sing karaoke, you and your guests will have a night to remember.',
                'prices' => [
                    '$55/hr for 2-6 pax',
                    '$95/hr for 12-15 pax',
                    '$135/hr for 20+ pax',
                ],
            ],
        ];

        $reviews = [
            [
                'text' => "\"We celebrated my Moms birthday who is no longer with us and we had a time that we will always remember. Loved this place down, rooms were clean, with other beautiful rooms as an option. Service was great, staff even gave us a lil cheerful dance to add to the vibe. Checkin and out was great. Great parking. Food & bottle is pricey but overall had a good time\"",
                'author' => 'Tamara M',
                'rating' => 'Food: 4/5 | Service: 5/5 | Atmosphere: 5/5',
            ],
        ];

        return view('pages.rooms', compact('rooms', 'rates', 'reviews'));
    }
}
