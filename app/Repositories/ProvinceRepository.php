<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository extends CrudRepository
{

    public function model()
    {
       return Province::class;
    }
}
