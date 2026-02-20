<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $features = [
            [
                'title' => 'VIP Rooms',
                'description' => 'Experience luxury in our exclusive VIP suites.',
                'icon' => 'crown',
                'image' => 'assets/img/pages/home/vip-room.jpg' // Placeholder path
            ],
            [
                'title' => 'High-Fidelity Audio',
                'description' => 'Sing your heart out with our state-of-the-art sound systems.',
                'icon' => 'mic',
                'image' => 'assets/img/pages/home/audio.jpg'
            ],
            [
                'title' => 'Gourmet Food',
                'description' => 'Delicious snacks and meals to keep you energized.',
                'icon' => 'utensils',
                'image' => 'assets/img/pages/home/food.jpg'
            ],
        ];

        $testimonials = [
            [
                'name' => 'Sarah J.',
                'quote' => 'Best karaoke place in town! The sound system is amazing.',
                'role' => 'Regular Customer'
            ],
            [
                'name' => 'Mike T.',
                'quote' => 'Great food and atmosphere. Highly recommended for parties.',
                'role' => 'Event Planner'
            ],
        ];

        return view('pages.home', compact('features', 'testimonials'));
    }
}
