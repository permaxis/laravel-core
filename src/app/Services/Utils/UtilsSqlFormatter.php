<?php

namespace Permaxis\Core\App\Services\Utils;


use Doctrine\ORM\Query\Parameter;

class UtilsSqlFormatter extends \SqlFormatter
{
    /**
     * @var \ND\Bundle\EdileadBundle\Util\UtilSqlFormatter
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     * class constructor
     *
     * @param void
     * @return void
     */
    private function __construct() {
    }

    /**
     * Method to create instance of class
     * created if does not exists.
     *
     * @param void
     * @return \ND\Bundle\EdileadBundle\Util\UtilSqlFormatter
     */
    public static function getInstance() {

        if(is_null(self::$_instance)) {
            self::$_instance = new UtilsSqlFormatter();
        }

        return self::$_instance;
    }

    public function getSql($sql, $bindParams = array())
    {
        $param_values = '';
        $col_names = '';
        foreach ($bindParams as $key => $value)
        {

            if (is_numeric($value) )
            {
                $sql = str_replace(':'.$key, $value ,$sql);
            }
            elseif (is_string($value))
            {
                $sql = str_replace(':'.$key,'\''.$value.'\'',$sql);
            }
            elseif (is_object($value) && $value instanceof Parameter)
            {

                $value = $value->getValue();
                if (is_numeric($value))
                {
                    $sql = preg_replace('/\?/', $value , $sql , 1);
                }
                elseif (is_string($value))
                {
                    $sql = preg_replace('/\?/', '\''.$value.'\'' , $sql , 1);
                }
                elseif (is_object($value))
                {
                    if (method_exists($value,'getId'))
                    {
                        $value = $value->getId();
                        $sql = preg_replace('/\?/', '\''.$value.'\'' , $sql , 1);
                    }
                    elseif ($value instanceof \DateTime)
                    {
                        $value = $value->format('Y-m-d H:i:s');
                        $sql = preg_replace('/\?/', '\''.$value.'\'' , $sql , 1);
                    }
                }
            }
        }

        $sqlInfo = $sql.$param_values.$col_names;

        return $sqlInfo;
    }

    /**
     *
     */
    public function sqlInfo($name = null , $sql, $bindParams = array(), $options = array())
    {
        $result = '';

        if (isset($options['cr']) && !empty($options['cr']))
        {
            $result = str_repeat("\n",$options['cr']);
        }

        if (!empty($name))
        {
            $result .= "\n#----".$name."------#\n";
        }

        $result .= "\n#----bindParams----\n". print_r($bindParams, true) ."------#\n";

        $sql = $this->getSql($sql, $bindParams);

        $result .= parent::format($sql);

        if (isset($options['html']) && $options['html'])
        {
            $result = str_replace("\n","<br/>",$result);
        }

        return $result;
    }


}


