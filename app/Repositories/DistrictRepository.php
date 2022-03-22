<?php

namespace App\Repositories;

use App\Models\District;

class DistrictRepository extends CrudRepository
{

    public function model()
    {
        return District::class;
    }

    /**
     * @param $city_id
     * @return IndonesiaDistrictsRepository[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByCityId($city_id)
    {
        return District::where('city_id', $city_id)->get(['id','city_id','district_name']);
    }
}
