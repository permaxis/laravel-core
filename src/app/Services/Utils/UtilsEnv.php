<?php
/**
 * Created by Permaxis.
 * User: mk2
 * Date: 18/12/2018
 * Time: 15:24
 */

namespace Permaxis\LaravelCore\app\Services\Utils;



class UtilsEnv
{
    /**
     * @var \ND\Bundle\EdileadBundle\Util\UtilsString
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     * @var string
     */
    private $env;

    /**
     * class constructor
     *
     * @param void
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Method to create instance of class
     * created if does not exists.
     *
     * @param void
     * @return \ND\Bundle\EdileadBundle\Util\UtilsString
     */
    public static function getInstance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new UtilsEnv();
        }

        return self::$_instance;
    }


    public function getEnv()
    {
        $env = null;

        // default env
        if (!empty($this->env))
        {
            return $this->env ;
        }

        if (defined('APPLICATION_ENV'))
        {
            $env = constant('APPLICATION_ENV');
            $this->setEnv($env);
        }
        elseif (getenv('APPLICATION_ENV'))
        {
            $env = getenv('APPLICATION_ENV');
            if (!defined('APPLICATION_ENV'))
            {
                define('APPLICATION_ENV',$env);
                $this->setEnv($env);
            }
        }

        if (!defined('APPLICATION_ENV'))
        {
            define('APPLICATION_ENV',$env);
            $this->setEnv($env);
        }

        return $env;
    }

    public function setEnv($env)
    {
        $this->env = $env;
    }

}