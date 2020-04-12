<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\Command;

class PackageMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:create
        {name? : The name of the entity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package scaffold';

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
            $this->line('You did not specify a name for the package...');
            $name = $this->ask('Package name: ');
        }

        if(isset($name)) {
            $this->info('Building package: ' . $name);


            $this->info('Package completed. See table below for generated files and their locations.');
        }else{
            $this->error('Name for package not provided. Operation aborted.');
        }

    }
}
