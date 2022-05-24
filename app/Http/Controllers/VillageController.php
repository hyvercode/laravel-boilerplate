<?php

namespace App\Http\Controllers;

use App\Services\VillageService;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    private VillageService $villageService;

    public function __construct(VillageService $villageService)
    {
        $this->villageService = $villageService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->villageService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->villageService->paginate($request);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->villageService->getById($id, $request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->villageService->create($request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update($id, Request $request)
    {
        return $this->villageService->updateById($id, $request);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function delete($id, Request $request)
    {
        return $this->villageService->deleteById($id, $request);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Utils\BusinessException
     */
    public function getByDistrict($id)
    {
        return $this->villageService->findByDistrict($id);
    }
}
