<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 17/03/22
 * Time: 14.52
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Repositories;

use App\Models\ProspectDebtor;

class ProspectDebtorRepository extends CrudRepository
{

    public function model()
    {
        return ProspectDebtor::class;
    }
}
