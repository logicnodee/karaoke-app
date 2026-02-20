@extends('layouts.app')

@section('title', 'Food & Beverages - KRLS Karaoke')

@section('content')
<div class="relative h-screen w-full bg-black overflow-hidden">
    <img src="{{ asset('assets/img/pages/food-beverages/cd297f_00d407e007cb4fc7908a3697ab76d592~mv2.jpg') }}" alt="Food & Beverages" class="absolute inset-0 w-full h-full object-cover opacity-40">
    <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-6xl md:text-8xl font-bold tracking-widest uppercase text-center px-4" style="font-family: 'wf_a339f259334e44ff9a746f30d';">FOOD & BEVERAGES</h1>
    </div>
</div>

<div class="bg-black text-white min-h-screen py-20 relative" style="font-family: 'madefor-display', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- Food Menu Section -->
        <div class="mb-20 space-y-24">
            <h2 class="text-3xl font-bold mb-12 text-center text-yellow-500 uppercase tracking-widest" style="font-family: 'wf_a339f259334e44ff9a746f30d';">FOOD MENU</h2>
            
            <!-- Starters: Image (Left) | Text (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled - 2025-06-16T112757_037.png') }}" 
                     alt="Starters" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl">
                <div>
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">STARTERS</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Southwest Eggroll</span>
                                <span class="text-yellow-500 font-bold text-lg">$17</span>
                            </div>
                            <p class="text-gray-400 text-sm">Grilled Chicken + Corn + Black Beans + Microgreens + Chipotle Ranch</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Vegetable Spring Rolls</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                            <p class="text-gray-400 text-sm">Rice Paper Rolls filled with Vegetables + Sweet Thai chili (Vegan)</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Oysters Rockefeller</span>
                                <span class="text-yellow-500 font-bold text-lg">$24</span>
                            </div>
                            <p class="text-gray-400 text-sm">Spinach + Green Onion + Fresh Parsley + Mozzarella + Toasted Panko</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Mains: Text (Left) | Image (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">MAINS</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Crab Cakes</span>
                                <span class="text-yellow-500 font-bold text-lg">$30</span>
                            </div>
                            <p class="text-gray-400 text-sm">Jumbo Crab Meat + Microgreens + Remoulade</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Chicken Wings</span>
                                <span class="text-yellow-500 font-bold text-lg">12pc $17 | 30pc $40</span>
                            </div>
                            <p class="text-gray-400 text-sm">Choice of Hot Lemon Pepper, Sweet Thai Chili, or Seasoned Dry Rub</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Protein Rice Bowl</span>
                                <span class="text-yellow-500 font-bold text-lg">$19</span>
                            </div>
                            <p class="text-gray-400 text-sm">Rice + Toasted Sesame + Scallions + Choice of Protein (Seasoned Beef or Pork Belly)</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Crazy Noodles</span>
                                <span class="text-yellow-500 font-bold text-lg">$21</span>
                            </div>
                            <p class="text-gray-400 text-sm">Asian Medley on Egg Noodles + Choice of Protein (Seasoned Beef or Vegetarian or Shrimp)</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Mics SmashBurger</span>
                                <span class="text-yellow-500 font-bold text-lg">$25</span>
                            </div>
                            <p class="text-gray-400 text-sm">Potato Bun + Two Smashed Patties + Grilled Onions + American Cheese + Bacon Jam & Chipotle Mayo</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Seared Ahi Salad</span>
                                <span class="text-yellow-500 font-bold text-lg">$23</span>
                            </div>
                            <p class="text-gray-400 text-sm">Seared Ahi Tuna Steak + Fresh Mixed Greens + Ginger Miso Dressing</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Chef’s Special</span>
                                <span class="text-yellow-500 font-bold text-lg">$40</span>
                            </div>
                            <p class="text-gray-400 text-sm">Revolving special created and curated by our Head Chef</p>
                        </li>
                    </ul>
                </div>
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-2 (56).png') }}" 
                     alt="Mains" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl order-1 md:order-2">
            </div>

            <!-- Desserts: Image (Left) | Text (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-1 (25).png') }}" 
                     alt="Desserts" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl">
                <div>
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">DESSERTS</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Mics Revolving Special</span>
                                <span class="text-yellow-500 font-bold text-lg">$18</span>
                            </div>
                            <p class="text-gray-400 text-sm">Specialty dessert created by @witchsugar</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Doughnuts</span>
                                <span class="text-yellow-500 font-bold text-lg">$11</span>
                            </div>
                            <p class="text-gray-400 text-sm">Fried Doughnut Holes + Sugar + Vanilla Ice Cream</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Berry Cheesecake</span>
                                <span class="text-yellow-500 font-bold text-lg">$12</span>
                            </div>
                            <p class="text-gray-400 text-sm">Mixed Berry Swirl Cheesecake + Berry Compote</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Fruit Tower</span>
                                <span class="text-yellow-500 font-bold text-lg">Small $32 | Large $42</span>
                            </div>
                            <p class="text-gray-400 text-sm">Seasonal Fruit</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sides: Text (Left) | Image (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">SIDES</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Kimchi Mac & Cheese</span>
                                <span class="text-yellow-500 font-bold text-lg">$12</span>
                            </div>
                            <p class="text-gray-400 text-sm">5-Cheese Blend + Kimchi + Elbow Pasta</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Seasoned Fries</span>
                                <span class="text-yellow-500 font-bold text-lg">$10</span>
                            </div>
                            <p class="text-gray-400 text-sm">Steak fries + Gochugaru + Furikake</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Korean Street Corn</span>
                                <span class="text-yellow-500 font-bold text-lg">$14</span>
                            </div>
                            <p class="text-gray-400 text-sm">Corn + Miso Mayo + Mozzarella</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Spring Salad</span>
                                <span class="text-yellow-500 font-bold text-lg">$12</span>
                            </div>
                            <p class="text-gray-400 text-sm">Spring Mix Greens + Thai Chili Vinaigrette</p>
                        </li>
                    </ul>
                </div>
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-2 (50).png') }}" 
                     alt="Sides" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl order-1 md:order-2">
            </div>
        </div>

        <!-- Drinks Menu Section -->
        <div class="mb-20 space-y-24">
            <h2 class="text-3xl font-bold mb-12 text-center text-yellow-500 uppercase tracking-widest" style="font-family: 'wf_a339f259334e44ff9a746f30d';">DRINKS MENU</h2>
            
            <!-- Cocktails: Image (Left) | Text (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-1 (27).png') }}" 
                     alt="Cocktails" 
                     class="w-full h-auto max-h-[40rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl">
                <div>
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">COCKTAILS</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Yuzu Margarita</span>
                                <span class="text-yellow-500 font-bold text-lg">$17</span>
                            </div>
                            <p class="text-gray-400 text-sm">Tequila + Yuzu + Agave + Yakari Salt Rim</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Nectar</span>
                                <span class="text-yellow-500 font-bold text-lg">$19</span>
                            </div>
                            <p class="text-gray-400 text-sm">Gin + Ginger + Honey Lemon + Magic Flower</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Blacker the Berry</span>
                                <span class="text-yellow-500 font-bold text-lg">$16</span>
                            </div>
                            <p class="text-gray-400 text-sm">Cognac + Blackberry + Mint + Sage + Club Soda</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Pinkheat</span>
                                <span class="text-yellow-500 font-bold text-lg">$17</span>
                            </div>
                            <p class="text-gray-400 text-sm">Tequila + Watermelon + Elderflower + Chili</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Night Shift</span>
                                <span class="text-yellow-500 font-bold text-lg">$19</span>
                            </div>
                            <p class="text-gray-400 text-sm">Vanilla Bean Vodka + Brown Sugar + Fresh Espresso</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Shanghai’d</span>
                                <span class="text-yellow-500 font-bold text-lg">$21</span>
                            </div>
                            <p class="text-gray-400 text-sm">Japanese Whiskey + Sherry + Sake + Benedictine + Smoke</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Wang Zai Creamsicle</span>
                                <span class="text-yellow-500 font-bold text-lg">$17</span>
                            </div>
                            <p class="text-gray-400 text-sm">Vodka + Orange Juice + Calpico + Wang Zai Milk</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">The Eighth Cocktail</span>
                                <span class="text-yellow-500 font-bold text-lg">$16</span>
                            </div>
                            <p class="text-gray-400 text-sm">Rum + Strawberry Puree + Clarified Cheesecake + Strawberry</p>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Mystery</span>
                                <span class="text-yellow-500 font-bold text-lg">$16</span>
                            </div>
                            <p class="text-gray-400 text-sm">I don’t know… just try it</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Beer: Text (Left) | Image (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">BEER</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Bud Light</span>
                                <span class="text-yellow-500 font-bold text-lg">$6</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Sapporo</span>
                                <span class="text-yellow-500 font-bold text-lg">$6</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Corona</span>
                                <span class="text-yellow-500 font-bold text-lg">$6</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Modelo</span>
                                <span class="text-yellow-500 font-bold text-lg">$6</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Stella Artois</span>
                                <span class="text-yellow-500 font-bold text-lg">$6</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <img src="{{ asset('assets/img/pages/food-beverages/Dips (5)_edited.png') }}" 
                     alt="Beer" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl order-1 md:order-2">
            </div>

            <!-- Soju: Image (Left) | Text (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-1 (28).png') }}" 
                     alt="Soju" 
                     class="w-full h-auto max-h-[30rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl">
                <div>
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">SOJU</h3>
                    <ul class="space-y-6">
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Original Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Strawberry Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Yogurt Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Watermelon Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Lychee Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Passionfruit Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Plum Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xl font-bold text-white">Seasonal Soju</span>
                                <span class="text-yellow-500 font-bold text-lg">$15</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Wine: Text (Left) | Image (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">WINE & CHAMPAGNE</h3>
                    <div class="space-y-8 text-gray-300">
                        <div>
                            <ul class="space-y-2">
                                <li class="flex justify-between"><span class="text-white">San Simeon (Pinot Noir)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">Le Jade (Chardonnay)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">Maddalena (Cabernet Sauvignon)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">White Haven (Sauvignon Blanc)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">Black Girl Magic (Riesling)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">Seven Daughters (Moscato)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                                <li class="flex justify-between"><span class="text-white">Whispering Angel (Rosé)</span> <span class="text-yellow-500 font-bold">$45</span></li>
                            </ul>
                        </div>
                        <div class="border-t border-zinc-700 pt-6">
                            <ul class="space-y-2">
                                <li class="flex justify-between"><span class="text-white">Kim Crawford (Sauvignon Blanc)</span> <span class="text-yellow-500 font-bold">$65</span></li>
                                <li class="flex justify-between"><span class="text-white">Salvestrin (Sauvignon Blanc)</span> <span class="text-yellow-500 font-bold">$80</span></li>
                                <li class="flex justify-between"><span class="text-white">Le Vindi Napa Valley (Chardonnay)</span> <span class="text-yellow-500 font-bold">$80</span></li>
                                <li class="flex justify-between"><span class="text-white">Willamette Valley (Pinot Noir)</span> <span class="text-yellow-500 font-bold">$80</span></li>
                                <li class="flex justify-between"><span class="text-white">Cellars Napa Valley (Cabernet Sauvignon)</span> <span class="text-yellow-500 font-bold">$80</span></li>
                                <li class="flex justify-between"><span class="text-white">J. McClelland (Cabernet Sauvignon)</span> <span class="text-yellow-500 font-bold">$80</span></li>
                            </ul>
                        </div>
                        <div class="border-t border-zinc-700 pt-6">
                            <ul class="space-y-2">
                                <li class="flex justify-between"><span class="text-white">Do.Epic.Shit (Sparkling Wine)</span> <span class="text-yellow-500 font-bold">$55</span></li>
                                <li class="flex justify-between"><span class="text-white">Riondo Frizzante (Prosecco)</span> <span class="text-yellow-500 font-bold">$65</span></li>
                                <li class="flex justify-between"><span class="text-white">Luc Belaire (Gold, Rare, Luxe)</span> <span class="text-yellow-500 font-bold">$100</span></li>
                                <li class="flex justify-between"><span class="text-white">Moët Hennessy Brut</span> <span class="text-yellow-500 font-bold">$175</span></li>
                                <li class="flex justify-between"><span class="text-white">Moët Nectar Impérial</span> <span class="text-yellow-500 font-bold">$200</span></li>
                                <li class="flex justify-between"><span class="text-white">Veuve Clicquot</span> <span class="text-yellow-500 font-bold">$185</span></li>
                                <li class="flex justify-between"><span class="text-white">Ace of Spades</span> <span class="text-yellow-500 font-bold">$700</span></li>
                                <li class="flex justify-between"><span class="text-white">Opus One (Cabernet Bordeaux Blend)</span> <span class="text-yellow-500 font-bold">$800</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-2 (51).png') }}" 
                     alt="Wine" 
                     class="w-full h-auto max-h-[40rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl order-1 md:order-2">
            </div>

            <!-- Bottle Prices: Image (Left) | Text (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="{{ asset('assets/img/pages/food-beverages/Untitled-1 (26).png') }}" 
                     alt="Bottle Prices" 
                     class="w-full h-auto max-h-[40rem] object-contain hover:scale-105 transition-transform duration-700 mx-auto drop-shadow-2xl">
                <div>
                    <h3 class="text-3xl font-bold text-yellow-500 mb-8 border-b border-zinc-700 pb-2" style="font-family: 'wf_a339f259334e44ff9a746f30d';">BOTTLE PRICES</h3>
                    <div class="grid grid-cols-1 gap-y-3 text-sm text-gray-300">
                        <div class="flex justify-between"><span>Bacardi</span> <span class="text-yellow-500">$185</span></div>
                        <div class="flex justify-between"><span>Bare Bone Vodka</span> <span class="text-yellow-500">$150</span></div>
                        <div class="flex justify-between"><span>Tito’s</span> <span class="text-yellow-500">$200</span></div>
                        <div class="flex justify-between"><span>Grey Goose</span> <span class="text-yellow-500">$200</span></div>
                        <div class="flex justify-between"><span>Don Julio Blanco</span> <span class="text-yellow-500">$220</span></div>
                        <div class="flex justify-between"><span>Don Julio Reposado</span> <span class="text-yellow-500">$250</span></div>
                        <div class="flex justify-between"><span>Don Julio Reposado XL</span> <span class="text-yellow-500">$500</span></div>
                        <div class="flex justify-between"><span>Tequila Ocho Blanco</span> <span class="text-yellow-500">$220</span></div>
                        <div class="flex justify-between"><span>Tequila Ocho Reposado</span> <span class="text-yellow-500">$250</span></div>
                        <div class="flex justify-between"><span>Patrón Silver</span> <span class="text-yellow-500">$200</span></div>
                        <div class="flex justify-between"><span>La Gritona Reposado</span> <span class="text-yellow-500">$250</span></div>
                        <div class="flex justify-between"><span>Don Julio 1942</span> <span class="text-yellow-500">$525</span></div>
                        <div class="flex justify-between"><span>Hennessy (1L)</span> <span class="text-yellow-500">$285</span></div>
                        <div class="flex justify-between"><span>Hennessy VSOP (1L)</span> <span class="text-yellow-500">$375</span></div>
                        <div class="flex justify-between"><span>D’USSE</span> <span class="text-yellow-500">$250</span></div>
                        <div class="flex justify-between"><span>Rémy Martin VSOP</span> <span class="text-yellow-500">$285</span></div>
                        <div class="flex justify-between"><span>Crown Royal/Apple</span> <span class="text-yellow-500">$200/$225</span></div>
                        <div class="flex justify-between"><span>Jack Daniels</span> <span class="text-yellow-500">$185</span></div>
                        <div class="flex justify-between"><span>Jameson</span> <span class="text-yellow-500">$225</span></div>
                        <div class="flex justify-between"><span>Woodford Reserve</span> <span class="text-yellow-500">$250</span></div>
                        <div class="flex justify-between"><span>Macallan 12yr</span> <span class="text-yellow-500">$275</span></div>
                        <div class="flex justify-between"><span>Macallan 15yr</span> <span class="text-yellow-500">$425</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hookah Menu -->
        <!-- Hookah Menu -->
        <div class="mb-32 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                
                <!-- Left Column: Text -->
                <div class="pl-4 lg:pl-12">
                    <h2 class="text-6xl font-serif text-yellow-500 mb-4 tracking-wide" style="font-family: 'wf_a339f259334e44ff9a746f30d';">HOOKAH</h2>
                    <div class="w-24 h-1 bg-yellow-600 mb-12"></div>

                    <!-- Special Mix -->
                    <div class="mb-12">
                        <h3 class="text-xl font-bold text-white mb-6 tracking-wide" style="font-family: 'wf_a339f259334e44ff9a746f30d';">Special Mix: $50</h3>
                        <ul class="space-y-3 text-white text-sm list-disc list-inside marker:text-gray-500 ml-2">
                            <li>Paradise in Mics (Blueberry, Peach)</li>
                            <li>Magic Mics (Orange, Peach, Mint)</li>
                            <li>Peel Off (Orange, Blueberry)</li>
                            <li>The Kami (Love 66, Kiwi)</li>
                            <li>Mile High Season (Mighty Freeze, White Gummy Bear)</li>
                        </ul>
                    </div>

                    <!-- Classics -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6 tracking-wide" style="font-family: 'wf_a339f259334e44ff9a746f30d';">Classics: $45</h3>
                        <ul class="space-y-3 text-white text-sm list-disc list-inside marker:text-gray-500 ml-2">
                            <li>White Gummy Bear</li>
                            <li>Love66</li>
                            <li>Blueberry</li>
                            <li>Blue Mist</li>
                            <li>Watermelon</li>
                            <li>Mighty Freeze</li>
                            <li>Peach</li>
                            <li>Orange</li>
                            <li>Mint</li>
                            <li>Kiwi</li>
                        </ul>
                    </div>
                </div>

                <!-- Right Column: Image with Decoration -->
                <div class="relative flex justify-end pr-4 lg:pr-0">
                    <!-- Gold Frame Background -->
                    <div class="absolute -top-12 -right-12 w-3/4 h-3/4 border-t-[12px] border-r-[12px] border-yellow-600 z-0 hidden lg:block"></div>
                    
                    <!-- Main Image -->
                    <div class="relative z-10 w-full max-w-lg aspect-square">
                         <img src="{{ asset('assets/img/pages/food-beverages/Untitled-2 (53).png') }}" 
                              alt="Hookah" 
                              class="w-full h-full object-contain drop-shadow-2xl">
                         
                         <!-- Geometric Pattern Overlay (Bottom Left) -->
                         <div class="absolute -bottom-8 -left-8 w-32 h-32 z-20 pointer-events-none hidden lg:block">
                            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" class="text-yellow-600 w-full h-full opacity-80" stroke-width="2">
                                <path d="M0 20 L20 20 L20 0 M0 40 L40 40 L40 0 M0 60 L60 60 L60 0" />
                                <path d="M20 100 L20 80 L0 80 M40 100 L40 60 L0 60" />
                            </svg>
                         </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Specialty Packages -->
        <div class="mb-20">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center text-yellow-500 uppercase tracking-widest" style="font-family: 'wf_a339f259334e44ff9a746f30d';">SPECIALTY PACKAGES</h2>
            <p class="text-center text-gray-400 mb-12 tracking-wide" style="font-family: 'madefor-display', sans-serif;">(All packages have the option of adding a hookah for $25)</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4 max-w-3xl mx-auto">
                <!-- Bronze -->
                <div class="relative group">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">BRONZE</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <div class="text-xl font-bold text-black mb-1">$125</div>
                        <p class="text-xs text-gray-600 mb-8 uppercase tracking-wider font-semibold">(VALUED AT $160)</p>
                        
                        <ul class="text-sm text-gray-800 space-y-4 text-left w-full">
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two buckets of beer (12 count) OR Two Bottles of Select Wines</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>3 Acqua Panna (Liter)</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>1 small fruit plate or small spinach and artichoke dip</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Silver -->
                <div class="relative group">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">SILVER</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <div class="text-xl font-bold text-black mb-1">$250</div>
                        <p class="text-xs text-gray-600 mb-8 uppercase tracking-wider font-semibold">(VALUED AT $300)</p>
                        
                        <ul class="text-sm text-gray-800 space-y-4 text-left w-full">
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two bottles of wine or champagne of choice</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>4 Acqua Panna (Liter)</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Small Fruit Plate</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Gold -->
                <div class="relative group">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">GOLD</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <div class="text-xl font-bold text-black mb-1">$480</div>
                        <p class="text-xs text-gray-600 mb-8 uppercase tracking-wider font-semibold">(VALUED AT $510)</p>
                        
                        <ul class="text-sm text-gray-800 space-y-4 text-left w-full">
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>1 bottle listed under bottle section (no liters)</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>One 52 oz juice</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>4 Acqua Panna (Liter)</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>30 Chicken Wings</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two orders of Seasoned Fries</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Platinum -->
                <div class="relative group">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">PLATINUM</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <div class="text-xl font-bold text-black mb-1">$685</div>
                        <p class="text-xs text-gray-600 mb-8 uppercase tracking-wider font-semibold">(VALUED AT $750)</p>
                        
                        <ul class="text-sm text-gray-800 space-y-4 text-left w-full">
                            <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two bottles listed under bottle section (no liters)</span>
                            </li>
                             <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two 52 oz juice</span>
                            </li>
                             <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>5 Acqua Panna (liter)</span>
                            </li>
                             <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Large Fruit Platter or Spinach Artichoke Dip</span>
                            </li>
                             <li class="flex gap-3">
                                <i data-lucide="wine" class="w-5 h-5 text-yellow-600 flex-shrink-0"></i>
                                <span>Two Speciality Hookahs</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Specials Section -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center text-yellow-500 uppercase tracking-widest" style="font-family: 'wf_a339f259334e44ff9a746f30d';">WEEKLY SPECIALS</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-8 px-4 max-w-5xl mx-auto">
                <!-- Movie Mondays -->
                <div class="relative group lg:col-span-2">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">MOVIE MONDAYS</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <ul class="text-sm text-gray-800 space-y-4 w-full">
                            <li class="flex flex-col items-center">
                                <span class="font-bold">Movie Nights (Rooms 777 & V1)</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Jäger Bomb | $10.45</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Weekly Movie Cocktail | $10</span>
                                <span class="text-xs text-gray-500 italic">(while supplies last)</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Industry Staff: 15% off rooms</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Lobby/Patio: Beer + Shot | $10</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 2-For-1 Tuesday -->
                <div class="relative group lg:col-span-2">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">2 FOR 1 TUESDAY</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <ul class="text-sm text-gray-800 space-y-4 w-full">
                            <li class="flex flex-col items-center">
                                <span>Cocktails: Buy one, get one free</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Desserts: Two for one</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Karaoke: Two hours for the price of one</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Burger & Fries Combo: $20</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Winners Circle Wednesdays -->
                <div class="relative group lg:col-span-2">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">WINNERS CIRCLE WEDNESDAYS</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <ul class="text-sm text-gray-800 space-y-4 w-full">
                            <li class="flex flex-col items-center">
                                <span>75¢ Wings</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Do.Epic.Sh*t Wine Bottle | $40</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Oysters Rockefeller | 12 for $40</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Bucket of 5 Beers | $22</span>
                                <span class="text-xs text-gray-500 italic">(Bud Light, Sapporo, IPA)</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>Select Wine Bottles | $40</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Live From The Lobby Thursdays -->
                <div class="relative group lg:col-span-2 lg:col-start-2">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">LIVE FROM THE LOBBY THURSDAYS</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <ul class="text-sm text-gray-800 space-y-4 w-full">
                            <li class="flex flex-col items-center">
                                <span>Karaoke Rooms: 50% off</span>
                                <span class="text-xs text-gray-500 italic">(till 9pm)</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Shot Flights (5 shots) | $20</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Hookahs | $30</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>Soju Bottles | $10</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>All Cocktails | $14</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Pour Decision Sunday -->
                 <div class="relative group lg:col-span-2">
                    <div class="absolute top-2 left-2 w-full h-full bg-yellow-600 rounded-sm"></div>
                    <div class="relative bg-white p-8 rounded-sm h-full flex flex-col items-center text-center">
                        <h3 class="text-2xl font-bold text-black mb-2 uppercase tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">POUR DECISION SUNDAY</h3>
                        <div class="w-8 h-1 bg-yellow-500 mb-6"></div>
                        
                        <ul class="text-sm text-gray-800 space-y-4 w-full">
                             <li class="flex flex-col items-center">
                                <span>Sake Bottles: 50% off</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>Soju Bottles | $10</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>Karaoke Rooms: Buy 3 Hours, Get 1 Free</span>
                            </li>
                             <li class="flex flex-col items-center">
                                <span>Patio/Lounge: Order 2 Apps, Free Samosas</span>
                            </li>
                            <li class="flex flex-col items-center">
                                <span>Hookah | $30</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Allergy Disclaimer -->
            <div class="mt-16 bg-black/50 p-6 rounded-lg text-center border border-zinc-800/50">
                 <h4 class="text-yellow-600 font-bold mb-2 uppercase text-sm tracking-wider" style="font-family: 'wf_a339f259334e44ff9a746f30d';">Allergy Disclaimer</h4>
                 <p class="text-xs text-gray-500 max-w-4xl mx-auto leading-relaxed">
                    At Mics Karaoke, the health and safety of our guests is our top priority. Please be aware that our kitchen and bar use common allergens including, but not limited to, peanuts, tree nuts, soy, wheat, dairy, eggs, shellfish, and gluten. While we take great care in food preparation, we cannot guarantee that any menu item will be completely free of allergens due to shared equipment and preparation areas. If you or someone in your party has a food allergy or dietary restriction, please inform your server before ordering. Our team will do our best to accommodate your needs; however, all guests assume responsibility for managing their own allergies and sensitivities. Your safety is important to us, and we encourage you to ask questions about ingredients and preparation methods.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
