<?php

namespace olckerstech\core\src;

use Illuminate\Support\ServiceProvider;
use olckerstech\core\src\Commands\CoreDeleteCommand;
use olckerstech\core\src\Commands\CoreInitializeCommand;
use olckerstech\core\src\Commands\CoreInstallCommand;
use olckerstech\core\src\Commands\ModelMakeCommand;
use olckerstech\core\src\Commands\PackageMakeCommand;
use olckerstech\core\src\Commands\PackageScaffoldMakeCommand;
use olckerstech\core\src\Commands\RepositoryInterfaceMakeCommand;
use olckerstech\core\src\Commands\RepositoryMakeCommand;
use olckerstech\core\src\Commands\ScaffoldMakeCommand;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Extend ModelMakeCommand to accommodate namespace change
        $this->app->extend('command.model.make', function ($command, $app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //** START: Any environment
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelMakeCommand::class, //Extends all model make commands to accommodate namespace change
            ]);
        }

        //Load translation files
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'core');

        //Load factories
        $this->loadFactoriesFrom(__DIR__.'/../database/factories');

        //Load Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/core.php');

        //Load Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        //** END: Any environment

        //** START: Local environment only
        if($this->app->isLocal() && $this->app->runningInConsole()) {
            $this->commands([
                CoreInstallCommand::class, //Controls the installation for the core components
                CoreDeleteCommand::class, //Deletes files during the installation process
                CoreInitializeCommand::class, //Initializes the Core Package before install
                PackageMakeCommand::class, //Creates a new package with stubs
                PackageScaffoldMakeCommand::class, //Creates a new entity scaffold within a package
                ScaffoldMakeCommand::class, //Creates a new entity scaffold within the normal app
                RepositoryMakeCommand::class, //Creates a new repository
                RepositoryInterfaceMakeCommand::class //Creates a new repository interface
            ]);

            //Publish config file
            $this->publishes([
                __DIR__.'/../config/core.php' => config_path('core.php')
            ], 'config');

            //Publish ModelMakeCommand
            $this->publishes([
                __DIR__.'/../src/Commands/ModelMakeCommand.php' => '/app/Console/Commands/ModelMakeCommand.php'
            ], 'model');

            //Publish install config file
            $this->publishes([
                __DIR__.'/../config/tmp_core_install.php' => config_path('tmp_core_install.php')
            ], 'install');

            //Publish translation files
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/core'),
            ], 'translations');

            //Publish Factories
            $this->publishes([
                __DIR__ . '/../database/factories/UserFactory.php' => '/database/factories/UserFactory.php'
            ], 'factories');

            //Publish Routes
            $this->publishes([
                __DIR__ . '/../routes/core.php' => '/routes/core.php'
            ], 'routes');

            //Publish User Model
            $this->publishes([
                __DIR__ . '/../resources/stubs/UserModelStub.php' => 'app/Models/User.php'
            ], 'user');

            //Publish default env file
            $this->publishes([
                __DIR__ . '/../resources/environment/.env.example' => '.env'
            ], 'env');

            //Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'migrations');

            //Publish stubs
            $this->publishes([
                __DIR__.'/../resources/stubs/' => '/stubs'
            ], 'stubs');

        }
        //** END: Local environment only
    }
}
