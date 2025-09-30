<?php

namespace App\Http\Controllers;

use App\Livewire\PublicDisplay;
use Illuminate\View\View;

class DisplayHandler extends Controller
{
    public function index(): View
    {
        return view('display.index');
    }

    public function livewire()
    {
        return app(PublicDisplay::class);
    }
}
