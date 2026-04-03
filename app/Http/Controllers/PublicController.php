<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display the public fees page
     */
    public function fees()
    {
        $fees = Fee::where('is_public', true)
                   ->orWhere('visibility', 'public')
                   ->with('course')
                   ->get();
        
        if ($fees->isEmpty()) {
            // Try alternative approaches to fetch public fees
            $fees = Fee::all();
        }
        
        return view('public.fees', [
            'fees' => $fees,
            'title' => 'School Fees'
        ]);
    }

    /**
     * Display the public resources page
     */
    public function resources()
    {
        return view('public.resources', [
            'title' => 'Resources'
        ]);
    }

    /**
     * Display test skills page
     */
    public function skills()
    {
        return view('public.skills', [
            'title' => 'Test Skills'
        ]);
    }
}
