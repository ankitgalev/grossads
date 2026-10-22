<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('advertiser.dashboard.index');
    }
}