<?php

namespace olckerstech\packager\src\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\src\traits\commandParser;
use olckerstech\packager\src\traits\packager;

class PackagerMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packager:create
        {name? : The name of the Package to create. You can provide fully qualified name. ex. vendor/package}
        {--exclude=* : Specify items to skip}
        {--entity= : Specify entity to populate new package with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package';

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

        $name = $this->getEntityName();
        $options = $this->options();

        $display_table = config('packager.command_settings.table');

        $headers = ['Command', 'Status'];
        $table = [];

        /*
         * Display before messages
         */
        $this->parseMessages(config('packager.command_messages.package_make_command.before'), $name);
        /*
         * Parse commands
         */
        $commands = config('packager.command_manifest.package_make_command');

        $bar = $this->output->createProgressBar(count($commands));

        foreach ($commands as $command) {
            $bar->advance();
            $command = $this->parsePlaceholders($command, $name);
            $table[] = [$command, $this->parseAndExecuteCommand($command, $options['exclude'])];
        }

        $bar->finish();
        $this->line(' Done');

        /*
         * Create config file
         */
        $this->comment('Creating config file...');
        $config_dir = base_path(str_replace('\\', '/','packages/'.$this->packageNameSpace.'\\config'));
        $this->createFolderIfNotExist($config_dir);
        $stub_dir = base_path(str_replace('\\', '/', 'packages/olckerstech/packager/resources/stubs'));
        copy($stub_dir.'/config.stub', $config_dir.'/'.$name.'.php');

        /*
         * Display table summary
         */
        if ($display_table) {
            $this->table($headers, $table);
        }
        /*
         * Display after messages
         */
        $this->parseMessages(config('packager.command_messages.package_make_command.after'), $name);

    }

    /**
     * Parse the package
     *
     * @return bool
     */
    public function parsePackage()
    {
        $name = $this->argument('name');

        if ($name !== null && $this->checkProvidedPackageName($name)) {
            if ($this->doesNotExistAndCantCreate()) {
                return false;
            }
            if(!file_exists(config('packager.packager_working_directory').'/'.$name)){
                if (!mkdir($concurrentDirectory = config('packager.packager_working_directory') . '/' . $name, 0777, true) && !is_dir($concurrentDirectory)) {
                    //throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    return false;
                }
            }
            return true;
        }

        $this->packagerDirectory = $this->laravel->basePath(config('packager.packager_working_directory'));

        return $this->manuallyGetPackageInformation();
    }


    public function manuallyGetPackageInformation()
    {
        if (!$this->getVendor('vendor')) {
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

    /**
     * Find the entity name to use for the package. Use Package name if none specified
     */
    public function getEntityName()
    {
        if ($this->hasOption('entity') && $this->option('entity') !== null) {
            return $this->option('entity');
        }

        return $this->packagerPackage;

    }

    public function getVendor($vendor)
    {
        $max_attempts = config('packager.command_settings.max_attempts');
        $i = 0;
        $endLoop = false;

        while ($i < $max_attempts && $endLoop === false) {
            if ($this->packagerVendor === null || $this->packagerVendor === '') {
                $this->packagerVendor = $this->ask('Name of Vendor: ');
                if ($this->packagerVendor !== null || $this->packagerVendor !== '') {
                    $endLoop = true;
                }
            } else if ($this->packagerVendor !== null || $this->packagerVendor !== '') {
                $endLoop = true;
            }
            $i++;
        }

        return !($this->packagerVendor === null || $this->packagerVendor === '');
    }

    public function getPackage($vendor)
    {
        $max_attempts = config('packager.command_settings.max_attempts');
        $i = 0;
        $endLoop = false;

        while ($i < $max_attempts && $endLoop === false) {
            if ($this->packagerPackage === null || $this->packagerPackage === '') {
                $this->packagerPackage = $this->ask('Name of Package: ');
                if ($this->packagerPackage !== null && $this->packagerPackage !== '') {
                    $endLoop = true;
                }
            } else if ($this->packagerPackage !== null && $this->packagerPackage !== '') {
                $endLoop = true;
            }
            $i++;
        }

        return !($this->packagerPackage === null || $this->packagerPackage === '');
    }
}
