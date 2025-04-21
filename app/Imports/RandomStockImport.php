<?php

namespace App\Imports;

use App\Models\RandomStock;
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

class RandomStockImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new RandomStock([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $plant = Plant::where('plant_name', $row['plant_name'])->firstOrFail();
            $size = Category::where('category_name', $row['size'])->firstOrFail();
            
            $salesTargetUsers = RandomStock::updateOrCreate(
                [
                    'plant_id' => $plant->id,
                    'plant_name' => $plant->plant_name,
                    'category_id' => $size->id,
                    'random_cut' => $row['random_cut']
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
            'size' => 'required|exists:categories,category_name',
            'random_cut' => 'required|in:10-25,25-35',
            'stock_qty' => 'required|numeric|gt:-1',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'random_cut.required' => 'The Random Cut is required.',
            'random_cut.in' => 'The Random Cut is invalid is in [10-25,25-35].',
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
