<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 21/05/2020
 * Time: 18:03
 */
if (! function_exists('getLoggerLevel')) {

    function getLoggerLevelLabel($key)
    {
        return \Permaxis\Core\App\Entities\Level::getLevelLabel($key);
    }

    function getLoggerLevelLabels($levels = [])
    {
        return \Permaxis\Core\App\Entities\Level::getLevelLabels($levels);
    }
}

