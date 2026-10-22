<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(
        Request $request,
        $user
    ) {
        /*
        |--------------------------------------------------------------------------
        | Inactive Account
        |--------------------------------------------------------------------------
        */

        if (!$user->isActive()) {

            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your account is inactive.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Based Dashboard
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'publisher') {
            return redirect()->route('publisher.dashboard');
        }

        if ($user->role === 'advertiser') {
            return redirect()->route('advertiser.dashboard');
        }

        auth()->logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Invalid account role.'
            ]);
    }
}