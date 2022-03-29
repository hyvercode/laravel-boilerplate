<?php

namespace App\Repositories;

use App\Models\Business;

class BusinessRepository extends CrudRepository
{

    public function model()
    {
       return Business::class;
    }
}
