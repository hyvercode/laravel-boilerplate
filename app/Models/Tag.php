<?php

namespace App\Models;

use App\Models\Post;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use UUID;
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
