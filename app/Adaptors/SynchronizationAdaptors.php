<?php

namespace App\Adaptors;

use App\Traits\RequestService;
use Illuminate\Http\Request;

class SynchronizationAdaptors
{
    use RequestService;

    public $baseUri;
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.sync-service.base_uri');
        $this->secret = config('services.sync-service.secret');
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendOTP($payload, $headers, Request $request)
    {
        $this->doPost('/api/v1/notifications/otp', $headers, $payload, $request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMail($payload, $headers, Request $request)
    {
        $this->doPost('/api/v1/notifications/mail', $headers, $payload, $request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fcmStore($payload, $headers, Request $request)
    {
        $this->doPost('/api/v1/fcm/create', $headers, $payload, $request);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \App\Utils\BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendNotification($payload, $headers, Request $request)
    {
        $this->doPost('/api/v1/fcm/send/notification', $headers, $payload, $request);
    }
}
