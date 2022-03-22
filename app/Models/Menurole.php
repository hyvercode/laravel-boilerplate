<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menurole extends Model
{
    public $table = "menu_role";
    public $guarded = [''];
    public $timestamps = false;
    use SoftDeletes;

}
