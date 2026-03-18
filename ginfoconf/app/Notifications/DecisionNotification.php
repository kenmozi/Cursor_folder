<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Submission $submission) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $conference = $this->submission->conference;
        $confTitle  = $conference->translation()?->title ?? $conference->slug;
        $status     = $this->submission->status->value;

        [$subject, $headline, $body] = match ($status) {
            'accepted' => [
                "Congratulations! Your paper has been accepted — {$confTitle}",
                "Your paper has been accepted!",
                "We are pleased to inform you that your paper **\"{$this->submission->title}\"** has been **accepted** for {$confTitle}. Please follow the camera-ready instructions to prepare your final submission.",
            ],
            'rejected' => [
                "Submission decision — {$confTitle}",
                "Decision on your submission",
                "After careful consideration by the program committee, we regret to inform you that your paper **\"{$this->submission->title}\"** was not accepted for {$confTitle}. We thank you for your submission and encourage you to consider future editions.",
            ],
            'revision_required' => [
                "Revision required — {$confTitle}",
                "Revision required for your submission",
                "The program committee has reviewed your paper **\"{$this->submission->title}\"** and requests a revision before a final decision can be made. Please log in to view the reviewer feedback and resubmit.",
            ],
            default => [
                "Update on your submission — {$confTitle}",
                "Your submission status has been updated",
                "The status of your paper **\"{$this->submission->title}\"** has been updated to **{$status}**.",
            ],
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Dear {$notifiable->name},")
            ->line($body)
            ->action('View Submission', config('app.frontend_url') . "/dashboard/author/submissions/{$this->submission->id}")
            ->salutation('The ' . $confTitle . ' Program Committee');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'decision',
            'submission_id' => $this->submission->id,
            'status'        => $this->submission->status->value,
            'title'         => $this->submission->title,
            'conference_id' => $this->submission->conference_id,
        ];
    }
}
