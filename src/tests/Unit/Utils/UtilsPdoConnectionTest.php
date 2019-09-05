<?php

namespace Permaxis\Core\App\Tests\Utils\Unit;

use Illuminate\Support\Facades\App;
use Permaxis\Core\App\Services\Utils\UtilsPdoConnection;
use Permaxis\Core\App\Tests\AbstractTest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilsFileTest extends AbstractTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDatabaseConnection()
    {
        $options = $this->getOptions();

        try{
            $utilsPdo = new UtilsPdoConnection(array(
                'database_host' => $options['database_host'],
                'database_name' => $options['database_name'],
                'database_username' => $options['database_username'],
                'database_password' => $options['database_password']

            ));

            $this->assertTrue(true, 'Pdo connection exists !');

        }

        catch(\PDOException $ex){
            $this->assertTrue(false, 'Pdo connection does not exists !');
        }


    }
}
