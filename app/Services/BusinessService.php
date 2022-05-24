<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Models\Business;
use App\Repositories\BusinessRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;

class BusinessService implements BaseService
{

    use BaseResponse;

    protected BusinessRepository $businessRepository;

    public function __construct(BusinessRepository $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->businessRepository->all(['id', 'business_name', 'active'], 'active', Constants::ACTIVE)
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
            $business = new Business();
            $business->business_name = $request->business_name;
            $business->active = $request->active;
            $business->created_by = auth()->user()->id;
            $this->businessRepository->create($business->toArray());
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
            $business = $this->businessRepository->getById($id);
            $business->active = Constants::NON_ACTIVE;
            $this->businessRepository->updateById($id, $business->toArray());
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
            $business = $this->businessRepository->getById($id);
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $business
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
            $this->businessRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'active', Constants::ACTIVE)
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
            $business = $this->businessRepository->getById($id);
            $business->business_name = $request->business_name;
            $business->active = $request->active;
            $business->updated_by = auth()->user()->id;
            $this->businessRepository->updateById($id, $business->toArray());
        } catch (\Exception $ex) {
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }
}
