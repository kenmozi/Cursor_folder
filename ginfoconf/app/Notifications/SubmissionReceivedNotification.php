<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceivedNotification extends Notification implements ShouldQueue
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
        $title      = $conference->translation()?->title ?? $conference->slug;

        return (new MailMessage)
            ->subject("Submission received — {$title}")
            ->greeting("Dear {$notifiable->name},")
            ->line("Your paper **\"{$this->submission->title}\"** has been successfully submitted to **{$title}**.")
            ->line("Submission ID: #{$this->submission->id}")
            ->action('View Submission', config('app.frontend_url') . "/dashboard/author/submissions/{$this->submission->id}")
            ->line('You will be notified when the review process is complete.')
            ->salutation('The ' . $title . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'submission_received',
            'submission_id' => $this->submission->id,
            'title'         => $this->submission->title,
            'conference_id' => $this->submission->conference_id,
        ];
    }
}
