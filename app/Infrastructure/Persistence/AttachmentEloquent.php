<?php


namespace App\Infrastructure\Persistence;


use App\Http\Resources\CompanyResource;
use App\Infrastructure\Formulation\BinnacleHelper;
use App\Infrastructure\Formulation\BucketHelper;
use App\Infrastructure\Formulation\MembershipHelper;
use App\Infrastructure\Formulation\UtilsHelper;
use App\Infrastructure\Formulation\WebsiteHelper;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class AttachmentEloquent
{
    use ResponseApiTrait;

    /**
     * @var Attachment
     */
    private $attachmentModel;

    public function __construct()
    {
        $this->attachmentModel = new Attachment();
    }

    /**
     *  Update or create an attachment by name
     *  Validate if the $name is 'logo' and try to generate an electronic document preview
     *
     * @param array $data
     * @param string $company
     * @param string $name parameter 'folder' in request
     * @return Builder|Model
     * @throws GuzzleException
     */
    public function updateCreate(array $data, string $company, string $name, string $ip = '172.18.0.1')
    {
        //Validate if the electronic document preview is generated
        if ($name === 'logo-invoice' || $name === 'logo-support-documents') {
            $companyDetails = Company::with(['fiscalResponsibilities', 'ciius'])->firstWhere('id', $company);
            $dynamicResource = [
                [
                    'model' => 'TaxDetail',
                    'constraints' => [
                        [
                            'field' => 'id',
                            'operator' => '=',
                            'parameter' => $companyDetails->tax_detail,
                        ]
                    ],
                    'fields' => [],
                    'multiple_record' => false
                ],
                [
                    'model' => 'FiscalResponsibility',
                    'constraints' => [],
                    'fields' => [],
                    'multiple_record' => true
                ],
                [
                    'model' => 'Ciiu',
                    'constraints' => [
                        [
                            'field' => 'code',
                            'operator' => '=',
                            'parameter' => $companyDetails->ciius()->where('is_main', true)->get()->first()->code ?? 0
                        ]
                    ],
                    'fields' => [],
                    'multiple_record' => false
                ]
            ];

            $modules = MembershipHelper::getAllMembershipModules()['modules'];

            $utils = UtilsHelper::dynamicResource($dynamicResource);

            $companyResource = CompanyResource::make($companyDetails)->additional([
                'modules' => $modules,
                'utils' => $utils
            ]);
            $websiteDomain = WebsiteHelper::getDomain(Company::COMPANY_CCXC);
            $bucketDetailData = [
                'supporting_document' => false,
                'domain' => $websiteDomain['domain'],
                'logo' => $data['id'] ?? null,
                'company_name' => $companyDetails->name ?? null,
                'supplier_economic_activity' => $companyResource['ciius'] ? implode(',', array_column($companyResource['ciius']->toArray(), 'code')) : null,
                'supplier_responsibilities_resolutions' => collect(collect($companyResource)['fiscal_responsibilities'])->map(function ($responsibility) {
                    return ['code' => $responsibility['code'], 'resolution' => $responsibility['number_resolution'], 'date' => $responsibility['date']];
                })
            ];
            $invoiceBucketDetail = BucketHelper::getElectronicInvoicePreview($bucketDetailData);
            abort_if(
                !isset($invoiceBucketDetail['url']),
                Response::HTTP_BAD_REQUEST,
                'There is an error in the generation of the invoice preview document'
            );

            $bucketDetailData['supporting_document'] = true;
            $supportDocumentBucketDetail = BucketHelper::getElectronicInvoicePreview($bucketDetailData);
            abort_if(
                !isset($supportDocumentBucketDetail['url']),
                Response::HTTP_BAD_REQUEST,
                'There is an error in the generation of the supporting document preview'
            );

            if (isset($data['logo-invoice'])) {
                if (auth()->user()) {
                    BinnacleHelper::internalActivity(
                        $ip,
                        auth()->user()->id,
                        auth()->user()->name,
                        auth()->user()->email,
                        $company,
                        'Facturación electrónica',
                        'Modificó el logo'
                    );
                }
            }
            if (isset($data['logo-support-documents'])) {
                if (auth()->user()) {
                    BinnacleHelper::internalActivity(
                        $ip,
                        auth()->user()->id,
                        auth()->user()->name,
                        auth()->user()->email,
                        $company,
                        'Documento soporte',
                        'Modificó el logo'
                    );
                }
            }
        } else {
            BinnacleHelper::internalActivity(
                $ip,
                auth()->user()->id,
                auth()->user()->name,
                auth()->user()->email,
                auth()->user()->company_id,
                'Facturación electrónica',
                'Modificó el RUT'
            );
        }
        return $this->attachmentModel->query()
            ->whereNull('deleted_at')
            ->updateOrCreate(
                [
                    'name' => $name,
                    'company_id' => $company
                ],
                [
                    'name' => $name,
                    'bucket_id' => $data['id'] ?? null,
                    'company_id' => $company,
                    'preview_url' => $invoiceBucketDetail['url'] ?? null,
                    'supporting_document_preview_url' => $supportDocumentBucketDetail['url'] ?? null
                ]
            );
    }

    /**
     * @param string $companyId
     * @param string $file
     * @return bool|null
     * @throws GuzzleException
     */
    public function deleteAttachment(string $companyId, string $file): bool
    {
        $attachment = $this->getAttachment($companyId, $file);
        if (BucketHelper::deleteBucketDetail($attachment['bucket_id']) == []) {
            $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_NOT_FOUND,
                'There is not attachment to delete'
            );
        }
        return $attachment->delete();
    }

    /**
     * @param string $companyId
     * @param string|null $file
     *
     * @return Model|null
     */
    public function getAttachment(string $companyId, string $file): ?Model
    {
        $bucket = $this->attachmentModel->query()
            ->whereNull('deleted_at')
            ->where([
                'name' => $file,
                'company_id' => $companyId
            ])
            ->first();
        if (isset($bucket["bucket_id"]) && BucketHelper::getBucketDetailByBucketId($bucket["bucket_id"])) {
            return $bucket;
        }
        return null;
    }

    public function updateAttachment(array $request, string $companyId, string $previewUrl)
    {
        return $this->attachmentModel->query()
            ->whereNull('deleted_at')
            ->updateOrCreate(
                [
                    'name' => $request['file'],
                    'company_id' => $companyId
                ],
                [
                    'bucket_id' => $request['id'] ?? null,
                    'preview_url' => $previewUrl ?? null
                ]
            );
    }
}
