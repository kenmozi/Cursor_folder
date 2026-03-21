<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Author\SubmissionController;
use App\Http\Controllers\Reviewer\AssignmentController;
use App\Http\Controllers\Admin\ConferenceController as AdminConferenceController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest.api')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Public routes (no auth needed)
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/conferences', [PublicController::class, 'conferences'])->name('conferences');
Route::get('/conferences/{slug}', [PublicController::class, 'conference'])->name('conference');

// Auth routes
Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Author
    Route::prefix('dashboard/author')->group(function () {
        Route::get('/', [SubmissionController::class, 'dashboard'])->name('author.dashboard');
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('author.submissions');
        Route::get('/submissions/new', [SubmissionController::class, 'create'])->name('author.submissions.create');
        Route::post('/submissions', [SubmissionController::class, 'store'])->name('author.submissions.store');
        Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('author.submissions.show');
        Route::post('/submissions/{id}/submit', [SubmissionController::class, 'submit'])->name('author.submissions.submit');
        Route::post('/submissions/{id}/withdraw', [SubmissionController::class, 'withdraw'])->name('author.submissions.withdraw');
        Route::post('/submissions/{id}/files/manuscript', [SubmissionController::class, 'uploadManuscript'])->name('author.submissions.upload');
        Route::delete('/submissions/{id}/files/{fileId}', [SubmissionController::class, 'deleteFile'])->name('author.submissions.file.delete');
    });

    // Reviewer
    Route::prefix('dashboard/reviewer')->group(function () {
        Route::get('/', [AssignmentController::class, 'dashboard'])->name('reviewer.dashboard');
        Route::get('/assignments/{id}', [AssignmentController::class, 'show'])->name('reviewer.assignments.show');
        Route::post('/assignments/{id}/respond', [AssignmentController::class, 'respond'])->name('reviewer.assignments.respond');
        Route::post('/assignments/{id}/review', [AssignmentController::class, 'saveReview'])->name('reviewer.assignments.review');
    });

    // Admin
    Route::prefix('dashboard/admin')->group(function () {
        Route::get('/', [AdminConferenceController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/conferences', [AdminConferenceController::class, 'index'])->name('admin.conferences');
        Route::get('/conferences/new', [AdminConferenceController::class, 'create'])->name('admin.conferences.create');
        Route::post('/conferences', [AdminConferenceController::class, 'store'])->name('admin.conferences.store');
        Route::get('/conferences/{slug}', [AdminConferenceController::class, 'show'])->name('admin.conferences.show');
        Route::get('/conferences/{slug}/edit', [AdminConferenceController::class, 'edit'])->name('admin.conferences.edit');
        Route::put('/conferences/{slug}', [AdminConferenceController::class, 'update'])->name('admin.conferences.update');
        Route::get('/conferences/{slug}/branding', [AdminConferenceController::class, 'branding'])->name('admin.conferences.branding');
        Route::post('/conferences/{slug}/media', [AdminConferenceController::class, 'uploadMedia'])->name('admin.conferences.media.upload');
        Route::delete('/conferences/{slug}/media/{type}', [AdminConferenceController::class, 'deleteMedia'])->name('admin.conferences.media.delete');
        Route::post('/conferences/{slug}/gallery', [AdminConferenceController::class, 'uploadGallery'])->name('admin.conferences.gallery.upload');
        Route::delete('/conferences/{slug}/gallery/{mediaId}', [AdminConferenceController::class, 'deleteGalleryItem'])->name('admin.conferences.gallery.delete');
        Route::get('/conferences/{slug}/dates', [AdminConferenceController::class, 'dates'])->name('admin.conferences.dates');
        Route::put('/conferences/{slug}/dates', [AdminConferenceController::class, 'updateDates'])->name('admin.conferences.dates.update');
        Route::get('/conferences/{slug}/tracks', [AdminConferenceController::class, 'tracks'])->name('admin.conferences.tracks');
        Route::post('/conferences/{slug}/tracks', [AdminConferenceController::class, 'storeTrack'])->name('admin.conferences.tracks.store');
        Route::put('/conferences/{slug}/tracks/{trackId}', [AdminConferenceController::class, 'updateTrack'])->name('admin.conferences.tracks.update');
        Route::delete('/conferences/{slug}/tracks/{trackId}', [AdminConferenceController::class, 'deleteTrack'])->name('admin.conferences.tracks.delete');
        Route::get('/conferences/{slug}/committee', [AdminConferenceController::class, 'committee'])->name('admin.conferences.committee');
        Route::post('/conferences/{slug}/committee', [AdminConferenceController::class, 'addCommitteeMember'])->name('admin.conferences.committee.add');
        Route::delete('/conferences/{slug}/committee/{memberId}', [AdminConferenceController::class, 'deleteCommitteeMember'])->name('admin.conferences.committee.delete');
        Route::get('/conferences/{slug}/submissions', [AdminConferenceController::class, 'submissions'])->name('admin.conferences.submissions');
        Route::get('/conferences/{slug}/submissions/{id}', [AdminConferenceController::class, 'showSubmission'])->name('admin.conferences.submissions.show');
        Route::post('/conferences/{slug}/submissions/{id}/decision', [AdminConferenceController::class, 'recordDecision'])->name('admin.conferences.submissions.decision');
        Route::get('/conferences/{slug}/reviewers', [AdminConferenceController::class, 'reviewers'])->name('admin.conferences.reviewers');
        Route::post('/conferences/{slug}/invitations', [AdminConferenceController::class, 'sendInvitation'])->name('admin.conferences.invitations.send');
        Route::delete('/conferences/{slug}/invitations/{invitationId}', [AdminConferenceController::class, 'cancelInvitation'])->name('admin.conferences.invitations.cancel');
        Route::get('/conferences/{slug}/assignments', [AdminConferenceController::class, 'assignments'])->name('admin.conferences.assignments');
        Route::post('/conferences/{slug}/assignments', [AdminConferenceController::class, 'assignReviewer'])->name('admin.conferences.assignments.assign');
        Route::delete('/conferences/{slug}/submissions/{submissionId}/assignments/{assignmentId}', [AdminConferenceController::class, 'unassignReviewer'])->name('admin.conferences.assignments.unassign');
    });
});
