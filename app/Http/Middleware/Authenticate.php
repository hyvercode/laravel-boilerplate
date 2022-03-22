<?php

namespace App\Http\Middleware;

use App\Helpers\Constants;
use App\Traits\BusinessException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return string|void|null
     * @throws BusinessException
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            throw new BusinessException(Constants::HTTP_CODE_409, 'Invalid username or password!', Constants::ERROR_CODE_9000);
        }
    }
}
