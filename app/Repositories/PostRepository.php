<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 08/04/22
 * Time: 10.32
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Repositories;

use App\Models\Post;

class PostRepository extends CrudRepository
{

    public function model()
    {
        return Post::class;
    }
}
