<?php

namespace App\Services;

use App\Helpers\Base64Converter;
use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Monolog\Logger;

class UserService implements BaseService
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->userRepository->all(['*'], 'active', true)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->userRepository->paginate($request->searchBy, $request->searchParam, $request->perPage, ['*'], 'page', $request->currentPage, 'active', true, $request->sortBy, $request->sort)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function create(Request $request)
    {
        try {
            $user = User::create([
                'company_id' => auth()->user()->company_id,
                'employee_id' => $request->employee_id,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'menu_roles' => $request->menu_roles,
                'created_by' => auth()->user()->id,
                'avatar' => Base64Converter::base64ToImage('avatars', $request->avatar),
                'active' => Constants::ACTIVE
            ]);

            event(new Registered($user));
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function deleteById($id, Request $request)
    {
        $record = $this->userRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $record->delete();
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getById($id, Request $request)
    {
        $record = $this->userRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $record
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function updateById($id, Request $request)
    {
        $user = $this->userRepository->getById($id);
        try {
            $user->username = $request->username;
            $user->email = $request->email;
            $user->menu_roles = $request->menu_roles;
            $user->avatar = Base64Converter::isBase64('avatars', $request->avatar);
            $user->active = $request->active;
            $user->updated_by = auth()->user()->id;
            $this->userRepository->updateById($id, $user->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getProfile(Request $request)
    {
        $record = $this->userRepository->getById(auth()->user()->id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $record
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Your old password does not match', Constants::ERROR_CODE_9000);
        }

        if (strcmp($request->old_password, $request->new_password) == 0) {
            //Current password and new password are same
            throw new BusinessException(Constants::HTTP_CODE_409, "New Password cannot be same as your current password. Please choose a different password.", Constants::ERROR_CODE_9000);
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
            throw new BusinessException(Constants::HTTP_CODE_409, 'Password should be at least 6 characters in length and should include at least one upper case letter, one number, and one special character.', Constants::ERROR_CODE_9000);
        }

        if ($validate->fails()) {
            throw new BusinessException(Constants::HTTP_CODE_422, $validate->errors(), Constants::ERROR_CODE_9000);
        }

        try {
            auth()->user()->update([
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

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function updateAvatar($id, Request $request)
    {
        $user = $this->userRepository->getById($id);
        try {
            $user->avatar = Base64Converter::isBase64('avatars', $request->avatar);
            $user->updated_by = auth()->user()->id;
            $this->userRepository->updateById($id, $user->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

}
