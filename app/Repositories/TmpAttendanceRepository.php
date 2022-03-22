<?php

namespace App\Repositories;

use App\Models\TmpAttendance;

class TmpAttendanceRepository extends CrudRepository
{

    public function model()
    {
        return TmpAttendance::class;
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
    public function paginatation(string $searchBy = null, string $searchParam = null, int $limit = 10, array $columns = ['coverage_areas.*', 'branches.branch_name', 'employees.employee_name'], string $pageName = 'page', int $page = 1, string $active = null, string $actived = null, string $filed1 = null, $value1 = null, string $date_filter = null, $date_from = null, $date_to = null, $sortBy = 'DESC', string $filed2 = null, $value2 = null)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();
        $models = $this->query
            ->where('tmp_attendances.' . $searchBy, 'LIKE', "%{$searchParam}%")
            ->where($filed1, $value1)
            ->orderBy('tmp_attendances.created_at', $sortBy)
            ->paginate($limit, ['tmp_attendances.*'], $pageName, $page);

        $this->unsetClauses();

        return $models;
    }
}
