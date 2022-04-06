<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menus extends Model
{
    use UUID;
    use SoftDeletes;
    protected $guarded = [];
    public $timestamps = false;
    protected $table="menus";
}
