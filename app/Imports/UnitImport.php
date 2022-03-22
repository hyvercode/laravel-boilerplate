<?php

namespace App\Imports;

use App\Models\Unit;
use App\Helpers\Constants;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Unit([
            'company_id' => $row['company_id'],
            'unit_name' => $row['unit_name'],
            'active' => Constants::NON_ACTIVE,
        ]);
    }
}
