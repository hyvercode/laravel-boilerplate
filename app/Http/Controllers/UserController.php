<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->userService->all($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->userService->paginate($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->userService->create($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->userService->getById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update($id, Request $request)
    {
        return $this->userService->updateById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function delete($id, Request $request)
    {
        return $this->userService->deleteById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function profile(Request $request)
    {
        return $this->userService->getProfile($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function changePassword(Request $request)
    {
        return $this->userService->changePassword($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function changeAvatar($id,Request $request)
    {
        return $this->userService->updateAvatar($id,$request);
    }
}
