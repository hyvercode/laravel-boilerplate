<?php

namespace App\Services;

use App\Models\AuthOtp;
use App\Repositories\AutOtpRepository;
use App\Helpers\CommonUtil;
use App\Helpers\DateTimeConverter;

class OtpService
{
    protected $otpRepository;

    public function __construct(AutOtpRepository $autOtpRepository)
    {
        $this->otpRepository = $autOtpRepository;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($id)
    {
        $otp = new AuthOtp();
        $otp->id = $id;
        $otp->otp = CommonUtil::intGenerate();
        $otp->expired =env('OTP_EXPIRED','30');
        $otp->expired_time=DateTimeConverter::exipredTimeIE(env('OTP_EXPIRED','30'));
        $otp->created_at = DateTimeConverter::getDateTimeNow();
        return  $this->otpRepository->create($otp->toArray());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->otpRepository->findById($id,['id','expired','expired_time']);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function deleteById($id){
        $this->otpRepository->deleteById($id);
    }

    /**
     * @param $user_id
     * @param $otp
     * @return mixed
     */
    public function verifikasiOtp($user_id, $otp){
        return $this->otpRepository->verifikasiOtp($user_id, $otp);
    }

}
