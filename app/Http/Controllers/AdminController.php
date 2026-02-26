<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection; // Add this use statement for collect()

class AdminController extends Controller
{
    // Middleware dipindahkan ke web.php untuk stabilitas session

    // Master Data Ruangan (Private untuk simulasi database)
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

    public function ringkasan()
    {
        // 1. DATA RUANGAN
        $semuaRuangan = $this->getMasterRuangan();
        $statusRuangan = [
            'total' => count($semuaRuangan),
            'kosong' => collect($semuaRuangan)->where('status', 'Kosong')->count(),
            'digunakan' => collect($semuaRuangan)->where('status', 'Digunakan')->count(),
            'cleaning' => collect($semuaRuangan)->where('status', 'Cleaning')->count()
        ];
        
        $monitoringWaktu = collect($semuaRuangan)->where('status', 'Digunakan')->map(function($room) {
             return [
                'ruangan' => $room['nama'],
                'tamu' => $room['tamu'],
                'sisa_waktu' => $room['sisa_waktu'],
                'durasi_pakai' => $room['durasi_pakai'] ?? null,
                'hampir_habis' => $room['hampir_habis'],
                'billing_mode' => $room['billing_mode'] ?? 'paket',
                'sisa_detik' => $room['sisa_detik'] ?? 0,
                'durasi_berjalan' => $room['durasi_berjalan'] ?? 0,
            ];
        })->values()->all();

        // 2. DATA KEUANGAN & BILLING
        $pendapatanHariIni = 12500000;
        $pendapatanBulanIni = 350000000;
        $transaksiHariIni = [
            ['no_tagihan' => 'INV/20260211/001', 'ruangan' => 'VIP 01', 'tamu' => 'Bpk. Ahmad', 'total' => 450000, 'status' => 'Lunas', 'waktu' => '10:30'],
            ['no_tagihan' => 'INV/20260211/002', 'ruangan' => 'Room 12', 'tamu' => 'Sdr. Kevin', 'total' => 125000, 'status' => 'Lunas', 'waktu' => '11:15'],
            ['no_tagihan' => 'INV/20260211/003', 'ruangan' => 'Room 05', 'tamu' => 'Sdr. Rizky', 'total' => 250000, 'status' => 'Berjalan', 'waktu' => '12:00'],
             ['no_tagihan' => 'INV/20260211/004', 'ruangan' => 'VVIP 02', 'tamu' => 'Ibu Ratna', 'total' => 1250000, 'status' => 'Lunas', 'waktu' => '13:45'],
        ];

        // 3. DATA LAGU & KATEGORI
        $totalLagu = 12500;
        $laguPopuler = [
             ['judul' => 'Sial', 'artis' => 'Mahalini', 'diputar' => 1250],
             ['judul' => 'Komang', 'artis' => 'Raim Laode', 'diputar' => 1100],
             ['judul' => 'Sang Dewi', 'artis' => 'Lyodra', 'diputar' => 980],
        ];
        $totalKategori = 12;

        // 4. DATA FOOD & BEVERAGES
        $totalMenu = 45;
        $stokMenipis = [
             ['nama' => 'French Fries', 'stok' => 5],
             ['nama' => 'Cola Zero', 'stok' => 8],
             ['nama' => 'Mineral Water', 'stok' => 12],
        ];
        $pesananAktifFnb = 5;

        // 5. DATA AKUN & MEMBERSHIP
        $totalMember = 1250;
        $memberBaruBulanIni = 45;
        $totalStaf = 25;
        $stafHadir = 18; // Manajemen Absensi

        // 6. OPERASIONAL LAINNYA
        $panggilanRoom = [
            ['ruangan' => 'Room 102', 'tipe' => 'Panggilan Operator', 'waktu' => '2 menit lalu'],
            ['ruangan' => 'VIP 01', 'tipe' => 'Minta Bill', 'waktu' => '5 menit lalu'],
        ];
        
        $laporanPending = [
            ['ruangan' => 'Room 205', 'isu' => 'AC Kurang Dingin', 'pelapor' => 'Sdr. Budi'],
            ['ruangan' => 'Room 101', 'isu' => 'Mic 2 Mati', 'pelapor' => 'Ibu Susi'],
        ];

        $reservasiHariIni = [
             ['tamu' => 'Kel. Bpk Haryono', 'ruangan' => 'Suite 01', 'jam' => '19:00', 'pax' => 15],
             ['tamu' => 'Reuni SMA 1', 'ruangan' => 'Party 01', 'jam' => '20:00', 'pax' => 25],
        ];

        $logAktivitas = [
            ['user' => 'Sari (Kasir)', 'aksi' => 'Cetak Invoice INV/001', 'waktu' => '10:35'],
            ['user' => 'Budi (Operator)', 'aksi' => 'Restart PC Room 105', 'waktu' => '11:00'],
            ['user' => 'Admin', 'aksi' => 'Update Harga Paket', 'waktu' => '09:00'],
            ['user' => 'Sari (Kasir)', 'aksi' => 'Input Stok F&B', 'waktu' => '08:45'],
        ];

        return view('dashboard.admin.ringkasan', compact(
            'statusRuangan', 
            'monitoringWaktu', 
            'pendapatanHariIni', 
            'pendapatanBulanIni',
            'transaksiHariIni',
            'totalLagu',
            'laguPopuler',
            'totalKategori',
            'totalMenu',
            'stokMenipis',
            'pesananAktifFnb',
            'totalMember',
            'memberBaruBulanIni',
            'totalStaf',
            'stafHadir',
            'panggilanRoom',
            'laporanPending',
            'reservasiHariIni',
            'logAktivitas'
        ));
    }

    public function ruangan()
    {
        $daftarRuangan = $this->getMasterRuangan();

        $paketHarga = [
            ['nama' => 'Paket Hemat 2 Jam', 'durasi' => '2 Jam', 'harga_weekday' => 90000, 'harga_weekend' => 130000],
            ['nama' => 'Paket Puas 3 Jam', 'durasi' => '3 Jam', 'harga_weekday' => 125000, 'harga_weekend' => 180000],
        ];

        return view('dashboard.admin.ruangan', compact('daftarRuangan', 'paketHarga'));
    }

    public function operator()
    {
        // Reuse master data logic
        $daftarRuangan = $this->getMasterRuangan();
        return view('dashboard.admin.manajemen-operator', compact('daftarRuangan'));
    }

    private function getMasterLagu() {
        $youtubeList = [
            ['judul' => 'Too Late To Be Right', 'artis' => 'Post Malone ft. Justin Bieber', 'youtube_id' => '9d3vyQEpFAM'],
            ['judul' => 'Best Songs Playlist 2026', 'artis' => 'Justin Bieber', 'youtube_id' => 'D2uFMpvaWpc'],
            ['judul' => 'Greatest Hits Full Album 2026', 'artis' => 'Justin Bieber', 'youtube_id' => 'bo9MmiQHTYY'],
            ['judul' => 'Chill Best Songs 2026', 'artis' => 'Justin Bieber', 'youtube_id' => 'TFyJmYy5V1M'],
            ['judul' => 'Trending Spotify Pop Hits', 'artis' => 'Justin Bieber', 'youtube_id' => 'DBSBflpPx68'],
            ['judul' => 'Most Popular Billie Eilish Songs', 'artis' => 'Billie Eilish', 'youtube_id' => 'pKfXFHLeR-w'],
            ['judul' => 'My 5 Best Songs from Billie Eilish', 'artis' => 'Billie Eilish', 'youtube_id' => 'mJT3ubUXF7E'],
            ['judul' => 'Top 10 Most Streamed Songs (Spotify)', 'artis' => 'Billie Eilish', 'youtube_id' => 'zTkAzhp10Hw'],
            ['judul' => 'Do You Ever Think of Me? (2026)', 'artis' => 'Billie Eilish', 'youtube_id' => 'YlnZQ33i22I'],
            ['judul' => 'I Don\'t Care (2026)', 'artis' => 'Billie Eilish', 'youtube_id' => 'bB1AsKeNPrU'],
            ['judul' => 'Taylor Swift Songs Playlist 2026', 'artis' => 'Taylor Swift', 'youtube_id' => 'SotYuDx_y84'],
            ['judul' => 'Top Songs Taylor Swift 2025 (Lyrics)', 'artis' => 'Taylor Swift', 'youtube_id' => 'MLMUaWnz5Og'],
            ['judul' => 'Greatest Hits Playlist 2026', 'artis' => 'Taylor Swift', 'youtube_id' => 'XmJkvQNfH1c'],
            ['judul' => 'Full Album Greatest Hits 2026', 'artis' => 'Taylor Swift', 'youtube_id' => 'yia1AfBAj5k'],
            ['judul' => 'Top Taylor Swift Songs Playlist 2026', 'artis' => 'Taylor Swift', 'youtube_id' => 'h8lpAY_xw4Q'],
            ['judul' => 'Billboard 2026 Top Hits', 'artis' => 'The Weeknd', 'youtube_id' => 'BRvKVvYO-n4'],
            ['judul' => 'Billboard Top 50 This Week', 'artis' => 'The Weeknd', 'youtube_id' => 'vaIn77n_Yb8'],
            ['judul' => 'NEW LOVE (Prod. LIZX)', 'artis' => 'The Weeknd', 'youtube_id' => 'R2tDkE-uHrY'],
            ['judul' => 'Open Hearts (Official Video)', 'artis' => 'The Weeknd', 'youtube_id' => 'Bn-3ICGjz0U'],
            ['judul' => 'Top Hits 2026', 'artis' => 'The Weeknd', 'youtube_id' => '0rYKbMjtnMo'],
        ];

        $songs = [];
        foreach ($youtubeList as $item) {
            $bahasa = 'Inggris';
            $songs[] = [
                'judul' => $item['judul'],
                'artis' => $item['artis'],
                'genre' => 'Pop',
                'bahasa' => $bahasa, 
                'durasi' => 240, 
                'diputar' => rand(500, 5000), 
                'file' => $item['youtube_id'],
                'url' => 'https://www.youtube.com/embed/' . $item['youtube_id'] . '?autoplay=1&controls=0&rel=0&showinfo=0&enablejsapi=1',
                'video_preview' => 'https://img.youtube.com/vi/' . $item['youtube_id'] . '/mqdefault.jpg',
                'cover' => 'https://img.youtube.com/vi/' . $item['youtube_id'] . '/mqdefault.jpg',
                'is_hls' => false, 
                'sprite_url' => null,
                'is_youtube' => true,
                'youtube_id' => $item['youtube_id']
            ];
        }

        return $songs;
    }

    public function lagu()
    {
        $katalogLagu = $this->getMasterLagu();
        return view('dashboard.admin.lagu', compact('katalogLagu'));
    }

    public function keuangan(Request $request)
    {
        // Default Filter: Month = Current Month
        $filterType = $request->get('filter_type', 'monthly');

        // Date Logic
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $laporanHarian = [];
        $totalPendapatanBulan = 0; // Or total for range
        
        // Determine Start and End for Data Generation
        if ($filterType === 'monthly') {
            $start = \Carbon\Carbon::parse($selectedMonth)->startOfMonth();
            $end = \Carbon\Carbon::parse($selectedMonth)->endOfMonth();
        } else {
            // Custom Range
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
        }

        // Generate Dummy Data Loop
        // We clone start to not affect the original variable if needed later
        $current = $start->copy();
        
        while ($current->lte($end)) {
            // Skip future dates
            if ($current->isFuture()) {
                $current->addDay();
                continue;
            }

            // Pseudo-random data
            $isWeekend = $current->isWeekend();
            $baseTrx = $isWeekend ? rand(30, 50) : rand(15, 30);
            $avgTicket = $isWeekend ? 350000 : 250000;
            
            $total = $baseTrx * $avgTicket;
            $pendapatanRuangan = $total * 0.7;
            $pendapatanFnb = $total * 0.25;
            $extend = $total * 0.05;

            $laporanHarian[] = [
                'tanggal' => $current->format('d M Y'),
                'raw_date' => $current->format('Y-m-d'),
                'jumlah_trx' => $baseTrx,
                'pendapatan_ruangan' => (int) $pendapatanRuangan,
                'pendapatan_fnb' => (int) $pendapatanFnb,
                'extend' => (int) $extend,
                'total' => (int) $total
            ];
            $totalPendapatanBulan += $total;

            $current->addDay();
        }

        // Reverse to show latest first in table
        $laporanHarian = array_reverse($laporanHarian);

        // 3. PREPARE CHART DATA
        // Prepare arrays for Chart.js (Chronological)
        $chartDataRaw = array_reverse($laporanHarian); 
        $chartLabels = collect($chartDataRaw)->pluck('tanggal')->all();
        $chartValues = collect($chartDataRaw)->pluck('total')->all();

        // 4. SUMMARY DATA
        // Calculate Growth (Real Formula: (Current - Previous) / Previous * 100)
        // Since we are using dummy data, we will simulate a "Previous Month Total" roughly close to current
        $previousTotal = ($totalPendapatanBulan > 0) ? $totalPendapatanBulan * rand(85, 115) / 100 : 0; 
        
        $growthPercentage = 0;
        if ($previousTotal > 0) {
            $growthPercentage = (($totalPendapatanBulan - $previousTotal) / $previousTotal) * 100;
        }

        $ringkasanKeuangan = [
            'hari_ini' => $laporanHarian[0]['total'] ?? 0, // Latest day in range
            'minggu_ini' => rand(50000000, 100000000), // Mock static
            'bulan_ini' => $totalPendapatanBulan, // Total of selected range/month
            'growth' => (int) $growthPercentage,
            'growth_is_positive' => $growthPercentage >= 0
        ];

        // 5. RIWAYAT BILLING (Mock based on range)
        // Ideally we should filter these by the range $startDate to $endDate
        // For mock purposes, let's just show some if they fall in range or just generic ones
        $allBills = [
            ['no_tagihan' => 'INV/20260211/001', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'ruangan' => 'VIP 01', 'tamu' => 'Bpk. Ahmad', 'kasir' => 'Sari', 'durasi' => '2 Jam', 'extend' => '1 Jam', 'total' => 450000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260211/002', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'ruangan' => 'Room 12', 'tamu' => 'Sdr. Kevin', 'kasir' => 'Sari', 'durasi' => '1 Jam', 'extend' => null, 'total' => 125000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260210/003', 'tanggal' => '10 Feb 2026', 'raw_date' => '2026-02-10', 'ruangan' => 'Room 05', 'tamu' => 'Sdr. Rizky', 'kasir' => 'Budi', 'durasi' => '3 Jam', 'extend' => null, 'total' => 750000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260209/004', 'tanggal' => '09 Feb 2026', 'raw_date' => '2026-02-09', 'ruangan' => 'VVIP 01', 'tamu' => 'Ibu Ratna', 'kasir' => 'Sari', 'durasi' => '4 Jam', 'extend' => '2 Jam', 'total' => 3500000, 'status' => 'Lunas'],
        ];

        $riwayatBilling = collect($allBills)->filter(function($bill) use ($chartDataRaw) {
             // Check if bill date is in the generated $laporanHarian (which represents the selected range)
             foreach($chartDataRaw as $day) {
                 if ($day['raw_date'] == $bill['raw_date']) return true;
             }
             return false;
        })->values()->all();

        return view('dashboard.admin.keuangan', compact(
            'ringkasanKeuangan', 
            'laporanHarian', 
            'riwayatBilling',
            'chartLabels',
            'chartValues',
            'filterType',
            'startDate',
            'endDate',
            'selectedMonth'
        ));
    }

    public function membership()
    {
        $members = [
            ['nama' => 'Bpk. Ahmad', 'nomor' => '081234567890', 'domisili' => 'Jakarta Selatan', 'poin' => 1500, 'tier' => 'Gold'],
            ['nama' => 'Ibu Maya', 'nomor' => '081987654321', 'domisili' => 'Bandung', 'poin' => 850, 'tier' => 'Silver'],
            ['nama' => 'Sdr. Kevin', 'nomor' => '082155556666', 'domisili' => 'Surabaya', 'poin' => 300, 'tier' => 'Bronze'],
            ['nama' => 'Sdr. Rizky', 'nomor' => '085711223344', 'domisili' => 'Jakarta Pusat', 'poin' => 2100, 'tier' => 'Platinum'],
            ['nama' => 'Ibu Susi', 'nomor' => '081399887766', 'domisili' => 'Bogor', 'poin' => 120, 'tier' => 'Bronze'],
        ];

        return view('dashboard.admin.membership', compact('members'));
    }

    public function akun()
    {
        $daftarUser = [
            [
                'nama' => 'Admin Utama', 
                'email' => 'admin@sgrt.com', 
                'password' => 'admin123',
                'role' => 'Admin', 
                'last_login' => '11 Feb 2026, 09:00', 
                'status' => 'Aktif',
                'ip_address' => '192.168.1.10',
                'features' => ['Ringkasan', 'Manajemen Ruangan', 'Manajemen Lagu', 'Food & Beverages', 'Keuangan', 'Riwayat Billing', 'Manajemen Akun', 'Membership', 'Pemesanan', 'Laporan']
            ],
            [
                'nama' => 'Sari Putri', 
                'email' => 'sari.kasir', 
                'password' => 'kasir456',
                'role' => 'Kasir', 
                'last_login' => '11 Feb 2026, 14:30', 
                'status' => 'Aktif',
                'features' => ['Ringkasan', 'Manajemen Ruangan', 'Food & Beverages', 'Membership', 'Pemesanan']
            ],
            [
                'nama' => 'Budi Santoso', 
                'email' => 'budi.op', 
                'password' => 'operator789',
                'role' => 'Operator', 
                'last_login' => '10 Feb 2026, 22:00', 
                'status' => 'Aktif',
                'features' => ['Ringkasan', 'Manajemen Lagu']
            ],
        ];

        return view('dashboard.admin.akun', compact('daftarUser'));
    }

    public function pemesanan()
    {
        $daftarRuangan = $this->getMasterRuangan();
        $menuItems = $this->getMenuItems();
        $paketHarga = [
            ['nama' => 'Paket Hemat 2 Jam', 'durasi' => '2 Jam', 'harga_weekday' => 90000, 'harga_weekend' => 130000],
            ['nama' => 'Paket Puas 3 Jam', 'durasi' => '3 Jam', 'harga_weekday' => 125000, 'harga_weekend' => 180000],
        ];

        return view('dashboard.admin.buat-pesanan', compact('daftarRuangan', 'menuItems', 'paketHarga'));
    }

    public function reservasiRoom()
    {
        $daftarBooking = [
            ['tamu' => 'Bpk. Ahmad', 'ruangan' => 'VIP 01', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'jam' => '19:00', 'durasi' => 2, 'kontak' => '08123456789', 'status' => 'Terkonfirmasi'],
            ['tamu' => 'Ibu Maya', 'ruangan' => 'Room 02', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'jam' => '20:30', 'durasi' => 3, 'kontak' => '08567890123', 'status' => 'Terkonfirmasi'],
            ['tamu' => 'Bpk. Gunawan', 'ruangan' => 'VIP Suite', 'tanggal' => '12 Feb 2026', 'raw_date' => '2026-02-12', 'jam' => '20:00', 'durasi' => 3, 'kontak' => '08123499999', 'status' => 'Menunggu'],
        ];

        return view('dashboard.admin.reservasi-room', compact('daftarBooking'));
    }

    private function getMenuItems() {
        return [
            // FOOD
            ['category' => 'Food', 'sub_category' => 'STARTERS', 'name' => 'Southwest Eggroll', 'price' => 17000, 'description' => 'Grilled Chicken + Corn + Black Beans + Microgreens + Chipotle Ranch', 'status' => 'Available', 'stock' => 50, 'image' => 'assets/img/pages/food-beverages/Untitled - 2025-06-16T112757_037.png'],
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

        return view('dashboard.admin.food-beverages', compact('menuItems'));
    }

    public function streamVideo(Request $request, $filename) {
        $path = public_path('assets/lagu/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        $size = filesize($path);
        $mime = 'video/mp4';
        $headers = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ];

        // Handle Range request for video seeking
        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);
            $start = intval($matches[1]);
            $end = isset($matches[2]) && $matches[2] !== '' ? intval($matches[2]) : $size - 1;

            if ($start > $end || $start >= $size) {
                return response('', 416, [
                    'Content-Range' => "bytes */$size",
                ]);
            }

            $length = $end - $start + 1;
            $headers['Content-Range'] = "bytes $start-$end/$size";
            $headers['Content-Length'] = $length;

            $stream = fopen($path, 'rb');
            fseek($stream, $start);
            $data = fread($stream, $length);
            fclose($stream);

            return response($data, 206, $headers);
        }

        // No Range header — serve full file
        $headers['Content-Length'] = $size;
        return response()->file($path, $headers);
    }
    public function laporan()
    {
        $laporan = [
            [
                'id' => 'RPT-001',
                'room' => 'VIP 01',
                'issue' => 'Video tidak bisa diputar',
                'description' => 'Layar blank saat memilih lagu "Bohemian Rhapsody"',
                'status' => 'Pending',
                'time' => now()->subMinutes(5)->format('H:i'),
                'reporter' => 'Bpk. Ahmad'
            ],
            [
                'id' => 'RPT-002',
                'room' => 'Room 104',
                'issue' => 'Audio pecah / tidak jernih',
                'description' => 'Suara speaker kiri kresek-kresek',
                'status' => 'Resolved',
                'time' => now()->subHours(1)->format('H:i'),
                'reporter' => 'Sdr. Rizky'
            ],
            [
                'id' => 'RPT-003',
                'room' => 'Room 106',
                'issue' => 'Lagu tidak sesuai judul',
                'description' => 'Judul "Kangen" tapi isinya "Pupus"',
                'status' => 'In Progress',
                'time' => now()->subHours(2)->format('H:i'),
                'reporter' => 'Bpk. Hendra'
            ],
        ];

        return view('dashboard.admin.laporan', compact('laporan'));
    }
    public function billing(Request $request)
    {
        // 1. Date Logic (Same as Keuangan)
        $filterType = $request->get('filter_type', 'monthly');
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        if ($filterType === 'monthly') {
            $start = \Carbon\Carbon::parse($selectedMonth)->startOfMonth();
            $end = \Carbon\Carbon::parse($selectedMonth)->endOfMonth();
        } else {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
        }

        // 2. Dummy Billing Data
        $allBills = [
            ['no_tagihan' => 'INV/20260211/001', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'ruangan' => 'VIP 01', 'tamu' => 'Bpk. Ahmad', 'kasir' => 'Sari', 'durasi' => '2 Jam', 'extend' => '1 Jam', 'total' => 450000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260211/002', 'tanggal' => '11 Feb 2026', 'raw_date' => '2026-02-11', 'ruangan' => 'Room 12', 'tamu' => 'Sdr. Kevin', 'kasir' => 'Sari', 'durasi' => '1 Jam', 'extend' => null, 'total' => 125000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260210/003', 'tanggal' => '10 Feb 2026', 'raw_date' => '2026-02-10', 'ruangan' => 'Room 05', 'tamu' => 'Sdr. Rizky', 'kasir' => 'Budi', 'durasi' => '3 Jam', 'extend' => null, 'total' => 750000, 'status' => 'Lunas'],
            ['no_tagihan' => 'INV/20260209/004', 'tanggal' => '09 Feb 2026', 'raw_date' => '2026-02-09', 'ruangan' => 'VVIP 01', 'tamu' => 'Ibu Ratna', 'kasir' => 'Sari', 'durasi' => '4 Jam', 'extend' => '2 Jam', 'total' => 3500000, 'status' => 'Lunas'],
            // Add more dummy as needed or just rely on these falling in range
        ];

        // 3. Filter Bills by Date Range
        $riwayatBilling = collect($allBills)->filter(function($bill) use ($start, $end) {
            $billDate = \Carbon\Carbon::parse($bill['raw_date']);
            return $billDate->betweenIncluded($start, $end);
        })->values()->all();

        return view('dashboard.admin.billing', compact(
            'riwayatBilling',
            'filterType',
            'selectedMonth',
            'startDate',
            'endDate'
        ));
    }

    private function getMasterKategori() {
        return [
            [
                'id' => 1,
                'name' => 'Indonesia',
                'slug' => 'indonesia',
                'description' => 'Jelajahi koleksi terbaik dari Indonesia. Pilihan terpopuler minggu ini khusus untuk menemani sesi karaoke Anda.',
                'banner_url' => '/assets/video_banner/indonesia.mp4',
                'banner_type' => 'video',
                'sub_categories' => ['Pop', 'Rock', 'Dangdut', 'Ballad', 'Jazz']
            ],
            [
                'id' => 2,
                'name' => 'Inggris',
                'slug' => 'inggris',
                'description' => 'Top hits international and billboard charts from around the world.',
                'banner_url' => '/assets/video_banner/english.mp4', 
                'banner_type' => 'video',
                'sub_categories' => ['Pop', 'R&B', 'Rock', 'Electronic', 'Country']
            ]
        ];
    }

    public function kategori()
    {
        $categories = $this->getMasterKategori();
        return view('dashboard.admin.kategori', compact('categories'));
    }

    public function activityLog()
    {
        $logAktivitas = [
            ['nama' => 'Admin Utama', 'aksi' => 'mengubah harga Paket Weekend VIP', 'waktu' => '5 menit yang lalu', 'ip' => '192.168.1.10'],
            ['nama' => 'Sari Putri', 'aksi' => 'melakukan check-in Room 05', 'waktu' => '12 menit yang lalu', 'ip' => '192.168.1.15'],
            ['nama' => 'Budi Santoso', 'aksi' => 'login ke sistem', 'waktu' => '14 menit yang lalu', 'ip' => '192.168.1.20'],
            ['nama' => 'Admin Utama', 'aksi' => 'menambahkan lagu baru "Birds of a Feather"', 'waktu' => '30 menit yang lalu', 'ip' => '192.168.1.10'],
            ['nama' => 'Sari Putri', 'aksi' => 'memproses pembayaran VIP 01 - Bpk. Ahmad (Rp 450.000)', 'waktu' => '45 menit yang lalu', 'ip' => '192.168.1.15'],
            ['nama' => 'Budi Santoso', 'aksi' => 'melakukan check-out Room 107', 'waktu' => '1 jam yang lalu', 'ip' => '192.168.1.20'],
            ['nama' => 'Admin Utama', 'aksi' => 'mengubah status menu "Kimchi Mac & Cheese" menjadi Habis', 'waktu' => '1 jam yang lalu', 'ip' => '192.168.1.10'],
            ['nama' => 'Sari Putri', 'aksi' => 'menambahkan member baru "Ibu Ratna" - Tier Gold', 'waktu' => '1 jam yang lalu', 'ip' => '192.168.1.15'],
            ['nama' => 'Admin Utama', 'aksi' => 'mengubah kapasitas Room 201 dari 6 menjadi 8 orang', 'waktu' => '2 jam yang lalu', 'ip' => '192.168.1.10'],
            ['nama' => 'Budi Santoso', 'aksi' => 'melakukan extend waktu Room 106 (+1 Jam)', 'waktu' => '2 jam yang lalu', 'ip' => '192.168.1.20'],
            ['nama' => 'Sari Putri', 'aksi' => 'memproses pesanan F&B Room 104 - 2x Chicken Wings, 1x Yuzu Margarita', 'waktu' => '2 jam yang lalu', 'ip' => '192.168.1.15'],
            ['nama' => 'Admin Utama', 'aksi' => 'menghapus lagu "Test Song" dari katalog', 'waktu' => '3 jam yang lalu', 'ip' => '192.168.1.10'],
            ['nama' => 'Budi Santoso', 'aksi' => 'melakukan check-in Suite 01 untuk Sdr. Kevin', 'waktu' => '3 jam yang lalu', 'ip' => '192.168.1.20'],
            ['nama' => 'Sari Putri', 'aksi' => 'mencetak struk billing INV/20260217/005', 'waktu' => '3 jam yang lalu', 'ip' => '192.168.1.15'],
            ['nama' => 'Admin Utama', 'aksi' => 'mengubah harga Room Regular Weekday menjadi Rp 55.000/jam', 'waktu' => '4 jam yang lalu', 'ip' => '192.168.1.10'],
        ];

        return view('dashboard.admin.activity-log', compact('logAktivitas'));
    }

    public function attendance()
    {
        return view('dashboard.admin.attendance');
    }

    public function manajemenAbsensi()
    {
        return view('dashboard.admin.manajemen-absensi');
    }
}
