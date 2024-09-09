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
        $productdetails = collect([]);
        foreach ($rows as $row) {
            if( $product = Product::updateOrCreate(['id' => $row['product_id'] ],[
                'active' => 'Y',
                'product_name' => isset($row['product_name'])? ucfirst($row['product_name']):'',
                'product_code' => isset($row['product_code'])? $row['product_code']:'',
                'new_group' => isset($row['new_group'])? $row['new_group']:'',
                'sub_group' => isset($row['sub_group'])? ucfirst($row['sub_group']):'',
                'expiry_interval' => isset($row['expiry_interval'])? ucfirst($row['expiry_interval']):'',
                'expiry_interval_preiod' => isset($row['expiry_interval_preiod'])? ucfirst($row['expiry_interval_preiod']):0,
                'display_name' => isset($row['display_name'])? ucfirst($row['display_name']):'',
                'description' => isset($row['description'])? ucfirst($row['description']):'',
                'subcategory_id' => isset($row['subcategory_id'])? $row['subcategory_id']:null,
                'category_id' => isset($row['category_id'])? $row['category_id']:null,
                'brand_id' => isset($row['brand_id'])? $row['brand_id']:null,
                'product_image' => isset($row['product_image'])? $row['product_image']:'',
                'unit_id' => isset($row['unit_id'])? $row['unit_id']:null,
                'suc_del' => isset($row['suc_del'])? $row['suc_del']:null,
                'created_by' => Auth::user()->id,
                'created_at' => getcurentDateTime(),
                'updated_at' => getcurentDateTime(),
                // 'specification' => isset($row['specification']) ? $row['specification'] :null,
                // 'part_no'       => isset($row['part_no']) ? $row['part_no'] :null,
                // 'product_no'    => isset($row['product_no']) ? $row['product_no'] :null,
                // 'model_no'      => isset($row['model_no']) ? $row['model_no'] :null,
                'specification' => isset($row['hp']) ? $row['hp'] :null,
                'part_no'       => isset($row['kw']) ? $row['kw'] :null,
                'product_no'    => isset($row['product_stage']) ? $row['product_stage'] :null,
                'model_no'      => isset($row['model_no']) ? $row['model_no'] :null,

            ]) )
            {
               ProductDetails::updateOrCreate(['product_id' => $product['id'] ],[
                    'active' => 'Y',
                    'product_id' => $product['id'],
                    'detail_title' => isset($row['detail_title'])? ucfirst($row['detail_title']):$row['product_name'],
                    'detail_description' => isset($row['detail_description'])? ucfirst($row['detail_description']):'',
                    'detail_image' => isset($row['detail_image'])? $row['detail_image']:'',
                    'mrp' => isset($row['mrp'])? $row['mrp']:0.00,
                    'price' => isset($row['price'])? $row['price']:$row['mrp'],
                    'discount' => isset($row['discount'])? $row['discount']:0.00,
                    'max_discount' => isset($row['max_discount'])? $row['max_discount']:0.00,
                    'selling_price' => isset($row['selling_price'])? $row['selling_price']:0.00,
                    'gst' => isset($row['gst'])? $row['gst']:0.00,
                    'isprimary' => isset($row['isprimary'])? $row['isprimary']:1,
                    'hsn_code' => isset($row['hsn_code'])? $row['hsn_code']:null,
                    'ean_code' => isset($row['ean_code'])? $row['ean_code']:null,
                    'created_at' => getcurentDateTime() ,
                    'updated_at' => getcurentDateTime()
                ]);
            }
        }
    }
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
