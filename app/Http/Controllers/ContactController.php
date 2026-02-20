<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        // Placeholder for form submission logic
        // Validate request
        // Send email / Store in DB
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you shortly.');
    }
}
