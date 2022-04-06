<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuListController;
use App\Http\Controllers\MenuRoleController;
use App\Http\Controllers\ProspectDebtorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * opt-service
 */
Route::get('/v1/otp/{account}', [AuthController::class, 'otp']);
Route::post('/v1/otp/verified', [AuthController::class, 'verifikasiOTP']);
Route::post('/v1/otp/changePassword', [AuthController::class, 'changePassword']);

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

Route::group(['prefix' => 'v1'], function () {
    /**
     * auth
     */
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/password/forgot', [AuthController::class, 'login']);
        Route::post('/password/otp', [AuthController::class, 'login']);
        Route::post('/password/validate', [AuthController::class, 'login']);
        Route::post('/password/reset', [AuthController::class, 'login']);
        Route::delete('/destroy/{id}', [AuthController::class, 'destroy']);
    });

    Route::group(['middleware' => ['jwt.verify']], function () {
        /**
         * auth
         */
        Route::group(['prefix' => 'auth'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });

        /**
         * user
         */
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'all']);
            Route::get('/paginate', [UserController::class, 'paginate']);
            Route::post('/create', [UserController::class, 'create']);
            Route::post('/update/{id}', [UserController::class, 'update']);
            Route::get('/show/{id}', [UserController::class, 'show']);
            Route::delete('/delete/{id}', [UserController::class, 'delete']);
            Route::get('/profile', [UserController::class, 'profile']);
        });

        /**
         * menu-service
         */
        Route::group(['prefix' => 'menus'], function () {
            Route::get('/', [MenuController::class, 'all']);
            Route::get('/paginate', [MenuController::class, 'paginate']);
            Route::post('/create', [MenuController::class, 'create']);
            Route::post('/update/{id}', [MenuController::class, 'update']);
            Route::get('/show/{id}', [MenuController::class, 'show']);
            Route::delete('/delete/{id}', [MenuController::class, 'delete']);
            Route::get('/menu', [MenuController::class, 'getMenu']);
        });

        /**
         * menu-list-service
         */
        Route::group(['prefix' => 'menu-list'], function () {
            Route::get('/', [MenuListController::class, 'all']);
            Route::get('/paginate', [MenuListController::class, 'paginate']);
            Route::post('/create', [MenuListController::class, 'create']);
            Route::post('/update/{id}', [MenuListController::class, 'update']);
            Route::get('/show/{id}', [MenuListController::class, 'show']);
            Route::delete('/delete/{id}', [MenuListController::class, 'delete']);
        });

        /**
         * menu-role-service
         */
        Route::group(['prefix' => 'menu-role'], function () {
            Route::get('/', [MenuRoleController::class, 'all']);
            Route::get('/paginate', [MenuRoleController::class, 'paginate']);
            Route::post('/create', [MenuRoleController::class, 'create']);
            Route::post('/update/{id}', [MenuRoleController::class, 'update']);
            Route::get('/show/{id}', [MenuRoleController::class, 'show']);
            Route::delete('/delete/{id}', [MenuRoleController::class, 'delete']);
        });

        //roles-service
        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', [RoleController::class, 'all']);
            Route::get('/paginate', [RoleController::class, 'paginate']);
        });

        //inbox-service
        Route::group(['prefix' => 'inboxs'], function () {
            Route::get('/', [InboxController::class, 'all']);
            Route::get('/paginate', [InboxController::class, 'paginate']);
            Route::post('/create', [InboxController::class, 'create']);
            Route::post('/read/{id}', [InboxController::class, 'read']);
            Route::get('/show/{id}', [InboxController::class, 'show']);
            Route::delete('/delete/{id}', [InboxController::class, 'delete']);
            Route::get('/count', [InboxController::class, 'count']);
        });

        //company-service
        Route::group(['prefix' => 'company'], function () {
            Route::get('/', [CompanyController::class, 'all']);
            Route::get('/paginate', [CompanyController::class, 'paginate']);
            Route::post('/create', [CompanyController::class, 'create']);
            Route::post('/update/{id}', [CompanyController::class, 'update']);
            Route::get('/show/{id}', [CompanyController::class, 'show']);
            Route::delete('/delete/{id}', [CompanyController::class, 'delete']);
        });

        //inboxs-service
        Route::group(['prefix' => 'inboxs'], function () {
            Route::get('/', [InboxController::class, 'all']);
            Route::get('/paginate', [InboxController::class, 'paginate']);
            Route::post('/create', [InboxController::class, 'create']);
            Route::post('/update/{id}', [InboxController::class, 'update']);
            Route::get('/show/{id}', [InboxController::class, 'show']);
            Route::delete('/delete/{id}', [InboxController::class, 'delete']);
            Route::get('/count', [InboxController::class, 'count']);
        });

        /**
         * Global
         */
        Route::group(['prefix' => 'global'], function () {
            //province-service
            Route::group(['prefix' => 'province'], function () {
                Route::get('/', ['uses' => 'ProvinceController@all']);
                Route::get('/paginate', ['uses' => 'ProvinceController@paginate']);
                Route::post('/create', ['uses' => 'ProvinceController@create']);
                Route::post('/update/{id}', ['uses' => 'ProvinceController@update']);
                Route::get('/show/{id}', ['uses' => 'ProvinceController@show']);
                Route::delete('/delete/{id}', ['uses' => 'ProvinceController@delete']);
            });

            //city-service
            Route::group(['prefix' => 'city'], function () {
                Route::get('/', ['uses' => 'CityController@all']);
                Route::get('/paginate', ['uses' => 'CityController@paginate']);
                Route::post('/create', ['uses' => 'CityController@create']);
                Route::post('/update/{id}', ['uses' => 'CityController@update']);
                Route::get('/show/{id}', ['uses' => 'CityController@show']);
                Route::delete('/delete/{id}', ['uses' => 'CityController@delete']);
                Route::get('/province/{id}', ['uses' => 'CityController@getByProvinceId']);
            });

            //district-service
            Route::group(['prefix' => 'district'], function () {
                Route::get('/', ['uses' => 'DistrictController@all']);
                Route::get('/paginate', ['uses' => 'DistrictController@paginate']);
                Route::post('/create', ['uses' => 'DistrictController@create']);
                Route::post('/update/{id}', ['uses' => 'DistrictController@update']);
                Route::get('/show/{id}', ['uses' => 'DistrictController@show']);
                Route::delete('/delete/{id}', ['uses' => 'DistrictController@delete']);
                Route::get('/city/{id}', ['uses' => 'DistrictController@getByCity']);
            });

            //village-service
            Route::group(['prefix' => 'village'], function () {
                Route::get('/', ['uses' => 'VillageController@all']);
                Route::get('/paginate', ['uses' => 'VillageController@paginate']);
                Route::post('/create', ['uses' => 'VillageController@create']);
                Route::post('/update/{id}', ['uses' => 'VillageController@update']);
                Route::get('/show/{id}', ['uses' => 'VillageController@show']);
                Route::delete('/delete/{id}', ['uses' => 'VillageController@delete']);
                Route::get('/district/{id}', ['uses' => 'VillageController@getByDistrict']);
            });

            //business-service
            Route::group(['prefix' => 'business'], function () {
                Route::get('/', ['uses' => 'BusinessController@all']);
                Route::get('/paginate', ['uses' => 'BusinessController@paginate']);
                Route::post('/create', ['uses' => 'BusinessController@create']);
                Route::post('/update/{id}', ['uses' => 'BusinessController@update']);
                Route::get('/show/{id}', ['uses' => 'BusinessController@show']);
                Route::delete('/delete/{id}', ['uses' => 'BusinessController@delete']);
            });
        });

    });
});



