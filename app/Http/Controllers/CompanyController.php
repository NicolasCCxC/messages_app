<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\AttachmentRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyBillingRequest;
use App\Http\Requests\Company\UpdateDomainRequest;
use App\Http\Requests\Company\UpdateCompanyAccountCreated;
use App\Infrastructure\Formulation\GatewayHelper;
use App\Helpers\ExtractJwtJsonHelper;
use App\Http\Requests\Company\UpdateCompanyMinimunDataRequest;
use App\Infrastructure\Persistence\CompanyEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class CompanyController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var CompanyEloquent
     */
    private $companyEloquent;

    /**
     * @var Request
     */
    private $request;

    public function __construct(CompanyEloquent $companyEloquent, Request $request)
    {
        $this->companyEloquent = $companyEloquent;
        $this->request = $request;
    }

    /**
     * Company information is obtained by id company
     *
     * @param string $company_id - necessary information of the request
     * @return JsonResponse
     */
    public function index(string $company_id): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getCompanyInfo($company_id),
            Module::SECURITY
        );
    }

    /**
     * Company information is obtained by email
     *
     * @param Request $request - necessary information of the request
     * @return JsonResponse
     */
    public function getCompanyInfoWithEmail(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyEloquent->getCompanyInfoWithEmail($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->storeCompany(
                $request->all(),
                $request->ip()
            ),
            Module::SECURITY
        );
    }

    /**
     * @param $id
     * @param UpdateCompanyRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function update($id, UpdateCompanyRequest $request): JsonResponse
    {
        //Return error 404 response if Company was not found
        if (!$this->companyEloquent->getCompanyInfo($id))
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_NOT_FOUND,
                'Company not found!'
            );

        return $this->successResponse(
            $this->companyEloquent->update($id, $request),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }

    /**
     * Obtains customer and user information by company ID
     *
     * @param Request $request - necessary information of the request
     * @return JsonResponse
     */
    public function getUsersAndClients(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyEloquent->getUserAndClients($payload["company_id"]),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }

    /**
     * Upload a company attachment - Accepted 'RUT' and 'logo'
     *
     * @param AttachmentRequest $request
     * @return JsonResponse
     * @throws GuzzleException Upload and document preview generation in Bucket
     */
    public function loadCompanyAttachment(AttachmentRequest $request): JsonResponse
    {
        if ($request->get('is_bucket_detail_id', false)) {
            $bucket_information = json_decode($request->get('bucket_data'), true);
        } else {
            $bucket_information = GatewayHelper::uploadHandler($request);
        }

        return $this->successResponse(
            $this->companyEloquent->createUpdateCompanyAttachment($bucket_information['data'], $request['company_id'], $request['folder'], $request->ip()),
            Module::SECURITY
        );
    }

    /**
     * @param string $id
     * @param string $name
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function deleteCompanyAttachment(string $id, string $name): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->deleteCompanyAttachment($id, $name),
            Module::SECURITY
        );
    }

    /**
     * @param Request $request
     * @param string $companyId
     * @param string $name
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getCompanyAttachment(Request $request, string $company, string $name): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getCompanyAttachment($company, $name, $request->ip()),
            Module::SECURITY
        );
    }

    public function updateDomain(UpdateDomainRequest $request)
    {
        return $this->successResponse(
            $this->companyEloquent->updateDomain($request->all()),
            Module::SECURITY
        );
    }

    /**
     * Search domain by company id
     *
     * @param Request $request - necessary information of the request
     * @return JsonResponse
     */
    public function getDomain(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyEloquent->getDomain($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * get invoices available
     *
     * @param string $company - uuid company
     * @return JsonResponse
     */
    public function getInvoicesAvailable(string $company): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getInvoicesAvailable($company, false),
            Module::SECURITY
        );
    }

    /**
     * get invoices available
     *
     * @param string $company - company uuid
     * @return JsonResponse
     */
    public function getSupportingDocumentAvailable(string $company): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getInvoicesAvailable($company, true),
            Module::SECURITY
        );
    }


    /**
     * Update the count of used electronic documents for a company.
     *
     * @param string $company - company uuid
     * @return JsonResponse
     */
    public function counterUsedElectronicDocuments(string $company): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->counterUsedElectronicDocuments($company),
            Module::SECURITY
        );
    }

    /**
     * get activate memberships
     *
     * @param string $company - company uuid
     * @return JsonResponse
     */
    public function getActiveMemberships(string $company): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getActiveMembershipsByCompany($company),
            Module::SECURITY
        );
    }

    /**
     * update information Billing
     *
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function updateInformationBilling(UpdateCompanyBillingRequest $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyEloquent->updateInformationBilling($payload["company_id"], $request->all()),
            Module::SECURITY
        );
    }


    public function getNamesCompanies(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getNamesCompanies($request->all()),
            Module::SECURITY
        );
    }

    /**
     * get companies administration
     *
     * @return JsonResponse
     */
    public function getCompaniesAdministration(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        if ($payload["company_id"] != Company::COMPANY_CCXC) {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_UNAUTHORIZED,
                'Unauthorized'
            );
        }
        return $this->successResponse(
            $this->companyEloquent->getCompaniesAdministration(),
            Module::SECURITY
        );
    }

    /**
     * @param $id
     * @param UpdateCompanyAccountCreated $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function updateAccountCreated(UpdateCompanyAccountCreated $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyEloquent->updateAccountCreated($payload["company_id"], $request),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }

    /*
    * Retrieves a client's company logo based on the provided data
    * @param Request $request
    * @return JsonResponse
    */
    public function getClientCompanyLogo(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getClientCompanyLogo(
                $request->all(),
                $request->ip()
            ),
            Module::SECURITY
        );
    }

    /**
     * Maintain the minimum data of a company
     * @param $id
     * @param UpdateCompanyMinimunDataRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function updateCompanyMinimunData($id, UpdateCompanyMinimunDataRequest $request): JsonResponse
    {
        //Return error 404 response if Company was not found
        if (!$this->companyEloquent->getCompanyInfo($id))
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_NOT_FOUND,
                'Company not found!'
            );

        return $this->successResponse(
            $this->companyEloquent->updateCompanyMinimunData($id, $request),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }

    /**
     * @return JsonResponse
     *
     * Retrieves all the companies.
     */
    public function getAllCompanies()
    {
        return $this->successResponse(
            $this->companyEloquent->getAllCompanies(),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }

    public function getInformationByBilling(string $companyId): JsonResponse
    {
        return $this->successResponse(
            $this->companyEloquent->getInformationByBilling($companyId),
            Module::SECURITY,
            Response::HTTP_OK
        );
    }
}
