<?php

namespace App\Http\Controllers;

use App\Services\InboxService;
use Illuminate\Http\Request;

class InboxController extends Controller
{

    private InboxService $inboxService;

    public function __construct(InboxService $inboxService)
    {
        $this->inboxService = $inboxService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function all(Request $request)
    {
        return $this->inboxService->all($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function paginate(Request $request)
    {
        return $this->inboxService->paginate($request);
    }


    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function create(Request $request)
    {
        return $this->inboxService->create($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function read($id, Request $request)
    {
        return $this->inboxService->read($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function show($id, Request $request)
    {
        return $this->inboxService->getById($id, $request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function count(Request $request)
    {
        return $this->inboxService->getCount($request);
    }
}
