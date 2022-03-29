<?php

namespace App\Repositories;

use App\Models\Inbox;

class InboxRepository extends CrudRepository
{

    public function model()
    {
        return Inbox::class;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function countByUserId($user_id)
    {
        return Inbox::where('user_id', $user_id)
            ->where('read', true)
            ->count();
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
    public function pagination(string $searchBy = null, string $searchParam = null, int $limit = 10, array $columns = ['*'], string $pageName = 'page', int $page = 1, $user_id, $sortBy = 'created_at', string $sort = 'DESC')
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query
            ->where('inboxs.' . $searchBy, 'LIKE', "%{$searchParam}%")
            ->where('inboxs.user_id', $user_id)
            ->leftJoin('users', 'inboxs.created_by', 'users.id')
            ->orderBy('inboxs.' . $sortBy, $sort)
            ->paginate($limit, ['inboxs.*', 'users.name', 'users.email', 'users.phone_number', 'users.avatar'], $pageName, $page);

        $this->unsetClauses();

        return $models;
    }
}
