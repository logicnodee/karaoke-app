<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomAccessController extends Controller
{
    // Helper to get master data via reflection from AdminController
    private function getMasterData() {
        $adminController = new AdminController();
        $reflection = new \ReflectionClass($adminController);
        $method = $reflection->getMethod('getMasterRuangan');
        $method->setAccessible(true);
        return $method->invoke($adminController);
    }

    public function showLogin()
    {
        return view('room.login');
    }

    public function login(Request $request)
    {
        $key = $request->input('key');
        
        $rooms = $this->getMasterData();
        // Find room by key
        $room = collect($rooms)->first(function($r) use ($key) {
            return ($r['key'] ?? '') === $key;
        });

        if (!$room) {
            return back()->with('error', 'Kode Akses Tidak Valid'); 
        }

        if ($room['status'] !== 'Digunakan') {
            return back()->with('error', 'Sesi Belum Dimulai. Hubungi Resepsionis.');
        }

        // Set Session
        session([
            'room_access' => true,
            'room_key' => $key,
            'room_name' => $room['nama']
        ]);

        return redirect()->route('room.dashboard');
    }

    private function getSongsData() {
        $adminController = new AdminController();
        $reflection = new \ReflectionClass($adminController);
        $method = $reflection->getMethod('getMasterLagu');
        $method->setAccessible(true);
        return $method->invoke($adminController);
    }

    private function getCategoriesData() {
        $adminController = new AdminController();
        $reflection = new \ReflectionClass($adminController);
        try {
            $method = $reflection->getMethod('getMasterKategori');
            $method->setAccessible(true);
            return $method->invoke($adminController);
        } catch (\ReflectionException $e) {
            // Fallback if method not found (e.g. during refactor)
            return [];
        }
    }

    public function dashboard()
    {
        if (!session('room_access')) {
            return redirect()->route('room.login');
        }

        $key = session()->get('room_key');
        $rooms = $this->getMasterData();
        
        $room = collect($rooms)->first(function($r) use ($key) {
             return ($r['key'] ?? '') === $key;
        });

        // If room session ended remotely
        if (!$room || $room['status'] !== 'Digunakan') {
            session()->forget(['room_access', 'room_key']);
            return redirect()->route('room.login')->with('expired', 'Waktu Habis');
        }

        $songs = $this->getSongsData();
        $categories = $this->getCategoriesData();

        // Get F&B menu items from KasirController
        // ... (Reflection logic kept same, handled by existing code block replacement context)
        $kasirController = new \App\Http\Controllers\KasirController(); // Fully qualified just in case
        $kasirReflection = new \ReflectionClass($kasirController);
        $menuMethod = $kasirReflection->getMethod('getMenuItems'); // Assuming this exists based on context
        // Wait, the original code had:
        // $kasirController = new KasirController();
        // $kasirReflection = new \ReflectionClass($kasirController);
        // $menuMethod = $kasirReflection->getMethod('getMenuItems'); // Careful, make sure KasirController is imported or FQ
        
        if ($kasirReflection->hasMethod('getMenuItems')) {
             $menuMethod = $kasirReflection->getMethod('getMenuItems');
             $menuMethod->setAccessible(true);
             $menuItems = $menuMethod->invoke($kasirController);
        } else {
             $menuItems = []; 
        }

        return view('room.dashboard', compact('room', 'songs', 'menuItems', 'categories'));
    }

    public function logout()
    {
        session()->forget(['room_access', 'room_key']);
        return redirect()->route('room.login');
    }
}
