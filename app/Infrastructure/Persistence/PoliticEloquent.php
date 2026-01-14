<?php

namespace App\Infrastructure\Persistence;

use App\Http\Resources\PrivacyPurposesResource;
use App\Http\Resources\PoliticResource;
use App\Infrastructure\Formulation\BinnacleHelper;
use App\Infrastructure\Formulation\BucketHelper;
use App\Models\Company;
use App\Models\Module;
use App\Models\Politic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use \Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\PrivacyPurpose;

class PoliticEloquent
{

    private $model;
    private $modelPrivacyPurposes;
    private $modelCompany;
    private $servicesModule;

    public function __construct()
    {
        $this->model = new Politic();
        $this->servicesModule = new Module();
        $this->modelCompany = new Company();
        $this->modelPrivacyPurposes = new PrivacyPurpose();
    }

    public function storesPolitics(array $data, string $ip = null): AnonymousResourceCollection
    {
        collect($data)->each(function (UploadedFile $item, $key) {
            try {
                $module = $this->servicesModule::where('name', 'BUCKET')->first();
                $response = Http::withToken($module->token)
                    ->attach(
                        'file',
                        $item->get(),
                        $item->getClientOriginalName())
                    ->post($module->description . '/bucket/upload-file',
                        [
                            'company_id' => auth()->user()->company_id,
                            'folder' => 'politic',
                            'service' => 'SECURITY',
                            'data' => '{}',
                        ]
                    );

                $this->model::where([
                    'company_id' => auth()->user()->company_id,
                    'type' => strtoupper($key)
                ])
                    ->delete();

                $this->model::create([
                    'type' => strtoupper($key),
                    'company_id' => auth()->user()->company_id,
                    'bucket_details_id' => $response['data']['id'] ?? Str::uuid()->toString(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Error on politic upload: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        });
        BinnacleHelper::internalActivity(
            $ip,
            auth()->user()->id,
            auth()->user()->name,
            auth()->user()->email,
            auth()->user()->company_id,
            'Perfil de la empresa',
            'Modificó políticas'
        );
        return PoliticResource::collection($this->assignUrls(auth()->user()->company_id));
    }

    public function getById(array $data)
    {
        $politic = $this->model::where([
            'company_id' => $data['company_id'],
            'type' => $data['type']
        ])->first();

        if (!$politic) return [];

        $detail = BucketHelper::getUrl($politic->bucket_details_id);
        if ($detail) {
            $politic['url'] = $detail['url'];
            $politic['name'] = $detail['file_original_name'];
        }
        return new PoliticResource($politic);
    }


    public function getAllPolitics($companyId): Collection
    {
        return $this->assignUrls($companyId);
    }

    public function delete(string $id): Collection
    {
        $politic = $this->model::findOrFail($id);
        $politic->delete();
        return $this->getAllPolitics(auth()->user()->company_id);
    }

    public function storeDataPrivacyPolicy(string $companyId, array $data)
    {
        $this->model::where([
            'company_id' => $companyId,
            'type' => $this->model::DATA_PRIVACY_POLICY
        ])
            ->delete();

        $this->model::create([
            'type' => $this->model::DATA_PRIVACY_POLICY,
            'company_id' => $companyId,
            'bucket_details_id' => $data['policy_uuid'],
        ]);

        return $this->getAllPolitics($companyId);
    }

    private function assignUrls(string $companyId)
    {
        $politics = $this->model::where('company_id', $companyId)->get();
        $details = collect(BucketHelper::getList($politics->pluck('bucket_details_id')));
        return collect($politics)->map(function ($politic) use ($details) {
            $select = $details->where('id', $politic->bucket_details_id)->first();
            $politic['url'] = $select['url'] ?? 'not found';
            $politic['name'] = $select['file_original_name'] ?? 'not found';

            return $politic;
        });
    }

    /**
     * Get all purposes by company
     *
     * @return AnonymousResourceCollection
     */
    public function getPurposeByCompanyId(string $companyId): AnonymousResourceCollection
    {
        // Get default privacy purposes
        $purposesFromPrivacyPurposes = $this->modelPrivacyPurposes::where('is_default', true)->get();
        // Get only company-related purposes where is_default is false
        $purposesFromPivotTable = $this->modelCompany::with('privacyPurposes')
            ->where('id', $companyId)
            ->get()
            ->flatMap(function ($company) {
                return $company->privacyPurposes->where('is_default', false);
            });
        // It combines the finalities of both collections, contains all finalities with is_default set to false, including those with no records in the pivot table.
        $allPurposes = $purposesFromPrivacyPurposes->concat($purposesFromPivotTable);

        return PrivacyPurposesResource::collection($allPurposes);
    }

    /**
     * Store or update only the purpose description field
     *
     * @param $data
     * @return PrivacyPurposesResource
     */
    public function storeOrUpdatePurpose(array $data, string $companyId): PrivacyPurposesResource
    {
        $purpose = $this->modelPrivacyPurposes::updateOrCreate(
            ['id' => $data['id'] ?? Str::uuid()->toString()],
            $data
        );
        $company = $this->modelCompany::find($companyId);
        // Synchronize company purposes in pivot table without deleting existing relationships
        $company->privacyPurposes()->sync([$purpose->id], false);

        return PrivacyPurposesResource::make($purpose);
    }

    /**
     * Destroy a purpose
     *
     * @param $purposeId
     * @param bool
     */
    public function deletePurposeById(string $purposeId): bool
    {
        return $this->modelPrivacyPurposes::find($purposeId)->delete();
    }

}
