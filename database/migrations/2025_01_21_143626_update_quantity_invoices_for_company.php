<?php

use App\Infrastructure\Services\InvoiceService;
use App\Models\Company;
use App\Models\MembershipSubModule;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companyModel = new Company;
        $invoiceService = new InvoiceService;
        $membershipSubModuleModel = new MembershipSubModule;

        $membershipSubModuleModel->all()->each(function ($membershipSubModule) {
            $membershipSubModule->total_invoices = $membershipSubModule->total_invoices ?? 0;
            $membershipSubModule->remaining_invoices = $membershipSubModule->remaining_invoices ?? 0;
            $membershipSubModule->save();
        });

        $companyModel::all()->each(function ($company) use ($invoiceService, $membershipSubModuleModel) {

            $numberDocumentsFromInvoice = ($invoiceService->getNumberInvoicesCreated($company->id)['data'] ?? 0);

            $documentsModulesIds = [1, 2, 3, 4, 11];
            $baseQuery = DB::table('memberships as m')
                ->join('membership_has_modules as mm', 'm.id', '=', 'mm.membership_id')
                ->join('membership_submodules as mms', 'mm.id', '=', 'mms.membership_has_modules_id')
                ->where('m.company_id', '=', $company->id)
                ->where('m.payment_status', 'APPROVED')
                ->where('m.payment_method', 'PAYU')
                ->whereIn('mms.sub_module_id', $documentsModulesIds);

            $numberDocumentsPurchased = (clone $baseQuery)
                ->sum('mms.total_invoices');

            $numberDocumentsRemaining = (clone $baseQuery)
                ->sum('mms.remaining_invoices');

            $moduleCompany = (clone $baseQuery)
                ->orderBy('m.initial_date', 'asc')
                ->first();

            if ($moduleCompany == null)
                return;

            $numberDocumentsUsed = $numberDocumentsPurchased - $numberDocumentsRemaining;
            if ($numberDocumentsUsed < $numberDocumentsFromInvoice) {
                $numberInvoices = $numberDocumentsFromInvoice - $numberDocumentsUsed;

                $activeDocuments = (clone $baseQuery)
                    ->get();

                // Verify if the company has unlimited documents module
                $unlimitedDocuments = $activeDocuments->where('sub_module_id', 11)->where('expiration_date', '>', Carbon::now())->first();

                if ($unlimitedDocuments != null) {
                    $membershipSubModuleModel::find($unlimitedDocuments->id)->update([
                        'total_invoices' => $numberInvoices,
                    ]);
                } else {
                    $remaining_invoices = ($company->invoices_available > $numberInvoices) ? $company->invoices_available - $numberInvoices : 0;
                    $membershipSubModuleModel::create([
                        'membership_has_modules_id' => $moduleCompany->membership_has_modules_id,
                        'sub_module_id' => 0,
                        'total_invoices' => ($company->invoices_available > $numberInvoices) ? $company->invoices_available : $numberInvoices,
                        'remaining_invoices' => $remaining_invoices,
                        'is_active' => $remaining_invoices > 0,
                        'expiration_date' => $moduleCompany->expiration_date,
                        'price' => 0,
                        'months' => 0,
                        'name' => 'Documentos electrónicos - Paquete ajuste inventario',
                        'price_old' => 0
                    ]);
                }

                if ($company->invoices_available > 0) {
                    $company->invoices_available = 0;
                    $company->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
