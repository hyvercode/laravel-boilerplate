<?php

namespace App\Http\Controllers;

use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController
{

    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu(Request $request){
        return $this->menuService->menu($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->menuService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->menuService->paginate($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function create(Request $request)
    {
        return $this->menuService->create($request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function update($id, Request $request)
    {
        return $this->menuService->updateById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function show($id, Request $request)
    {
        return $this->menuService->getById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function delete($id, Request $request)
    {
        return $this->menuService->deleteById($id, $request);
    }
}
