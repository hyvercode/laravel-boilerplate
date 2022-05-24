<?php

namespace App\Http\Controllers;

use App\Services\BusinessService;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    private BusinessService $businessService;

    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->businessService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->businessService->paginate($request);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Utils\BusinessException
     */
    public function show($id, Request $request)
    {
        return $this->businessService->getById($id, $request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Utils\BusinessException
     */
    public function create(Request $request)
    {
        return $this->businessService->create($request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Utils\BusinessException
     */
    public function update($id, Request $request)
    {
        return $this->businessService->updateById($id, $request);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Utils\BusinessException
     */
    public function delete($id, Request $request)
    {
        return $this->businessService->deleteById($id, $request);
    }
}
