<?php

namespace App\Providers;

use App\Infrastructure\Formulation\WebsiteHelper;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->changeResetPasswordUrl();
    }

    private function changeResetPasswordUrl()
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            if($notifiable instanceof \App\Models\Client){
                $baseUrl = $_SERVER['HTTP_ORIGIN'].'/change-password';
                return "{$baseUrl}?token={$token}&email={$notifiable['email']}";
            }

            $baseUrl = env('FRONTEND_URL', 'http://qa-app.famiefi.com/change-password');
            return "{$baseUrl}?token={$token}&email={$notifiable['email']}";
        });
    }
}
