<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 17/03/22
 * Time: 14.53
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Services;

use App\adaptors\SitamaGateway;
use App\Enums\Status;
use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\ProspectDebtor;
use App\Repositories\ProspectDebtorRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProspectDebtorService implements BaseService
{

    private $prospectDebtorRepository;
    private $sitamaGateway;

    public function __construct(ProspectDebtorRepository $prospectDebtorRepository,
                                SitamaGateway            $sitamaGateway)
    {
        $this->prospectDebtorRepository = $prospectDebtorRepository;
        $this->sitamaGateway = $sitamaGateway;
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function all(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->prospectDebtorRepository->all(['*'])
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function paginate(Request $request)
    {
        return BaseResponse::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $this->prospectDebtorRepository->paginate($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page)
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
            $prospectDebtor = new ProspectDebtor();
            $prospectDebtor->booking_date = DateTimeConverter::getDateTimeNow();
            $prospectDebtor->booking_number = DateTimeConverter::dateTimeFormat(DateTimeConverter::getDateTimeNow(), 'ymd') . CommonUtil::randomNumber();
            $prospectDebtor->fullname = $request->fullname;
            $prospectDebtor->phone_number = CommonUtil::phoneNumber($request->phone_number);
            $prospectDebtor->email = $request->email;
            $prospectDebtor->address = $request->address;
            $prospectDebtor->license_plate = str_replace(" ", "", $request->license_plate);
            $prospectDebtor->vehicle_type = $request->vehicle_type;
            $prospectDebtor->application_status = Status::new;
            $prospectDebtor->created_at = DateTimeConverter::getDateTimeNow();
            $this->prospectDebtorRepository->create($prospectDebtor->toArray());

            //Send mail
            $this->sendMail($prospectDebtor);

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
     * @param ProspectDebtor $prospectDebtor
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function sendMail(ProspectDebtor $prospectDebtor)
    {
        //Send Mail to Admin
        $content =
            "Hi !, Admin this is new Debtor,
            \n Name          : $prospectDebtor->fullname
            \n Phone Number  : $prospectDebtor->phone_number
            \n Email         : $prospectDebtor->email
            \n Address       : $prospectDebtor->address
            \n License Plate : $prospectDebtor->license_plate
            \n Vehicle       : $prospectDebtor->vehicle_type

            \n Regards
            \n agreesip.com";
        $this->sitamaGateway->sendMails($content);

        //Send Mail to prospect debtor
        $content =
            "Hi ! Bpk/Ibu $prospectDebtor->fullname, aplikasi pengajuan Anda telah kami terima
            \n Nomor Booking $prospectDebtor->booking_number

            \nPENTING: Demi keamanan aplikasi pengajuan Anda, mohon simpan dan tidak menyebarkan kode ini kepada siapa pun.

            \n Terima Kasih
            \n agreesip.com";
        $this->sitamaGateway->sendMail($prospectDebtor->email, 'Name : $prospectDebtor->fullname', $content);
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
        $record = $this->prospectDebtorRepository->getById($id);
        if (empty($record)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }

        try {
            $record->delete();
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
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function getById($id, Request $request)
    {
        $record = $this->prospectDebtorRepository->getById($id);
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
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function updateById($id, Request $request)
    {
        $prospectDebtor = $this->prospectDebtorRepository->getById($id);
        if (empty($prospectDebtor)) {
            throw new BusinessException(Constants::HTTP_CODE_409, Constants::ERROR_MESSAGE_9001, Constants::ERROR_CODE_9001);
        }
        try {
            $prospectDebtor->application_status = $request->application_status;
            $prospectDebtor->updated_at = DateTimeConverter::getDateTimeNow();
            $prospectDebtor->updated_by = auth()->user()->id;
            $this->prospectDebtorRepository->updateById($id, $prospectDebtor->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return BaseResponse::statusResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
        );
    }
}
