<?php
/**
 * Created by PhpStorm.
 * User: abdel
 * Date: 13/08/2019
 * Time: 16:04
 */

namespace Permaxis\Core\App\Services\Utils;

use Ramsey\Uuid\Uuid;

class UtilsUuid
{
    static function uuid()
    {
        return Uuid::uuid1() ;
    }
}