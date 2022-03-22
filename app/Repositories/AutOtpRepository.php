<?php

namespace App\Repositories;

use App\Models\AuthOtp;

class AutOtpRepository extends CrudRepository
{

    public function model()
    {
        return AuthOtp::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id,array $columns = ['*']){
        return AuthOtp::where('id',$id)->get()->first();
    }

    /**
     * @param $user_id
     * @param $otp
     * @return mixed
     */
    public function verifikasiOtp($user_id,$otp){
        return AuthOtp::where('id',$user_id)
            ->where('otp',$otp)
            ->get('expired_time')->first();
    }
}
