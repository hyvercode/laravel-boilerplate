<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuList extends Model
{
    use UUID;
    use HasFactory;
    use SoftDeletes;
    public $guarded = [];
    public $timestamps = false;
    protected $table="menu_list";
}
