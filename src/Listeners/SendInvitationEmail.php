<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Listeners;

use CleaniqueCoders\Inviteable\Events\InvitationCreated;
use CleaniqueCoders\Inviteable\Mail\InvitationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public function handle(InvitationCreated $event): void
    {
        $invitation = $event->invitation;
        $inviteable = $invitation->inviteable;

        if (! $inviteable || ! isset($inviteable->email)) {
            return;
        }

        $token = $invitation->plainToken ?? $invitation->token;

        $mailable = new InvitationMail($token);

        if (config('inviteable.mail.queue', true)) {
            $connection = config('inviteable.mail.queue_connection');
            $queue = config('inviteable.mail.queue_name');

            $pendingMail = Mail::to($inviteable);

            if ($connection || $queue) {
                $mailable->onConnection($connection)->onQueue($queue);
            }

            $pendingMail->queue($mailable);
        } else {
            Mail::to($inviteable)->send($mailable);
        }
    }

    public function viaConnection(): ?string
    {
        return config('inviteable.mail.queue_connection');
    }

    public function viaQueue(): ?string
    {
        return config('inviteable.mail.queue_name');
    }
}
