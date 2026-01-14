<?php


namespace App\Infrastructure\Persistence;

use App\Http\Resources\PointSale\PointSaleResource;
use App\Models\PointSale;
use Illuminate\Support\Str;

class PointSaleEloquent
{
    /**
     * @var PointSale
     */
    private $model;

    public function __construct()
    {
        $this->model = new PointSale();
    }

    public function getByIds(array $ids)
    {
        return $this->model::whereIn('id',$ids)->get();
    }

    function save(array $pointSale)
    {
        $pointSale = $this->model::updateOrCreate(
            [
                'id' => $pointSale['id'] ?? Str::uuid()->toString()
            ], $pointSale
        );
        return new PointSaleResource($pointSale);
    }

    public function delete(string $id)
    {
        return ['status' => $this->model::findOrFail($id)->delete()];
    }

    public function deleteByIds(array $ids)
    {
        return $this->model::whereIn('id',$ids)->delete();
    }
}
