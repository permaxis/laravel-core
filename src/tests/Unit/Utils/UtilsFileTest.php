<?php

namespace Permaxis\LaravelCore\tests\Unit\Utils;

use Illuminate\Support\Facades\App;
use Permaxis\LaravelCore\app\Services\Tests\AbstractTest;

class UtilsFileTest extends AbstractTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetParametersFromFile()
    {
        $options = $this->getOptions();

        if (!isset($options['file']) || empty($options['file']))
        {
            throw new \Exception('file not set!');
        }

        $file = $options['file'];

        $utilsFile = App::make('Utils\UtilsFile');

        $result = $utilsFile->getParametersFromFile($file);

        $this->assertArrayHasKey('parameters',$result,'File does not exist or is empty !');
    }
}
