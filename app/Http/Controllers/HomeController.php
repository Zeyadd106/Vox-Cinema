<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $currentMovies = Movie::where('status', 'current')->get();
        return view('home', compact('currentMovies'));
    }

    /**
     * Display the coming soon page.
     */
    public function comingSoon()
    {
        $comingSoonMovies = Movie::where('status', 'coming_soon')->get();
        return view('coming-soon', compact('comingSoonMovies'));
    }
}
