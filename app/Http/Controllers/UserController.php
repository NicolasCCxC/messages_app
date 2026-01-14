<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Infrastructure\Persistence\UserEloquent;
use App\Infrastructure\Persistence\CompanyEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\RecaptchaGoogleHelper;
use App\Helpers\ExtractJwtJsonHelper;
use App\Infrastructure\Persistence\ClientEloquent;
use App\Infrastructure\Services\InvoiceService;
use App\Models\Client;

class UserController extends Controller
{

    use ResponseApiTrait;

    /**
     * @var UserEloquent
     */
    private $userEloquent;

    /**
     * @var CompanyEloquent
     */
    private $companyEloquent;

    /**
     * @var ClientEloquent
     */
    private $clientEloquent;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        UserEloquent $userEloquent,
        CompanyEloquent $companyEloquent,
        ClientEloquent $clientEloquent,
        InvoiceService $invoiceService,
        Request $request
    ) {
        $this->userEloquent = $userEloquent;
        $this->companyEloquent = $companyEloquent;
        $this->clientEloquent = $clientEloquent;
        $this->invoiceService = $invoiceService;
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->userEloquent->getAllCompanyUsers($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * get infor super users
     *
     * @param string $company - company uuid
     * @return JsonResponse
     */
    public function getSuperUserCompany(string $company): JsonResponse
    {
        return $this->successResponse(
            $this->userEloquent->getSuperUserCompany($company),
            Module::SECURITY
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->userEloquent->storeUser($request->all(), $request->ip()),
            Module::SECURITY,
            'Created resource',
            Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function storeUserLogin(StoreUserRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);
        $recaptchaValidation =  RecaptchaGoogleHelper::validateRecaptchaFromRequest($request, $credentials);
        if ($recaptchaValidation && $recaptchaValidation['code'] !== 200) {
            return $this->errorResponse(
                Module::SECURITY,
                $recaptchaValidation['code'],
                $recaptchaValidation['message']
            );
        }

        $this->userEloquent->storeUser($request->all(), $request->ip());
        $token = auth()->attempt($credentials);
        $user = auth()->user();
        $token = ExtractJwtJsonHelper::refreshToken($token, $user->id, $user->company_id, false);
        $user->last_login = now();
        $user->save();

        // We create the final consumer
        $clientId = $this->clientEloquent->createOrGetFinalCustomer($user->company_id);
        $this->invoiceService->storeCustomer([
            'name' => 'Consumidor Final',
            'document_type' => Client::DEFAULT_DOCUMENT_TYPE_FINAL_CUSTOMER,
            'document_number' => Client::DEFAULT_DOCUMENT_NUMBER_FINAL_CUSTOMER,
            'client_id' => $clientId,
            'tax_details_name' => 'No aplica',
            'tax_details_code' => 'ZZ'
        ], $user->company_id);


        return $this->successResponse(
            ExtractJwtJsonHelper::responseWithToken($token, $user, $this->companyEloquent->getCompanyInfo($user->company_id), false, ''),
            Module::SECURITY,
        );
    }


    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return $this->successResponse(
            $this->userEloquent->getUserById($id),
            Module::SECURITY,
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $response = $this->userEloquent->updateUser($request->all(), $request->ip());

        if (isset($response['statusCode']) && $response['statusCode'] === Response::HTTP_FORBIDDEN) {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_FORBIDDEN,
                $response['message']
            );
        }

        return $this->successResponse(
            $response,
            Module::SECURITY,
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteUserRequest $request
     * @return JsonResponse
     */
    public function destroy(DeleteUserRequest $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->userEloquent->userSoftDelete($request->all(), $payload["company_id"], $request->ip()),
            Module::SECURITY,
        );
    }

    /**
     * Filter by user Permission
     *
     * @param string $companyId company uuid
     * @return JsonResponse
     */
    public function filterbyUserPermission(string $companyId)
    {
        return $this->successResponse(
            $this->userEloquent->filterbyUserPermission($companyId),
            Module::SECURITY,
        );
    }

    public function updateFirstLogin(string $id)
    {
        return $this->successResponse(
            $this->userEloquent->updateFirstLogin($id),
            Module::SECURITY,
        );
    }

    /**
     * get users availbe
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function usersAvailable(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->userEloquent->usersAvailable($payload["company_id"]),
            Module::SECURITY,
        );
    }

    /**
     * get users availbe
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function addCompanyJwt(Request $request): JsonResponse
    {
        return $this->successResponse(
            ExtractJwtJsonHelper::getConvertToken($request),
            Module::SECURITY,
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getUsersActiveMembership(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->userEloquent->getUsersActiveMembership(),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }
}
