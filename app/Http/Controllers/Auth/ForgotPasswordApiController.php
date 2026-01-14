<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotRequest;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordApiController extends Controller
{
    use ResponseApiTrait;

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     * @param ForgotRequest $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(ForgotRequest $request): JsonResponse
    {
        // Attempt to send the password reset email to user.
        $response = Password::broker('users')->sendResetLink(
            $this->credentials($request)
        );
        // After attempting to send the link, we can examine the response to see
        // the message we need to show to the user and then send out a
        // proper response.
        return $this->sendResetLinkResponse($request, $response);
    }

    /**
     * Send the response for a successful password reset link.
     * @param Request $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response): JsonResponse
    {

        // On success, a string $response is returned with value of RESET_LINK_SENT
        // from the Password facade (the default is "passwords.sent")
        // Laravel trans() function translates this response to the text
        // designated in resources/lang/en/passwords.php
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
