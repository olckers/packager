<?php

namespace olckerstech\packager\src;

use Illuminate\Support\ServiceProvider;
use olckerstech\packager\src\Commands\PackageMakeCommand;
use olckerstech\packager\src\Commands\PackagerExceptionMakeCommand;
use olckerstech\packager\src\Commands\PackagerJobMakeCommand;
use olckerstech\packager\src\Commands\PackagerListenerMakeCommand;
use olckerstech\packager\src\Commands\PackagerMailMakeCommand;
use olckerstech\packager\src\Commands\PackagerModelMakeCommand;
use olckerstech\packager\src\Commands\PackagerNotificationMakeCommand;
use olckerstech\packager\src\Commands\PackagerObserverMakeCommand;
use olckerstech\packager\src\Commands\PackagerPolicyMakeCommand;
use olckerstech\packager\src\Commands\PackagerProviderMakeCommand;
use olckerstech\packager\src\Commands\PackagerRequestMakeCommand;
use olckerstech\packager\src\Commands\PackagerResourceMakeCommand;
use olckerstech\packager\src\Commands\PackagerScaffoldMakeCommand;
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
            ]);
            /*
             * OVERRIDE Illuminate\Foundation\Console commands for package purposes
             */

            //Publish config file
            $this->publishes([
                __DIR__.'/../config/packager.php' => config_path('packager.php')
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
