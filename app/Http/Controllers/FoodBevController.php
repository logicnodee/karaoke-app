<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FoodBevController extends Controller
{
    public function index()
    {
        $menuCategories = [
            'Appetizers' => [
                ['name' => 'Crispy Calamari', 'price' => '$12', 'description' => 'Lightly fried squid with marinara sauce.'],
                ['name' => 'Mozzarella Sticks', 'price' => '$10', 'description' => 'Breaded cheese sticks with ranch dip.'],
                ['name' => 'Buffalo Wings', 'price' => '$14', 'description' => 'Spicy chicken wings served with celery.'],
            ],
            'Main Courses' => [
                ['name' => 'Karaoke Burger', 'price' => '$16', 'description' => 'Juicy beef patty with cheese, lettuce, and special sauce.'],
                ['name' => 'Pasta Carbonara', 'price' => '$18', 'description' => 'Creamy pasta with bacon and parmesan.'],
                ['name' => 'Grilled Salmon', 'price' => '$22', 'description' => 'Fresh salmon fillet with seasonal vegetables.'],
            ],
            'Beverages' => [
                ['name' => 'Signature Cocktail', 'price' => '$12', 'description' => 'Our house special blend.'],
                ['name' => 'Craft Beer', 'price' => '$8', 'description' => 'Selection of local ales.'],
                ['name' => 'Soda', 'price' => '$4', 'description' => 'Unlimited refills.'],
            ],
        ];

        return view('pages.food-beverages', compact('menuCategories'));
    }
}
