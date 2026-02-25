<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class ForgotPasswordController extends Controller
{

    public function sendEmail(Request $request)
    {
        $email = null;
        if ($request->filled('email')) {
            validateParam($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);
            $email = $request->input('email');
        } elseif ($request->filled('mobile') && $request->filled('country_code')) {
            $mobile = ltrim($request->input('country_code'), '+') . ltrim($request->input('mobile'), '0');
            $user = User::where('mobile', $mobile)->first();
            if (!$user) {
                return apiResponse2(0, 'not_found', trans('auth.user_not_found'));
            }
            if (empty($user->email)) {
                return apiResponse2(0, 'no_email', trans('auth.reset_requires_email'));
            }
            $email = $user->email;
        } else {
            return apiResponse2(0, 'validation_error', trans('validation.required', ['attribute' => 'email or mobile']));
        }

        $token = \Illuminate\Support\Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $generalSettings = getGeneralSettings();
        $emailData = [
            'token' => $token,
            'generalSettings' => $generalSettings,
            'email' => $email
        ];
        try {
            Mail::send('web.default.auth.password_verify', $emailData, function ($message) use ($email, $generalSettings) {
                $message->from(!empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS'));
                $message->to($email);
                $message->subject('Reset Password Notification');
            });

            return apiResponse2(1, 'done', trans('auth.forget_password'));

        } catch (\Exception $e) {
            return apiResponse2(0, 'failure', trans('auth.forget_password_failure'));
        }
    }
}
