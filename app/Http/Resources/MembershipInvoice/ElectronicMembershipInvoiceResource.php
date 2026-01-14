<?php

namespace App\Http\Resources\MembershipInvoice;
use Illuminate\Support\Carbon;
use App\Http\Resources\MembershipInvoice\MembershipProductResource;

class ElectronicMembershipInvoiceResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function toArray($request, $dataQueryUtils): array
    {
        return [
            'date' => Carbon::now()->format('Y-m-d'),
            'person_id' => null,
            'customer_id' => null,
            'client_id' => null,
            'document_number_purchasing_manager' => null,
            'fiscal_responsibilities' => $request['additional']['fiscal_responsibilities'],
            'country_id' => $request['additional']['country_id'],
            'department_id' => $request['additional']['department_id'],
            'document_type' => $request['additional']['document_type'],
            'document_number' => $request['dniNumber'],
            'phone' => $request['contactPhone'],
            'email' => $request['emailAddress'],
            'document_type_purchasing_manager' => null,
            'department_name' => $request['billingAddress']['state'],
            'tax_details_code' => $request['additional']['tax_details_code'],
            'tax_details_name' => $request['additional']['tax_details_name'],
            'document_number_sales_manager' => null,
            'document_type_sales_manager' => null,
            'address' => $request['billingAddress']['street1'],
            'sales_manager' => null,
            'type_taxpayer_id' => $request['additional']['type_taxpayer_id'],
            'type_taxpayer_name' => $request['additional']['type_taxpayer_name'],
            'country_name' => 'Colombia',
            'name' => $request['fullName'],
            'city_id' => $request['additional']['city_id'],
            'city_name' => $request['billingAddress']['city'],
            'postal_code' => $request['billingAddress']['postalCode'],
            'invoice_type' => "INVOICE",
            'note' => null,
            'apply_deductible' => true,
            'is_paid' => true,
            'is_electronic_invoice' => true,
            'payment_method_id' => $dataQueryUtils['payment_method']['id'],
            "sale_channel" => "WEBSITE",
            'source_type' => "CUSTOMERS",
            'total_invoice' => $request['total_invoice'],
            'total_discount' => 0,
            'total_sale_value' => $request['total_sale_value'],
            'total_sale' => $request['total_sale'],
            'total' => $request['total'],
            'sending_charge' => 0,
            'total_iva' => 0,
            'retefuente' => 0,
            'reteica' => 0,
            'reteiva' => 0,
            'base_retefuente' => 0,
            'base_reteica' => 0,
            'base_reteiva' => 0,
            'total_impoconsumption' => 0,
            'prefix_id' => $request['prefix'],
            'prefix_id_name' => $request['prefix_id_name'],
            'send_address' => $request['billingAddress']['street1'],
            'company_address' => $request['ccxc']['address'],
            'company_postal_code' => $request['ccxc']['postal_code'],
            'number_purchase_order' => null,
            'foreign_exchange_id' => $dataQueryUtils['foreign_exchange']['id'],
            'foreign_exchange_name' => $dataQueryUtils['foreign_exchange']['name'],
            'payment_type_id' => $dataQueryUtils['payment_type']['id'],
            'payment_type_name' => $dataQueryUtils['payment_type']['name'],
            'purchasing_manager' => null,
            'aggregation_method' => "ELECTRONICS",
            'electronic_billing' => true,
            'apply_electronic_invoice' => true,
            "loaded_inventory" => false,
            'time_issue' => Carbon::now()->format('H:i:s'),
            'invoice_state' => "ACCEPTED",
            "number_max" => 2000,
            "number" => 0,
            "taxes" => [
                [
                    "name" => "IVA 01",
                    "base" => 0,
                    "percentage" => "19",
                    "title" => "",
                    "value" => 0
                ],
                [
                    "name" => "IVA 02",
                    "base" => 0,
                    "percentage" => "5",
                    "title" => "",
                    "value" => 0
                ],
                [
                    "name" => "IVA 03",
                    "base" => 0,
                    "percentage" => "0",
                    "title" => "",
                    "value" => 0
                ],
                [
                    "name" => "IVA 04",
                    "base" => 0,
                    "percentage" => "0",
                    "title" => "",
                    "value" => 0
                ],
                [
                    "name" => "IVA 05",
                    "base" => 0,
                    "percentage" => "0",
                    "title" => "",
                    "value" => 0
                ]
            ],
            "withholdings" => [
                [
                    "name" => "06 ReteFuente",
                    "base" => $request['total'],
                    "percentage" => "0",
                    "title" => "*Retefuente",
                    "value" => 0
                ],
                [
                    "name" => "07 ReteICA",
                    "base" => $request['total'],
                    "percentage" => "0",
                    "title" => "*Reteica",
                    "value" => 0
                ],
                [
                    "name" => "08 ReteIVA",
                    "base" => $request['total'],
                    "percentage" => "0",
                    "title" => "*Reteiva",
                    "value" => 0
                ]
            ],
            'products' => MembershipProductResource::collection($request['products']),
            'is_draft' => false,
            'total_ibua' => 0.00,
            'total_icui' => 0.00,
        ];
    }
}
