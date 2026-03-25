<?php

namespace App\Notifications;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SendVerificationMagicLink extends Notification
{
    /** @var Verification */
    protected $verification;

    public function __construct(Verification $verification)
    {
        $this->verification = $verification;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $generalSettings = getGeneralSettings();
        $siteName = $generalSettings['site_name'] ?? config('app.name');
        $subject = trans('auth.email_confirmation');

        $url = URL::temporarySignedRoute(
            'verification.verify-email',
            now()->addMinutes(60),
            ['id' => $this->verification->id]
        );

        $email = $this->verification->email ?? $this->verification->mobile;

        return (new MailMessage)
            ->subject($subject . ' - ' . $siteName)
            ->from(
                !empty($generalSettings['site_email']) ? $generalSettings['site_email'] : config('mail.from.address'),
                config('mail.from.name')
            )
            ->view('web.default.emails.verifyMagicLink', [
                'url' => $url,
                'siteName' => $siteName,
                'email' => $email,
                'generalSettings' => $generalSettings,
            ]);
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
