<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function login(Request $request)
    {
        $data = $request->all();
        $hasMobileFields = !empty($data['mobile']) || !empty($data['country_code']);

        if ($hasMobileFields) {
            $rules = [
                'mobile' => 'required|string|numeric',
                'country_code' => 'required|string',
                'password' => 'required|string|min:6',
            ];
        } else {
            $rules = [
                'username' => 'required|string',
                'password' => 'required|string|min:6',
            ];
            if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $data['username'] ?? '')) {
                $rules['username'] = 'required|string|email';
            }
        }

        validateParam($data, $rules);

        return $this->attemptLogin($request);
    }

    public function username()
    {
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (empty($this->username)) {
            if (!empty(request('mobile')) && !empty(request('country_code'))) {
                $this->username = 'mobile';
            } elseif (preg_match($email_regex, request('username', ''))) {
                $this->username = 'email';
            } else {
                $this->username = 'mobile';
            }
        }
        return $this->username;
    }

    /**
     * Get the login identifier value (email or normalized mobile).
     */
    protected function getLoginIdentifier(Request $request): string
    {
        if (!empty($request->mobile) && !empty($request->country_code)) {
            return ltrim($request->country_code, '+') . ltrim($request->mobile, '0');
        }
        return (string) $request->get('username', '');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = [
            $this->username() => $this->getLoginIdentifier($request),
            'password' => $request->get('password')
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return apiResponse2(0, 'incorrect', trans('auth.incorrect'));
        }
        return $this->afterLogged($request, $token);
    }

    public function afterLogged(Request $request, $token, $verify = false)
    {
        $user = auth('api')->user();

        if ($user->ban) {
            $time = time();
            $endBan = $user->ban_end_at;
            if (!empty($endBan) and $endBan > $time) {
                auth('api')->logout();
                return apiResponse2(0, 'banned_account', trans('auth.banned_account'));
            } elseif (!empty($endBan) and $endBan < $time) {
                $user->update([
                    'ban' => false,
                    'ban_start_at' => null,
                    'ban_end_at' => null,
                ]);
            }

        }

        if ($user->status != User::$active and !$verify) {
            // auth('api')->logout();
            auth('api')->logout();
            //  dd(apiAuth());
            $verificationController = new VerificationController();
            $checkConfirmed = $verificationController->checkConfirmed($user, $this->username(), $this->getLoginIdentifier($request));

            if ($checkConfirmed['status'] == 'send') {

                return apiResponse2(0, 'not_verified', trans('api.auth.not_verified'));

            } elseif ($checkConfirmed['status'] == 'verified') {
                $user->update([
                    'status' => User::$active,
                ]);
            }
        } elseif ($verify) {
            $user->update([
                'status' => User::$active,
            ]);

        }

        if ($user->status != User::$active) {
            \auth('api')->logout();
            return apiResponse2(0, 'inactive_account', trans('auth.inactive_account'));
        }

        $profile_completion = [];
        $data['token'] = $token;
        $data['user_id'] = $user->id;
        $data['role_name'] = $user->role_name ?? 'user';
        $data['full_name'] = $user->full_name ?? '';
        if (!$user->full_name) {
            $profile_completion[] = 'full_name';
            $data['profile_completion'] = $profile_completion;
        }

        return apiResponse2(1, 'login', trans('auth.login'), $data);


    }

    public function logout()
    {
        auth('api')->logout();
        if (!apiAuth()) {
            return apiResponse2(1, 'logout', trans('auth.logout'));
        }
        return apiResponse2(0, 'failed', trans('auth.logout.failed'));
    }


}
