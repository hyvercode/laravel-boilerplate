<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Province;
use App\Repositories\ProvinceRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;

class ProvinceService implements BaseService
{

    use BaseResponse;

    protected ProvinceRepository $provinceRepository;


    public function __construct(ProvinceRepository $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->provinceRepository->all(['id', 'province_name_id', 'province_name_en'], 'active', Constants::ACTIVE)
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
            $provincies = new Province();
            $provincies->id = $request->id;
            $provincies->province_name_id = $request->province_name_id;
            $provincies->province_name_en = $request->province_name_en;
            $provincies->coordinate = json_encode($request->coordinate);
            $provincies->active = $request->active;
            $provincies->created_by =auth()->user()->id;
            $this->provinceRepository->create($provincies->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function deleteById($id, Request $request)
    {
        try {
            $provincies = $this->provinceRepository->getById($id);
            $provincies->active = Constants::NON_ACTIVE;
            $this->provinceRepository->updateById($id, $provincies->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getById($id, Request $request)
    {
        try {
            $provincies = $this->provinceRepository->getById($id);
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $provincies
        );
    }

    /**
     * @param null $searchBy
     * @param null $searchParam
     * @param int $limit
     * @param array|string[] $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->provinceRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'active', Constants::ACTIVE)
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
        try {
            $provincies = $this->provinceRepository->getById($id);
            $provincies->province_name_id = $request->province_name_id;
            $provincies->province_name_en = $request->province_name_en;
            $provincies->coordinate = json_encode($request->coordinate);
            $provincies->active = $request->active;
            $provincies->updated_by =auth()->user()->id;
            $this->provinceRepository->updateById($id, $provincies->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }
}
