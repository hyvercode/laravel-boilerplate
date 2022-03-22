<?php

namespace App\Http\Controllers;

use App\Services\ProspectDebtorService;
use Illuminate\Http\Request;

class ProspectDebtorController extends Controller
{
    protected $prospectDebtorService;

    public function __construct(ProspectDebtorService $prospectDebtorService)
    {
        $this->prospectDebtorService = $prospectDebtorService;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function all(Request $request)
    {
        return $this->prospectDebtorService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->prospectDebtorService->paginate($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function create(Request $request)
    {
        return $this->prospectDebtorService->create($request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        return $this->prospectDebtorService->updateById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function show($id, Request $request)
    {
        return $this->prospectDebtorService->getById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function delete($id, Request $request)
    {
        return $this->prospectDebtorService->deleteById($id, $request);
    }
}
