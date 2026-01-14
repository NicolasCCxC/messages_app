<?php


namespace App\Infrastructure\Persistence;


use App\Http\Resources\PhysicalStore\PhysicalStoreResource;
use App\Models\PhysicalStore;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class PhysicalStoreEloquent
{
    /**
     * @var PhysicalStore
     */
    private $model;

    /**
     * @var PointSaleEloquent
     */
    private $pointSaleEloquent;



    public function __construct(PointSaleEloquent $pointSaleEloquent)
    {
        $this->model = new PhysicalStore();
        $this->pointSaleEloquent = $pointSaleEloquent;
    }

    /**
     * @param string $companyId
     * @return PhysicalStoreResource
     * @throws GuzzleException
     */
    public function getAllPhysicalStoresByCompany(string $companyId)
    {
        return PhysicalStoreResource::collection($this->model->query()->where('company_id', $companyId)->get());
    }

    /**
     * @param $request
     * @return PhysicalStoreResource
     * @throws GuzzleException
     */
    public function store(array $request)
    {
        $physicalStores = collect($request)->map(function($physicalStore){
            $physicalStoreModel = $this->save($physicalStore);
            $physicalStoreModel['point_sales'] = collect($physicalStore['point_sales'])->map(function($pointSale) use($physicalStoreModel){
                $pointSale['physical_store_id'] = $physicalStoreModel->id;
                return $this->pointSaleEloquent->save($pointSale);
            });
            return $physicalStoreModel;
        });
        return PhysicalStoreResource::collection($physicalStores);
    }

    function save(array $physicalStore)
    {
        $physicalStore = $this->model::updateOrCreate(
            [
                'id' => $physicalStore['id'] ?? Str::uuid()->toString()
            ], $physicalStore
        );
        return new PhysicalStoreResource($physicalStore);
    }

    public function delete(string $id)
    {
        return ['status' => $this->model::findOrFail($id)->delete()];
    }

    public function deletePointSale(string $id)
    {
        return $this->pointSaleEloquent->delete($id);
    }

    public function deletePhysicalStoreOrPointSaleByIds(array $ids)
    {
        $physicalStoreIds = $this->model::whereIn('id', $ids)->get();
        $pointSalesIds = $this->pointSaleEloquent->getByIds($ids);

        return [
            "physical_store" => $this->model::whereIn('id',$physicalStoreIds->pluck('id')->toArray())->delete() ? $physicalStoreIds : [],
            "point_sales" => $this->pointSaleEloquent->deleteByIds($pointSalesIds->pluck('id')->toArray()) ? $pointSalesIds : []
        ];
    }
}
