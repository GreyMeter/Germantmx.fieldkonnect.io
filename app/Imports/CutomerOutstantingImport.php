<?php

namespace App\Imports;

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
use App\Models\User;
use Validator;

class CutomerOutstantingImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new CustomerOutstanting([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (is_numeric($row['date'])) {
                $excelDate = $row['date'] - 25569;
                $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                $row['date'] = !empty($row['date']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
            } else {
                $date = $row['date'];
                $row['date'] = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            }
            // dd($row);
            $salesTargetUsers = CustomerOutstanting::updateOrCreate(
                [
                    'customer_id' => $row['customer_id'],
                    'date' => $row['date']
                ],
                [
                    'customer_name' => $row['customer_name'],
                    'rate' => $row['rate'],
                    'order_qty' => $row['order_qty'],
                    'dispatch' => $row['dispatch'],
                    'pending' => $row['pending'],
                    'days' => $row['days']
                ]
            );
        }
    }

    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'customer_id.required' => 'The Customer ID is required.',
            'customer_id.exists' => 'The Customer ID is not exists.',
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
