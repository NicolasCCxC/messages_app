<?php

use App\Http\Controllers\Auth\ForgotPasswordApiController;
use App\Http\Controllers\Auth\ResetPasswordApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyForeignExchangeController;
use App\Http\Controllers\CompanyDeviceController;
use App\Http\Controllers\PoliticController;
use App\Http\Controllers\PrefixController;
use App\Http\Controllers\PhysicalStoreController;
use App\Http\Controllers\MembershipPurchaseProcessController;
use App\Http\Controllers\PayTransactionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('account-created', [AuthController::class, 'accountCreated']);
    Route::post('register', [UserController::class, 'storeUserLogin']);

    Route::post('utils', [GatewayController::class, 'lock']);
    Route::post('client', [GatewayController::class, 'lock']);
    Route::post('invoice', [GatewayController::class, 'lock']);
    Route::post('notification', [GatewayController::class, 'lock']);
    Route::post('website', [GatewayController::class, 'lock']);
    Route::post('invoice/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
    Route::post('bucket', [GatewayController::class, 'lock']);
    Route::post('binnacle', [GatewayController::class, 'lock']);

    Route::post('add-company-jwt-services', [UserController::class, 'addCompanyJwt']);
    Route::put('user/update-first-login/{id}', [UserController::class, 'updateFirstLogin']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'update']);
});

Route::group(['prefix' => 'clients'], function () {
    Route::get('/final-customer/{companyId}', [ClientController::class, 'createOrGetFinalCustomer']);
    Route::get('/search/{documentId}/{companyId}', [ClientController::class, 'getClientByDocument']);
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/last-login', [ClientController::class, 'showClient']);
    Route::post('/billing',  [ClientController::class, 'storeBilling']);
});

Route::group(['prefix' => 'users'], function () {

    Route::post('/', [UserController::class, 'store']);
    Route::post('/password/email', [ForgotPasswordApiController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [ResetPasswordApiController::class, 'reset']);


    Route::get('/', [UserController::class, 'index']);
    Route::get('/available', [UserController::class, 'usersAvailable']);
    Route::get('/super/{company}', [UserController::class, 'getSuperUserCompany']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/', [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'destroy']);
    Route::group(['prefix' => 'role'], function () {
        Route::get('/{companyId}', [UserController::class, 'filterbyUserPermission']);
    });
});

Route::group(['prefix' => 'permission', 'middleware' => 'jwt'], function () {
    Route::get('/', [PermissionController::class, 'index']);
    Route::get('/format', [PermissionController::class, 'formatPermissions']);
    Route::post('/', [PermissionController::class, 'store']);
});

Route::group(['prefix' => 'company'], function () {
    Route::get('/all-companies', [CompanyController::class, 'getAllCompanies']);
    Route::post('/update-account-created', [CompanyController::class, 'updateAccountCreated']);
    Route::get('/active-memberships/{company}', [CompanyController::class, 'getActiveMemberships']);
    Route::post('/get-names-companies', [CompanyController::class, 'getNamesCompanies']);
    Route::get('/companies-administration', [CompanyController::class, 'getCompaniesAdministration']);
    Route::group(['middleware' => 'jwt'], function () {
        Route::group(['prefix' => 'memberships', 'middleware' => 'ipAddress'], function () {
            Route::post('/pay-create-token', [MembershipController::class, 'storePayAndCreateToken']);
            Route::post('/pay-with-token', [MembershipController::class, 'storePayWithToken']);
            Route::post('/pay-without-token', [MembershipController::class, 'storePayWithoutToken']);
            Route::post('/pay-pse', [MembershipController::class, 'storePayPse']);
            Route::get('/update-status-pay/{transaction}', [MembershipController::class, 'updateStatusPay']);
            Route::post('/cancel-memberships', [MembershipController::class, 'cancelModulesMemberships']);
            Route::post('/validate-modules', [MembershipController::class, 'validateModules']);
            Route::get('/pages-available', [MembershipController::class, 'getPagesAvailable']);
            Route::get('/validate-status-transaction', [MembershipController::class, 'validateStatusTransaction']);
            Route::get('/details', [MembershipController::class, 'getDetailsMembership']);
            Route::get('/binnacle', [MembershipController::class, 'getBinnacleMembership']);
            Route::post('/validate-access-free-documents', [MembershipController::class, 'validateAccessFreeDocuments']);
            Route::get('/get-companies-active-membership', [UserController::class, 'getUsersActiveMembership']);
            Route::get('/transactions/transaction/{transaction_id}', [PayTransactionController::class, 'getByTransaction']);
            Route::patch('transactions/update-json-pse', [PayTransactionController::class, 'updateJsonPseUrlResponse']);
        });
        Route::get('/information-by-billing/{company}', [CompanyController::class, 'getInformationByBilling']);
        Route::post('/update-domain', [CompanyController::class, 'updateDomain']);
        Route::get('/get-domain/{company}', [CompanyController::class, 'getDomain']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/email', [CompanyController::class, 'getCompanyInfoWithEmail']);
        Route::put('/company/{id}', [CompanyController::class, 'update']);
        Route::get('membership', [MembershipController::class, 'index']);
        Route::get('company-staff', [CompanyController::class, 'getUsersAndClients']);
        Route::post('/membership/pay', [MembershipController::class, 'pay']);
        Route::post('/upload-company-attachment', [CompanyController::class, 'loadCompanyAttachment']);
        Route::delete('/upload-company-attachment/{id}/{name}', [CompanyController::class, 'deleteCompanyAttachment']);
        Route::get('/upload-company-attachment/{companyId}/{name}', [CompanyController::class, 'getCompanyAttachment']);
        Route::group(['prefix' => 'physical-store'], function () {
            Route::get('/', [PhysicalStoreController::class, 'getAllPhysicalStoresByCompany']);
            Route::post('/', [PhysicalStoreController::class, 'store']);
            Route::delete('/point-sale/{id}', [PhysicalStoreController::class, 'deletePointSale']);
            Route::delete('/physicals-or-points', [PhysicalStoreController::class, 'deletePhysicalStoreOrPointSaleByIds']);
            Route::delete('/{id}', [PhysicalStoreController::class, 'delete']);
        });
        Route::get('/{company}', [CompanyController::class, 'index']);
        Route::put('/update-billing', [CompanyController::class, 'updateInformationBilling']);
        Route::put('/update-company-minimum-data/{id}', [CompanyController::class, 'updateCompanyMinimunData']);
    });

    Route::group(['prefix' => 'companies-foreign-exchange'], function () {
        Route::post('/', [CompanyForeignExchangeController::class, 'store']);
        Route::put('/{id}', [CompanyForeignExchangeController::class, 'update']);
        Route::post('/list/{companyId}', [CompanyForeignExchangeController::class, 'getAll']);
        Route::delete('/many/{companyId}', [CompanyForeignExchangeController::class, 'delete']);
    });
    Route::post('/company-logo', [CompanyController::class, 'getClientCompanyLogo']);
});

Route::group(['prefix' => 'prefixes', 'middleware' => 'jwt'], function () {
    Route::post('/', [PrefixController::class, 'store']);
    Route::post('/synchronize', [PrefixController::class, 'getSynchronize']);
    Route::post('/notes', [PrefixController::class, 'storeNotes']);
    Route::post('/purchase/company', [PrefixController::class, 'getPrefixPurchase']);
    Route::post('/company/{companyId}', [PrefixController::class, 'getTypePrefix']);
    Route::post('/specific', [PrefixController::class, 'getSpecificPrefix']);
    Route::delete('/delete', [PrefixController::class, 'deletePrefixes']);
    Route::post('/rank-depletion', [PrefixController::class, 'rankDepletionPrefix']);
    Route::post('/set-type', [PrefixController::class, 'setResolutionType']);
});

Route::group(['prefix' => 'politic', 'middleware' => 'jwt'], function () {
    Route::post('/', [PoliticController::class, 'store']);
    Route::get('/', [PoliticController::class, 'index']);
    Route::post('/by-type', [PoliticController::class, 'show']);
    Route::post('/data-privacy/{companyId}', [PoliticController::class, 'storeDataPrivacyPolicy']);
    Route::delete('/{id}', [PoliticController::class, 'delete']);

    Route::get('/privacy-purposes', [PoliticController::class, 'getPurposeByCompanyId']);
    Route::post('/privacy-purposes', [PoliticController::class, 'storeOrUpdatePrivacyPurpose']);
    Route::delete('/privacy-purposes/{purposeId}', [PoliticController::class, 'deletePrivacyPurpose']);
});

Route::group(['prefix' => 'gateway', 'middleware' => 'auth.services'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'utils'], function () {

    Route::group(['middleware' => 'jwt'], function () {
        Route::post('/', [GatewayController::class, 'gate']);
    });
});

Route::group(['prefix' => 'inventory', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'bucket', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'qualification', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'binnacle', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'notification', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'invoice', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'electronic-invoice', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
});

Route::group(['prefix' => 'website', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
    Route::post('/upload', [GatewayController::class, 'upload']);
    Route::post('/upload-many-files', [GatewayController::class, 'uploadManyFiles']);
});

Route::group(['prefix' => 'accounting', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'shopping', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'domain', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'pays', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'invoices-available'], function () {
    Route::get('/{company}', [CompanyController::class, 'getInvoicesAvailable']);
});

Route::group(['prefix' => 'remaining-invoices'], function () {
    Route::get('/{company}', [CompanyController::class, 'counterUsedElectronicDocuments']);
});

Route::group(['prefix' => 'supporting-document-available'], function () {
    Route::get('/{company}', [CompanyController::class, 'getSupportingDocumentAvailable']);
});

Route::group(['prefix' => 'companies-devices'], function () {
    Route::post('/', [CompanyDeviceController::class, 'store']);
    Route::delete('/many', [CompanyDeviceController::class, 'delete']);
    Route::get('/company/{companyId}', [CompanyDeviceController::class, 'getByCompany']);
});

Route::group(['prefix' => 'payroll', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'electronic-payroll', 'middleware' => 'jwt'], function () {
    Route::post('/', [GatewayController::class, 'gate']);
});

Route::group(['prefix' => 'purchase-process', 'middleware' => 'jwt'], function () {
    Route::post('/', [MembershipPurchaseProcessController::class, 'store']);
    Route::get('/', [MembershipPurchaseProcessController::class, 'getMembershipPurchaseProcess']);
    Route::delete('/', [MembershipPurchaseProcessController::class, 'deleteDetailByIdAndCompany']);
});
