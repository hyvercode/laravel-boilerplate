<?php

namespace App\Imports;

use App\Helpers\Constants;
use App\Models\JobTitle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JobTitleImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        return new JobTitle([
            'company_id' => $row['company_id'],
            'job_title_name' => $row['job_title_name'],
            'active' => Constants::NON_ACTIVE,
        ]);
    }
}
