<?php

use App\Http\Controllers\CompanyInformationController;
use App\Http\Controllers\CompanyPaymentGatewayController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\MembershipPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\WebHookController;
use App\Http\Controllers\WompiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

$router->group(['prefix' => 'webhooks'], function () use ($router) {
    $router->group(['prefix' => 'payu'], function () use ($router) {
        $router->post('/website-client-transfer/{transactionId}', [WebHookController::class, 'websiteCLientTransfer']);
    });
});

$router->group(['prefix' => 'pays'], function () use ($router) {


    $router->group(['prefix' => 'membership'], function () use ($router) {
        $router->post('/cash', [MembershipPaymentController::class, 'cash']);
        $router->post('/report/{transactionId}', [MembershipPaymentController::class, 'paymentReport']);
        $router->group(['middleware' => 'pingPayu'], function () use ($router) {
            $router->post('/recurring-payment-registration', [MembershipPaymentController::class, 'recurringPaymentRegistration']);
            $router->post('/payment-without-token', [MembershipPaymentController::class, 'paymentWithOutToken']);
            $router->post('/get-card-token', [MembershipPaymentController::class, 'getCreditCardTokenId']);
            $router->post('/delete-card-token', [MembershipPaymentController::class, 'deleteCardToken']);
            $router->post('/payment-with-token', [MembershipPaymentController::class, 'paymentWithToken']);
            $router->get('/get-payu-data/{companyId}', [MembershipPaymentController::class, 'getDataPayu']);
            $router->post('/get-details-transaction', [MembershipPaymentController::class, 'getDetailsTransaction']);
            $router->get('/get-card-payu', [MembershipPaymentController::class, 'getCardPayu']);
            $router->get('/pse-banks', [MembershipPaymentController::class, 'getPseBanks']);
            $router->post('/pse', [MembershipPaymentController::class, 'pse']);
        });
    });

    $router->group(['prefix' => 'payment-gateway'], function () use ($router) {
        $router->get('/', [PaymentGatewayController::class, 'index']);
    });

    $router->group(['prefix' => 'company-set-up'], function () use ($router) {
        $router->post('/', [CompanyInformationController::class, 'store']);
        $router->get('/', [CompanyInformationController::class, 'get']);
    });

    $router->group(['prefix' => 'company-payments'], function () use ($router) {
        $router->post('/', [CompanyPaymentGatewayController::class, 'store']);
        $router->get('/', [CompanyPaymentGatewayController::class, 'getAll']);
    });

    $router->group(['prefix' => 'gateway', 'middleware' => 'pingPayu'], function () use ($router) {
        $router->get('/methods/{id}',[GatewayController::class, 'methods']);
        $router->get('/pse-banks/{id}', [GatewayController::class, 'pse']);
        $router->post('/pse-transfer/{id}', [GatewayController::class, 'pseTransfer']);
        $router->post('/credit-card-transfer/{id}', [GatewayController::class, 'creditCardTransfer']);
        $router->post('/cash-transfer/{id}', [GatewayController::class, 'cashTransfer']);
        $router->get('/report/{id}/{transactionId}', [GatewayController::class, 'report']);
    });

    $router->group(['prefix' => 'payment'], function () use ($router) {
        $router->get('/{clientId}', [PaymentController::class, 'clientIndex']);
    });
});
