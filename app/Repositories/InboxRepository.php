<?php

namespace App\Repositories;

use App\Models\Inbox;

class InboxRepository extends CrudRepository
{

    public function model()
    {
        return Inbox::class;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function countByUserId($user_id){
        return Inbox::where('user_id',$user_id)
            ->where('read',true)
            ->count();
    }
}
