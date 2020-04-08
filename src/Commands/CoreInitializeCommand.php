<?php

namespace olckerstech\core\src\Commands;

use Illuminate\Console\Command;

class CoreInitializeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the core framework. Always run this before running core:install';

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
        $this->info('Initializing Core package');

        //Publish install configuration file.
        $this->call('vendor:publish', [
            '--tag' => 'install'
        ]);

        $this->info('Initialization completed successfully');
        $this->comment('Run "php artisan core:install" to install the Core Package.');
        $this->call('optimize:clear');

    }
}
