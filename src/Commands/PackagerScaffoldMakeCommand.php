<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\Command;

class PackagerScaffoldMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:scaffold
        {name? : The name of the entity}
        {--package= : The name of the package the scaffold is to be created in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new entity scaffold inside a package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $options = $this->options();

        if (!isset($name)) {
            $this->line('You did not specify a name for the entity the scaffold is being created for. Please specify a name, examples - User, Tenant, Permission, etc');
            $name = $this->ask('Entity name: ');
        }

        if (isset($name)) {
            if (!isset($options['package']) || $options['package'] === false) {
                $package = $this->ask('Package name: ');
            } else {
                $package = $options['package'];
            }
            $this->info('Building scaffold for entity: ' . $name . ' in package: ' . $package);


            $this->info('Scaffold completed. See table below for generated files and their locations.');
        } else {
            $this->error('Name for scaffold entity not provided. Operation aborted.');
        }

    }
}
