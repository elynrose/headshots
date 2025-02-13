<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Fal;

class HomeController
{
    public function index()
    {
        $fals= Fal::get();
        return view('frontend.home', compact('fals'));
    }
}
