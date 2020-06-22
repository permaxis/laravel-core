<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 01/06/2020
 * Time: 13:02
 */

namespace Permaxis\Core\App\Services\Entities;

trait Migration
{
    public static function getPrefixTable()
    {
        if (property_exists(self::class, 'prefix_table'))
        {
            return (config()->has(self::$prefix_table))? config()->get(self::$prefix_table) : '';
        }
        else
        {
            return '';
        }
    }
}