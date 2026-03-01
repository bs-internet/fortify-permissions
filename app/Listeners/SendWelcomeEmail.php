<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserCreated;
use App\Mail\Users\WelcomeUserMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

/**
 * Listener for sending welcome email to newly created users.
 *
 * Generates a password reset token and sends a welcome email
 * with a password setup link.
 */
class SendWelcomeEmail implements ShouldQueue
{
    /**
     * Handle the user created event.
     */
    public function handleCreated(UserCreated $event): void
    {
        $token = Password::createToken($event->user);

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $event->user->email,
        ], false));

        Mail::to($event->user->email)
            ->queue(new WelcomeUserMail($event->user, $resetUrl));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<class-string, string>
     */
    public function subscribe(): array
    {
        return [
            UserCreated::class => 'handleCreated',
        ];
    }
}
