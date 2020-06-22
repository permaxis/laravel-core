<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 13/08/2019
 * Time: 16:04
 */

namespace Permaxis\Core\App\Services\Utils;

use Ramsey\Uuid\Uuid;

class UtilsUuid
{
    static function uuid($version = 4)
    {
        switch ($version)
        {
            case(1):
                $uuid = Uuid::uuid1() ;
                break;
            case(4):
                $uuid = Uuid::uuid4() ;
                break;
            default:
                $uuid = Uuid::uuid4();
                break;
        }

        return $uuid;
    }

    static function verify($uuid, $version = 4)
    {
        switch ($version)
        {
            case(4):
                $uuid_v4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';
                if (!preg_match($uuid_v4, $uuid))
                {
                    return false;
                }
        }

        return true;
    }
}