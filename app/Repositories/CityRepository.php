<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends CrudRepository
{

    public function model()
    {
        return City::class;
    }

    /**
     * @param $province_id
     * @return mixed
     */
    public function findByProvince($province_id)
    {
        return City::where('province_id', $province_id)->get(['id','province_id','city_name']);
    }
}
