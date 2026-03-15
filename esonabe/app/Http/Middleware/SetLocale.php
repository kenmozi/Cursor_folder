<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } elseif (Auth::check() && Auth::user()->locale) {
            App::setLocale(Auth::user()->locale);
        } else {
            App::setLocale(config('app.locale', 'fr'));
        }

        return $next($request);
    }
}
