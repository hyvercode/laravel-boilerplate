<?php


namespace App\Utils;

use App\Models\Loggers;
use App\Models\Telemetri;

class Monologger
{

    /**
     * @param $level
     * @param $message
     */
    public static function log($level, $message, $request_id = null)
    {
//        $logs = new Loggers();
//        $logs->request_id = $request_id;
//        $logs->level = $level;
//        $logs->message = $message;
//        $logs->created_at = DateTimeConverter::getDateTimeNow();
//        $logs->save();
    }

    /**
     * @param $level
     * @param $request
     */
    public static function logTelemetri($level, $request_id, $request, $universal_id, $response)
    {
//        $telemetri = new Telemetri();
//        $telemetri->request_id = $request_id;
//        $telemetri->level = $level;
//        $telemetri->universal_id = $universal_id;
//        $telemetri->client_ip = $request->getClientIp();
//        $telemetri->platform = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]) ? 'Mobile' : 'Desktop';
//        $telemetri->ip = $request->ip();
//        $telemetri->host = $request->getHttpHost();
//        $telemetri->full_url = $request->getUri();
//        $telemetri->path = $request->path();
//        $telemetri->method = $request->method();
//        $telemetri->token = $request->bearerToken();
//        $telemetri->session = $request->getSession();
//        $telemetri->reqeust_contents = json_decode($request->isMethod('post') ? $request->getContent() : $request->getQueryString(),true);
//        $telemetri->response_contents = $response;
//        $telemetri->created_at = DateTimeConverter::getDateTimeNow();
//        $telemetri->save();
    }
}
