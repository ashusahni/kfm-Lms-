<?php

namespace App\Http\Controllers\Api\Config;

use App\Api\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function list(Request $request)
    {
        return self::get();
    }

    public static function get()
    {
        try {
            $registerMethod = getGeneralSettings('register_method') ?? 'email';
            $userLanguages = getGeneralSettings('user_languages');
            if (!empty($userLanguages) and is_array($userLanguages)) {
                $userLanguages = getLanguages($userLanguages);
            } else {
                $userLanguages = [];
            }

            $paymentChannels = [];
            try {
                $paymentChannels = PaymentChannel::all()->groupBy('status');
            } catch (\Throwable $e) {
                Log::warning('ConfigController: PaymentChannel::all() failed: ' . $e->getMessage());
            }

            $financialSettings = getFinancialSettings() ?? [];
            $getFinancialSettings = $financialSettings['minimum_payout'] ?? 0;
            $currencySettings = getFinancialCurrencySettings() ?? [];
            $currency = [
                'sign' => currencySign(),
                'name' => currency()
            ];

            $features = getFeaturesSettings() ?? [];
            $referralSettings = getReferralSettings() ?? [];

            $offlineBankAccount = null;
            try {
                $offlineBankAccount = getOfflineBanksTitle();
            } catch (\Throwable $e) {
                Log::warning('ConfigController: getOfflineBanksTitle() failed: ' . $e->getMessage());
            }

            $data = [
                'register_method' => $registerMethod,
                'offline_bank_account' => $offlineBankAccount ?? null,
                'user_language' => $userLanguages,
                'payment_channels' => $paymentChannels,
                'minimum_payout_amount' => $getFinancialSettings,
                'currency' => $currency,
                'currency_position' => $currencySettings['currency_position'] ?? 'left',
                'currency_decimal' => $currencySettings['currency_decimal'] ?? 0,
                'price_display' => getFinancialSettings('price_display') ?? 'only_price',
                'show_google_login_button' => !empty($features['google_login_status'] ?? false),
                'show_facebook_login_button' => !empty($features['facebook_login_status'] ?? false),
                'showOtherRegisterMethod' => (bool) (getGeneralSettings('show_other_register_method') ?? false),
                'selectRolesDuringRegistration' => ['user', 'teacher', 'organization'],
                'referralSettings' => [
                    'status' => !empty($referralSettings['referral'] ?? false),
                ],
            ];
            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            Log::error('ConfigController::get failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Configuration could not be loaded.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Register config per role (user, teacher, organization).
     * App may use this to get custom fields for the registration form.
     */
    public function registerConfig($role)
    {
        $allowed = ['user', 'teacher', 'organization'];
        if (!in_array($role, $allowed)) {
            return response()->json(['data' => ['fields' => []]], 200);
        }
        $data = ['role' => $role, 'fields' => []];
        return response()->json(['data' => $data]);
    }


}
