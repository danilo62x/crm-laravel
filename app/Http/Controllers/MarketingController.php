<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MarketingController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing', ['pageTitle' => null]);
    }

    public function contact(): View
    {
        return view('pages.contact', ['pageTitle' => __('Contato')]);
    }
}
