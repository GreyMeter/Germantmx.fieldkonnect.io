<?php

namespace App\Imports;

use App\Models\Couriers;
use Maatwebsite\Excel\Concerns\ToModel;

class CouriersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Couriers([
            //
        ]);
    }
}
