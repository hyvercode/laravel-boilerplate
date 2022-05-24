<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\City;
use App\Repositories\CityRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;

class CityService implements BaseService
{

    use BaseResponse;
    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->cityRepository->all(['id', 'province_id', 'city_name'], 'active', Constants::ACTIVE)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function create(Request $request)
    {
        $province = $this->cityRepository->findByProvince($request->province_id);
        if (count($province) < 1) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $city = new City();
            $city->id = $request->id;
            $city->province_id = $request->province_id;
            $city->city_name = $request->city_name;
            $city->coordinate = json_encode($request->coordinate);
            $city->active = $request->active;
            $city->created_by = auth()->user()->id;
            $this->cityRepository->create($city->toArray());
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
            $city = $this->cityRepository->getById($id);
            $city->active = Constants::NON_ACTIVE;
            $this->cityRepository->updateById($id, $city->toArray());
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
            $records = $this->cityRepository->getById($id);
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $records
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
            $this->cityRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'active', Constants::ACTIVE)
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
        $province = $this->cityRepository->findByProvince($request->province_id);
        if (count($province) < 1) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        try {
            $city = $this->cityRepository->getById($id);
            $city->province_id = $request->province_id;
            $city->city_name = $request->city_name;
            $city->coordinate = json_encode($request->coordinate);
            $city->active = $request->active;
            $city->updated_by = auth()->user()->id;
            $this->cityRepository->updateById($id, $city->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }

    /**
     * @param $province_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByProvince($province_id)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->cityRepository->findByProvince($province_id)
        );
    }
}
