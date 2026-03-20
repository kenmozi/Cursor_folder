<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function home()
    {
        $response = $this->api->getConferences(['limit' => 6]);
        $conferences = $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];

        return view('home', ['conferences' => $conferences]);
    }

    public function conferences(Request $request)
    {
        $params = array_filter([
            'page' => $request->get('page', 1),
            'limit' => 12,
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ]);

        $response = $this->api->getConferences($params);
        $data = $response->successful() ? $response->json() : [];
        $conferences = $data['data'] ?? $data ?? [];
        $meta = $data['meta'] ?? [];

        return view('conferences.index', [
            'conferences' => $conferences,
            'meta' => $meta,
        ]);
    }

    public function conference(string $slug)
    {
        $response = $this->api->getConference($slug);

        if (!$response->successful()) {
            abort(404);
        }

        $conference = $response->json('data') ?? $response->json();

        return view('conferences.show', ['conference' => $conference]);
    }
}
