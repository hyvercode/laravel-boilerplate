<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Village;
use App\Repositories\VillageRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;

class VillageService implements BaseService
{

    use BaseResponse;

    private VillageRepository $villageRepository;

    public function __construct(VillageRepository $villageRepository)
    {
        $this->villageRepository = $villageRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->villageRepository->all(['id', 'district_id', 'postal_code', 'village_name'], 'active', Constants::ACTIVE)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function create(Request $request)
    {
        $village = $this->villageRepository->findByDistrictId($request->district_id);
        if (count($village) < 1) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $village = new Village();
            $village->id = $request->id;
            $village->district_id = $request->district_id;
            $village->village_name = $request->village_name;
            $village->coordinate = json_encode($request->coordinate);
            $village->postal_code = $request->postal_code;
            $village->active = $request->active;
            $village->created_by = auth()->user()->id;
            $this->villageRepository->create($village->toArray());
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
            $record = $this->villageRepository->getById($id);
            $record->active = Constants::NON_ACTIVE;
            $this->villageRepository->updateById($id, $record->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
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
            $record = $this->villageRepository->getById($id);
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $record
        );
    }

    /**
     * @param null $searchBy
     * @param null $searchParam
     * @param int $limit
     * @param array|string[] $columns
     * @param string $pageName
     * @param int $page
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->villageRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'active', Constants::ACTIVE)
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
        $district = $this->villageRepository->findByDistrictId($request->district_id);
        if (count($district) < 1) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $village = $this->villageRepository->getById($id);
            $village->district_id = $request->district_id;
            $village->village_name = $request->village_name;
            $village->coordinate = json_encode($request->coordinate);
            $village->active = $request->active;
            $village->postal_code = $request->postal_code;
            $village->created_by = auth()->user()->id;
            $this->villageRepository->updateById($id, $district->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function findByDistrict($id)
    {
        try {
            $record = $this->villageRepository->findByDistrictId($id);
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $record
        );
    }
}
