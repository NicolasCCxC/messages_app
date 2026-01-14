<?php

namespace App\Console\Commands;

use App\Mail\PasswordChangeNotification;
use App\Models\PasswordChange;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Auth\Notifications\ResetPassword;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Infrastructure\Formulation\GatewayHelper;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SendEmailForPasswordChangeNotification extends Command
{
    protected $signature = 'validate:password-change-notification';
    protected $description = 'Send email notification for password change';

    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            $lastPasswordChange = PasswordChange::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();
            $dateTime = $lastPasswordChange ? Carbon::createFromFormat('d/m/Y H:i', $lastPasswordChange->change_date) : null;
            if (Carbon::parse($dateTime)->diffInMonthsInt(now()) >= 6) {
                $token = $this->createJwtToken($user);
                $resetLink = $this->createResetLink($user, $token);
                $this->sendEmailChangePasswordSixMonths([
                    'link' => $resetLink,
                    'email' => $user->email,
                ]);
                PasswordChange::create([
                    'user_id' => $user->id,
                    'change_date' =>  Carbon::now()->setTimezone(env('APP_TIMEZONE', 'America/Bogota'))->format('d/m/Y h:i'),

                ]);
            }
        }
        $this->info('Emails sent successfully for password change notifications.');
    }

    public function sendEmailChangePasswordSixMonths (array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/notifications/change-password-months',
            'method' => 'POST',
            'service' => 'NOTIFICATION',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => $data['company_id'] ?? Str::uuid()->toString(),
        ]);
    }

    protected function createJwtToken($user)
    {
        return Password::broker()->createToken($user);
    }

    protected function createResetLink($user, $token)
    {
        $baseUrl = env('FRONTEND_URL', 'http://qa-app.famiefi.com/change-password');
        return "{$baseUrl}?token={$token}&email={$user->email}";
    }
}
