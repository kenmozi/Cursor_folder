<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $this->api->login($request->only('email', 'password'));

        if ($response->successful()) {
            $data = $response->json();
            session([
                'api_token' => $data['token'] ?? $data['data']['token'] ?? null,
                'api_user' => $data['user'] ?? $data['data']['user'] ?? null,
            ]);

            $intended = session('intended', '/dashboard/author');
            session()->forget('intended');

            return redirect($intended);
        }

        $error = $response->json('message') ?? 'Invalid credentials.';
        return back()->with('error', $error)->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'affiliation' => 'nullable',
            'country' => 'nullable',
            'password' => 'required|min:8|confirmed',
        ]);

        $response = $this->api->register($request->only('name', 'email', 'affiliation', 'country', 'password', 'password_confirmation'));

        if ($response->successful()) {
            $data = $response->json();
            session([
                'api_token' => $data['token'] ?? $data['data']['token'] ?? null,
                'api_user' => $data['user'] ?? $data['data']['user'] ?? null,
            ]);

            return redirect('/dashboard/author');
        }

        $error = $response->json('message') ?? 'Registration failed.';
        $errors = $response->json('errors') ?? [];

        return back()->withErrors(array_merge(['message' => $error], $errors))->withInput($request->except('password', 'password_confirmation'));
    }

    public function logout(Request $request)
    {
        try {
            $this->api->logout();
        } catch (\Exception $e) {
            // Ignore API errors on logout
        }

        session()->flush();

        return redirect('/login');
    }
}
