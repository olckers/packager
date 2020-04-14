<?php

namespace DummyPackageNamespace;

use Illuminate\Support\ServiceProvider;

class DummyClass extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app['config']->get('packager') === null) {
            $this->app['config']->set('packager', require __DIR__ . '/../config/packager.php');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // ModelMakeCommand::class, //Extends all model make commands to accommodate namespace change
            ]);
        }

        //Load translation files
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'core');

        //Load factories
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');

        //Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/core.php');

        //Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        //** START: Local environment only
        if ($this->app->isLocal() && $this->app->runningInConsole()) {
            $this->commands([
                //RepositoryMakeCommand::class, //Creates a new repository
            ]);

            //Publish config file
            $this->publishes([
                __DIR__ . '/../config/core.php' => config_path('core.php')
            ], 'config');

            //Publish translation files
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/core'),
            ], 'translations');

            //Publish Factories
            //$this->publishes([
            //    __DIR__ . '/../database/factories/UserFactory.php' => '/database/factories/UserFactory.php'
            //], 'factories');

            //Publish Routes
            //$this->publishes([
            //    __DIR__ . '/../routes/core.php' => '/routes/core.php'
            //], 'routes');

            //Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations')
            ], 'migrations');
        }
        //** END: Local environment only
    }
}
