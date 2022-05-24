<?php

namespace App\Http\Controllers;

use App\Services\MenuRoleService;
use Illuminate\Http\Request;

class MenuRoleController extends Controller
{
    private MenuRoleService $menuRoleService;

    public function __construct(MenuRoleService $menuRoleService)
    {
        $this->menuRoleService = $menuRoleService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->menuRoleService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->menuRoleService->paginate($request);
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->menuRoleService->create($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update($id, Request $request)
    {
        return $this->menuRoleService->updateById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->menuRoleService->getById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function delete($id, Request $request)
    {
        return $this->menuRoleService->deleteById($id, $request);
    }
}
