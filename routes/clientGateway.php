<?php

use App\Http\Controllers\ClientGatewayController;
use App\Http\Controllers\AuthClient\AuthClientController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthClient\ForgotPasswordClientApiController;
use App\Http\Controllers\AuthClient\ResetPasswordClientApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;

/*
|--------------------------------------------------------------------------
| Client Gateway Routes
|--------------------------------------------------------------------------
|
| Here is where you can register client gateway routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "client" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth-client'], function () {

    Route::get('captcha', [AuthClientController::class, 'getCaptcha']);
    Route::post('login', [AuthClientController::class, 'login']);
    Route::post('register', [ClientController::class, 'store']);
    Route::post('store-verification-token', [AuthClientController::class, 'storeVerificationToken']);
    Route::post('verify-token', [AuthClientController::class, 'verifyToken']);
    Route::group(['middleware' => ['client.guard:client-api', 'jwt.auth']], function () {

        Route::post('logout', [AuthClientController::class, 'logout']);
        Route::post('refresh', [AuthClientController::class, 'refresh']);
        Route::get('me', [AuthClientController::class, 'me']);
        Route::put('me', [AuthClientController::class, 'update']);
    });
});

Route::group(['prefix' => 'clients'], function () {

    Route::post('/', [ClientController::class, 'store']);
    Route::post('/password/email', [ForgotPasswordClientApiController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [ResetPasswordClientApiController::class, 'reset']);

    Route::group(['middleware' => ['client.guard:client-api', 'jwt.auth']], function () {

        Route::get('/{company}', [ClientController::class, 'index']);
        Route::get('/user/{id}', [ClientController::class, 'show']);
        Route::put('/', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'initial-data'], function () {
    Route::get('/', [ClientController::class, 'initialData']);
});

Route::group(['prefix' => 'no-auth'], function () {
    Route::post('/', [GatewayController::class, 'lock']);
});

Route::group(['prefix' => 'utils'], function () {

    Route::group(['middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
        Route::post('/', [ClientGatewayController::class, 'gate']);
    });
});

Route::group(['prefix' => 'no-auth'], function () {
    Route::post('/', [ClientGatewayController::class, 'lock']);
});

Route::group(['prefix' => 'inventory', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
    Route::post('/upload', [ClientGatewayController::class, 'upload']);
});

Route::group(['prefix' => 'bucket', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'qualification', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'binnacle', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'notification', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'invoice', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
    Route::post('/upload', [ClientGatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'website', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'accounting', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'shopping', 'middleware' => ['client.guard:client-api', 'jwt.auth']], function () {
    Route::post('/', [ClientGatewayController::class, 'gate']);
});

Route::group(['prefix' => 'pays', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});
