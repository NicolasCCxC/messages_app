<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Login\UserResource;
use App\Infrastructure\Persistence\CompanyEloquent;
use App\Helpers\LogHelper;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\RecaptchaGoogleHelper;
use App\Helpers\ExtractJwtJsonHelper;

class AuthController extends Controller
{

    private $companyEloquent;

    public function __construct(CompanyEloquent $companyEloquent)
    {
        $this->companyEloquent = $companyEloquent;
    }

    use ResponseApiTrait;

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request, $isAccountCreated = false): JsonResponse
    {
        $credentials = ["email" => $request->input('email'), 'password' => $request->input('password')];
        $isAccountCreated = (boolean) $request->input('is_account_created');
        $recaptchaValidation =  RecaptchaGoogleHelper::validateRecaptchaFromRequest($request, $credentials);
        if ($recaptchaValidation && $recaptchaValidation['code'] !== 200) {
            LogHelper::saveLog(json_encode($recaptchaValidation['message']),  $recaptchaValidation['code'], 'RecaptchaValidation error', 'SECURITY-0001');
            return $this->errorResponse(
                Module::SECURITY,
                $recaptchaValidation['code'],
                $recaptchaValidation['message']
            );
        }
        if (!$token = auth()->attempt($credentials)) {
            LogHelper::saveLog(json_encode("Unauthorized"), Response::HTTP_UNAUTHORIZED, 'Invalid credentials', 'SECURITY-0002');
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_UNAUTHORIZED,
                'Unauthorized'
            );
        }
        $user = auth()->user();
        $membershipMessageError = $this->companyEloquent->getMembershiploginErrorMessage($user->company_id);
        if ($membershipMessageError != '' && !$isAccountCreated) {
            LogHelper::saveLog(json_encode($membershipMessageError), Response::HTTP_FORBIDDEN, 'Membership error', 'SECURITY-0003', $user->company_id, $user->id);
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_FORBIDDEN,
                $membershipMessageError
            );
        }
        $token = ExtractJwtJsonHelper::refreshToken($token, $user->id, $user->company_id, false);
        $user->last_login = now();
        $user->save();
        return $this->successResponse(
            ExtractJwtJsonHelper::responseWithToken($token, $user, $this->companyEloquent->getCompanyInfo($user->company_id), true, $isAccountCreated ? ExtractJwtJsonHelper::TYPE_ACCOUNT_CREATED : ''),
            Module::SECURITY,
        );
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->successResponse(
            null,
            Module::SECURITY,
            'Successfully logged out',
        );
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->responseWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @param null $user
     * @return JsonResponse
     */
    protected function responseWithToken(string $token): JsonResponse
    {
        $user = auth()->user();
        $emailList = env('EMAILS_FOR_CUSTOMER_ADMINISTRATION', '');
        $emails = array_map('strtolower', explode(',', $emailList));
        return $this->successResponse(
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => new UserResource($user),
                'company' => $this->companyEloquent->getCompanyInfo($user->company_id),
                'is_administration_customer' => in_array(strtolower($user->email ?? ''), $emails)
            ],
            Module::SECURITY,
        );
    }
}
