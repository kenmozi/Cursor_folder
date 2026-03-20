<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function dashboard()
    {
        $response = $this->api->adminGetConferences(['limit' => 5]);
        $conferences = $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];

        return view('dashboard.admin.index', ['conferences' => $conferences]);
    }

    public function index(Request $request)
    {
        $params = array_filter([
            'page' => $request->get('page', 1),
            'limit' => 15,
            'status' => $request->get('status'),
        ]);

        $response = $this->api->adminGetConferences($params);
        $data = $response->successful() ? $response->json() : [];
        $conferences = $data['data'] ?? $data ?? [];
        $meta = $data['meta'] ?? [];

        return view('dashboard.admin.conferences.index', [
            'conferences' => $conferences,
            'meta' => $meta,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.conferences.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:100|regex:/^[a-z0-9\-]+$/',
            'title' => 'required|string|max:500',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'blind_mode' => 'required|in:double,single,open',
            'timezone' => 'required|string',
        ]);

        $data = $request->only([
            'slug', 'title', 'subtitle', 'description', 'blind_mode', 'timezone',
            'submission_open', 'submission_close', 'review_open', 'review_close',
            'notification_date', 'camera_ready_date',
        ]);

        $response = $this->api->adminCreateConference($data);

        if ($response->successful()) {
            $conference = $response->json('data') ?? $response->json();
            $slug = $conference['slug'] ?? $data['slug'];
            return redirect()->route('admin.conferences.show', $slug)->with('success', 'Conference created successfully.');
        }

        $error = $response->json('message') ?? 'Failed to create conference.';
        return back()->withErrors(['message' => $error])->withInput();
    }

    public function show(string $slug)
    {
        $response = $this->api->adminGetConference($slug);

        if (!$response->successful()) {
            return redirect()->route('admin.conferences')->with('error', 'Conference not found.');
        }

        $conference = $response->json('data') ?? $response->json();

        return view('dashboard.admin.conferences.show', ['conference' => $conference]);
    }

    public function edit(string $slug)
    {
        $response = $this->api->adminGetConference($slug);

        if (!$response->successful()) {
            return redirect()->route('admin.conferences')->with('error', 'Conference not found.');
        }

        $conference = $response->json('data') ?? $response->json();

        return view('dashboard.admin.conferences.edit', ['conference' => $conference]);
    }

    public function update(Request $request, string $slug)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'blind_mode' => 'required|in:double,single,open',
            'timezone' => 'required|string',
        ]);

        $data = $request->only([
            'title', 'subtitle', 'description', 'blind_mode', 'timezone',
            'submission_open', 'submission_close', 'review_open', 'review_close',
            'notification_date', 'camera_ready_date',
        ]);

        $response = $this->api->adminUpdateConference($slug, $data);

        if ($response->successful()) {
            return back()->with('success', 'Conference updated successfully.');
        }

        $error = $response->json('message') ?? 'Failed to update conference.';
        return back()->withErrors(['message' => $error])->withInput();
    }

    public function branding(string $slug)
    {
        $response = $this->api->adminGetConference($slug);
        $conference = $response->successful() ? ($response->json('data') ?? $response->json()) : [];

        return view('dashboard.admin.conferences.branding', ['conference' => $conference]);
    }

    public function uploadMedia(Request $request, string $slug)
    {
        $request->validate([
            'type' => 'required|in:logo,cover',
            'file' => 'required|file|image|max:5120',
        ]);

        $response = $this->api->adminUploadMedia($slug, $request->input('type'), $request->file('file'));

        if ($response->successful()) {
            return back()->with('success', 'Media uploaded successfully.');
        }

        $error = $response->json('message') ?? 'Failed to upload media.';
        return back()->with('error', $error);
    }

    public function deleteMedia(Request $request, string $slug, string $type)
    {
        $response = $this->api->adminDeleteMedia($slug, $type);

        if ($response->successful()) {
            return back()->with('success', 'Media deleted.');
        }

        $error = $response->json('message') ?? 'Failed to delete media.';
        return back()->with('error', $error);
    }

    public function dates(string $slug)
    {
        $response = $this->api->adminGetConference($slug);
        $conference = $response->successful() ? ($response->json('data') ?? $response->json()) : [];

        return view('dashboard.admin.conferences.dates', ['conference' => $conference]);
    }

    public function updateDates(Request $request, string $slug)
    {
        $data = $request->only([
            'submission_open', 'submission_close',
            'review_open', 'review_close',
            'notification_date', 'camera_ready_date',
        ]);

        $response = $this->api->adminUpdateConference($slug, $data);

        if ($response->successful()) {
            return back()->with('success', 'Dates updated successfully.');
        }

        $error = $response->json('message') ?? 'Failed to update dates.';
        return back()->with('error', $error);
    }

    public function committee(string $slug)
    {
        $response = $this->api->adminGetConference($slug);
        $conference = $response->successful() ? ($response->json('data') ?? $response->json()) : [];

        return view('dashboard.admin.conferences.committee', ['conference' => $conference]);
    }

    public function submissions(Request $request, string $slug)
    {
        $params = array_filter([
            'page' => $request->get('page', 1),
            'limit' => 20,
            'status' => $request->get('status'),
        ]);

        $response = $this->api->adminGetSubmissions($slug, $params);
        $data = $response->successful() ? $response->json() : [];
        $submissions = $data['data'] ?? $data ?? [];
        $meta = $data['meta'] ?? [];

        return view('dashboard.admin.conferences.submissions', [
            'slug' => $slug,
            'submissions' => $submissions,
            'meta' => $meta,
            'currentStatus' => $request->get('status'),
        ]);
    }

    public function showSubmission(string $slug, int $id)
    {
        $response = $this->api->adminGetSubmission($slug, $id);

        if (!$response->successful()) {
            return redirect()->route('admin.conferences.submissions', $slug)->with('error', 'Submission not found.');
        }

        $submission = $response->json('data') ?? $response->json();

        return view('dashboard.admin.conferences.submission-detail', [
            'slug' => $slug,
            'submission' => $submission,
        ]);
    }

    public function recordDecision(Request $request, string $slug, int $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected,revision_required',
            'note' => 'nullable|string',
            'notify' => 'nullable|boolean',
        ]);

        $data = $request->only('status', 'note', 'notify');

        $response = $this->api->adminRecordDecision($slug, $id, $data);

        if ($response->successful()) {
            return back()->with('success', 'Decision recorded successfully.');
        }

        $error = $response->json('message') ?? 'Failed to record decision.';
        return back()->with('error', $error);
    }

    public function reviewers(string $slug)
    {
        $reviewersResponse = $this->api->adminGetReviewers($slug);
        $invitationsResponse = $this->api->adminGetInvitations($slug);

        $reviewers = $reviewersResponse->successful() ? ($reviewersResponse->json('data') ?? $reviewersResponse->json() ?? []) : [];
        $invitations = $invitationsResponse->successful() ? ($invitationsResponse->json('data') ?? $invitationsResponse->json() ?? []) : [];

        return view('dashboard.admin.conferences.reviewers', [
            'slug' => $slug,
            'reviewers' => $reviewers,
            'invitations' => $invitations,
        ]);
    }

    public function sendInvitation(Request $request, string $slug)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $response = $this->api->adminSendInvitation($slug, $request->only('email', 'message'));

        if ($response->successful()) {
            return back()->with('success', 'Invitation sent successfully.');
        }

        $error = $response->json('message') ?? 'Failed to send invitation.';
        return back()->with('error', $error);
    }

    public function cancelInvitation(string $slug, int $invitationId)
    {
        $response = $this->api->adminCancelInvitation($slug, $invitationId);

        if ($response->successful()) {
            return back()->with('success', 'Invitation cancelled.');
        }

        $error = $response->json('message') ?? 'Failed to cancel invitation.';
        return back()->with('error', $error);
    }

    public function assignments(Request $request, string $slug)
    {
        $assignmentsResponse = $this->api->adminGetAssignments($slug, ['page' => $request->get('page', 1), 'limit' => 20]);
        $submissionsResponse = $this->api->adminGetSubmissions($slug, ['limit' => 200]);
        $reviewersResponse = $this->api->adminGetReviewers($slug);

        $assignments = $assignmentsResponse->successful() ? ($assignmentsResponse->json('data') ?? $assignmentsResponse->json() ?? []) : [];
        $submissions = $submissionsResponse->successful() ? ($submissionsResponse->json('data') ?? $submissionsResponse->json() ?? []) : [];
        $reviewers = $reviewersResponse->successful() ? ($reviewersResponse->json('data') ?? $reviewersResponse->json() ?? []) : [];

        return view('dashboard.admin.conferences.assignments', [
            'slug' => $slug,
            'assignments' => $assignments,
            'submissions' => $submissions,
            'reviewers' => $reviewers,
        ]);
    }

    public function assignReviewer(Request $request, string $slug)
    {
        $request->validate([
            'submission_id' => 'required|integer',
            'reviewer_id' => 'required|integer',
            'due_date' => 'nullable|date',
        ]);

        $response = $this->api->adminAssignReviewer($slug, $request->input('submission_id'), $request->only('reviewer_id', 'due_date'));

        if ($response->successful()) {
            return back()->with('success', 'Reviewer assigned successfully.');
        }

        $error = $response->json('message') ?? 'Failed to assign reviewer.';
        return back()->with('error', $error);
    }

    public function unassignReviewer(string $slug, int $submissionId, int $assignmentId)
    {
        $response = $this->api->adminUnassignReviewer($slug, $submissionId, $assignmentId);

        if ($response->successful()) {
            return back()->with('success', 'Reviewer unassigned.');
        }

        $error = $response->json('message') ?? 'Failed to unassign reviewer.';
        return back()->with('error', $error);
    }
}
