<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function dashboard()
    {
        $response = $this->api->getAssignments();
        $assignments = $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];

        $stats = [
            'pending' => count(array_filter($assignments, fn($a) => ($a['status'] ?? '') === 'pending')),
            'in_progress' => count(array_filter($assignments, fn($a) => in_array($a['status'] ?? '', ['accepted', 'in_progress']))),
            'completed' => count(array_filter($assignments, fn($a) => ($a['status'] ?? '') === 'completed')),
        ];

        return view('dashboard.reviewer.index', [
            'assignments' => $assignments,
            'stats' => $stats,
        ]);
    }

    public function show(int $id)
    {
        $response = $this->api->getAssignment($id);

        if (!$response->successful()) {
            return redirect()->route('reviewer.dashboard')->with('error', 'Assignment not found.');
        }

        $assignment = $response->json('data') ?? $response->json();
        $review = null;

        $status = $assignment['status'] ?? '';
        if (in_array($status, ['accepted', 'in_progress', 'completed'])) {
            $reviewResponse = $this->api->getReview($id);
            if ($reviewResponse->successful()) {
                $review = $reviewResponse->json('data') ?? $reviewResponse->json();
            }
        }

        return view('dashboard.reviewer.assignments.show', [
            'assignment' => $assignment,
            'review' => $review,
        ]);
    }

    public function respond(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:accept,decline',
            'reason' => 'nullable|string',
        ]);

        $response = $this->api->respondToAssignment($id, $request->input('action'), $request->input('reason'));

        if ($response->successful()) {
            $action = $request->input('action') === 'accept' ? 'accepted' : 'declined';
            return back()->with('success', "Assignment {$action} successfully.");
        }

        $error = $response->json('message') ?? 'Failed to respond to assignment.';
        return back()->with('error', $error);
    }

    public function saveReview(Request $request, int $id)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'recommendation' => 'required|string',
            'comments' => 'required|string',
            'confidential_comments' => 'nullable|string',
        ]);

        $data = $request->only('score', 'recommendation', 'comments', 'confidential_comments');
        $submit = $request->input('action') === 'submit';

        $response = $this->api->saveReview($id, $data, $submit);

        if ($response->successful()) {
            $message = $submit ? 'Review submitted successfully.' : 'Review saved as draft.';
            return back()->with('success', $message);
        }

        $error = $response->json('message') ?? 'Failed to save review.';
        return back()->withErrors(['message' => $error])->withInput();
    }
}
