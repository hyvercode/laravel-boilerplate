<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository extends CrudRepository
{

    public function model()
    {
        return Role::class;
    }
}
