<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Infrastructure\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateHistoricalInvoiceMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-history-invoice-memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the invoice history to synchronize the counters with the invoices created in Invoice.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyModel = new Company;
        $invoiceService = new InvoiceService;
        $membershipModel = new Membership;
        $membershipHasModulesModel = new MembershipHasModules;
        $membershipSubModuleModel = new MembershipSubModule;

        $companyModel::all()->each(function ($company) use ($invoiceService, $membershipModel, $membershipHasModulesModel, $membershipSubModuleModel) {

            $numberDocumentsFromInvoice = ($invoiceService->getNumberInvoicesCreated($company->id)['data'] ?? 0);
            if ($numberDocumentsFromInvoice != 0) {
                $this->info('updating company: ' . $company->id);
                $documentsModulesIds = MembershipHasModules::SUBMODULES_INVOICE_WITH_INVENTORY_ADJUSTMENT;
                $baseQuery = DB::table('memberships as m')
                    ->join('membership_has_modules as mm', 'm.id', '=', 'mm.membership_id')
                    ->join('membership_submodules as mms', 'mm.id', '=', 'mms.membership_has_modules_id')
                    ->where('m.company_id', '=', $company->id)
                    ->where('m.payment_status', Membership::PAYMENT_STATUS_APPROVED)
                    ->where('m.payment_method', Membership::PAYMENT_METHOD_PAYU)
                    ->whereIn('mms.sub_module_id', $documentsModulesIds);

                $numberDocumentsPurchased = (clone $baseQuery)
                    ->sum('mms.total_invoices');

                $numberDocumentsRemaining = (clone $baseQuery)
                    ->sum('mms.remaining_invoices');

                $totalDocumentsAvailableInvoice = $numberDocumentsPurchased - $numberDocumentsFromInvoice ?? 0;
                $unlimitedDocuments = (clone $baseQuery)->where('sub_module_id', 11)->where('mm.expiration_date', '>', Carbon::now())->first();
                if ($unlimitedDocuments != null) {
                    if ($unlimitedDocuments->total_invoices && $unlimitedDocuments->total_invoices != 0) {
                        $numberDocumentsFromInvoice = $numberDocumentsFromInvoice - $numberDocumentsRemaining;
                        if ($numberDocumentsFromInvoice > 0) {
                            $membershipSubModuleModel::find($unlimitedDocuments->id)->update([
                                'total_invoices' => $numberDocumentsFromInvoice
                            ]);
                        }
                    }
                } else {
                    if ($totalDocumentsAvailableInvoice != $numberDocumentsRemaining) {
                        $activeDocuments = (clone $baseQuery)->orderBy('m.initial_date', 'asc')->get();
                        foreach ($activeDocuments as $module) {
                            if ($module->total_invoices != null) {
                                if ($numberDocumentsFromInvoice > $module->total_invoices) {
                                    $membershipSubModuleModel::find($module->id)->update([
                                        'remaining_invoices' => 0
                                    ]);
                                    $numberDocumentsFromInvoice = $numberDocumentsFromInvoice - $module->total_invoices;
                                } elseif ($numberDocumentsFromInvoice > 0 && $numberDocumentsFromInvoice < $module->total_invoices) {
                                    $this->activeInvoiceMembership($module, $membershipModel, $membershipHasModulesModel);
                                    $membershipSubModuleModel::find($module->id)->update([
                                        'remaining_invoices' => $module->total_invoices - $numberDocumentsFromInvoice,
                                        'is_active' => true
                                    ]);
                                    $numberDocumentsFromInvoice = 0;
                                } else {
                                    $this->activeInvoiceMembership($module, $membershipModel, $membershipHasModulesModel);
                                    $membershipSubModuleModel::find($module->id)->update([
                                        'remaining_invoices' => $module->total_invoices,
                                        'is_active' => true
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->info('Update the invoice history completed');
    }


    /**
     * Function that activates invoice memberships, modules and submodules
     *
     * @param mixed $module The actual module invoice object.
     * @param Membership $membershipModel Model membership.
     * @param MembershipHasModules $membershipHasModules Model membership has module.
     * @return void
     */
    public function activeInvoiceMembership($module, Membership $membershipModel, MembershipHasModules $membershipHasModules): void
    {
        $membership = $membershipModel::find($module->membership_id);
        if (!$membership->is_active) {
            $membership->update(['is_active' => true]);
        }
        $membershipHasModules::where('membership_id', $module->membership_id)->WhereNotIn('membership_modules_id', [2, 4, 16])->update(['is_active' => true]);
    }
}
