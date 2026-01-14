<?php

namespace App\Http\Resources;

use App\Infrastructure\Formulation\MembershipHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Infrastructure\Formulation\UtilsHelper;

class SubModulesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     */
    public function toArray($request)
    {
        $subModules = UtilsHelper::getUtils(['membership_sub_modules'])['membership_sub_modules'];
        $modules_filtered = collect($subModules)->filter(function ($subModule) {
            return $subModule['id'] === $this->sub_module_id;
        })->first();
        $modules_filtered['is_frequent_payment'] = $this->is_frequent_payment;

        return $modules_filtered;
    }
}
