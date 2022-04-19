<?php

namespace App\Models;

use App\Models\Post;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use UUID;
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($category) {
            $category->posts()->delete();
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
