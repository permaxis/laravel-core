<?php
namespace Permaxis\Core;
use Illuminate\Support\ServiceProvider;
/**
 * Created by PhpStorm.
 * User: dakin
 * Date: 16/08/2019
 * Time: 16:16
 */
class CoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
    public function register()
    {
    }
}