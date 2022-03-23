<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuList extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $guarded = [];
    public $timestamps = false;
}
