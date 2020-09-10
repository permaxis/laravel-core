<?php

namespace Permaxis\LaravelCore\app\Services\Utils;

class UtilsRequest
{
    static function trimInput(&$input)
    {
        array_walk_recursive($input, function(&$value) {
            if (!is_object($value) && !UtilsString::getInstance()->isNullOrEmpty($value))
            {
                $value = trim($value);
            }
        });
    }

}
