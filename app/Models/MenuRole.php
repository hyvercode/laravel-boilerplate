<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuRole extends Model
{
    use UUID;
    use SoftDeletes;
    public $guarded = [];
    public $timestamps = false;
}
