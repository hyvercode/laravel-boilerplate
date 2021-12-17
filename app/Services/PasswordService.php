<?php

namespace App\Services;

use App\Adaptors\SynchronizationAdaptors;
use App\Repositories\UserRepository;
use App\Utils\BaseResponse;
use App\Utils\BusinessException;
use App\Utils\CommonUtil;
use App\Utils\Constants;
use App\Utils\DateTimeConverter;
use App\Utils\Monologger;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DateTime;

class PasswordService
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
    public function forgot(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->userRepository->findUserByUsername($request->username);
        //is not active
        if (!$user || str_replace(' ', '', $user->status) !== Constants::ACTIVE) {
            throw new BusinessException(Constants::HTTP_CODE_200, 'OTP has been sent', Constants::HTTP_CODE_200,$request->auth['request_id']);
        }

        try {
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
                "desc" => "pickers",
                "user_id" => 1,
                "title" => "Pickers",
                "body" => "Pickers forgot password",
                "tag" => "Alert",
                "icon" => "https://file.sitama.co.id/storage/grosirmotor/thumbnail/grosirmotor.png",
                "click_action" => "",
                "app_name" => "PICKERS",
                "type" => "INBOX",
                "scope"=>$user->api_roles,
            ];
            $this->synchronizationAdaptors->sendOTP($payload, $request->header(), $request);
            $this->synchronizationAdaptors->sendNotification($payload, $request->header(), $request);
            $this->synchronizationAdaptors->sendMail($payload, $request->header(), $request);


        } catch (\Exception $ex) {
            Monologger::log(Constants::ERROR, $ex->getMessage(),$request->get('auth')['request_id']);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000,$request->auth['request_id']);
        } catch (GuzzleException $gx) {
            Monologger::log(Constants::ERROR, $gx->getMessage());
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                'session_id' => $session,
                'expired' => DateTimeConverter::exipredTime(env('OTP_EXPIRED', 300 / 5))
            ],
            $request->auth['request_id']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function reset(Request $request): \Illuminate\Http\JsonResponse
    {

        $session = CommonUtil::encrypt_decrypt(Constants::DECRYPT, $request->session_id);
        $str_arr = explode(",", $session);
        $time1 = new DateTime ($str_arr[1]);
        $time2 = new DateTime (DateTimeConverter::dateAddSecond(DateTimeConverter::getDateTimeNow(), 18000));
        if ($time1 > $time2) {
            throw new BusinessException(Constants::HTTP_CODE_401, Constants::ERROR_MESSAGE_401, Constants::HTTP_CODE_401,$request->auth['request_id']);
        }

        $user = $this->userRepository->getById(CommonUtil::encrypt_decrypt(Constants::DECRYPT, $str_arr[0]));
        //is not active
        if (!$user || str_replace(' ', '', $user->status) !== Constants::ACTIVE) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9002, Constants::ERROR_CODE_9002,$request->auth['request_id']);
        }

        $validate = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $request->password);
        $lowercase = preg_match('@[a-z]@', $request->password);
        $number = preg_match('@[0-9]@', $request->password);
        $specialChars = preg_match('@[^\w]@', $request->password);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($request->password) < 6) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Password should be at least 6 characters in length and should include at least one upper case letter, one number, and one special character.', Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        if ($validate->fails()) {
            throw new BusinessException(Constants::HTTP_CODE_422, $validate->errors(), Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->update_at = DateTimeConverter::getDateTimeNow();
            $user->update_by = $user->id;
            $this->userRepository->updateById($user->id, $user->toArray());
            Redis::del($request->session_id);
        } catch (\Exception $ex) {
            Monologger::log(Constants::ERROR, $ex->getMessage(),$request->get('auth')['request_id']);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $request->auth['request_id']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function change(Request $request): \Illuminate\Http\JsonResponse
    {

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Your old password does not match', Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        if (strcmp($request->old_password, $request->new_password) == 0) {
            //Current password and new password are same
            throw new BusinessException(Constants::HTTP_CODE_409, "New Password cannot be same as your current password. Please choose a different password.", Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        $validate = Validator::make($request->all(), [
            'new_password' => 'required|min:6',
            'old_password' => 'required|min:6',
            'password_confirmation' => 'required|same:new_password',
        ]);

        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $request->new_password);
        $lowercase = preg_match('@[a-z]@', $request->new_password);
        $number = preg_match('@[0-9]@', $request->new_password);
        $specialChars = preg_match('@[^\w]@', $request->new_password);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($request->new_password) < 6) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Password should be at least 6 characters in length and should include at least one upper case letter, one number, and one special character.', Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        if ($validate->fails()) {
            throw new BusinessException(Constants::HTTP_CODE_422, $validate->errors(), Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        try {
            auth()->user()->update([
                'password' => Hash::make($request->new_password)
            ]);
        } catch (\Exception $ex) {
            Monologger::log(Constants::ERROR, $ex->getMessage(),$request->get('auth')['request_id']);
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $request->auth['request_id']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function validate(Request $request): \Illuminate\Http\JsonResponse
    {

        $user = $this->userRepository->getById($request->id);
        //is not active
        if (str_replace(' ', '', $user->status) !== Constants::ACTIVE) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9002, Constants::ERROR_CODE_9000,$request->auth['request_id']);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Your old password does not match', Constants::ERROR_MESSAGE_9000,$request->auth['request_id']);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $request->auth['request_id']
        );
    }
}
