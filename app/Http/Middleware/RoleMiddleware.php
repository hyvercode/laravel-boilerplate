<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 08/03/22
 * Time: 18.12
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Http\Middleware;

use App\Helpers\Constants;
use Closure;

class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        if ($guard != null)
            if (in_array($guard, explode(",", auth()->user()->menu_roles))) {
                return $next($request);
            }
        return response()->json(['code' => Constants::HTTP_CODE_403, 'message' => "You don't have access"], 200);
    }
}
