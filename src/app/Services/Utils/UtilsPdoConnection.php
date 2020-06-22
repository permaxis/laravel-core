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
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class UtilsPdoConnection
{

    /**
     * class constructor
     *
     * @param void
     * @return void
     */
    public function __construct($params = array())
    {
        $this->connection = $this->getDatabaseConnection($params);
    }

    public function getDatabaseConnection($params)
    {

        try {
            $databaseConnection = new \PDO('mysql:host='.$params['database_host'].';dbname='.$params['database_name'], $params['database_username'], $params['database_password']);
        } catch (\PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }

        $databaseConnection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        return $databaseConnection;

    }

    public function getDatabaseName()
    {
        return $this->connection->query('SELECT database() as database_name')->fetchColumn();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}