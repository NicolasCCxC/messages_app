<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Membership;

class UpdateModulesSubmodules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $memberships = Membership::with(['modules' => function($query) {
            return $query
            ->whereNotNull('expiration_date')
            ->whereIn('membership_modules_id', [2, 3, 4]);
        },'modules.membershipSubmodules'])
        ->where('payment_method', '!=', null)
        ->whereNotNull('expiration_date')
        ->whereHas('modules')
        ->where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
        ->where('purchase_date', '<=','2024-06-30')->get();

        $memberships->map(function ($membership) {
            $date = $membership->purchase_date;
            if (is_numeric($date) && (int)$date == $date && $date >= 0) {
                $formattedDate = date('Y-m-d', $date);
            } else {
                $formattedDate = $date;
            }
            $membership->modules->map(function ($module) use($formattedDate) {
                switch ($module->membership_modules_id) {
                    case 2:
                        $module->name = "Sitio web y tienda virtual";
                    break;
                    case 3:
                        if($formattedDate < '2024-02-20') {
                            $module->name = "Facturación electrónica";
                        } else {
                            $module->name = "Documentos Electrónicos: Facturación electrónica, Documento Soporte, Notas débito y crédito, Notas de ajuste";
                        }                        
                    break;
                    case 4:
                        $module->name = "Administración de bodegas";
                    break;
                }
                $module->save();
                $module->membershipSubmodules->map(function ($submodule) use($formattedDate, $module) {
                    if($module->membership_modules_id == 2){
                        $websiteBaseName = 'Sitio web y tienda virtual - ';   
                        switch ($submodule->sub_module_id) {
                            case 5:
                                $submodule->name = $websiteBaseName."Plan Básico";
                            break;
                            case 7:
                                $submodule->name = $websiteBaseName."Plan Avanzado";
                            break;
                            case 6:
                                $submodule->name = $websiteBaseName."Plan Estándar";
                            break;
                            case 10:
                                $submodule->name = $websiteBaseName."Plan Premium";
                            break;
                        }
                    } else {
                        if($formattedDate < '2024-01-20') {
                            $invoiceBaseName = 'Facturación electrónica - ';
                            $dataInvoice = [     
                                [
                                    'id' => 1,
                                    'name' => $invoiceBaseName.'60 facturas',
                                ],
                                [
                                    'id' => 2,
                                    'name' => $invoiceBaseName.'120 facturas',
                                ],
                                [
                                    'id' => 3,
                                    'name' => $invoiceBaseName.'300 facturas',
                                ],
                                [
                                    'id' => 4,
                                    'name' => $invoiceBaseName.'720 facturas',
                                ],
                                [
                                    'id' => 11,
                                    'name' => $invoiceBaseName.'Ilimitados por año',
                                ],
                            ];
                        } else {
                            $invoiceBaseName = 'Documentos Electrónicos - ';
                            $dataInvoice = [     
                                [
                                    'id' => 1,
                                    'name' => $invoiceBaseName.'15 Documentos por año',
                                ],
                                [
                                    'id' => 2,
                                    'name' => $invoiceBaseName.'60 Documentos por año',
                                ],
                                [
                                    'id' => 3,
                                    'name' => $invoiceBaseName.'120 Documentos por año',
                                ],
                                [
                                    'id' => 4,
                                    'name' => $invoiceBaseName.'300 Documentos por año',
                                ],
                                [
                                    'id' => 11,
                                    'name' => $invoiceBaseName.'Ilimitados por año',
                                ],
                            ];
                        }  
                        $package = collect($dataInvoice)->where('id', $submodule->sub_module_id)->first();
                        if($package) {
                            $submodule->name = $package["name"];
                        }
                    }
                    $submodule->save();
                });
            });
        });
    }
}
