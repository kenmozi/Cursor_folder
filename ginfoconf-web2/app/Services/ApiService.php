<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ApiService
{
    private string $baseUrl;
    private ?string $token;
    private string $locale;

    public function __construct()
    {
        $this->baseUrl = config('api.url');
        $this->token = session('api_token');
        $this->locale = session('locale', 'en');
    }

    private function client()
    {
        $client = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->withQueryParameters(['locale' => $this->locale]);
        if ($this->token) {
            $client = $client->withToken($this->token);
        }
        return $client;
    }

    // Auth
    public function register(array $data): Response
    {
        return $this->client()->post('/auth/register', $data);
    }

    public function login(array $data): Response
    {
        return $this->client()->post('/auth/login', $data);
    }

    public function logout(): Response
    {
        return $this->client()->post('/auth/logout');
    }

    public function me(): Response
    {
        return $this->client()->get('/auth/me');
    }

    // Public
    public function getConferences(array $params = []): Response
    {
        return $this->client()->get('/conferences', $params);
    }

    public function getConference(string $slug): Response
    {
        return $this->client()->get("/conferences/{$slug}");
    }

    public function getInvitation(string $token): Response
    {
        return $this->client()->get("/invitations/{$token}");
    }

    public function acceptInvitation(string $token): Response
    {
        return $this->client()->post("/invitations/{$token}/accept");
    }

    public function declineInvitation(string $token, ?string $reason = null): Response
    {
        return $this->client()->post("/invitations/{$token}/decline", ['reason' => $reason]);
    }

    // Author
    public function getSubmissions(array $params = []): Response
    {
        return $this->client()->get('/submissions', $params);
    }

    public function getSubmission(int $id): Response
    {
        return $this->client()->get("/submissions/{$id}");
    }

    public function createSubmission(array $data): Response
    {
        return $this->client()->post('/submissions', $data);
    }

    public function updateSubmission(int $id, array $data): Response
    {
        return $this->client()->put("/submissions/{$id}", $data);
    }

    public function submitSubmission(int $id): Response
    {
        return $this->client()->post("/submissions/{$id}/submit");
    }

    public function withdrawSubmission(int $id): Response
    {
        return $this->client()->post("/submissions/{$id}/withdraw");
    }

    public function uploadManuscript(int $id, $file): Response
    {
        return Http::baseUrl($this->baseUrl)->withToken($this->token)
            ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
            ->post("/submissions/{$id}/files/manuscript");
    }

    public function deleteFile(int $submissionId, int $fileId): Response
    {
        return $this->client()->delete("/submissions/{$submissionId}/files/{$fileId}");
    }

    // Reviewer
    public function getAssignments(array $params = []): Response
    {
        return $this->client()->get('/reviewer/assignments', $params);
    }

    public function getAssignment(int $id): Response
    {
        return $this->client()->get("/reviewer/assignments/{$id}");
    }

    public function respondToAssignment(int $id, string $action, ?string $reason = null): Response
    {
        return $this->client()->post("/reviewer/assignments/{$id}/respond", ['action' => $action, 'reason' => $reason]);
    }

    public function getReview(int $assignmentId): Response
    {
        return $this->client()->get("/reviewer/assignments/{$assignmentId}/review");
    }

    public function saveReview(int $assignmentId, array $data, bool $submit = false): Response
    {
        return $this->client()->post("/reviewer/assignments/{$assignmentId}/review", array_merge($data, ['submit' => $submit]));
    }

    // Admin
    public function adminGetConferences(array $params = []): Response
    {
        return $this->client()->get('/admin/conferences', $params);
    }

    public function adminGetConference(string $slug): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}");
    }

    public function adminCreateConference(array $data): Response
    {
        return $this->client()->post('/admin/conferences', $data);
    }

    public function adminUpdateConference(string $slug, array $data): Response
    {
        return $this->client()->put("/admin/conferences/{$slug}", $data);
    }

    public function adminUpsertContent(string $slug, string $locale, array $data): Response
    {
        return $this->client()->put("/admin/conferences/{$slug}/content/{$locale}", $data);
    }

    public function adminUploadMedia(string $slug, string $type, $file): Response
    {
        return Http::baseUrl($this->baseUrl)->withToken($this->token)
            ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
            ->post("/admin/conferences/{$slug}/media", ['type' => $type]);
    }

    public function adminDeleteMedia(string $slug, string $type): Response
    {
        return $this->client()->delete("/admin/conferences/{$slug}/media/{$type}");
    }

    public function adminGetReviewers(string $slug): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}/reviewers");
    }

    public function adminGetInvitations(string $slug): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}/invitations");
    }

    public function adminSendInvitation(string $slug, array $data): Response
    {
        return $this->client()->post("/admin/conferences/{$slug}/invitations", $data);
    }

    public function adminCancelInvitation(string $slug, int $invitationId): Response
    {
        return $this->client()->delete("/admin/conferences/{$slug}/invitations/{$invitationId}");
    }

    public function adminGetSubmissions(string $slug, array $params = []): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}/submissions", $params);
    }

    public function adminGetSubmission(string $slug, int $id): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}/submissions/{$id}");
    }

    public function adminRecordDecision(string $slug, int $id, array $data): Response
    {
        return $this->client()->post("/admin/conferences/{$slug}/submissions/{$id}/decision", $data);
    }

    public function adminGetAssignments(string $slug, array $params = []): Response
    {
        return $this->client()->get("/admin/conferences/{$slug}/assignments", $params);
    }

    public function adminAssignReviewer(string $slug, int $submissionId, array $data): Response
    {
        return $this->client()->post("/admin/conferences/{$slug}/submissions/{$submissionId}/assignments", $data);
    }

    public function adminUnassignReviewer(string $slug, int $submissionId, int $assignmentId): Response
    {
        return $this->client()->delete("/admin/conferences/{$slug}/submissions/{$submissionId}/assignments/{$assignmentId}");
    }
}
