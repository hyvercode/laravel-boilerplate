<?php

namespace App\Repositories;

use App\Models\MenuList;

class MenuListRepository extends CrudRepository
{

    public function model()
    {
        return MenuList::class;
    }
}
