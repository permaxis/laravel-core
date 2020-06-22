<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 13/08/2019
 * Time: 16:04
 */

namespace Permaxis\Core\App\Services\Entities;

use Ramsey\Uuid\Uuid;

class UuidManager
{
    static function uuid()
    {
        return Uuid::uuid1() ;
    }
}