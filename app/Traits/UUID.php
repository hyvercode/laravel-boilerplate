<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 23/03/22
 * Time: 18.11
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Traits;

use Illuminate\Support\Str;

trait UUID
{
    protected static function boot()
    {
        // Boot other traits on the Model
        parent::boot();

        /**
         * Listen for the creating event on the user model.
         * Sets the 'id' to a UUID using Str::uuid() on the instance being created
         */
        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    /**
     * @return false
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @return string
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function getKeyType()
    {
        return 'string';
    }
}
