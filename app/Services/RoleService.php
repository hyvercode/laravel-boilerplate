<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Repositories\RoleRepository;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;

class RoleService implements BaseService
{
    use BaseResponse;
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->roleRepository->all(['*'])
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function paginate(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->roleRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page)
        );
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.
    }

    public function deleteById($id, Request $request)
    {
        // TODO: Implement deleteById() method.
    }

    public function getById($id, Request $request)
    {
        // TODO: Implement getById() method.
    }

    public function updateById($id, Request $request)
    {
        // TODO: Implement updateById() method.
    }
}
