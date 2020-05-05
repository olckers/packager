<?php

namespace olckerstech\packager\Commands;

use Illuminate\Console\Command;
use olckerstech\packager\traits\commandParser;
use olckerstech\packager\traits\packager;

class PackagerControllerMakeCommand extends Command
{
    use packager;
    use commandParser;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'packager:controller';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /*
     *  {--model= : Generate a resource controller for the given model.}
     *  {--parent= : Generate a nested resource controller class.}
     */
    protected $signature = 'packager:controller
        {name : Name of the Controller}
        {--package= : Fully qualified package name the Controller belongs to}
        {--api : Exclude the create and edit methods from the controller.}
        {--force : Create the class even if the controller already exists}
        {--invokable : Generate a single method, invokable controller class.}
        {--resource : Generate a resource controller class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a new controller for an entity in a package';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Namespace modifier for this generator command instance
     *
     * @var string
     */
    protected $packageNameSpaceModifier = 'src\\Controllers';

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
        $this->info('Creating Controller: ' . $this->argument('name'));
        if (!$this->parsePackage()) {
            $this->error('FAILED. Could not create Controller');
            return false;
        }

        $options = $this->parsePackageControllerOptions();

        $result = $this->executeCommand('make:controller', $options);
        if ($result === 'Success') {
            if (!$this->copyAndDelete($this->argument('name'))) {
                $this->error('FAILED. Could either not move and/or delete the created files');
                return false;
            }
        } else {
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
