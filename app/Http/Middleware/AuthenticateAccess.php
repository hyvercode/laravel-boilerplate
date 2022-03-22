<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 17/03/22
 * Time: 15.04
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Http\Middleware;

use App\Helpers\Constants;
use App\Utils\BusinessException;
use Closure;

class AuthenticateAccess
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws BusinessException
     */
    public function handle($request, Closure $next)
    {
        $allowedSecrets = explode(',', env('ALLOWED_SECRETS'));
        if (in_array($request->header('x-api-key'), $allowedSecrets)) {
            return $next($request);
        }
        return response()->json(['code' => Constants::HTTP_CODE_401, 'message' => Constants::ERROR_MESSAGE_401 . ' Invalid API-KEY'], 401);
    }
}
