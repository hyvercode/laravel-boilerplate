<?php

namespace App\Services;

use App\Helpers\Base64Converter;
use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyService implements BaseService
{

    use BaseResponse;

    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
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
            $this->companyRepository->all(['*'], 'active', Constants::ACTIVE)
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
            $this->companyRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, 'active', true, 'id', auth()->user()->company_id)
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function create(Request $request)
    {

        $code = $this->companyRepository->existsBy('company_code', strtoupper($request->company_code));
        if ($code) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'This code [ ' . $request->company_code . ' ] already used by other Company, please change! your Company Code', Constants::ERROR_CODE_9001);
        }

        try {
            $company = new Company();
            $company->company_code = strtoupper($request->company_code);
            $company->company_name = $request->company_name;
            $company->company_alias = $request->company_alias;
            $company->address = $request->address;
            $company->province_id = $request->province_id;
            $company->city_id = $request->city_id;
            $company->district_id = $request->district_id;
            $company->village_id = $request->village_id;
            $company->business_id = $request->business_id;
            $company->postal_code = $request->postal_code;
            $company->phone_number = $request->phone_number;
            $company->email = $request->email;
            $company->contact_person = $request->contact_person;
            $company->contact_number = CommonUtil::phoneNumber($request->contact_number);
            $company->npwp_no = $request->npwp_no;
            $company->npwp_path = Base64Converter::base64ToImage($request->company_code, $request->npwp_path);
            $company->siup_no = $request->siup_no;
            $company->siup_path = Base64Converter::base64ToImage($request->company_code, $request->siup_path);
            $company->image = Base64Converter::base64ToImage($request->company_code, $request->image);
            $company->coordinate = $request->coordinate;
            $company->active = $request->active;
            $company->created_at = DateTimeConverter::getDateTimeNow();
            $company->created_by = auth()->user()->id;
            $this->companyRepository->create($company->toArray());
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function deleteById($id, Request $request)
    {
        $record = $this->companyRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $record->delete();
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getById($id, Request $request)
    {
        $record = $this->companyRepository->getById($id);
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
     */
    public function updateById($id, Request $request)
    {
        $company = $this->companyRepository->getById($id);
        if (empty($company)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $company->company_name = $request->company_name;
            $company->company_alias = $request->company_alias;
            $company->address = $request->address;
            $company->province_id = $request->province_id;
            $company->city_id = $request->city_id;
            $company->district_id = $request->district_id;
            $company->village_id = $request->village_id;
            $company->business_id = $request->business_id;
            $company->postal_code = $request->postal_code;
            $company->phone_number = $request->phone_number;
            $company->email = $request->email;
            $company->contact_person = $request->contact_person;
            $company->contact_number = CommonUtil::phoneNumber($request->contact_number);
            $company->npwp_no = $request->npwp_no;
            $company->npwp_path = Base64Converter::isBase64($request->company_code, $request->npwp_path);
            $company->siup_no = $request->siup_no;
            $company->siup_path = Base64Converter::isBase64($request->company_code, $request->siup_path);
            $company->image = Base64Converter::isBase64($request->company_code, $request->image);
            $company->coordinate = $request->coordinate;
            $company->active = $request->active;
            $company->updated_at = DateTimeConverter::getDateTimeNow();
            $company->updated_by = auth()->user()->id;
            $this->companyRepository->updateById($id, $company->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200
        );
    }
}
