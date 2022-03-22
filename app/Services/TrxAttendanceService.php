<?php

namespace App\Services;

use App\Helpers\Base64Converter;
use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\TmpAttendance;
use App\Models\TrxAttendance;
use App\Repositories\EmployeeRepository;
use App\Repositories\TmpAttendanceRepository;
use App\Repositories\TrxAttendanceRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrxAttendanceService
{

    private $trxAttendanceRepository;
    private $employeeRepository;
    private $tmpAttendanceRepository;

    public function __construct(TrxAttendanceRepository $trxAttendanceRepository,
                                EmployeeRepository      $employeeRepository,
                                TmpAttendanceRepository $tmpAttendanceRepository)
    {
        $this->trxAttendanceRepository = $trxAttendanceRepository;
        $this->employeeRepository = $employeeRepository;
        $this->tmpAttendanceRepository = $tmpAttendanceRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function attendance(Request $request)
    {
        //find employee
        $employee = $this->employeeRepository->findByCompanyIdAndEmployeeIdAdnBranchId(auth()->user()->company_id, auth()->user()->employee_id);
        if ($employee === null) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            DB::beginTransaction();
            //find attendance
            $clock = DateTimeConverter::dateTimeFormat($request->clock, Constants::DATE_TIME_YYYY_MM_DD_HH_MM_SS);
            $attendance = $this->trxAttendanceRepository->findByEmployeeIdClock(auth()->user()->employee_id, DateTimeConverter::dateTimeFormat($clock, Constants::DATE_YYYY_MM_DD));
            if ($attendance === null && strtoupper(trim($request->status)) === Constants::CLOCK_IN) {
                $attendance_in = new TrxAttendance();
                $attendance_in->company_id = auth()->user()->company_id;
                $attendance_in->branch_id = $employee->branch_id;
                $attendance_in->employee_id = $employee->id;
                $attendance_in->check_in = $clock;
                $attendance_in->photo_path_in = Base64Converter::base64ToImage(Constants::ATTENDANCE_DIR . '/' . $employee->company_code . '/' . $employee->branch_id . '/' . $employee->id . '/' . DateTimeConverter::dateTimeFormatNow('Ymd'), $request->photo_path);
                $attendance_in->location_in = $request->location;
                $attendance_in->coordinate_in = $request->coordinate;
                $attendance_in->created_by = $employee->id;
                $attendance_in->created_at = DateTimeConverter::getDateTimeNow();
                $attendance_in->status = Constants::CLOCK_IN;
                $records = $this->trxAttendanceRepository->create($attendance_in->toArray());
            } else {
                $attendance->check_out = $clock;
                $attendance->photo_path_out = Base64Converter::base64ToImage(Constants::ATTENDANCE_DIR . '/' . $employee->company_code . '/' . $employee->branch_id . '/' . $employee->id . '/' . DateTimeConverter::dateTimeFormatNow('Ymd'), $request->photo_path);
                $attendance->location_out = $request->location;
                $attendance->coordinate_out = $request->coordinate;
                $attendance->updated_by = $employee->id;
                $attendance->status = Constants::CLOCK_OUT;
                $attendance->updated_at = DateTimeConverter::getDateTimeNow();
                $records = $this->trxAttendanceRepository->updateById($attendance->id, $attendance->toArray());
            }
            //store tmp
            $this->storeTmpAttendance($records, $request);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                "status" => $records->status,
                "clock_in" => $records->check_in,
                "location_in" => $records->location_in,
                "photo_path_in" => $records->photo_path_in,
                "clock_out" => $records->check_out,
                "location_out" => $records->location_out,
                "photo_path_out" => $records->photo_path_out,
                "working_hours" => empty($records->check_out) ? 0 : DateTimeConverter::calculatingDateTimeDifference($records->check_in, $records->check_out)
            ]
        );
    }

    /**
     * @param $txrAttendance
     * @param Request $request
     */
    private function storeTmpAttendance($txrAttendance, Request $request)
    {
        $tmpAttendance = new TmpAttendance();
        $tmpAttendance->company_id= auth()->user()->company_id;
        $tmpAttendance->attendance_id = $txrAttendance->id;
        $tmpAttendance->clock = DateTimeConverter::dateTimeFormat($request->clock, Constants::DATE_TIME_YYYY_MM_DD_HH_MM_SS);
        $tmpAttendance->photo_path = strtoupper($request->status) === Constants::CLOCK_IN ? $txrAttendance->photo_path_in : $txrAttendance->photo_path_out;
        $tmpAttendance->location = $request->location;
        $tmpAttendance->coordinate = $request->coordinate;
        $tmpAttendance->created_by = $txrAttendance->employee_id;
        $tmpAttendance->created_at = DateTimeConverter::getDateTimeNow();
        $tmpAttendance->status = strtoupper($request->status);
        $tmpAttendance->mode = strtoupper($request->mode);
        $this->tmpAttendanceRepository->create($tmpAttendance->toArray());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function checkAttendance(Request $request)
    {
        $attendance = $this->trxAttendanceRepository->findByEmployeeIdDate( auth()->user()->employee_id, DateTimeConverter::getDateTimeNow());
        if ($attendance === null) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9007, Constants::ERROR_CODE_9007);
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                "clock_in" => $attendance->check_in,
                "location_in" => $attendance->location_in,
                "photo_path_in" => $attendance->photo_path_in,
                "clock_out" => $attendance->check_out,
                "location_out" => $attendance->location_out,
                "photo_path_out" => $attendance->photo_path_out,
                "status" => $attendance->status,
                "working_hours" => empty($attendance->check_out) ? 0 : DateTimeConverter::calculatingDateTimeDifference($attendance->check_in, $attendance->check_out)
            ]
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->trxAttendanceRepository->paginatation($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, $request->dateFilter, $request->dateFrom, $request->dateTo)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginateTmp(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->tmpAttendanceRepository->paginatation($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'status', Constants::ACTIVE, 'attendance_id', $request->attendance_id)
        );
    }
}
