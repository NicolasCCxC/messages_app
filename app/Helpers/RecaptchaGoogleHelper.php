<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

/**
 * Class validarRecaptchaGoogle validate token client
 */
class RecaptchaGoogleHelper
{
    /**
     * Sends a request to validate the recaptcha token.
     *
     * @param string $token The recaptcha token provided by the client.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function validateToken(string $recaptchaToken)
    {
        $secretKey = config('app.recaptcha_secret_key');
        $response = false;
        try {
            $responseApi = Http::asForm()->post(config('app.url_validate_recaptcha'), [
                'secret' => $secretKey,
                'response' => $recaptchaToken,
            ]);
            if ($responseApi['success'] == true) {
                $response = true;
            }

            return $response;
        } catch (\Exception $e) {
            return $response;
        }
    }

    /**
     * Validates the recaptcha token from the request headers.
     *
     * @param Request $request The incoming request.
     * @param array $credentials An array containing user credentials.
     * @return array|null An array with error code and message or null if valid.
     */
    public static function validateRecaptchaFromRequest($request, array $credentials)
    {
        if (
            !config('app.debug') ||
            (config('app.debug') && $credentials['email'] != config('app.email_automation', 'automation@ccxc.us'))
        ) {
            if (!$request->header('Recaptcha')) {
                return ['code' => 401, 'message' => 'Recaptcha header is missing'];
            }

            $recaptchaToken = $request->header('Recaptcha');
            $isTokenValid = self::validateToken($recaptchaToken);

            if (!$isTokenValid && $recaptchaToken !== config('app.recaptcha_test')) {
                return ['code' => 401, 'message' => 'Unauthorized'];
            }
        }
        return null;
    }
}

