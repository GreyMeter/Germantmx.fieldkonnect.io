<?php

namespace App\Imports;

use App\Models\Settings;
use Maatwebsite\Excel\Concerns\ToModel;

class SettingsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Settings([
            //
        ]);
    }
}
