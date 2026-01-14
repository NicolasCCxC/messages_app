<?php

namespace App\Http\Controllers\AuthClient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotClientRequest;
use App\Http\Requests\Auth\ForgotRequest;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordClientApiController extends Controller
{
    use ResponseApiTrait;

    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param ForgotRequest $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(ForgotClientRequest $request): JsonResponse
    {
        $response = Password::broker('clients')->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Send the response for a successful password reset link.
     *
     * @param Request $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response): JsonResponse
    {

        return $this->successResponse(
            [],
            Module::SECURITY,
            trans($response)
        );

    }

    /**
     * Send the response for a failed password reset link.
     *
     * @param Request $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response): JsonResponse
    {
        return $this->errorResponse(
            Module::SECURITY,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            trans($response)
        );
    }
}
