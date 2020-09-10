<?php

namespace Permaxis\LaravelCore\app\tests\Utils\Unit;

use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilsEnvTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetEnv()
    {
        $utilsEnv = App::make('Utils\UtilsEnv');

        $env = $utilsEnv->getEnv();

        $this->assertNotNull($env,'APPLICATION_ENV does not exists !');
    }
}
