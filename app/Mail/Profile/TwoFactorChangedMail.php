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
 * Mailable for two-factor authentication change notifications.
 *
 * Sends an email to the user informing them about their 2FA status change
 * with security context information.
 */
class TwoFactorChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User $user The user whose 2FA status was changed
     * @param bool $enabled Whether 2FA was enabled or disabled
     * @param string $ipAddress IP address from which the change was made
     * @param string $userAgent User agent from which the change was made
     */
    public function __construct(
        public readonly User $user,
        public readonly bool $enabled,
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
                settings('mail_from_address'),
                settings('mail_from_name')
            ),
            subject: $this->enabled
                ? 'İki Faktörlü Doğrulama Etkinleştirildi'
                : 'İki Faktörlü Doğrulama Devre Dışı Bırakıldı',
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
            markdown: 'mail.profile.two-factor-changed',
            with: [
                'user' => $this->user,
                'enabled' => $this->enabled,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'changedAt' => now()->format('d.m.Y H:i'),
            ],
        );
    }
}
