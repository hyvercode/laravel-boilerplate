<?php

namespace App\Services;

use App\Adaptors\SynchronizationAdaptors;
use App\Repositories\UserRepository;
use App\Utils\BaseResponse;
use App\Utils\BusinessException;
use App\Utils\CommonUtil;
use App\Utils\Constants;
use App\Utils\DateTimeConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OtpService
{

    private $userRepository;
    private $synchronizationAdaptors;

    public function __construct(UserRepository          $userRepository,
                                SynchronizationAdaptors $synchronizationAdaptors)
    {
        $this->userRepository = $userRepository;
        $this->synchronizationAdaptors = $synchronizationAdaptors;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function generate(Request $request)
    {
        $user = $this->userRepository->findUserByUsername($request->username);
        //is not active
        if (!$user || str_replace(' ', '', $user->status) !== Constants::ACTIVE) {
            throw new BusinessException(Constants::HTTP_CODE_200, 'OTP has been sent', Constants::HTTP_CODE_200,$request->auth['request_id']);
        }

        //Store to Redis
        $otp = CommonUtil::randomNumber();
        $session = CommonUtil::encrypt_decrypt(Constants::ENCRYPT, $user->id);
        Redis::del($session);

        Redis::rpush($session, $user->phone_number, $otp);
        Redis::expire($session, 300); // 5 minutes

        //send mail,otp.notifications
        $payload = [
            "email_from" => "pickers@sitama.co.id",
            "from" => "Pickers",
            "subject" => "OTP",
            "email_to" => $user->email,
            "contents" => $otp . " is your pickers verification code",
            "otp" => $otp,
            "phone" => $user->phone_number,
            "desc" => "pickers"
        ];
        $this->synchronizationAdaptors->sendOTP($payload, $request->header(),$request);
        $this->synchronizationAdaptors->sendMail($payload, $request->header(),$request);

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                'session_id' => $session,
                'expired' => DateTimeConverter::exipredTime(env('OTP_EXPIRED', 300 / 5))
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function verified(Request $request)
    {
        $session = $request->session_id;
        $otp = $request->otp;

        if (Redis::lrange($session, 0, 0) == NULL) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001,$request->auth['request_id']);
        }

        $session_otp = Redis::lrange($session, 1, 1);
        if ($session_otp[0] != $otp) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9004, Constants::ERROR_CODE_9004,$request->auth['request_id']);
        }

        Redis::del($session);

        //Store to Redis
        $otp = CommonUtil::randomNumber();
        $session = CommonUtil::encrypt_decrypt(Constants::ENCRYPT, $session . ',' . DateTimeConverter::dateTimeFormatNow('YmdHis'));
        Redis::rpush($session, $session . ',' . DateTimeConverter::dateTimeFormatNow('YmdHis'), $otp);
        Redis::expire($session, 600); // 10 minutes

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                'session_id' => $session
            ],
            $request->auth['request_id']
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function flush()
    {
        Redis::flushDB();
        return response()->json(['status' => 'Successful']);
    }

}
