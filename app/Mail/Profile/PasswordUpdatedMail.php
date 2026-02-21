<?php

declare(strict_types=1);

namespace App\Mail\Profile;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for password update notifications.
 *
 * Sends an email to the user informing them about their password change
 * with security context information.
 */
class PasswordUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User $user The user whose password was changed
     * @param string $ipAddress IP address from which the password was changed
     * @param string $userAgent User agent from which the password was changed
     */
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress,
        public readonly string $userAgent
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                site_email(),
                sender_name()
            ),
            subject: 'Şifreniz Değiştirildi',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.profile.password-updated',
            with: [
                'user' => $this->user,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'changedAt' => now()->format('d.m.Y H:i'),
            ],
        );
    }
}
