<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Public\ConferenceController as PublicConferenceController;
use App\Http\Controllers\Api\Public\InvitationController;
use App\Http\Controllers\Api\Author\SubmissionController;
use App\Http\Controllers\Api\Author\SubmissionFileController;
use App\Http\Controllers\Api\Reviewer\ReviewController;
use App\Http\Controllers\Api\Admin\ConferenceController as AdminConferenceController;
use App\Http\Controllers\Api\Admin\ConferenceContentController;
use App\Http\Controllers\Api\Admin\ConferenceMediaController;
use App\Http\Controllers\Api\Admin\ReviewerController;
use App\Http\Controllers\Api\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Api\Admin\AssignmentController;
use App\Http\Controllers\Api\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Public (no auth) ──────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login',    [AuthController::class, 'login']);

    // Public conference listing
    Route::get('conferences',          [PublicConferenceController::class, 'index']);
    Route::get('conferences/{conference}', [PublicConferenceController::class, 'show']);

    // Reviewer invitation (token-based)
    Route::get('invitations/{token}',         [InvitationController::class, 'show']);
    Route::post('invitations/{token}/decline', [InvitationController::class, 'decline']);

    // ── Authenticated ──────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth utilities
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);

        // Accept invitation (must be logged in)
        Route::post('invitations/{token}/accept', [InvitationController::class, 'accept']);

        // ── Author: Submissions ────────────────────────────────────────────
        Route::prefix('submissions')->group(function () {
            Route::get('/',          [SubmissionController::class, 'index']);
            Route::post('/',         [SubmissionController::class, 'store']);
            Route::get('/{submission}',       [SubmissionController::class, 'show']);
            Route::put('/{submission}',       [SubmissionController::class, 'update']);
            Route::post('/{submission}/submit',   [SubmissionController::class, 'submit']);
            Route::post('/{submission}/withdraw',  [SubmissionController::class, 'withdraw']);

            // Files
            Route::post('/{submission}/files/manuscript',     [SubmissionFileController::class, 'storeManuscript']);
            Route::post('/{submission}/files/supplementary',  [SubmissionFileController::class, 'storeSupplementary']);
            Route::delete('/{submission}/files/{file}',       [SubmissionFileController::class, 'destroy']);
            Route::get('/{submission}/files/{file}/download', [SubmissionFileController::class, 'download']);
        });

        // ── Reviewer ───────────────────────────────────────────────────────
        Route::prefix('reviewer')->group(function () {
            Route::get('assignments',              [ReviewController::class, 'assignments']);
            Route::get('assignments/{assignment}', [ReviewController::class, 'showAssignment']);
            Route::post('assignments/{assignment}/respond', [ReviewController::class, 'respondToAssignment']);

            Route::post('assignments/{assignment}/review', [ReviewController::class, 'storeReview']);
            Route::get('assignments/{assignment}/review',  [ReviewController::class, 'showReview']);
        });

        // ── Admin: Conference Management ────────────────────────────────────
        Route::prefix('admin')->group(function () {

            // Conferences
            Route::get('conferences',              [AdminConferenceController::class, 'index']);
            Route::post('conferences',             [AdminConferenceController::class, 'store']);
            Route::get('conferences/{conference}', [AdminConferenceController::class, 'show']);
            Route::put('conferences/{conference}', [AdminConferenceController::class, 'update']);

            // Conference content / translations
            Route::get('conferences/{conference}/content',                [ConferenceContentController::class, 'index']);
            Route::put('conferences/{conference}/content/{locale}',       [ConferenceContentController::class, 'upsert']);
            Route::delete('conferences/{conference}/content/{locale}',    [ConferenceContentController::class, 'destroy']);

            // Conference media
            Route::post('conferences/{conference}/media',         [ConferenceMediaController::class, 'store']);
            Route::delete('conferences/{conference}/media/{type}', [ConferenceMediaController::class, 'destroy']);

            // Reviewer invitations
            Route::get('conferences/{conference}/invitations',         [ReviewerController::class, 'invitations']);
            Route::post('conferences/{conference}/invitations',        [ReviewerController::class, 'invite']);
            Route::delete('conferences/{conference}/invitations/{invitation}', [ReviewerController::class, 'cancel']);

            // Reviewer roster
            Route::get('conferences/{conference}/reviewers',            [ReviewerController::class, 'reviewers']);
            Route::delete('conferences/{conference}/reviewers/{userId}', [ReviewerController::class, 'removeReviewer']);

            // Submissions (admin view)
            Route::get('conferences/{conference}/submissions',                              [AdminSubmissionController::class, 'index']);
            Route::get('conferences/{conference}/submissions/{submission}',                 [AdminSubmissionController::class, 'show']);
            Route::post('conferences/{conference}/submissions/{submission}/decision',        [AdminSubmissionController::class, 'decision']);

            // Assignments
            Route::get('conferences/{conference}/assignments',                                    [AssignmentController::class, 'index']);
            Route::post('conferences/{conference}/submissions/{submission}/assignments',           [AssignmentController::class, 'store']);
            Route::delete('conferences/{conference}/submissions/{submission}/assignments/{assignment}', [AssignmentController::class, 'destroy']);
        });

        // ── Super Admin ─────────────────────────────────────────────────────
        Route::prefix('super-admin')->middleware('super_admin')->group(function () {
            Route::get('users',                  [UserController::class, 'index']);
            Route::get('users/{user}',           [UserController::class, 'show']);
            Route::get('users/{user}/roles',     [UserController::class, 'roles']);
            Route::patch('users/{user}/super-admin', [UserController::class, 'setSuperAdmin']);
            Route::delete('users/{user}',        [UserController::class, 'destroy']);
        });
    });
});
