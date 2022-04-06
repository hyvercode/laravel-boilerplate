<?php

namespace App\Services;

use App\Helpers\Base64Converter;
use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\Menus;
use App\Repositories\MenuRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuService implements BaseService
{

    private $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->menuRepository->all(['*'], 'active', true)
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
            $this->menuRepository->paginate($request->searchBy, $request->searchParam, $request->perPage, ['*'], 'page', $request->currentPage, null, null, $request->sortBy, $request->sort)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function create(Request $request)
    {
        try {
            $menu = new Menus();
            $menu->name = $request->name;
            $menu->href = $request->href;
            $menu->slug = $request->slug;
            $menu->icon = Base64Converter::base64ToImage($menu->company_id . '/banner', $request->icon);
            $menu->parent_id = $request->parent_id;
            $menu->menu_id = $request->menu_id;
            $menu->sequence = $menu->max('sequence') + 1;
            $menu->active = $request->active;
            $menu->created_at = DateTimeConverter::getDateTimeNow();
            $menu->created_by = auth()->user()->id;
            $this->menuRepository->create($menu->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function deleteById($id, Request $request)
    {
        $record = $this->menuRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $record->forceDelete();
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getById($id, Request $request)
    {
        $record = $this->menuRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        return BaseResponse::buildResponse(
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
     */
    public function updateById($id, Request $request)
    {
        $menu = $this->menuRepository->getById($id);
        try {
            $menu->name = $request->name;
            $menu->href = $request->href;
            $menu->slug = $request->slug;
            $menu->parent_id = $request->parent_id;
            $menu->is_icon = $request->is_icon;
            $menu->icon = Base64Converter::isBase64('/menus', $request->icon);
            $menu->menu_id = $request->menu_id;
            $menu->active = $request->active;
            $menu->updated_at = DateTimeConverter::getDateTimeNow();
            $menu->updated_by = auth()->user()->id;
            $this->menuRepository->updateById($id, $menu->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function menu(Request $request)
    {
        try {
            $user = auth()->user();
            if (!empty($user)) {
                $roles = $user->menu_roles;
            } else {
                $roles = '';
            }
        } catch (\Exception $e) {
            $roles = '';
        }
        if ($request->has('menu')) {
            $menuName = $request->input('menu');
        } else {
            $menuName = 'sidebar_menu';
        }

        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->menuRepository->getMenu($roles, $menuName)
        );
    }
}
