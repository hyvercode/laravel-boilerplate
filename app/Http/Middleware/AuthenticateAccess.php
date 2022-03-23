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

    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
