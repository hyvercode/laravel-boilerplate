<?php

namespace App\Imports;

use App\Helpers\Constants;
use App\Models\KpiDirectory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KpiDirectoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new KpiDirectory([
            'company_id' => $row['company_id'],
            'kpi_code' => $row['kpi_code'],
            'kpi_name' => $row['kpi_name'],
            'bsc_id' => $row['bsc_id'],
            'kpi_group_id' => $row['kpi_group_id'],
            'target_type_id' => $row['target_type_id'],
            'definisi' => $row['definisi'],
            'branch_id' => $row['branch_id'],
            'active' => Constants::NON_ACTIVE,
        ]);
    }
}
