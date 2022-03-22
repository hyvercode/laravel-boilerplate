<?php

namespace App\Repositories;

use App\Models\Menulist;

class MenuListRepository extends CrudRepository
{

    public function model()
    {
        return Menulist::class;
    }
}
