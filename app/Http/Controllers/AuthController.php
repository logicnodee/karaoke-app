<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $credentials = [
        'admin' => ['password' => 'admin', 'role' => 'admin', 'name' => 'Administrator'],

        'operator' => ['password' => 'operator', 'role' => 'operator', 'name' => 'Staf Operator'],
        'kasir' => ['password' => 'kasir', 'role' => 'kasir', 'name' => 'Kasir / Billing'],
    ];

    public function showLogin()
    {
        if (session('auth_role')) {
            if (session('auth_role') === 'admin') return redirect()->route('admin.ringkasan');
            if (session('auth_role') === 'kasir') return redirect()->route('kasir.dashboard');
            return redirect()->route('dashboard.' . session('auth_role'));
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (isset($this->credentials[$email]) && $this->credentials[$email]['password'] === $password) {
            $user = $this->credentials[$email];
            session([
                'auth_role' => $user['role'],
                'auth_name' => $user['name'],
                'auth_email' => $email,
            ]);

            if ($user['role'] === 'admin') return redirect()->route('admin.ringkasan');
            if ($user['role'] === 'kasir') return redirect()->route('kasir.dashboard');
            return redirect()->route('dashboard.' . $user['role']);
        }

        return back()->withErrors(['login' => 'Email atau password salah.'])->withInput();
    }

    public function logout()
    {
        session()->forget(['auth_role', 'auth_name', 'auth_email']);
        return redirect()->route('login');
    }



    public function operatorDashboard()
    {
        if (session('auth_role') !== 'operator') return redirect()->route('login');

        // Reuse Admin Controller's logic for consistency (in a real app, this would be a Service class)
        // For now, call the same private method by making it temporarily public or copying it.
        // Or better, let's just use the AdminController instance or static method if possible, 
        // but since they are in the same file/logic group in this dummy setup, I'll copy the data structure 
        // or instantiate AdminController.
        
        // Simulating the same data source:
        $adminController = new AdminController();
        // Reflection to access private method for simulation
        $reflection = new \ReflectionClass($adminController);
        $method = $reflection->getMethod('getMasterRuangan');
        $method->setAccessible(true);
        $daftarRuangan = $method->invoke($adminController);

        return view('dashboard.operator', compact('daftarRuangan'));
    }
}
