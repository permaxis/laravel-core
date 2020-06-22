<?php

namespace Permaxis\Core\App\Services\Utils;

class UtilsArray
{

    static function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = static::array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }
    
    /**
     * @todo remove if want to, no need any more
     * put $put in $array at poistion $postion
     */    
    static function array_put_at(array &$array, $put, $position)
    {
        $array = array_slice($array, 0, $position, true) +
                $put +
                array_slice($array, $position, count($array) - $position, true)
        ;
    }

    static function objectToArray($tab = array())
    {
        $array = json_decode(json_encode($tab), true);

        return $array;
    }

}
