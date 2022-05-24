<?php

namespace App\Http\Controllers;

use App\Services\MenuListService;
use Illuminate\Http\Request;

class MenuListController extends Controller
{
    private MenuListService $menuListService;

    public function __construct(MenuListService $menuListService)
    {
        $this->menuListService = $menuListService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->menuListService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->menuListService->paginate($request);
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->menuListService->create($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update($id, Request $request)
    {
        return $this->menuListService->updateById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->menuListService->getById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function delete($id, Request $request)
    {
        return $this->menuListService->deleteById($id, $request);
    }
}
