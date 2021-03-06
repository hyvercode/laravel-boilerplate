<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\Inbox;
use App\Repositories\InboxRepository;
use App\Traits\BaseResponse;
use App\Traits\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InboxService
{
    use BaseResponse;
    private InboxRepository $inboxRepository;

    public function __construct(InboxRepository $inboxRepository)
    {
        $this->inboxRepository = $inboxRepository;
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
            $this->inboxRepository->all(['*'], null, null, 'company_id', auth()->user()->company_id)
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
            $this->inboxRepository->pagination($request->searchBy, $request->searchParam, $request->limit, ['*'], 'page', $request->page, auth()->user()->id, $request->sortBy, $request->sort)
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
            $inbox = new Inbox();
            $inbox->subject = $request->subject;
            $inbox->body = $request->body;
            $inbox->type = $request->type;
            $inbox->user_id = auth()->user()->id;
            $inbox->icon = $request->icon;
            $inbox->created_at = DateTimeConverter::getDateTimeNow();
            $inbox->created_by = auth()->user()->id;
            $this->inboxRepository->create($inbox->toArray());
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
     */
    public function deleteById($id, Request $request)
    {
        try {
            $this->inboxRepository->deleteById($id);
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
     */
    public function getById($id, Request $request)
    {
        $record = $this->inboxRepository->getById($id);
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
    public function read($id, Request $request)
    {
        $inbox = $this->inboxRepository->getById($id);
        try {
            $inbox->read = true;
            $inbox->updated_at = DateTimeConverter::getDateTimeNow();
            $inbox->updated_by = auth()->user()->id;
            $read = $this->inboxRepository->updateById($id, $inbox->toArray());
        } catch (\Exception $ex) {
            Log::error(Constants::ERROR, ['message' => $ex->getMessage()]);
            throw new BusinessException(Constants::HTTP_CODE_500, Constants::ERROR_MESSAGE_9000, Constants::ERROR_CODE_9000);
        }

        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            $read
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws BusinessException
     */
    public function getCount(Request $request)
    {
        return self::buildResponse(
            Constants::HTTP_CODE_200,
            Constants::HTTP_MESSAGE_200,
            [
                "total" => $this->inboxRepository->countByUserId(auth()->user()->id)
            ]
        );
    }

}
