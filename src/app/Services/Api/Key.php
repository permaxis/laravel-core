<?php
/**
 * Created by PhpStorm.
 * User: dakin
 * Date: 25/02/2020
 * Time: 17:53
 */

namespace Permaxis\Core\App\Services\Api;


use Illuminate\Support\Facades\File;

class Key
{
    public static function registerPublicKey($config_key, $name = 'api_public_key' )
    {
        if (config()->has($config_key))
        {
            $filename = config()->get($config_key);
            if (File::isFile(storage_path($filename))) {
                $filename = storage_path($filename);
            }
            if (File::isFile($filename))
            {
                config()->set($name, File::get($filename));
            }
        }
    }
}