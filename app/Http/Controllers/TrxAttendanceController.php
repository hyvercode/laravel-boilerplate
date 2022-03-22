<?php

namespace App\Http\Controllers;

use App\Services\TrxAttendanceService;
use Illuminate\Http\Request;

class TrxAttendanceController extends Controller
{
    private $trxAttendanceService;

    public function __construct(TrxAttendanceService $trxAttendanceService)
    {
        $this->trxAttendanceService = $trxAttendanceService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Utils\BusinessException
     */
    public function postAttendance(Request $request)
    {
        return $this->trxAttendanceService->attendance($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Utils\BusinessException
     */
    public function getCheckAttendance(Request $request)
    {
        return $this->trxAttendanceService->checkAttendance($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Utils\BusinessException
     */
    public function paginate(Request $request)
    {
        return $this->trxAttendanceService->paginate($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Utils\BusinessException
     */
    public function paginateTmp(Request $request)
    {
        return $this->trxAttendanceService->paginateTmp($request);
    }
}
