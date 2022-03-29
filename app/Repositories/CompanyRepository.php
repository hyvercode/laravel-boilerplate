<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository extends CrudRepository
{

    public function model()
    {
        return Company::class;
    }
}
