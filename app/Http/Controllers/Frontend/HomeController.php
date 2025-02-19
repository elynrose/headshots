<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Fal;
use App\Models\Generate;
use App\Http\Controllers\Controller;


class HomeController
{
    public function index()
    {
       
        $generates = Generate::with(['train', 'user'])
        ->where('parent', null)
        ->orderBy('id', 'desc')->paginate(9);

        $fals = Fal::get();

        return view('frontend.generates.index', compact('generates', 'fals'));
    }
}
