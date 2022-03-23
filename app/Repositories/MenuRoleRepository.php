<?php

namespace App\Repositories;

use App\Models\MenuRole;

class MenuRoleRepository extends CrudRepository
{

    public function model()
    {
        return MenuRole::class;
    }

    /**
     * @param string|null $searchBy
     * @param string|null $searchParam
     * @param int $limit
     * @param array $columns
     * @param string $pageName
     * @param int $page
     * @param string|null $active
     * @param bool $actived
     * @param string|null $filed1
     * @param $value1
     * @param $sortBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginatation(string $searchBy = null, string $searchParam = null, int $limit = 10, array $columns = ['*'], string $pageName = 'page', int $page = 1, string $active = null, bool $actived = true, string $filed1 = null, $value1 = null, $sortBy = 'DESC')
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query
            ->where('menu_role.' . $searchBy, 'LIKE', "%{$searchParam}%")
            ->where('menu_role.' . $active, $actived)
            ->leftJoin('menus', 'menu_role.menus_id', '=', 'menus.id')
            ->orderBy('menu_role.created_at', $sortBy)
            ->paginate($limit, ['menu_role.*', 'menus.name'], $pageName, $page);

        $this->unsetClauses();

        return $models;
    }
}
