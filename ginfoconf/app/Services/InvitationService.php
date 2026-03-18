<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\Conference;
use App\Models\ReviewerInvitation;
use App\Models\User;
use App\Notifications\ReviewerInvitedNotification;
use Illuminate\Support\Str;

class InvitationService
{
    /**
     * Send a reviewer invitation. Creates a ConferenceRole for existing users upon acceptance.
     * No duplicate pending invitation is allowed per email per conference.
     */
    public function invite(Conference $conference, User $inviter, string $email, ?string $message = null): ReviewerInvitation
    {
        // Prevent duplicate pending invitations
        $existing = ReviewerInvitation::where('conference_id', $conference->id)
            ->where('email', $email)
            ->whereIn('status', [InvitationStatus::Pending->value])
            ->first();

        if ($existing && !$existing->isExpired()) {
            abort(409, 'A pending invitation already exists for this email.');
        }

        $user       = User::where('email', $email)->first();
        $invitation = ReviewerInvitation::create([
            'conference_id' => $conference->id,
            'inviter_id'    => $inviter->id,
            'user_id'       => $user?->id,
            'email'         => $email,
            'token'         => Str::random(64),
            'status'        => InvitationStatus::Pending,
            'message'       => $message,
            'expires_at'    => now()->addDays(14),
        ]);

        // Notify via email
        if ($user) {
            $user->notify(new ReviewerInvitedNotification($invitation));
        } else {
            // Use on-demand notification for non-registered users
            \Illuminate\Support\Facades\Notification::route('mail', $email)
                ->notify(new ReviewerInvitedNotification($invitation));
        }

        return $invitation;
    }

    /**
     * Accept an invitation. Creates the reviewer ConferenceRole.
     * The user MUST be authenticated to accept.
     */
    public function accept(ReviewerInvitation $invitation, User $user): ReviewerInvitation
    {
        abort_unless($invitation->isPending(), 422, 'Invitation is no longer valid.');

        $invitation->update([
            'status'       => InvitationStatus::Accepted,
            'user_id'      => $user->id,
            'responded_at' => now(),
        ]);

        // Assign reviewer role in conference (idempotent)
        \App\Models\ConferenceRole::firstOrCreate([
            'conference_id' => $invitation->conference_id,
            'user_id'       => $user->id,
            'role'          => 'reviewer',
        ], [
            'assigned_by' => $invitation->inviter_id,
        ]);

        return $invitation->fresh();
    }

    /**
     * Decline an invitation. Does NOT require authentication.
     */
    public function decline(ReviewerInvitation $invitation, ?string $reason = null): ReviewerInvitation
    {
        abort_unless($invitation->isPending(), 422, 'Invitation is no longer valid.');

        $invitation->update([
            'status'         => InvitationStatus::Declined,
            'decline_reason' => $reason,
            'responded_at'   => now(),
        ]);

        return $invitation->fresh();
    }

    /**
     * Mark expired invitations (run via scheduled command).
     */
    public function expireStale(): int
    {
        return ReviewerInvitation::where('status', InvitationStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => InvitationStatus::Expired]);
    }
}
