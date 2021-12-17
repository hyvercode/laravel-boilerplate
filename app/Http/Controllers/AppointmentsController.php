<?php

namespace App\Http\Controllers;

use App\Services\AppointmentsService;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    private $AppointmentService;

    public function __construct(AppointmentsService $appointmentsService)
    {
        $this->AppointmentService = $appointmentsService;
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pickerCreate(Request $request)
    {
        return $this->AppointmentService->create($request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pickerReschedule(Request $request)
    {
        return $this->AppointmentService->reschedule($request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pickerAppointments(Request $request)
    {
        return $this->AppointmentService->appointments($request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pickerReschedules(Request $request)
    {
        return $this->AppointmentService->reschedules($request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approve(Request $request)
    {
        return $this->AppointmentService->approve($request);
    }
}
