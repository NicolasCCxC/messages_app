<?php

namespace App\Observers;

use App\Infrastructure\Formulation\WebsiteHelper;
use App\Models\Company;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     *
     * @param Company $company
     * @return void
     */
    public function created(Company $company)
    {
        //
    }

    /**
     * Handle the Company "updated" event.
     *
     * @param Company $company
     * @return void
     */
    public function updated(Company $company)
    {
        $originalDomain = $company->getOriginal('domain');
        if($company->isDirty('domain')){
            //if the domain already exist and the model contain old diggypyme subdomain and the new domain does have subdomain
            if($originalDomain !== null && str_contains($originalDomain, env('SUBDOMAIN')) && !str_contains($company->domain, env('SUBDOMAIN'))){
                $company->domain = $originalDomain;
                $company->save();

                $website = WebsiteHelper::updateDomain([
                    'company_id' => $company->company_id,
                    'domain' => $company->domain['domain'],
                    'company_name' => $company->name
                ]);

            }
        }
    }

    /**
     * Handle the Company "deleted" event.
     *
     * @param Company $company
     * @return void
     */
    public function deleted(Company $company)
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     *
     * @param Company $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     *
     * @param Company $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}
