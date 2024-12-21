<?php

namespace App\Imports;

use App\Models\BranchStock;
use App\Models\Brand;
use App\Models\Plant;
use App\Models\Category;
use App\Models\CustomerOutstanting;
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
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\PrimarySales;
use App\Models\UnitMeasure;
use App\Models\User;
use Validator;

class BranchStockImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new BranchStock([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $plant = Plant::where('plant_name', $row['plant_name'])->firstOrFail();
            $brand = Brand::where('brand_name', $row['brand'])->firstOrFail();
            $size = Category::where('category_name', $row['size'])->firstOrFail();
            $grade = UnitMeasure::where('unit_name', $row['grade'])->firstOrFail();
                        
            $salesTargetUsers = BranchStock::updateOrCreate(
                [
                    'plant_id' => $plant->id,
                    'plant_name' => $plant->plant_name,
                    'brand_id' => $brand->id,
                    'unit_id' => $grade->id,
                    'category_id' => $size->id
                ],
                [
                    'stock' => $row['stock_qty']
                ]
            );
        }
    }

    public function rules(): array
    {
        $rules = [
            'plant_name' => 'required|exists:plants,plant_name',
            'brand' => 'required|exists:brands,brand_name',
            'size' => 'required|exists:categories,category_name',
            'grade' => 'required|exists:unit_measures,unit_name',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'unit_id.required' => 'The Unit ID is required.',
            'unit_id.required' => 'The Unit ID is not found.',
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
