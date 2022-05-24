<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\MenuRole;
use App\Repositories\MenuRoleRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuRoleService implements BaseService
{
    use BaseResponse;
    private MenuRoleRepository $menuRoleRepository;

    public function __construct(MenuRoleRepository $menuRoleRepository)
    {
        $this->menuRoleRepository = $menuRoleRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->menuRoleRepository->all(['*'], 'active', true, 'company_id', auth()->user()->company_id)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->menuRoleRepository->paginatation($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'company_id', auth()->user()->company_id)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function create(Request $request)
    {
        try {
            $menu = new MenuRole();
            $menu->company_id = auth()->user()->company_id;
            $menu->role_name = $request->role_name;
            $menu->menus_id = $request->menus_id;
            $menu->active = $request->active;
            $menu->created_at = DateTimeConverter::getDateTimeNow();
            $menu->created_by = auth()->user()->id;
            $this->menuRoleRepository->create($menu->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function deleteById($id, Request $request)
    {
        $record = $this->menuRoleRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $record->forceDelete();
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function getById($id, Request $request)
    {
        $record = $this->menuRoleRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $record
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function updateById($id, Request $request)
    {
        $menu = $this->menuRoleRepository->getById($id);

        try {
            $menu->role_name = $request->role_name;
            $menu->menus_id = $request->menus_id;
            $menu->active = $request->active;
            $menu->updated_at = DateTimeConverter::getDateTimeNow();
            $menu->updated_by = auth()->user()->id;
            $this->menuRoleRepository->updateById($id, $menu->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }
}
