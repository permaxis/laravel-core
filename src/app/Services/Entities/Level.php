<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 14/05/2020
 * Time: 18:19
 */

namespace Permaxis\LaravelCore\app\Entities;

class Level
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;


    static $levels = [
        self::DEBUG,
        self::INFO,
        self::NOTICE,
        self::WARNING,
        self::ERROR,
        self::CRITICAL,
        self::ALERT,
        self::EMERGENCY,
    ];

    static function getLevelLabels($levels = [])
    {
        $results = [];
        $tab_levels = self::$levels;
        if (!empty($levels))
        {
            $tab_levels = array_intersect(self::$levels,$levels);
        }
        foreach ($tab_levels as $level)
        {
            switch ($level)
            {
                case(self::DEBUG);
                    $level_value = 'debug';
                    break;
                case(self::INFO);
                    $level_value = 'info';
                    break;
                case(self::NOTICE);
                    $level_value = 'notice';
                    break;
                case(self::WARNING);
                    $level_value = 'warning';
                    break;
                case(self::ERROR);
                    $level_value = 'error';
                    break;
                case(self::CRITICAL);
                    $level_value = 'critical';
                    break;
                case(self::ALERT);
                    $level_value = 'alert';
                    break;
                case(self::EMERGENCY);
                    $level_value = 'emergency';
                    break;
                default:
                    $level_value = $level;
                    break;
            }

            $results[$level] = $level_value;

        }


        return $results;
    }

    static function getLevelLabel($key)
    {
        $levels = self::getLevelLabels();
        if (in_array($key, array_keys($levels)))
        {
            return $levels[$key];
        }
        return null;
    }

}