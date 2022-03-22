<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Traits\BusinessException
     */
    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Traits\BusinessException
     */
    public function register(Request $request)
    {
        return $this->authService->register($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->authService->logout();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        return $this->authService->refreshToken($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        return $this->authService->destroy($id, $request);
    }

    /**
     * @param $account
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Traits\BusinessException
     */
    public function otp($account){
        return $this->authService->generateOTP($account);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Traits\BusinessException
     */
    public function verifikasiOTP(Request $request){
        return $this->authService->verifikasiOTP($request);
    }

    public function changePassword(Request $request)
    {
        return $this->authService->changePassword($request);
    }
}
