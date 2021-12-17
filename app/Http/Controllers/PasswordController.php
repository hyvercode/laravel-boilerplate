<?php

namespace App\Http\Controllers;

use App\Services\PasswordService;
use App\Utils\BusinessException;
use Illuminate\Http\Request;

class PasswordController extends Controller
{

    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function forgotPassword(Request $request)
    {
        return $this->passwordService->forgot($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function resetPassword(Request $request)
    {
        return $this->passwordService->reset($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function changePassword(Request $request)
    {
        return $this->passwordService->change($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function validatePassword(Request $request)
    {
        return $this->passwordService->validate($request);
    }
}
