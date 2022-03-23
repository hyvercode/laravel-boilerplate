<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuRole extends Model
{
    use SoftDeletes;
    public $guarded = [];
    public $timestamps = false;
}
