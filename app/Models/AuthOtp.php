<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthOtp extends Model
{
    use HasFactory;
    use UUID;
    protected $guarded=[];
    public $timestamps = false;
}
