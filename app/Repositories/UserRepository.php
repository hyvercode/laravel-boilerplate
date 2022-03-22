<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\CommonUtil;

class UserRepository extends CrudRepository
{

    public function model()
    {
        return User::class;
    }

    /**
     * @param $account
     * @return null
     */
    public function findByAccount($account)
    {
        if (strpos($account, '@')) {
            return User::where('email', $account)->get(['id', 'email'])->first();
        } else {
            return User::where('phone_number', $account)->get(['id', 'email'])->first();
        }
    }
}
