<?php

namespace App\Infrastructure\Persistence;

use App\Http\Resources\PrefixResource;
use App\Infrastructure\Formulation\BinnacleHelper;
use App\Infrastructure\Formulation\ElectronicInvoiceHelper;
use App\Infrastructure\Formulation\NotificationHelper;
use App\Infrastructure\Services\InvoiceService;
use App\Models\Prefix;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrefixEloquent
{
    private $model;
    private $invoiceService;

    public function __construct()
    {
        $this->model = new Prefix();
        $this->invoiceService = new InvoiceService();
    }

    public function store(array $data, string $ip)
    {
        $prefixes = collect($data)->map(function ($data) {
            return $this->model::UpdateOrCreate(
                [
                    'id' => $data['id'] ?? Str::uuid()->toString(),
                    'company_id' => $data['company_id']
                ],
                $data
            );
        });
        $changes = $prefixes->filter(function ($prefixes) {
            return $prefixes->wasChanged();
        });
        BinnacleHelper::internalActivity(
            $ip,
            auth()->user()->id,
            auth()->user()->name,
            auth()->user()->email,
            auth()->user()->company_id,
            'Facturación electrónica',
            ($changes->count() > 0 ? 'Modificó' : 'Agregó') . ' resolución de facturas de contingencia'
        );

        return $prefixes;
    }

    public function storeNotes(array $data, string $ip)
    {
        $date = Carbon::now();
        $prefixes = collect($data)->map(function ($data) use ($date) {
            $data['initial_validity'] = $date;
            $data['final_validity'] = $date->addHours(100);
            $data['final_authorization_range'] = 995000000;
            $data['initial_authorization_range'] = 000000001;
            return $this->model::UpdateOrCreate(
                [
                    'id' => $data['id'] ?? Str::uuid()->toString(),
                    'company_id' => $data['company_id']
                ],
                $data
            );
        });

        $changes = $prefixes->filter(function ($prefixes) {
            return $prefixes->wasChanged();
        });

        BinnacleHelper::internalActivity(
            $ip,
            auth()->user()->id,
            auth()->user()->name,
            auth()->user()->email,
            auth()->user()->company_id,
            'Facturación electrónica',
            ($changes->count() > 0 ? 'Modificó' : 'Agregó') . ' prefijo nota débito/crédito'
        );

        return $prefixes;
    }

    /**
     * Get the prefixes by type and company in order of last used
     *
     * @param string $companyId
     * @param array $types
     * @service INVOICE /invoices/consecutives/last-by-prefix
     * @return AnonymousResourceCollection
     */
    public function getPrefix(string $companyId, array $types): AnonymousResourceCollection
    {
        $prefixes = $this->model::where('company_id', $companyId)->whereIn('type', $types)->orderBy('initial_validity', 'desc')->get();
        return PrefixResource::collection($prefixes);
    }

    public function getSpecificPrefix(array $data)
    {
        return $this->model::where('company_id', $data['company_id'])->where('id', $data['prefix_id'])->first();
    }

    /**
     * Delete of prefixes if there is not record in invoices
     * @param array $request
     * @param string $ip
     * @service INVOICE /invoices/consecutives/last-by-prefix
     *
     */
    public function deletePrefixes(array $request, string $ip)
    {
        $companyId = auth()->user()->company_id;
        $prefixes = $this->model::whereIn('id', $request)->get();
        // Extract the IDs from the records and create a flat array
        $prefixesIds = $prefixes->map->only(['id'])->flatten()->toArray();
        $consecutive = $this->invoiceService->getLastConsecutiveByPrefix(
            $companyId,
            ['prefixes' => $prefixesIds]
        )['data'];
        // Extract the values of the 'prefix_id' key from each element in the $consecutive array.
        $consecutiveIds = array_column($consecutive, 'prefix_id');
        // Delete records with IDs in $prefixesIds but not in $consecutiveIds.
        $this->model::whereIn('id', array_diff($prefixesIds, $consecutiveIds))->delete();
        BinnacleHelper::internalActivity(
            $ip,
            auth()->user()->id,
            auth()->user()->name,
            auth()->user()->email,
            $companyId,
            'Facturación electrónica',
            'Eliminó prefijo nota débito/crédito [' . implode(', ', array_diff($prefixesIds, $consecutiveIds)) . ']'
        );

        // Retrieve records with IDs in $consecutiveIds but not in $prefixes.
        return $this->model::whereIn('id', array_diff($consecutiveIds, $prefixesIds))->get();
    }

    public function getSynchronize(array $request, string $companyId)
    {
        $resolutions = ElectronicInvoiceHelper::getResolutions($request, $companyId);
        collect($resolutions)->each(function ($resolution) use ($companyId) {
            try {
                $this->model::firstOrCreate(
                    [
                        'resolution_number' => $resolution['resolution_number'],
                        'prefix' => $resolution['resolution_prefix'],
                    ],
                    [
                        'type' => $resolution['resolution_type'],
                        'initial_validity' => Carbon::parse($resolution['resolution_date_from'])->getTimestamp(),
                        'final_validity' => Carbon::parse($resolution['resolution_date_to'])->getTimestamp(),
                        'final_authorization_range' => $resolution['resolution_to'],
                        'initial_authorization_range' => $resolution['resolution_from'],
                        'resolution_technical_key' => $resolution['resolution_technical_key'],
                        'company_id' => $companyId,
                    ]
                );
            } catch (\Exception $e) {
                Log::warning(sprintf(
                    'Error in %s line %s message %s',
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage()
                ));
            }
        });
        return $this->getPrefix($companyId, [Prefix::INVOICE, Prefix::SUPPORTING_DOCUMENT, Prefix::UNASSIGNED]);
    }

    public function rankDepletionPrefix(array $data)
    {
        $prefix = $this->model::findOrFail($data['prefix_id']);
        $totalConsecutives = $prefix->final_authorization_range - $prefix->initial_authorization_range;
        $tenPercent = $prefix->initial_authorization_range + ($totalConsecutives * 0.9);
        if ($data['number'] >= $tenPercent) {
            return NotificationHelper::storeNotification($prefix, Prefix::RANK_DEPLETION_NOTIFICATION);
        }
    }

    /**
     * get or create purchase supplier prefixes
     *
     * @param string $companyId
     * @param array $data
     *
     * @return AnonymousResourceCollection
     */
    public function getPrefixPurchase(string $companyId, array $data): AnonymousResourceCollection
    {
        if (isset($data['prefix']) && $data['prefix']) {
            $date = Carbon::now();
            $data['company_id'] = $companyId;
            $data['type'] = Prefix::PURCHASE_SUPPLIER;
            $data['initial_validity'] = $date;
            $data['final_validity'] = $date->addYear(10);
            $data['final_authorization_range'] = Prefix::FINAL_AUTHORIZATION_RANGE;
            $data['initial_authorization_range'] = Prefix::INITIAL_AUTHORIZATION_RANGE;
            $prefixModel = $this->model::firstOrNew(
                [
                    'company_id' => $data['company_id'],
                    'prefix' => $data['prefix'],
                ],
                $data
            );
            $prefixModel->save();
        }

        return PrefixResource::collection(Prefix::where('type', Prefix::PURCHASE_SUPPLIER)->where('company_id', $companyId)->get());
    }

    /**
     *
     * @param string $companyId
     * @param array $request
     * @return AnonymousResourceCollection
     */
    public function setResolutionType(string $companyId, array $request): AnonymousResourceCollection
    {
        collect($request)->map(function ($item) use ($companyId) {
            $resolution = $this->model->where('id', $item['resolution_id'])->where('company_id', $companyId)->first();
            if ($resolution) {
                $resolution->type = $item['type'];
                $resolution->contingency = $item['contingency'];
                $resolution->save();
            }
        });

        return PrefixResource::collection($this->model->where('company_id', $companyId)->get());
    }
}
