<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        return redirect()->route('kasir.pemesanan');
    }

    // Master Data Ruangan (Duplicated from AdminController for sync)
    private function getMasterRuangan() {
        return [
            // LANTAI 1 (10 Ruangan)
            [
                'nama' => 'VIP 01', 'lantai' => 1, 'tipe' => 'VIP', 'kapasitas' => 10,
                'harga_weekday' => 150000, 'harga_weekend' => 200000,
                'status' => 'Digunakan', 'tamu' => 'Bpk. Ahmad (Demo Timer)',
                'key' => 'K-V01',
                'sisa_waktu' => '00:00:20', 'durasi_pakai' => null,
                'hampir_habis' => true, 'billing_mode' => 'paket',
                'booking_start' => now()->subHours(1)->subMinutes(59)->subSeconds(40), 'booking_duration' => 2,
                'sisa_detik' => 20, 'durasi_berjalan' => 7180
            ],
            [
                'nama' => 'VIP 02', 'lantai' => 1, 'tipe' => 'VIP', 'kapasitas' => 10,
                'harga_weekday' => 150000, 'harga_weekend' => 200000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 101', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Digunakan', 'tamu' => 'Ibu Maya',
                'key' => 'K-101',
                'sisa_waktu' => '01:45:00', 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => 'paket',
                'booking_start' => now()->subMinutes(15), 'booking_duration' => 2,
                'sisa_detik' => 6300, 'durasi_berjalan' => 900
            ],
            [
                'nama' => 'Room 102', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Cleaning', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 103', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
             [
                'nama' => 'Room 104', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Digunakan', 'tamu' => 'Sdr. Rizky',
                'key' => 'K-104',
                'sisa_waktu' => null, 'durasi_pakai' => '02:30:11',
                'hampir_habis' => false, 'billing_mode' => 'open',
                'booking_start' => now()->subHours(2)->subMinutes(30), 'booking_duration' => 0,
                'sisa_detik' => 0, 'durasi_berjalan' => 9011
            ],
            [
                'nama' => 'Room 105', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 6,
                'harga_weekday' => 60000, 'harga_weekend' => 85000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 106', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 6,
                'harga_weekday' => 60000, 'harga_weekend' => 85000,
                'status' => 'Digunakan', 'tamu' => 'Bpk. Hendra',
                'key' => 'K-106',
                'sisa_waktu' => '00:04:49', 'durasi_pakai' => null,
                'hampir_habis' => true, 'billing_mode' => 'paket',
                'booking_start' => now()->subMinutes(55), 'booking_duration' => 1,
                'sisa_detik' => 289, 'durasi_berjalan' => 3311
            ],
            [
                'nama' => 'Room 107', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Cleaning', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 108', 'lantai' => 1, 'tipe' => 'Regular', 'kapasitas' => 4,
                'harga_weekday' => 50000, 'harga_weekend' => 75000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],

            // LANTAI 2 (10 Ruangan)
            [
                'nama' => 'VVIP 01', 'lantai' => 2, 'tipe' => 'VVIP', 'kapasitas' => 15,
                'harga_weekday' => 250000, 'harga_weekend' => 350000,
                'status' => 'Digunakan', 'tamu' => 'Ibu Ratna',
                'key' => 'K-VV01',
                'sisa_waktu' => null, 'durasi_pakai' => '01:15:20',
                'hampir_habis' => false, 'billing_mode' => 'open',
                'booking_start' => now()->subHours(1)->subMinutes(15), 'booking_duration' => 0,
                'sisa_detik' => 0, 'durasi_berjalan' => 4520
            ],
            [
                'nama' => 'VVIP 02', 'lantai' => 2, 'tipe' => 'VVIP', 'kapasitas' => 15,
                'harga_weekday' => 250000, 'harga_weekend' => 350000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
             [
                'nama' => 'Suite 01', 'lantai' => 2, 'tipe' => 'Suite', 'kapasitas' => 25,
                'harga_weekday' => 500000, 'harga_weekend' => 750000,
                'status' => 'Digunakan', 'tamu' => 'Sdr. Kevin',
                'key' => 'K-S01',
                'sisa_waktu' => '02:45:00', 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => 'paket',
                'booking_start' => now()->subMinutes(15), 'booking_duration' => 3,
                'sisa_detik' => 9900, 'durasi_berjalan' => 900
            ],
            [
                'nama' => 'Room 201', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 6,
                'harga_weekday' => 60000, 'harga_weekend' => 85000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 202', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 6,
                'harga_weekday' => 60000, 'harga_weekend' => 85000,
                'status' => 'Digunakan', 'tamu' => 'Bpk. Budi',
                'key' => 'K-202',
                'sisa_waktu' => null, 'durasi_pakai' => '00:45:10',
                'hampir_habis' => false, 'billing_mode' => 'open',
                'booking_start' => now()->subMinutes(45), 'booking_duration' => 0,
                'sisa_detik' => 0, 'durasi_berjalan' => 2710
            ],
             [
                'nama' => 'Room 203', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 6,
                'harga_weekday' => 60000, 'harga_weekend' => 85000,
                'status' => 'Cleaning', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 204', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 8,
                'harga_weekday' => 80000, 'harga_weekend' => 120000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Room 205', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 8,
                'harga_weekday' => 80000, 'harga_weekend' => 120000,
                'status' => 'Digunakan', 'tamu' => 'Ibu Susi',
                'key' => 'K-205',
                'sisa_waktu' => '00:25:00', 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => 'paket',
                'booking_start' => now()->subMinutes(35), 'booking_duration' => 1,
                'sisa_detik' => 1500, 'durasi_berjalan' => 2100
            ],
             [
                'nama' => 'Room 206', 'lantai' => 2, 'tipe' => 'Regular', 'kapasitas' => 8,
                'harga_weekday' => 80000, 'harga_weekend' => 120000,
                'status' => 'Kosong', 'tamu' => null,
                'key' => null,
                'sisa_waktu' => null, 'durasi_pakai' => null,
                'hampir_habis' => false, 'billing_mode' => null,
                'booking_start' => null, 'booking_duration' => null,
                'sisa_detik' => 0, 'durasi_berjalan' => 0
            ],
            [
                'nama' => 'Party 01', 'lantai' => 2, 'tipe' => 'Party', 'kapasitas' => 30,
                'harga_weekday' => 1000000, 'harga_weekend' => 1500000,
                'status' => 'Digunakan', 'tamu' => 'Komunitas Mobil',
                'key' => 'K-P01',
                'sisa_waktu' => null, 'durasi_pakai' => '03:10:05',
                'hampir_habis' => false, 'billing_mode' => 'open',
                'booking_start' => now()->subHours(3)->subMinutes(10), 'booking_duration' => 0,
                'sisa_detik' => 0, 'durasi_berjalan' => 11405
            ],
        ];
    }

    public function ruangan()
    {
        $daftarRuangan = $this->getMasterRuangan();

        $paketHarga = [
            ['nama' => 'Paket Hemat 2 Jam', 'durasi' => '2 Jam', 'harga_weekday' => 90000, 'harga_weekend' => 130000],
            ['nama' => 'Paket Puas 3 Jam', 'durasi' => '3 Jam', 'harga_weekday' => 125000, 'harga_weekend' => 180000],
        ];

        return view('dashboard.kasir.ruangan', compact('daftarRuangan', 'paketHarga'));
    }

    private function getMenuItems() {
        return [
            // FOOD
            ['category' => 'Food', 'sub_category' => 'STARTERS', 'name' => 'Southwest Eggroll', 'price' => 17000, 'description' => 'Grilled Chicken + Corn + Black Beans + Microgreens + Chipotle Ranch', 'status' => 'Available', 'stock' => 50, 'image' => 'assets/img/pages/food-beverages/Untitled - 2025-06-16T112757_037.png'],
            // FOOD - Starters
            ['category' => 'Food', 'sub_category' => 'STARTERS', 'name' => 'Vegetable Spring Rolls', 'price' => 150000, 'description' => 'Rice Paper Rolls filled with Vegetables + Sweet Thai chili (Vegan)', 'status' => 'Available', 'stock' => 45, 'image' => 'assets/img/pages/food-beverages/Untitled - 2025-06-16T112757_037.png'],
            ['category' => 'Food', 'sub_category' => 'STARTERS', 'name' => 'Oysters Rockefeller', 'price' => 240000, 'description' => 'Spinach + Green Onion + Fresh Parsley + Mozzarella + Toasted Panko', 'status' => 'Available', 'stock' => 30, 'image' => 'assets/img/pages/food-beverages/Untitled - 2025-06-16T112757_037.png'],
            
            ['category' => 'Food', 'sub_category' => 'MAINS', 'name' => 'Crab Cakes', 'price' => 300000, 'description' => 'Jumbo Crab Meat + Microgreens + Remoulade', 'status' => 'Available', 'stock' => 25, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'],
            [
                'category' => 'Food', 
                'sub_category' => 'MAINS', 
                'name' => 'Chicken Wings', 
                'price' => 170000, 
                'variations' => [
                    ['name' => 'Small (12pc)', 'price' => 170000],
                    ['name' => 'Large (30pc)', 'price' => 400000]
                ],
                'description' => 'Choice of Hot Lemon Pepper, Sweet Thai Chili, or Seasoned Dry Rub', 
                'status' => 'Available', 
                'stock' => 100,
                'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'
            ],
            ['category' => 'Food', 'sub_category' => 'MAINS', 'name' => 'Protein Rice Bowl', 'price' => 190000, 'description' => 'Rice + Toasted Sesame + Scallions + Choice of Protein (Seasoned Beef or Pork Belly)', 'status' => 'Available', 'stock' => 40, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'],
            ['category' => 'Food', 'sub_category' => 'MAINS', 'name' => 'Crazy Noodles', 'price' => 210000, 'description' => 'Asian Medley on Egg Noodles + Choice of Protein', 'status' => 'Available', 'stock' => 60, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'],
            ['category' => 'Food', 'sub_category' => 'MAINS', 'name' => 'Mics SmashBurger', 'price' => 250000, 'description' => 'Potato Bun + Two Smashed Patties + Grilled Onions + American Cheese + Bacon Jam & Chipotle Mayo', 'status' => 'Available', 'stock' => 80, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'],
            ['category' => 'Food', 'sub_category' => 'MAINS', 'name' => 'Seared Ahi Salad', 'price' => 230000, 'description' => 'Seared Ahi Tuna Steak + Fresh Mixed Greens + Ginger Miso Dressing', 'status' => 'Available', 'stock' => 20, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (56).png'],
            
            ['category' => 'Food', 'sub_category' => 'SIDES', 'name' => 'Kimchi Mac & Cheese', 'price' => 120000, 'description' => '5-Cheese Blend + Kimchi + Elbow Pasta', 'status' => 'Available', 'stock' => 45, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (50).png'],
            ['category' => 'Food', 'sub_category' => 'SIDES', 'name' => 'Seasoned Fries', 'price' => 100000, 'description' => 'Steak fries + Gochugaru + Furikake', 'status' => 'Available', 'stock' => 150, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (50).png'],
            ['category' => 'Food', 'sub_category' => 'SIDES', 'name' => 'Korean Street Corn', 'price' => 140000, 'description' => 'Corn + Miso Mayo + Mozzarella', 'status' => 'Available', 'stock' => 55, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (50).png'],

            ['category' => 'Food', 'sub_category' => 'DESSERTS', 'name' => 'Mics Revolving Special', 'price' => 180000, 'description' => 'Specialty dessert created by @witchsugar', 'status' => 'Available', 'stock' => 15, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (25).png'],
            ['category' => 'Food', 'sub_category' => 'DESSERTS', 'name' => 'Doughnuts', 'price' => 110000, 'description' => 'Fried Doughnut Holes + Sugar + Vanilla Ice Cream', 'status' => 'Available', 'stock' => 65, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (25).png'],
            ['category' => 'Food', 'sub_category' => 'DESSERTS', 'name' => 'Berry Cheesecake', 'price' => 120000, 'description' => 'Mixed Berry Swirl Cheesecake + Berry Compote', 'status' => 'Available', 'stock' => 25, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (25).png'],

            // BEVERAGES
            ['category' => 'Beverages', 'sub_category' => 'COCKTAILS', 'name' => 'Yuzu Margarita', 'price' => 170000, 'description' => 'Tequila + Yuzu + Agave + Yakari Salt Rim', 'status' => 'Available', 'stock' => 40, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (27).png'],
            ['category' => 'Beverages', 'sub_category' => 'COCKTAILS', 'name' => 'Nectar', 'price' => 190000, 'description' => 'Gin + Ginger + Honey Lemon + Magic Flower', 'status' => 'Available', 'stock' => 35, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (27).png'],
            ['category' => 'Beverages', 'sub_category' => 'COCKTAILS', 'name' => 'Blacker the Berry', 'price' => 160000, 'description' => 'Cognac + Blackberry + Mint + Sage + Club Soda', 'status' => 'Available', 'stock' => 30, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (27).png'],
            
            ['category' => 'Beverages', 'sub_category' => 'SOJU', 'name' => 'Original Soju', 'price' => 150000, 'description' => 'Classic Korean Spirit', 'status' => 'Available', 'stock' => 100, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (28).png'],
            ['category' => 'Beverages', 'sub_category' => 'SOJU', 'name' => 'Strawberry Soju', 'price' => 150000, 'description' => 'Strawberry Flavored', 'status' => 'Available', 'stock' => 80, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (28).png'],
            ['category' => 'Beverages', 'sub_category' => 'SOJU', 'name' => 'Yogurt Soju', 'price' => 150000, 'description' => 'Yogurt Flavored', 'status' => 'Available', 'stock' => 75, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (28).png'],
            
            ['category' => 'Beverages', 'sub_category' => 'BEER', 'name' => 'Bud Light', 'price' => 60000, 'description' => 'Lager', 'status' => 'Available', 'stock' => 200, 'image' => 'assets/img/pages/food-beverages/Dips (5)_edited.png'],
            ['category' => 'Beverages', 'sub_category' => 'BEER', 'name' => 'Sapporo', 'price' => 60000, 'description' => 'Japanese Lager', 'status' => 'Available', 'stock' => 150, 'image' => 'assets/img/pages/food-beverages/Dips (5)_edited.png'],

            ['category' => 'Beverages', 'sub_category' => 'WINE & CHAMPAGNE', 'name' => 'San Simeon (Pinot Noir)', 'price' => 450000, 'description' => 'Red Wine', 'status' => 'Available', 'stock' => 12, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (51).png'],
            ['category' => 'Beverages', 'sub_category' => 'WINE & CHAMPAGNE', 'name' => 'Moët Hennessy Brut', 'price' => 1750000, 'description' => 'Champagne', 'status' => 'Available', 'stock' => 8, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (51).png'],

            ['category' => 'Beverages', 'sub_category' => 'SPECIALTY PACKAGES', 'name' => 'BRONZE', 'price' => 1250000, 'description' => 'Two buckets of beer OR Two Bottles of Wine + 3 Acqua Panna + Small Fruit Plate', 'status' => 'Available', 'stock' => 20, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (26).png'],
            ['category' => 'Beverages', 'sub_category' => 'SPECIALTY PACKAGES', 'name' => 'SILVER', 'price' => 2500000, 'description' => 'Two bottles of wine/champagne + 4 Acqua Panna + Small Fruit Plate', 'status' => 'Available', 'stock' => 15, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (26).png'],
            ['category' => 'Beverages', 'sub_category' => 'SPECIALTY PACKAGES', 'name' => 'GOLD', 'price' => 4800000, 'description' => '1 Bottle + 52oz Juice + 4 Acqua Panna + 30 Wings + 2 Fries', 'status' => 'Available', 'stock' => 10, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (26).png'],
            ['category' => 'Beverages', 'sub_category' => 'SPECIALTY PACKAGES', 'name' => 'PLATINUM', 'price' => 6850000, 'description' => '2 Bottles + 2 Juices + 5 Acqua Panna + Large Fruit Platter + 2 Speciality Hookahs', 'status' => 'Available', 'stock' => 5, 'image' => 'assets/img/pages/food-beverages/Untitled-1 (26).png'],
             
            ['category' => 'Beverages', 'sub_category' => 'HOOKAH', 'name' => 'Special Mix', 'price' => 500000, 'description' => 'Paradise in Mics, Magic Mics, Peel Off, The Kami', 'status' => 'Available', 'stock' => 25, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (53).png'],
            ['category' => 'Beverages', 'sub_category' => 'HOOKAH', 'name' => 'Classics', 'price' => 450000, 'description' => 'White Gummy Bear, Love66, Blueberry, Mint', 'status' => 'Available', 'stock' => 30, 'image' => 'assets/img/pages/food-beverages/Untitled-2 (53).png'],
        ];
    }

    public function foodBeverages()
    {
        $menuItems = $this->getMenuItems();

        return view('dashboard.kasir.food-beverages', compact('menuItems'));
    }
    public function pemesanan()
    {
        $daftarRuangan = $this->getMasterRuangan();
        $menuItems = $this->getMenuItems();
        $paketHarga = [
            ['nama' => 'Paket Hemat 2 Jam', 'durasi' => '2 Jam', 'harga_weekday' => 90000, 'harga_weekend' => 130000],
            ['nama' => 'Paket Puas 3 Jam', 'durasi' => '3 Jam', 'harga_weekday' => 125000, 'harga_weekend' => 180000],
        ];

        return view('dashboard.kasir.pemesanan', compact('daftarRuangan', 'menuItems', 'paketHarga'));
    }
}
