<?php

namespace App\Http\Controllers;

use App\Services\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function all(Request $request)
    {
       return $this->bankService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->bankService->paginate($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function create(Request $request)
    {
        return $this->bankService->create($request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        return $this->bankService->updateById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function show($id, Request $request)
    {
        return $this->bankService->getById($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \App\Traits\BusinessException
     */
    public function delete($id, Request $request)
    {
        return $this->bankService->deleteById($id, $request);
    }
}
