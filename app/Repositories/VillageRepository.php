<?php

namespace App\Repositories;

use App\Models\Village;

class VillageRepository extends CrudRepository
{

    public function model()
    {
        return Village::class;
    }

    /**
     * @param $districtId
     * @return mixed
     */
    public function findByDistrictId($districtId)
    {
        return Village::where('district_id', $districtId)->get(['id','district_id','postal_code','village_name']);
    }
}
