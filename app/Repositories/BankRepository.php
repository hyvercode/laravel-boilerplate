<?php

namespace App\Repositories;

use App\Models\Bank;

class BankRepository extends CrudRepository
{

    public function model()
    {
        return Bank::class;
    }
}
