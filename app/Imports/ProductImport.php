<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductDetails;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Auth;

class ProductImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;
    
    public function model(array $row)
    {
        return new Product([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            dd($row);
            if( $product = Product::updateOrCreate(['id' => $row['product_id'] ],[
                'active' => 'Y',
                'product_code' => isset($row['product_code'])? $row['product_code']:'',
                'description' => isset($row['standard_weight_kgmtr'])? ucfirst($row['standard_weight_kgmtr']):'',
                'category_id' => isset($row['size_id'])? $row['size_id']:null,
                'brand_id' => isset($row['brand_id'])? $row['brand_id']:null,
                'unit_id' => isset($row['grade_id'])? $row['grade_id']:null,
                'created_by' => Auth::user()->id,
                'created_at' => getcurentDateTime(),
                'updated_at' => getcurentDateTime(),
                'part_no'       => isset($row['weight_per_bundle_in_kg']) ? $row['weight_per_bundle_in_kg'] :null,
                'product_no'    => isset($row['no_of_pcs_per_40ft_bundle']) ? $row['no_of_pcs_per_40ft_bundle'] :null,
                'gst'      => isset($row['gst']) ? $row['gst'] :'18',

            ]) )
            {
            //    ProductDetails::updateOrCreate(['product_id' => $product['id'] ],[
            //         'active' => 'Y',
            //         'product_id' => $product['id'],
            //         'detail_title' => isset($row['detail_title'])? ucfirst($row['detail_title']):$row['product_name'],
            //         'detail_description' => isset($row['detail_description'])? ucfirst($row['detail_description']):'',
            //         'detail_image' => isset($row['detail_image'])? $row['detail_image']:'',
            //         'mrp' => isset($row['mrp'])? $row['mrp']:0.00,
            //         'price' => isset($row['price'])? $row['price']:$row['mrp'],
            //         'discount' => isset($row['discount'])? $row['discount']:0.00,
            //         'max_discount' => isset($row['max_discount'])? $row['max_discount']:0.00,
            //         'selling_price' => isset($row['selling_price'])? $row['selling_price']:0.00,
            //         'gst' => isset($row['gst'])? $row['gst']:0.00,
            //         'isprimary' => isset($row['isprimary'])? $row['isprimary']:1,
            //         'hsn_code' => isset($row['hsn_code'])? $row['hsn_code']:null,
            //         'ean_code' => isset($row['ean_code'])? $row['ean_code']:null,
            //         'created_at' => getcurentDateTime() ,
            //         'updated_at' => getcurentDateTime()
            //     ]);
            }
        }
    }
    public function rules(): array
    {
        return [
            'product_code' => 'required|unique:products,product_code',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
