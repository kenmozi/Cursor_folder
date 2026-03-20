<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('api_token')) {
            session(['intended' => $request->url()]);
            return redirect('/login');
        }

        // Verify token is still valid
        try {
            $api = new ApiService();
            $response = $api->me();
            if ($response->status() === 401) {
                session()->flush();
                return redirect('/login')->with('error', 'Your session has expired. Please log in again.');
            }
        } catch (\Exception $e) {
            // If API is unreachable, allow through (fail open)
        }

        return $next($request);
    }
}
