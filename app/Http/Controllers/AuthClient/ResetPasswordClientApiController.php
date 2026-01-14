<?php

namespace App\Http\Controllers\AuthClient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetRequest;
use App\Models\Module;
use App\Providers\RouteServiceProvider;
use App\Traits\ResponseApiTrait;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordClientApiController extends Controller
{

    use ResetsPasswords, ResponseApiTrait;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Reset the given user's password.
     *
     * @param ResetRequest $request
     * @return JsonResponse
     */
    public function reset(ResetRequest $request): JsonResponse
    {
        $response = Password::broker('clients')->reset(
            $this->credentials($request), function ($client, $password) {
            $this->resetPassword($client, $password);
        }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        $user->save();
        event(new PasswordReset($user));

    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetResponse(Request $request, $response): JsonResponse
    {
        return $this->successResponse(
            [],
            Module::SECURITY,
            trans($response),
        );
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response): JsonResponse
    {
        return $this->errorResponse(
            Module::SECURITY,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            trans($response)
        );
    }
}
