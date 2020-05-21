<?php

namespace olckerstech\packager;

use Illuminate\Support\ServiceProvider;
use olckerstech\packager\Commands\PackagerChannelMakeCommand;
use olckerstech\packager\Commands\PackagerCommandMakeCommand;
use olckerstech\packager\Commands\PackagerComponentMakeCommand;
use olckerstech\packager\Commands\PackagerControllerMakeCommand;
use olckerstech\packager\Commands\PackagerFactoryMakeCommand;
use olckerstech\packager\Commands\PackagerMakeCommand;
use olckerstech\packager\Commands\PackagerEventMakeCommand;
use olckerstech\packager\Commands\PackagerExceptionMakeCommand;
use olckerstech\packager\Commands\PackagerJobMakeCommand;
use olckerstech\packager\Commands\PackagerListenerMakeCommand;
use olckerstech\packager\Commands\PackagerMailMakeCommand;
use olckerstech\packager\Commands\PackagerMiddlewareMakeCommand;
use olckerstech\packager\Commands\PackagerMigrationMakeCommand;
use olckerstech\packager\Commands\PackagerModelMakeCommand;
use olckerstech\packager\Commands\PackagerNotificationMakeCommand;
use olckerstech\packager\Commands\PackagerObserverMakeCommand;
use olckerstech\packager\Commands\PackagerPolicyMakeCommand;
use olckerstech\packager\Commands\PackagerProviderMakeCommand;
use olckerstech\packager\Commands\PackagerRepositoryInterfaceMakeCommand;
use olckerstech\packager\Commands\PackagerRepositoryMakeCommand;
use olckerstech\packager\Commands\PackagerRequestMakeCommand;
use olckerstech\packager\Commands\PackagerResourceMakeCommand;
use olckerstech\packager\Commands\PackagerScaffoldMakeCommand;
use olckerstech\packager\Commands\PackagerSeederMakeCommand;
use olckerstech\packager\Commands\PackagerTestMakeCommand;
use olckerstech\packager\Commands\RepositoryInterfaceMakeCommand;
use olckerstech\packager\Commands\RepositoryMakeCommand;
use olckerstech\packager\Commands\ScaffoldMakeCommand;

class PackagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app['config']->get('packager') === null) {
            $this->app['config']->set('packager', require __DIR__.'/../config/packager.php');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //** START: Local environment only
        if ($this->app->isLocal() && $this->app->runningInConsole()) {
            $this->commands([
                PackagerMakeCommand::class, //Creates a new package with stubs
                PackagerScaffoldMakeCommand::class, //Creates a new entity scaffold within a package
                ScaffoldMakeCommand::class, //Creates a new entity scaffold within the normal app
                PackagerModelMakeCommand::class, // Creates a new Eloquent user model within the desired package
                PackagerProviderMakeCommand::class, //Creates a new ServiceProvider class within a package
                PackagerResourceMakeCommand::class, //Creates a new Resource class within a package
                PackagerRequestMakeCommand::class, //Creates a new Request class within a package
                PackagerPolicyMakeCommand::class, //Creates a new Policy class within a package
                PackagerObserverMakeCommand::class, //Creates a new Observer class within a package
                PackagerNotificationMakeCommand::class, //Creates a new Notification class within a package
                PackagerMailMakeCommand::class, //Creates a new Mail for a package
                PackagerListenerMakeCommand::class, //Creates a new Listener for a package
                PackagerJobMakeCommand::class, //Creates a new Job for a package
                PackagerExceptionMakeCommand::class, //Creates a new Exception for a package
                PackagerEventMakeCommand::class, //Creates a new Event for a package
                PackagerComponentMakeCommand::class, //Creates new component for a package
                PackagerChannelMakeCommand::class, //Creates new channel for a package
                PackagerTestMakeCommand::class, //Creates a new test for a package
                PackagerControllerMakeCommand::class, //Creates a new controller for a package
                PackagerRepositoryMakeCommand::class, //Creates a new repository for a package
                PackagerRepositoryInterfaceMakeCommand::class, //Creates a new repository interface for a packager repository
                PackagerMigrationMakeCommand::class, //Creates a new migration for a package
                PackagerFactoryMakeCommand::class, //Creates a new model factory for a package
                PackagerSeederMakeCommand::class, //Create a new seeder for a package
                PackagerMiddlewareMakeCommand::class, //Create a new middleware class for a package
                PackagerCommandMakeCommand::class, //Create a new command for a package
                RepositoryMakeCommand::class, //Creates a new repository
                RepositoryInterfaceMakeCommand::class //Creates a new repository interface
            ]);
            /*
             * OVERRIDE Illuminate\Foundation\Console commands for package purposes
             */

            //Publish config file
            $this->publishes([
                __DIR__ . '/../config/packager.php' => config_path('packager.php')
            ], 'config');

            //Publish stubs
            /*   $this->publishes([
                   __DIR__.'/../resources/stubs/' => '/stubs'
               ], 'stubs');
   */
        }
        //** END: Local environment only
    }
}
