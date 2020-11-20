<?php

namespace Qihucms\Live;

use Illuminate\Support\ServiceProvider;
use Qihucms\Live\Console\Install;
use Qihucms\Live\Console\Uninstall;
use Qihucms\Live\Console\Upgrade;

class QihuLiveServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Install::class,
            Uninstall::class,
            Upgrade::class
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views','qihu-live');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','qihu-live');

        $this->publishes([
            __DIR__.'/../resources/asset' => public_path('asset/live'),
        ], 'public');
    }
}
