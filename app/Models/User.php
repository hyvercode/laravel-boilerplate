<?php

namespace App\Models;

use App\Helpers\CommonUtil;
use App\Traits\UUID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use UUID;
    use Notifiable;
    use SoftDeletes;
    use HasFactory;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "name" => $this->username,
            "menu_roles" => $this->menu_roles,
            "avatar" => $this->avatar,
            "login_id"=>CommonUtil::generateUUID()
        ];
    }

    protected $attributes = [
        'menu_roles' => 'user',
    ];
}
