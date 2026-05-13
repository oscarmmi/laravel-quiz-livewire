<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application dashboard displaying public quizzes.
     */
    public function index(): View
    {
        $quizzes = Quiz::availableFor()
            ->has('questions')
            ->withCount('questions')
            ->get();
            
        return view('dashboard', compact('quizzes'));
    }
}
