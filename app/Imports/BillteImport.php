<?php

namespace App\Imports;

use App\Models\Billet;
use App\Models\Plant;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
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

class BillteImport implements ToModel, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        if (isset($row['date']) && is_numeric($row['date'])) {
            $excelDate = $row['date'] - 25569; // Adjust for Excel's epoch
            $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
            $row['date'] = !empty($row['date']) ? Carbon::createFromTimestamp($unixTimestamp)->toDateString() : '';
        }
        $row['to'] = Plant::where('plant_name', '=', $row['to'])->first()->id;
        return new Billet([
            'date' => $row['date'],
            'from_is' => $row['from'],
            'to_is' => $row['to'],
            'material' => $row['material'],
            'quantity' => $row['quantity'],
            'output' => $row['output'],
            'balance' => $row['quantity'] - $row['output'],
            'rate' => $row['rate'],
            'vehicle_no' => $row['vehicle_no'],
            'remarks' => $row['remarks'],
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => 'required',
            'to' => 'required|exists:plants,plant_name',
            'quantity' => 'required|numeric',
            'output' => 'required|numeric'
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
