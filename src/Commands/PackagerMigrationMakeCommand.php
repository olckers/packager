<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\traits\commandParser;
use olckerstech\packager\traits\packager;

class PackagerMigrationMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:migration';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /*
     *  {--model= : Generate a resource controller for the given model.}
     *  {--parent= : Generate a nested resource controller class.}
     */
    protected $signature = 'packager:migration
        {name : Name of the Controller}
        {--package= : Fully qualified package name the Controller belongs to}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new migration for an entity in a package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'database\\migrations';

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
        $this->info('Creating Migration: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Migration');
            return false;
        }

        $options = $this->parsePackageControllerOptions();

        if(!array_key_exists('--path', $options)){
            $options += ['--path' =>
                    str_replace('\\', '/',config('packager.packager_working_directory').
                    '/'.$this->packageNameSpace.'/'.$this->packageNameSpaceModifier)
                ];
        }

        if($this->createFolderIfNotExist($options['--path'])) {
            $result = $this->executeCommand('make:migration', $options);
        }else{
            return false;
        }

        if ($result !== 'Success' && $result !== 0) {
                $this->error($result);
                return false;
        }

        return true;
    }

    public function parsePackageControllerOptions()
    {
        $returnOptions = [];
        $returnOptions += $this->parseOption('name=' . $this->argument('name'));

        $options = $this->options();

        foreach ($options as $option => $value) {
            if (($option !== 'package') && $value) {
                if (is_string($value)) {
                    $returnOptions += $this->parseOption('--' . $option . '=' . $value);
                } else {
                    $returnOptions += $this->parseOption('--' . $option);
                }
            }
        }

        return $returnOptions;
    }

    public function executeCommand($command, $options)
    {
        //$return = false;
        try {
            if (config('packager.command_settings.silent')) {
                $return = $this->callSilent($command, $options);
            } else {
                $return = $this->call($command, $options);
            }
        } catch (\Exception $e) {
            return 'Failed: ' . $e->getMessage();
        }
        return $return;
    }

    /**
     * Copies the created files from the app directory to packages. Files in
     * app directory is deleted after successful copy.
     *
     * @return bool
     */
    public function copyAndDelete($name = false)
    {
        if ($name) {
            $from = $this->laravel->basePath() . '/App/Http/Controllers/' . $name . '.php';
            $package_dir = base_path(str_replace('\\', '/', 'packages/' . $this->packageNameSpace . '/' . $this->packageNameSpaceModifier));
            $to = $package_dir . '/' . $name . '.php';
            $this->line('Moving created files to package...');
            if (!file_exists($package_dir)) {
                if (!mkdir($package_dir, 0777, true) && !is_dir($package_dir)) {
                    //throw new \RuntimeException(sprintf('Directory "%s" was not created', $package_dir));
                }
            }
            $this->line('Fixing namespace...');
            $this->replaceControllerNamespace($from);
            copy($from, $to);
            $this->line('Deleting temporary file...');
            unlink($from);
            $this->line('Done');
        }
        return true;
    }

    /**
     * Replace the namespace for the controller
     * @param $controllerPath
     */
    public function replaceControllerNamespace($controllerPath)
    {
        $contents = file_get_contents($controllerPath);
        $contents = str_replace(
            'namespace App\Http\Controllers;',
            'namespace ' . $this->packageNameSpace . '\\' . $this->packageNameSpaceModifier . '; use App\Http\Controllers\Controller;',
            $contents
        );
        file_put_contents($controllerPath, $contents);
    }
}
