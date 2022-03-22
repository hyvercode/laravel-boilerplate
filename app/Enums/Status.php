<?php
/**
 * Created by PhpStorm.
 * User: mohirwanh@gmail.com
 * Date: 08/03/22
 * Time: 10.42
 * @author mohirwanh <mohirwanh@gmail.com>
 */

namespace App\Enums;

use MyCLabs\Enum\Enum;

final class Status extends Enum
{
    const new = "NEW";
    const approved = 'APPROVED';
    const rejected = 'REJECTED';
}

