<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\traits\commandParser;

class ScaffoldMakeCommand extends Command
{
    use commandParser;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold
        {name? : The name of the entity}
        {--exclude=* : Specify items to skip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a entity scaffold in the Laravel framework. Commonly used functions and features';

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

        $display_table = config('packager.command_settings.table');

        $headers = ['Command', 'Status'];
        $table = [];

        if (!isset($name)) {
            $this->line('You did not specify a name for the entity the scaffold is being created for. Please specify a name, examples - User, Tenant, Permission, etc');
            $name = $this->ask('Entity name: ');
        }

        if (isset($name)) {
            /*
             * Display before messages
             */
            $this->parseMessages(config('packager.command_messages.scaffold_make_command.before'), $name);
            /*
             * Parse commands
             */
            $commands = config('packager.command_manifest.scaffold_make_command');

            $bar = $this->output->createProgressBar(count($commands));

            foreach ($commands as $command) {
                $bar->advance();
                $command = $this->parsePlaceholders($command, $name);
                $table[] = [$command, $this->parseAndExecuteCommand($command, $options['exclude'])];
            }

            $bar->finish();
            $this->line(' Done');

            /*
             * Display table summary
             */
            if ($display_table) {
                $this->table($headers, $table);
            }
            /*
             * Display after messages
             */
            $this->parseMessages(config('packager.command_messages.scaffold_make_command.after'), $name);
        } else {
            $this->error('Name for scaffold entity not provided. Operation aborted.');
        }

    }

}
