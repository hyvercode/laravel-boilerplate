<?php

namespace App\Repositories;

use App\Models\TrxAttendance;
use Illuminate\Support\Facades\DB;

class TrxAttendanceRepository extends CrudRepository
{

    /**
     * @return string
     */
    public function model()
    {
        return TrxAttendance::class;
    }

    /**
     * @param $employee_id
     * @param $created_at
     * @return mixed
     */
    public function findByEmployeeIdDate($employee_id, $created_at)
    {
        return TrxAttendance::where('employee_id', $employee_id)
            ->whereDate('created_at', '=', $created_at)
            ->get()->first();
    }

    /**
     * @param $employee_id
     * @param $created_at
     * @return mixed
     */
    public function findByEmployeeIdClock($employee_id, $created_at)
    {
        return TrxAttendance::where('employee_id', $employee_id)
            ->whereDate('check_in', '=', $created_at)
            ->get()->first();
    }

    /**
     * @param string|null $searchBy
     * @param null $searchParam
     * @param int $limit
     * @param array|string[] $columns
     * @param string $pageName
     * @param int $page
     * @param string|null $active
     * @param string|null $actived
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginatation(string $searchBy = null, string $searchParam = null, int $limit = 10, array $columns = ['*'], string $pageName = 'page', int $page = 1, string $date_filter = null, $date_from = null, $date_to = null, $sortBy = 'DESC')
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        if ($date_filter && $date_from && $date_to && $sortBy) {
            $models = $this->query
                ->where('trx_attendances.' . $searchBy, 'LIKE', "%{$searchParam}%")
                ->whereBetween('trx_attendances.' . $date_filter, [$date_from, $date_to])
                ->join('employees', 'trx_attendances.employee_id', 'employees.id')
                ->orderBy('trx_attendances.created_at', $sortBy)
                ->paginate($limit, ['trx_attendances.*','employees.first_name','employees.images','employees.phone_number','employees.email'], $pageName, $page);
        } else {
            $models = $this->query
                ->where('trx_attendances.' . $searchBy, 'LIKE', "%{$searchParam}%")
                ->join('employees', 'trx_attendances.employee_id', 'employees.id')
                ->orderBy('trx_attendances.created_at', $sortBy)
                ->paginate($limit, ['trx_attendances.*', 'employees.first_name','employees.images','employees.phone_number','employees.email'], $pageName, $page);
        }

        $this->unsetClauses();

        return $models;
    }
}
