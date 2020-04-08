<?php

namespace olckerstech\packager\src;

use Illuminate\Support\ServiceProvider;
use olckerstech\packager\src\Commands\PackageMakeCommand;
use olckerstech\packager\src\Commands\PackageScaffoldMakeCommand;
use olckerstech\packager\src\Commands\ScaffoldMakeCommand;

class PackagerServiceProvider extends ServiceProvider
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
        //** START: Local environment only
        if($this->app->isLocal() && $this->app->runningInConsole()) {
            $this->commands([
                PackageMakeCommand::class, //Creates a new package with stubs
                PackageScaffoldMakeCommand::class, //Creates a new entity scaffold within a package
                ScaffoldMakeCommand::class, //Creates a new entity scaffold within the normal app
            ]);

            //Publish config file
            $this->publishes([
                __DIR__.'/../config/packager.php' => config_path('packager.php')
            ], 'config');

            //Publish stubs
            $this->publishes([
                __DIR__.'/../resources/stubs/' => '/stubs'
            ], 'stubs');

        }
        //** END: Local environment only
    }
}
