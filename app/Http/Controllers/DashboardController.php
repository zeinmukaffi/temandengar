<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard Pencerita.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
        ]);
    }
}