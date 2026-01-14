<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetRequest;
use App\Models\Module;
use App\Models\PasswordChange;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\ResponseApiTrait;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Formulation\GatewayHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ResetPasswordApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

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
        $user = User::where('email', $request->input('email'))->first();

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = Password::broker('users')->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );
        if($response == Password::PASSWORD_RESET){

            $passwordChange = PasswordChange::create([
                'user_id' => $user->id,
                'change_date' =>  Carbon::now()->setTimezone(env('APP_TIMEZONE', 'America/Bogota'))->format('d/m/Y h:i'),
                'change_location' => $request["change_location"],
                'change_device' => $request["change_device"],
                'longitude' => $request["longitude"],
                'latitude' => $request["latitude"],
            ]);

            $this->sendEmailResetPasswordConfirmation([
                'change_location' => $request["change_location"],
                'change_device' => $request["change_device"],
                'email' => $request["email"],
                'date' => Carbon::now()->setTimezone(env('APP_TIMEZONE', 'America/Bogota'))->format('d/m/Y h:i A'),
            ]);
            
            return $this->sendResetResponse($request, $response);
        } else{
            return $this->sendResetFailedResponse($request, $response);
        }
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
        //Removed the action who tries to set the "Remember me" cookie
        //$user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
        //By default, Laravel will attempt to automatically log in the user
        //$this->guard()->login($user);
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

    /**
     * Send email reset password
     *
     * @param array $data
     * @return JsonResponse
     */
    public function sendEmailResetPasswordConfirmation(array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/notifications/reset-password-confirmation',
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => $data['company_id'] ?? Str::uuid()->toString(),
        ]);
    }
}
