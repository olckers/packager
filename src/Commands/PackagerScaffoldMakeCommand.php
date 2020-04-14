<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\src\traits\commandParser;
use olckerstech\packager\src\traits\packager;

class PackagerScaffoldMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:scaffold
        {name? : The name of the entity}
        {--exclude=* : Specify items to skip}
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
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create package');
            return false;
        }
        $options = $this->options();
        $name = $this->argument('name');

        $display_table = config('packager.command_settings.table');

        $headers = ['Command', 'Status'];
        $table = [];

        /*
         * Display before messages
         */
        $this->parseMessages(config('packager.command_messages.package_scaffold_make_command.before'), $name);
        /*
         * Parse commands
         */
        $commands = config('packager.command_manifest.package_scaffold_make_command');

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
        $this->parseMessages(config('packager.command_messages.package_scaffold_make_command.after'), $name);

    }

    /**
     * Parse the package
     *
     * @return bool
     */
    public function parsePackage()
    {
        $name = $this->argument('name');

        $this->packagerDirectory = $this->laravel->basePath(config('packager.packager_working_directory'));

        if ($name !== null && $this->checkProvidedPackageName($name) && !$this->doesNotExistAndCantCreate()) {
            $this->packageNameSpace = $this->packagerVendor . '\\' . $this->packagerPackage;
            return true;
        }



        return $this->manuallyGetPackageInformation();
    }


    public function manuallyGetPackageInformation()
    {
        if (!$this->getVendor()) {
            return false;
        }

        if (!$this->getPackage($this->packagerVendor)) {
            return false;
        }

        $this->packageNameSpace = $this->packagerVendor . '\\' . $this->packagerPackage;

        if ($this->doesNotExistAndCantCreate()) {
            return false;
        }

        return true;
    }
}
