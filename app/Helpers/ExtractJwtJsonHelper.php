<?php

namespace App\Helpers;
use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\Login\ClientResource;
use App\Http\Resources\Login\UserResource;
use App\Http\Resources\UserAccountCreatedResource;
/**
 * Class validarRecaptchaGoogle validate token client
 */
class ExtractJwtJsonHelper
{
    /* TOKEN EXPORT TYPES */
    const TYPE_WEBSITE = 'WEBSITE';
    const TYPE_ACCOUNT_CREATED = "ACCOUNT_CREATED";

    /**
     * makes the request to validate the token
     *
     * @param Request $request token client
     * @return array The result
     */
    public static function getJwtInformation(Request $request):array
    {
        return JWTAuth::manager()->getJWTProvider()->decode($request->bearerToken());
    }

    /**
     * makes the request to validate the token
     *
     * @param Request $request token client
     * @return array The result
     */
    public static function getConvertToken(Request $request):array
    {
        $toke = JWTAuth::manager()->getJWTProvider()->decode($request->bearerToken());
        $payload['company_id'] =  $request->header('company_id');
        $payload['service'] =  $request->all()["service"];
        $token = JWTAuth::manager()->getJWTProvider()->encode($payload);
        return ["token" => $token];
    }



    /**
     * Refreshes the token with updated claims
     *
     * @param string $token The original token
     * @param string $user The authenticated user
     * @param string $company The attempt data, including company_id
     * @return string The new token with updated expiration
     */
    public static function refreshToken($token, $user, $company, $isWebsite)
    {
        $ttlMinutes = !$isWebsite ? (int)auth()->factory()->getTTL() : (int)auth()->guard('client-api')->factory()->getTTL(); 
        $expiryTime = Carbon::now(env('APP_TIMEZONE', 'America/Bogota'))
        ->addDays(($ttlMinutes / (24 * 60)) -1)
        ->setTime(23, 59, 0)
        ->timestamp;

        $newToken = JWTAuth::setToken($token)
            ->claims([
                'user_id' => $user,
                'company_id' => $company,
                'exp' => $expiryTime
            ])
            ->refresh();

        return $newToken;
    }

    /**
     * Get the token array structure.
     *
    * @param string $token The generated access token.
    * @param User $user The authenticated user.
    * @param array $company The company information (used when the request is not from a website).
    * @param boolean $isLogin Indicates whether the request is after a successful login.
    * @param string $type Indicates if the request is from a website.
    */
    public static function responseWithToken(string $token, $user, $company, $isLogin, string $type): array
    {
        if ($type == self::TYPE_WEBSITE) {
            $expiresIn = auth()->guard('client-api')->factory()->getTTL() * 60;
            $expiresInUnix = Carbon::now(env('APP_TIMEZONE', 'America/Bogota'))
                ->addDays(((int) auth()->guard('client-api')->factory()->getTTL() / (24 * 60)) -1)
                ->setTime(23, 59, 0)
                ->timestamp;
            $responseData = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $expiresIn,
                'expires_in_unix' => $expiresInUnix,
                'user' => new ClientResource($user)
            ];
        } else {
            $emailList = env('EMAILS_FOR_CUSTOMER_ADMINISTRATION', '');
            $emails = array_map('strtolower', explode(',', $emailList));
            $expiresIn = auth()->factory()->getTTL() * 60;
            $expiresInUnix = Carbon::now(env('APP_TIMEZONE', 'America/Bogota'))
                ->addDays(((int) auth()->factory()->getTTL() / (24 * 60)) -1)
                ->setTime(23, 59, 0)
                ->timestamp;
            if ($type == self::TYPE_ACCOUNT_CREATED) {
                $company = Company::find($user->company_id);
                $responseData =     [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => $expiresIn,
                    'expires_in_unix' => $expiresInUnix,
                    'user' => new UserResource($user),
                    'company' => new UserAccountCreatedResource($company),
                ];
            } else {
                $responseData = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => $expiresIn,
                    'expires_in_unix' => $expiresInUnix,
                    'user' => new UserResource($user),
                    'company' => $company,
                    'is_administration_customer' => $isLogin ? in_array(strtolower($user->email ?? ''), $emails): false,
                ];
            }
        }
        return $responseData;
    }

}
