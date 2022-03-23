<?php

namespace App\Services;

use App\Adaptors\MailGateway;
use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Repositories\UserRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use App\Helpers\DateTimeConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{

    use BaseResponse;

    protected $jwt;
    protected $authOtpService;

    public function __construct(JWTAuth        $jwt, OtpService $authOtpService,
                                UserRepository $userRepository, MailGateway $sitamaGateway)
    {
        $this->jwt = $jwt;
        $this->authOtpService = $authOtpService;
        $this->userRepository = $userRepository;
        $this->sitamaGateway = $sitamaGateway;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function login(Request $request)
    {
        $credential = [
            'email' => $request->email,
            'password' => $request->password,
            'active' => true,
        ];

        $ttl = env('JWT_TTL', 1440);
        if ($request->remember_me) {
            $ttl = env('JWT_REMEMBER_TTL', 1051200);
        }

        if (!$token = auth()->setTTL($ttl)->attempt($credential)) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Invalid username or password!', Constants::ERROR_CODE_9000);
        }

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->generateToken($token),
            CommonUtil::generateUUID()
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        auth()->invalidate(true);
        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->generateToken(auth()->refresh())
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function destroy($id, Request $request)
    {

        $token = auth()->tokenById($id);
        if (!isset($token)) {
            throw new BusinessException(Constants::HTTP_CODE_403, 'Invalid Authorization', Constants::HTTP_CODE_403);
        }

        $this->jwt->setToken($token)->invalidate(true);
        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $token
     * @param $username
     * @return array
     * @throws BusinessException
     */
    protected function generateToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    /**
     * @param $account
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function generateOTP($account)
    {
        try {
            $user = $this->userRepository->findByAccount($account);
            if (empty($user)) {
                throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_CODE_9000, Constants::ERROR_CODE_9000);
            }
            $otp = $this->authOtpService->getById($user->id);
            /*
             * crate otp
             */
            if (!empty($otp)) {
                $this->authOtpService->deleteById($user->id);
            }
            /*
             * update otp
             */
            $otp = $this->authOtpService->create($user->id);

            /*
             * SMS OTP
             */
            $this->sitamaGateway->sendOtp($otp->otp, $user->mobilephoneno);

            /*
             * MAIL OTP
             */
            $content = "Hi, welcome user! \n{$otp->otp} is your PMS verification code";
            $this->sitamaGateway->sendMail($user->email, '', $content);
            $response = array("id" => CommonUtil::encrypt_decrypt(Constants::ENCRYPT, $user->id), "expired" => intval($otp->expired), "expired_time" => $otp->expired_time);
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $response
        );
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws BusinessException
     */
    public function verifikasiOTP(Request $request)
    {
        /*
     * Check id registered or not
     */
        $user_id = $this->userRepository->getById(CommonUtil::encrypt_decrypt(Constants::DECRYPT, $request->account));
        if (empty($user_id)) {
            throw new BusinessException(Constants::HTTP_CODE_409, "Account not found!", Constants::ERROR_CODE_9000);
        }

        /*
         * Check ida and otp_code
         */
        $verify_otp = $this->authOtpService->verifikasiOtp($user_id->id, $request->otp_code);
        if (empty($verify_otp)) {
            throw new BusinessException(Constants::HTTP_CODE_409,  " Invalid OTP code!", Constants::ERROR_CODE_9000);
        }

        /*
         * check if otp code expired
         */

        if (strtotime($verify_otp->expired_time) < strtotime(DateTimeConverter::getDateTimeNow())) {
            throw new BusinessException(Constants::HTTP_CODE_409, "OTP code has been expired!", Constants::ERROR_CODE_9000);
        }

        $response = array("id" => CommonUtil::encrypt_decrypt(Constants::ENCRYPT, $user_id->id));

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $response
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function changePassword(Request $request)
    {
        $account = $this->userRepository->getById(CommonUtil::encrypt_decrypt(Constants::DECRYPT, $request->account));

        $uppercase = preg_match('@[A-Z]@', $request->new_password);
        $lowercase = preg_match('@[a-z]@', $request->new_password);
        $number = preg_match('@[0-9]@', $request->new_password);
        $specialChars = preg_match('@[^\w]@', $request->new_password);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($request->new_password) < 6) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Password should be at least 6 characters in length and should include at least one upper case letter, one number, and one special character.', Constants::ERROR_CODE_9000);
        }

        try {
            $account->update([
                'password' => bcrypt($request->new_password)
            ]);
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }
}
