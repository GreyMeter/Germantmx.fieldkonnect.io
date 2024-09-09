<?php

namespace App\Imports;

use App\Models\NeftRedemptionDetails;
use App\Models\Redemption;
use Carbon\Carbon;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RedemptionImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if($row['status'] == '3' || $row['status'] == '4'){
                NeftRedemptionDetails::updateOrCreate(
                    [
                        'redemption_id' => $row['redemption_id']
                    ],
                    [
                        'redemption_id' => $row['redemption_id'],
                        'utr_number' => $row['transaction_id_utr_no'],
                        'tds' => $row['tds'],
                        'remark' => $row['details']
                ]);
                
            }
            if($row['payment_date'] && $row['payment_date'] != null && $row['payment_date'] != '')
            {
                $unixTimestamp = ($row['payment_date'] - 25569) * 86400;
                $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
                $update_at = $carbonDate;
            }else{
                $update_at = date('Y-m-d h:i:s', strtotime(Carbon::now()));
            }
            $updateStatus = Redemption::where('id', $row['redemption_id'])->update(['status' => $row['status'], 'remark' => $row['details'], 'updated_at' => $update_at]);
        }
    }

    public function rules(): array
    {
        return [
            'redemption_id' => [
                'required',
                'exists:redemptions,id',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Redemption::where('id', $value)
                            ->where('redeem_mode', 2)
                            ->exists()) {
                        $fail('The redemption ID('.$value.') is not NEFT Redemption.');
                    }
                },
            ],
            'status' => 'required|numeric',
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
