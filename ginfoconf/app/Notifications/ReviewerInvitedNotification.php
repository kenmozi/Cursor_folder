<?php

namespace App\Notifications;

use App\Models\ReviewerInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerInvitedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ReviewerInvitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $conference  = $this->invitation->conference;
        $confTitle   = $conference->translation()?->title ?? $conference->slug;
        $acceptUrl   = config('app.frontend_url') . "/invitations/{$this->invitation->token}/accept";
        $declineUrl  = config('app.frontend_url') . "/invitations/{$this->invitation->token}/decline";

        $mail = (new MailMessage)
            ->subject("Invitation to review papers — {$confTitle}")
            ->greeting("Dear colleague,")
            ->line("You have been invited to serve as a reviewer for **{$confTitle}**.")
            ->line("Your expertise and contribution to the peer review process would be greatly valued.");

        if ($this->invitation->message) {
            $mail->line('**Message from the program committee:**')
                 ->line($this->invitation->message);
        }

        return $mail
            ->action('Accept Invitation', $acceptUrl)
            ->line("If you are unable to participate, you may [decline here]({$declineUrl}).")
            ->line('This invitation expires on ' . $this->invitation->expires_at->toFormattedDateString() . '.')
            ->salutation('The ' . $confTitle . ' Program Committee');
    }
}
