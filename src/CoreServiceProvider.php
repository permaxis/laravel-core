<?php
namespace Permaxis\Laravel\Core;
use Illuminate\Support\ServiceProvider;
use Permaxis\Laravel\Core\App\Services\Blade\BladeMacroServiceProvider;

/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 16/08/2019
 * Time: 16:16
 */
class CoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        BladeMacroServiceProvider::boot();


    }
    public function register()
    {
        $this->registerUtils();

    }

    private function registerUtils()
    {
        $this->app->singleton('Utils\UtilsEnv', function ($app) {
            return App\Services\Utils\UtilsEnv::getInstance();
        });

        $this->app->singleton('Utils\UtilsFile', function ($app) {
            return App\Services\Utils\UtilsFile::getInstance();
        });

    }

}