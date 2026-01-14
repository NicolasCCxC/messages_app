<?php

namespace App\Http\Resources\MembershipInvoice;

class MembershipProductResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function collection($request)
    {
        $productsInvoice = [];
        collect($request)->map(function ($product, $index) use (&$productsInvoice) {
            $data = [
                "number" => "00" . ($index + 1),
                "id" => $product["product_id"],
                "total_buy" => $product["unit_value"],
                "reference" => $product["reference"],
                "unit_cost" => $product["unit_value"],
                "sku_internal" => $product["sku_internal"],
                "unique_product_name" => $product["name"],
                "unique_products_id" => $product["id"],
                "warehouse_id" => null,
                "warehouse_name" => "N/A",
                "batch" => "N/A",
                "batch_id" => null,
                "input_date_expiration" => "N/A",
                "batch_detail_id" => null,
                "date_expiration" => null,
                "description" => $product["product"]["description"],
                "quantity" => 1,
                "unit_measurements_id" => $product["unit_measurement_id"],
                "unit_value" => $product["unit_value"],
                "delivery_cost" => 0,
                "iva" => "Exento (0%)",
                "ciiu_id" => $product["product"]["ciiu_id"] ? $product["product"]["ciiu_id"] : "2",
                "is_product" => false,
                "is_inventoriable" => false,
                "is_apply_ibua" => false,
                "milliliter" => 0,
                "fee_ibua" => 0,
                "total_ibua" => 0,
                "icui" => 0,
                "product_taxes" => $product["product"]["product_taxes"],
                "percentage" => "Excluido",
                "percentage_discount" => 0,
                "check" => false,
                "text_fields" => [
                    "warehouse" => "",
                    "batch" => "",
                    "date_expiration" => ""
                ],
                "quantityMax" => 0,
                "measurement" => $product["unit_measurement_name"],
                "ivaObject" => collect($product["product"]["product_taxes"])->where("tax_type", "=", "IVA"),
                "percentageObject" => collect($product["product"]["product_taxes"])->where("tax_type", "=", "CONSUMPTION"),
                "is_mandate" => false,
                "mandate" => null,
                "mandate_id" => null,
                "discount" => 0,
                "taxes" => collect($product["product"]["product_taxes"]),
            ];
            array_push($productsInvoice, $data);
        });
        return $productsInvoice;
    }
}
