<?php

namespace App\Infrastructure\Persistence;

use App\Models\Ciiu;

class CiiuEloquent
{

    private $model;

    public function __construct(Ciiu $ciiu)
    {
        $this->model= $ciiu;
    }

    public function storeCiuu(string $companyId, array $data)
    {
        $idsCiius = collect($data)->map(function ($value, $index) use($companyId) {
            return $this->model::firstOrCreate([
                'company_id' => $companyId,
                'code' => $value['code'],
                'name' => $value['name'],
                'ciiu_id' => $value['ciiu_id'],
                'is_main' => $index === 0,
            ]);
        });


        $this->model::where('company_id',$companyId)
            ->whereNotIn('id', $idsCiius->pluck('id'))
            ->delete();

    }
}
