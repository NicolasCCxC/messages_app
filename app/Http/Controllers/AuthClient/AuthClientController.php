<?php

namespace App\Http\Controllers\AuthClient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use App\Http\Resources\Login\ClientResource;
use App\Infrastructure\Persistence\ClientEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Helpers\ExtractJwtJsonHelper;
use App\Http\Requests\Auth\StoreVerificationTokenRequest;
use App\Http\Requests\Auth\VerifyTokenRequest;
use Mews\Captcha\Facades\Captcha;


class AuthClientController extends Controller
{

    use ResponseApiTrait;

    private $clientELoquent;

    public function __construct(ClientEloquent $clientELoquent)
    {
        $this->clientELoquent = $clientELoquent;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param ClientLoginRequest $request
     * @return JsonResponse
     */
    public function login(ClientLoginRequest $request): JsonResponse
    {
        $attempt = request(['email', 'password', 'company_id']);
        $credentials = request(['email', 'password']);

        if (
            $this->clientELoquent->attemptLogin($attempt) &&
            !$token = auth()->guard('client-api')->attempt($credentials)
        ) {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_UNAUTHORIZED,
                'Unauthorized'
            );
        }
        $user = auth()->guard('client-api')->user();
        $token = ExtractJwtJsonHelper::refreshToken($token, $user->id, $attempt['company_id'], true);
        $user->last_login = now();
        $user->save();
        return $this->successResponse(ExtractJwtJsonHelper::responseWithToken($token, $user, null, false, ExtractJwtJsonHelper::TYPE_WEBSITE), Module::SECURITY);
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
        auth()->guard('client-api')->logout();

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
    protected function responseWithToken(string $token, $user = null): JsonResponse
    {
        $user = $user ?: auth()->guard('client-api')->user();
        return $this->successResponse(
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth()->guard('client-api')->factory()->getTTL() * 60,
                'expires_in_unix' => Carbon::now('America/Bogota')->addMinutes(auth()->guard('client-api')->factory()->getTTL())->subDay()->setTime(23, 59, 0)->timestamp,
                'user' => new ClientResource($user)
            ],
            Module::SECURITY
        );
    }

    /**
     * Generates a response with a CAPTCHA.
     *
     * @return JsonResponse
     */
    public function getCaptcha(): JsonResponse
    {
        try {
            return $this->successResponse(
                [
                    'captcha' => Captcha::create('default', true)
                ],
                Module::SECURITY
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Stores a record to email_verification_token table
     *
     * @return JsonResponse
     */
    public function storeVerificationToken(StoreVerificationTokenRequest $request): JsonResponse
    {
        $this->clientELoquent->storeVerificationToken($request->all());
        return $this->successResponse(
            null,
            Module::SECURITY,
            'Token stored successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Receives a token and verifies it
     *
     * @return JsonResponse
     */
    public function verifyToken(VerifyTokenRequest $request): JsonResponse
    {
        $response = $this->clientELoquent->verifyToken($request->all());
        if ($response['status']) {
            return $this->successResponse(
                null,
                Module::SECURITY,
                $response['message']
            );
        }
        return $this->errorResponse(
            Module::SECURITY,
            Response::HTTP_BAD_REQUEST,
            $response['message']
        );
    }
}
