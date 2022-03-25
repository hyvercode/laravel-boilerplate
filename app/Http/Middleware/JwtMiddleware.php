<?php

namespace App\Http\Middleware;

use App\Helpers\Constants;
use App\Traits\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws BusinessException
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $token = $request->headers->get('Authorization');
            if (empty($user->personal_access_token) || $token === 'Bearer ' . $user->personal_access_token) {
                throw new BusinessException(Constants::HTTP_CODE_403, 'Token is Invalid', Constants::HTTP_CODE_403);
            }
            if (!$user->active) {
                throw new BusinessException(Constants::HTTP_CODE_409, 'Your Account not active', Constants::HTTP_CODE_409);
            }

            if ($user->email_verified_at) {
                throw new BusinessException(Constants::HTTP_CODE_409, 'Your Account not active', Constants::HTTP_CODE_409);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                throw new BusinessException(Constants::HTTP_CODE_403, 'Token is Invalid', Constants::HTTP_CODE_403);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                throw new BusinessException(Constants::HTTP_CODE_403, 'Token is Expired', Constants::HTTP_CODE_401);
            } else {
                throw new BusinessException(Constants::HTTP_CODE_403, 'Authorization Token not found ', Constants::HTTP_CODE_403);
            }
        }
        return $next($request);
    }
}
