<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixeds
     */
    public function all(Request $request)
    {
        return $this->companyService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixeds
     */
    public function paginate(Request $request)
    {
        return $this->companyService->paginate($request);
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->companyService->create($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update($id, Request $request)
    {
        return $this->companyService->updateById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->companyService->getById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function delete($id, Request $request)
    {
        return $this->companyService->deleteById($id, $request);
    }

}
