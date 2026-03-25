@extends('web.default.layouts.email')

@section('body')
    <td valign="top" class="bodyContent" mc:edit="body_content">
        <h1 class="h1">{{ trans('auth.email_confirmation') }} - {{ $siteName }}</h1>
        <p>{{ trans('auth.email_confirmation_template_body', ['email' => $email, 'site' => $siteName]) }}</p>
        <p>{{ trans('auth.click_link_below_to_verify') ?? 'Click the button below to verify your email and sign in.' }}</p>
        <p style="margin-top: 24px;">
            <a href="{{ $url }}" class="btn" style="display: inline-block; padding: 12px 24px;">{{ trans('auth.verify_my_email') ?? 'Verify my email' }}</a>
        </p>
        <p class="text-muted" style="margin-top: 24px; font-size: 12px; color: #888;">
            {{ trans('notification.email_ignore_msg') ?? 'If you did not request this, you can ignore this email.' }}
        </p>
    </td>
@endsection
