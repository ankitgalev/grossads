<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'publisher') {
            return redirect()->route('publisher.dashboard');
        }

        if ($role === 'advertiser') {
            return redirect()->route('advertiser.dashboard');
        }

        auth()->logout();

        return redirect()->route('login');
    }
}