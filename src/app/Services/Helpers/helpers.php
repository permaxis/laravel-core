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
        return \Permaxis\LaravelCore\app\Services\Entities\Level::getLevelLabel($key);
    }

    function getLoggerLevelLabels($levels = [])
    {
        return \Permaxis\LaravelCore\app\Services\Entities\Level::getLevelLabels($levels);
    }
}

