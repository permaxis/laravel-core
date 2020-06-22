<?php
/**
 * Created by Permaxis.
 * User: mk2
 * Date: 18/12/2018
 * Time: 15:24
 */

namespace Permaxis\Core\App\Services\Utils;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Yaml\Yaml;

class UtilsFile
{
    /**
     * @var \ND\Bundle\EdileadBundle\Util\UtilsFile
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
    private function __construct()
    {
    }

    /**
     * Method to create instance of class
     * created if does not exists.
     *
     * @param void
     * @return \ND\Bundle\EdileadBundle\Util\UtilsFile
     */
    public static function getInstance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new UtilsFile();
        }

        return self::$_instance;
    }

    public function getParametersFromFile($filename, $path = null, $options = array())
    {
        if (empty($path) && defined('APP_CONFIG_DIR'))
        {
            $configDirectories = APP_CONFIG_DIR;

        }
        else
        {
            $configDirectories = [$path];
        }

        if (!empty($path))
        {
            $fileLocator = new FileLocator($configDirectories);
            $file = $fileLocator->locate($filename, null, true);
        }
        elseif (file_exists($filename))
        {
            $file = $filename;
        }

        if (empty($file))
        {
            throw new \Exception('File '.$filename.' not found !');
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext)
        {
            case('yml'):
                $parameters = Yaml::parse(file_get_contents($file));
                if (isset($parameters['imports']) && !empty($parameters['imports']))
                {
                    foreach ($parameters['imports'] as $import)
                    {
                        //load import file and merge
                        //only one level of import
                        if (!isset($import['resource']) || empty($import['resource']))
                        {
                            continue;
                        }

                        $filename = $import['resource'];

                        $file = $fileLocator->locate($filename, null, true);
                        $parametersImport = Yaml::parse(file_get_contents($file));
                        if (!empty($parametersImport))
                        {
                            $parameters = array_merge_recursive ( $parameters, $parametersImport   );
                        }
                    }
                }
                if ($filename == 'web_logging.yml')
                {
                    print_r($parameters);
                }

                if (isset($options['recursive_import']) && $options['recursive_import'])
                {
                    $this->recursiveImport($parameters);
                }

                break;
            case('php'):
                ob_start();
                return include $file;
                $parameters = ob_get_clean();
                break;
        }


        return $parameters;
    }

    public function recursiveImport(&$params = array())
    {
        if (is_array($params))
        {
            foreach ($params as $k1 => $v1)
            {
                if (is_array($v1))
                {
                    if ($k1 === 'imports')
                    {
                        foreach ($v1 as $k2 => $v2)
                        {
                            $v1 = array_merge_recursive(UtilsFile::getInstance()->getParametersFromFile($v2['resource'], __DIR__.'/../Resources/config/parameters/export_data_from_query'));
                        }
                    }

                    $this->recursiveImport($v1);
                    $params[$k1] = $v1;
                }
            }
        }
    }
}