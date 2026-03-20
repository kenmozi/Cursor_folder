<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function dashboard()
    {
        $response = $this->api->getSubmissions();
        $submissions = $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];

        $stats = [
            'total' => count($submissions),
            'submitted' => count(array_filter($submissions, fn($s) => ($s['status'] ?? '') === 'submitted')),
            'under_review' => count(array_filter($submissions, fn($s) => ($s['status'] ?? '') === 'under_review')),
            'accepted' => count(array_filter($submissions, fn($s) => ($s['status'] ?? '') === 'accepted')),
        ];

        return view('dashboard.author.index', [
            'submissions' => array_slice($submissions, 0, 5),
            'stats' => $stats,
        ]);
    }

    public function index(Request $request)
    {
        $params = array_filter([
            'page' => $request->get('page', 1),
            'limit' => 15,
            'status' => $request->get('status'),
        ]);

        $response = $this->api->getSubmissions($params);
        $data = $response->successful() ? $response->json() : [];
        $submissions = $data['data'] ?? $data ?? [];
        $meta = $data['meta'] ?? [];

        return view('dashboard.author.submissions.index', [
            'submissions' => $submissions,
            'meta' => $meta,
        ]);
    }

    public function show(int $id)
    {
        $response = $this->api->getSubmission($id);

        if (!$response->successful()) {
            return redirect()->route('author.submissions')->with('error', 'Submission not found.');
        }

        $submission = $response->json('data') ?? $response->json();

        return view('dashboard.author.submissions.show', ['submission' => $submission]);
    }

    public function create()
    {
        $response = $this->api->getConferences(['limit' => 100]);
        $conferences = $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];

        return view('dashboard.author.submissions.create', ['conferences' => $conferences]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'conference_id' => 'required',
            'title' => 'required|string|max:500',
            'abstract' => 'required|string',
            'keywords' => 'nullable|string',
        ]);

        $data = $request->only('conference_id', 'title', 'abstract', 'keywords', 'track_id');

        // Handle authors
        if ($request->has('authors')) {
            $data['authors'] = $request->input('authors');
        }

        $response = $this->api->createSubmission($data);

        if ($response->successful()) {
            $submission = $response->json('data') ?? $response->json();
            $id = $submission['id'] ?? null;
            return redirect()->route('author.submissions.show', $id)->with('success', 'Submission created successfully.');
        }

        $error = $response->json('message') ?? 'Failed to create submission.';
        return back()->withErrors(['message' => $error])->withInput();
    }

    public function submit(int $id)
    {
        $response = $this->api->submitSubmission($id);

        if ($response->successful()) {
            return back()->with('success', 'Submission submitted successfully.');
        }

        $error = $response->json('message') ?? 'Failed to submit.';
        return back()->with('error', $error);
    }

    public function withdraw(int $id)
    {
        $response = $this->api->withdrawSubmission($id);

        if ($response->successful()) {
            return back()->with('success', 'Submission withdrawn.');
        }

        $error = $response->json('message') ?? 'Failed to withdraw.';
        return back()->with('error', $error);
    }

    public function uploadManuscript(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:51200',
        ]);

        $response = $this->api->uploadManuscript($id, $request->file('file'));

        if ($response->successful()) {
            return back()->with('success', 'Manuscript uploaded successfully.');
        }

        $error = $response->json('message') ?? 'Failed to upload manuscript.';
        return back()->with('error', $error);
    }

    public function deleteFile(int $id, int $fileId)
    {
        $response = $this->api->deleteFile($id, $fileId);

        if ($response->successful()) {
            return back()->with('success', 'File deleted.');
        }

        $error = $response->json('message') ?? 'Failed to delete file.';
        return back()->with('error', $error);
    }
}
